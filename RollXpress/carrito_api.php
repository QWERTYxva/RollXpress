<?php
// carrito_api.php - Versión Final y Completa

// Iniciamos la sesión para acceder al ID del usuario
session_start();
// Incluimos la conexión a la base de datos
require_once 'db.php';

// 1. SEGURIDAD: Nos aseguramos de que el usuario haya iniciado sesión para cualquier acción.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Error: Debes iniciar sesión para realizar esta acción.']);
    exit();
}

$usuario_id = $_SESSION['user_id'];
// Leemos los datos enviados por JavaScript (en formato JSON)
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

// Le decimos al navegador que la respuesta siempre será en formato JSON
header('Content-Type: application/json');

try {
    switch ($action) {
        case 'get':
            $stmt = $pdo->prepare(
                "SELECT ci.id, ci.producto_id, ci.cantidad, ci.is_custom, ci.custom_nombre, ci.custom_precio, ci.custom_descripcion, p.nombre, p.precio, p.imagen 
                 FROM carrito_items ci 
                 LEFT JOIN productos p ON ci.producto_id = p.id 
                 WHERE ci.usuario_id = ?"
            );
            $stmt->execute([$usuario_id]);
            echo json_encode(['success' => true, 'cart' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;

        case 'add':
            if (isset($data['custom_product'])) {
                $custom = $data['custom_product'];
                $sql = "INSERT INTO carrito_items (usuario_id, producto_id, is_custom, custom_nombre, custom_precio, custom_descripcion, cantidad) VALUES (?, NULL, 1, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$usuario_id, $custom['nombre'], $custom['precio'], $custom['custom_description'], 1]);
            } elseif (isset($data['producto_id'])) {
                $producto_id = $data['producto_id'];
                $stmt = $pdo->prepare("SELECT * FROM carrito_items WHERE usuario_id = ? AND producto_id = ? AND is_custom = 0");
                $stmt->execute([$usuario_id, $producto_id]);
                $item = $stmt->fetch();
                if ($item) {
                    $stmt = $pdo->prepare("UPDATE carrito_items SET cantidad = cantidad + 1 WHERE id = ?");
                    $stmt->execute([$item['id']]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO carrito_items (usuario_id, producto_id, cantidad) VALUES (?, ?, 1)");
                    $stmt->execute([$usuario_id, $producto_id]);
                }
            } else {
                throw new Exception('Datos de producto no válidos.');
            }
            echo json_encode(['success' => true]);
            break;


        case 'update':
            $item_id = $data['item_id'] ?? 0;
            $cantidad = $data['cantidad'] ?? 0;
            if (!$item_id) throw new Exception('ID de item no válido.');

            if ($cantidad > 0) {
                $stmt = $pdo->prepare("UPDATE carrito_items SET cantidad = ? WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$cantidad, $item_id, $usuario_id]);
            } else {
                $stmt = $pdo->prepare("DELETE FROM carrito_items WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$item_id, $usuario_id]);
            }
            echo json_encode(['success' => true, 'message' => 'Carrito actualizado.']);
            break;

        case 'checkout':
            $direccion = strip_tags(trim($data['direccion'] ?? ''));
            if (empty($direccion)) throw new Exception('La dirección es obligatoria.');

            $pdo->beginTransaction(); // Iniciar transacción
            
            $stmt_cart = $pdo->prepare("SELECT ci.id, ci.cantidad, ci.is_custom, ci.custom_nombre, ci.custom_precio, ci.custom_descripcion, p.nombre, p.precio FROM carrito_items ci LEFT JOIN productos p ON ci.producto_id = p.id WHERE ci.usuario_id = ?");
            $stmt_cart->execute([$usuario_id]);
            $items_pedido = $stmt_cart->fetchAll();

            if (empty($items_pedido)) throw new Exception('El carrito está vacío.');

            $total = 0;
            $detalles_para_json = [];
            foreach ($items_pedido as $item) {
                $precio_item = $item['is_custom'] ? $item['custom_precio'] : $item['precio'];
                $nombre_item = $item['is_custom'] ? $item['custom_nombre'] . ' (Personalizado)' : $item['nombre'];
                $total += $precio_item * $item['cantidad'];
                $detalles_para_json[] = ['nombre' => $nombre_item, 'cantidad' => $item['cantidad'], 'descripcion' => $item['custom_descripcion']];
            }
            $detalles_del_pedido = json_encode($detalles_para_json);

            $sql_pedido = "INSERT INTO pedidos (usuario_id, detalles_del_pedido, total, direccion_entrega, estado) VALUES (?, ?, ?, ?, 'Recibido')";
            $stmt_pedido = $pdo->prepare($sql_pedido);
            $stmt_pedido->execute([$usuario_id, $detalles_del_pedido, $total, $direccion]);
            $pedido_id = $pdo->lastInsertId();

            $stmt_delete = $pdo->prepare("DELETE FROM carrito_items WHERE usuario_id = ?");
            $stmt_delete->execute([$usuario_id]);

            $pdo->commit(); // Confirmar transacción
            echo json_encode(['success' => true, 'message' => 'Pedido creado.', 'pedido_id' => $pedido_id]);
            break;

        default:
            throw new Exception('Acción no reconocida.');
    }
} catch (Exception $e) {
    // Si algo falla en el 'try', se captura el error aquí.
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // Revertir transacción si falló
    }
    http_response_code(500); // Código de error de servidor
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
?>