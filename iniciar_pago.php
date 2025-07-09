<?php
// iniciar_pago_flow.php - VERSIÓN FINAL CORREGIDA

session_start();
require_once 'config.php'; // Carga tus credenciales
require_once 'db.php';     // Carga la conexión a la BD

// --- 1. Validaciones de Seguridad Esenciales ---

// Si no hay un usuario logueado, no se puede pagar.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=session');
    exit();
}

// Verificamos que el monto a pagar exista en la sesión (lo guardamos en checkout.php)
// Si no existe o es cero, algo salió mal, así que redirigimos.
if (!isset($_SESSION['monto_total_a_pagar']) || $_SESSION['monto_total_a_pagar'] <= 0) {
    header('Location: checkout.php?error=no_amount_in_session');
    exit();
}

// --- 2. Obtenemos los Datos de Forma Segura ---

$monto = $_SESSION['monto_total_a_pagar'];
$usuario_id = $_SESSION['user_id'];

// Generamos una orden de compra única y segura en el servidor.
$orden_compra = 'rollxpress-' . $usuario_id . '-' . time();

// Obtenemos el email del usuario desde la BD para la notificación de Flow.
$stmt = $pdo->prepare("SELECT email FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario_email = $stmt->fetchColumn();

// Si por alguna razón no se encuentra el email, usamos uno de respaldo.
if (!$usuario_email) {
    $usuario_email = 'sin-email@rollxpress.cl';
}


// --- 3. Preparamos los Datos para la API de Flow ---

$concepto = 'Compra de productos en RollXpress';
// Construimos las URLs de retorno y confirmación de forma dinámica.
$url_base = "https://tudominio.com"; // <-- IMPORTANTE: Cambia esto a la URL real de tu sitio web
$url_confirmacion = $url_base . '/confirmar_pago.php';
$url_retorno = $url_base . '/pedidos.php?pago=exitoso';


$parametros = [
    'apiKey'        => FLOW_API_KEY,
    'commerceOrder' => $orden_compra,
    'subject'       => $concepto,
    'currency'      => 'CLP',
    'amount'        => $monto,
    'email'         => $usuario_email,
    'urlConfirmation' => $url_confirmacion,
    'urlReturn'     => $url_retorno,
];

// --- 4. Firmamos la Petición (Requisito de seguridad de Flow) ---
ksort($parametros);
$datos_a_firmar = '';
foreach ($parametros as $key => $value) {
    $datos_a_firmar .= $key . $value;
}
$firma = hash_hmac('sha256', $datos_a_firmar, FLOW_SECRET_KEY);
$parametros['s'] = $firma;


// --- 5. Enviamos la Solicitud a Flow y Redirigimos ---
try {
    $ch = curl_init(FLOW_API_URL . '/payment/create');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));

    $respuesta_raw = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }
    curl_close($ch);

    $respuesta_json = json_decode($respuesta_raw, true);

    if (isset($respuesta_json['url']) && isset($respuesta_json['token'])) {
        // Antes de redirigir, eliminamos el monto de la sesión para que no se pueda reutilizar.
        unset($_SESSION['monto_total_a_pagar']);

        $url_pago_flow = $respuesta_json['url'] . '?token=' . $respuesta_json['token'];
        header('Location: ' . $url_pago_flow);
        exit();
    } else {
        throw new Exception($respuesta_json['message'] ?? 'Respuesta inválida de Flow.');
    }

} catch (Exception $e) {
    // Si algo falla, lo registramos (opcional) y redirigimos con un error claro.
    error_log("Error en iniciar_pago.php: " . $e->getMessage());
    header('Location: checkout.php?error=flow_connection&message=' . urlencode($e->getMessage()));
    exit();
}
?>