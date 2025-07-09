<?php
// confirmar_pago_flow.php - VERSIÓN FINAL Y CORREGIDA

require_once 'config.php'; // Carga tus credenciales de Flow y configuración
require_once 'db.php';     // Carga la conexión a la base de datos

// 1. OBTENER EL TOKEN DE LA NOTIFICACIÓN
// Flow siempre envía la confirmación a través de un método POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 Method Not Allowed
    echo "Metodo no permitido.";
    exit;
}

$token = $_POST['token'] ?? null;
if (!$token) {
    http_response_code(400); // 400 Bad Request
    echo "Token no proporcionado.";
    // Es una buena práctica registrar estos eventos en un log de errores.
    // error_log('Flow Confirmation: No se recibió el token.');
    exit;
}

// 2. PREPARAMOS LOS DATOS PARA CONSULTAR EL ESTADO DEL PAGO A FLOW
$parametros = [
    'apiKey' => FLOW_API_KEY,
    'token'  => $token,
];

// 3. FIRMAMOS LA SOLICITUD (ES REQUERIDO POR FLOW POR SEGURIDAD)
// Se ordenan los parámetros alfabéticamente antes de crear la firma.
ksort($parametros);
$datos_a_firmar = '';
foreach ($parametros as $key => $value) {
    $datos_a_firmar .= $key . $value;
}
$firma = hash_hmac('sha256', $datos_a_firmar, FLOW_SECRET_KEY);
$parametros['s'] = $firma;

// 4. CONSULTAMOS A LA API DE FLOW PARA OBTENER EL ESTADO REAL DEL PAGO
// Usamos cURL para hacer la petición a la API de Flow.
try {
    $ch = curl_init();
    // Preparamos la URL con los parámetros (ej: /payment/getStatus?apiKey=...&token=...&s=...)
    curl_setopt($ch, CURLOPT_URL, FLOW_API_URL . '/payment/getStatus?' . http_build_query($parametros));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $respuesta_raw = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }
    
    curl_close($ch);

    if ($http_code != 200 || !$respuesta_raw) {
        throw new Exception('Flow no respondió correctamente. Código HTTP: ' . $http_code);
    }

    $respuesta_json = json_decode($respuesta_raw, true);

} catch (Exception $e) {
    // Si la comunicación con Flow falla, respondemos con error y lo registramos.
    http_response_code(503); // 503 Service Unavailable
    // error_log('Flow Confirmation: Fallo en cURL. ' . $e->getMessage());
    echo 'Error al contactar a Flow.';
    exit;
}


// 5. VERIFICAMOS EL ESTADO DEL PAGO RECIBIDO DE FLOW
// status = 1 (pendiente), 2 (pagada), 3 (rechazada), 4 (anulada)
if (isset($respuesta_json['status']) && $respuesta_json['status'] == 2) {
    
    // Si el pago está APROBADO, procedemos a guardar la orden.
    // Para identificar al usuario, extraemos su ID desde la orden de compra que generamos.
    $orden_compra = $respuesta_json['commerceOrder'] ?? '';
    preg_match('/rx-(\d+)-/', $orden_compra, $matches);
    $usuario_id = $matches[1] ?? null;

    if (!$usuario_id) {
        // Si no podemos identificar al usuario, no podemos procesar el pedido.
        http_response_code(400); // 400 Bad Request
        // error_log('Flow Confirmation: No se pudo extraer el usuario_id de la orden ' . $orden_compra);
        exit;
    }
    
    // Medida de seguridad: verificamos que no hayamos procesado ya este pedido.
    $stmt_check = $pdo->prepare("SELECT id FROM pedidos WHERE transaccion_id = ?");
    $stmt_check->execute([$orden_compra]);
    if ($stmt_check->fetch()) {
        http_response_code(200); // Ya fue procesado, respondemos OK a Flow pero no hacemos nada más.
        echo 'Pedido ya registrado.';
        exit;
    }

    // Usamos una transacción de base de datos para asegurar que todo se guarde correctamente.
    // Si algo falla, se revierte todo y no se crea el pedido a medias.
    $pdo->beginTransaction();
    try {
        $monto_pagado_flow = $respuesta_json['amount'];
        
        // Obtenemos los items del carrito para este usuario desde la base de datos
        $stmt_cart = $pdo->prepare(
            "SELECT ci.id, ci.is_custom, ci.cantidad, ci.custom_nombre, ci.custom_precio, ci.custom_descripcion, p.nombre, p.precio 
             FROM carrito_items ci 
             LEFT JOIN productos p ON ci.producto_id = p.id 
             WHERE ci.usuario_id = ?"
        );
        $stmt_cart->execute([$usuario_id]);
        $items_pedido = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items_pedido)) {
            throw new Exception('El carrito para el usuario ' . $usuario_id . ' estaba vacío al confirmar el pago.');
        }

        // Medida de seguridad CLAVE: Recalculamos el total y lo comparamos con lo que Flow dice que se pagó.
        // Esto evita que alguien modifique el precio en el frontend.
        $total_recalculado = 0;
        $detalles_para_json = [];
        foreach ($items_pedido as $item) {
            $es_personalizado = $item['is_custom'] == 1;
            $precio_item = $es_personalizado ? $item['custom_precio'] : $item['precio'];
            $nombre_item = $es_personalizado ? ($item['custom_nombre'] ?: 'Roll Personalizado') : $item['nombre'];
            $descripcion = $es_personalizado ? $item['custom_descripcion'] : '';
            
            $total_recalculado += $precio_item * $item['cantidad'];
            $detalles_para_json[] = ['nombre' => $nombre_item, 'cantidad' => $item['cantidad'], 'descripcion' => $descripcion];
        }
        
        // Comparamos los montos (con una pequeña tolerancia para decimales).
        if (abs((float)$total_recalculado - (float)$monto_pagado_flow) > 1) {
            throw new Exception("ALERTA DE SEGURIDAD: Monto pagado en Flow ($monto_pagado_flow) no coincide con el total del carrito ($total_recalculado) para el usuario $usuario_id.");
        }

        // Obtenemos la dirección de entrega del usuario
        $stmt_user = $pdo->prepare("SELECT direccion FROM usuarios WHERE id = ?");
        $stmt_user->execute([$usuario_id]);
        $direccion_entrega = $stmt_user->fetchColumn();

        // Convertimos los detalles a JSON para guardarlos en un solo campo
        $detalles_del_pedido_json = json_encode($detalles_para_json, JSON_UNESCAPED_UNICODE);

        // Insertamos el pedido en la tabla `pedidos`
        $sql_pedido = "INSERT INTO pedidos (usuario_id, detalles_del_pedido, total, direccion_entrega, estado, transaccion_id) VALUES (?, ?, ?, ?, 'Recibido', ?)";
        $stmt_pedido = $pdo->prepare($sql_pedido);
        $stmt_pedido->execute([$usuario_id, $detalles_del_pedido_json, $monto_pagado_flow, $direccion_entrega, $orden_compra]);

        // Vaciamos el carrito del usuario ahora que la compra está completa
        $stmt_delete = $pdo->prepare("DELETE FROM carrito_items WHERE usuario_id = ?");
        $stmt_delete->execute([$usuario_id]);
        
        // Si todo salió bien, confirmamos los cambios en la base de datos.
        $pdo->commit();
        
    } catch (Exception $e) {
        // Si algo falló, revertimos todos los cambios.
        $pdo->rollBack();
        // error_log('Flow Confirmation: Error al procesar el pedido para la orden ' . $orden_compra . '. Error: ' . $e->getMessage());
        http_response_code(500); // 500 Internal Server Error
        echo 'Error interno al guardar el pedido.';
        exit;
    }
}

// Respondemos a Flow con un código 200 para que sepa que recibimos la confirmación.
http_response_code(200);
echo "Confirmacion recibida.";
?>