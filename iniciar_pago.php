<?php
// iniciar_pago.php
session_start();
require_once 'vendor/autoload.php'; // Si instalaste con Composer
// Si lo descargaste manualmente, la ruta sería:
require_once 'transbank_sdk/init.php'; 

use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;

// Configuración para el ambiente de PRUEBAS (Integración)
WebpayPlus::configureForIntegration(
    '597055555532', 
    '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1B'
);

$monto = $_POST['monto'] ?? 0;
$orden_compra = $_POST['orden_compra'] ?? 'orden-invalida';
$session_id = session_id();
$return_url = 'https://www.rollxpress.cl/confirmar_pago.php'; // URL a la que volverá el cliente

if ($monto <= 0) {
    die("El monto del pedido no es válido.");
}

try {
    $response = (new Transaction)->create($orden_compra, $session_id, $monto, $return_url);
    
    // Si la creación de la transacción es exitosa, redirigimos al usuario a WebPay
    if (isset($response->url) && isset($response->token)) {
        header('Location: ' . $response->url . '?token_ws=' . $response->token);
        exit();
    } else {
        die("No se pudo crear la transacción en WebPay.");
    }

} catch (Exception $e) {
    die('Error al conectar con WebPay: ' . $e->getMessage());
}
?>