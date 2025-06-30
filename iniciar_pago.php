<?php
// iniciar_pago.php (Versión final compatible con Transbank SDK v3.x)

// Iniciar la sesión al principio de todo.
session_start();

// Cargar el autoloader de Composer, que gestiona todas las librerías.
require_once __DIR__ . '/vendor/autoload.php';

// Importar las clases necesarias de Transbank.
use Transbank\Webpay\Options;
use Transbank\Webpay\WebpayPlus\Transaction;

// --- Verificación de Datos de Entrada ---
// Asegurarse de que el monto viene por POST y es un número válido.
if (!isset($_POST['monto']) || !is_numeric($_POST['monto']) || $_POST['monto'] <= 0) {
    die("Error: El monto del pedido no es válido o no fue proporcionado.");
}
$monto = (float)$_POST['monto'];

// Generar un ID de orden de compra único si no se proporciona.
$orden_compra = $_POST['orden_compra'] ?? 'RX-' . time();

// --- Configuración de Transbank ---
// Es una buena práctica definir las credenciales en variables.
// Credenciales de PRUEBAS (Integración) para Webpay Plus.
$commerceCode = '597055555532';
$apiKey = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1B';

// La URL a la que Transbank redirigirá al usuario después del pago.
// DEBE ser una URL pública y accesible desde internet.
$return_url = 'https://www.rollxpress.cl/confirmar_pago.php';

// --- Creación de la Transacción ---
try {
    // ESTA ES LA LÍNEA CORREGIDA:
    // Para el SDK v3, se usa el método estático `forIntegration` para obtener la configuración de prueba.
    $options = Options::forIntegration($commerceCode, $apiKey);

    // Se crea una instancia de la transacción, pasándole la configuración correcta.
    $tx = new Transaction($options);

    // Obtiene el ID de sesión actual.
    $session_id = session_id();

    // Crea la transacción en Transbank.
    $response = $tx->create($orden_compra, $session_id, $monto, $return_url);

    // --- Redirección a Webpay ---
    // Si la respuesta contiene una URL y un Token, la creación fue exitosa.
    if (isset($response->url) && isset($response->token)) {
        // Redirige al usuario al formulario de pago de Webpay.
        header('Location: ' . $response->url . '?token_ws=' . $response->token);
        exit; // Termina la ejecución del script para asegurar la redirección.
    } else {
        // Si la respuesta no tiene la forma esperada, muestra un error detallado.
        echo "<h3>Error al crear la transacción</h3>";
        echo "<p>La respuesta de Transbank no fue la esperada.</p>";
        echo "<pre>";
        print_r($response);
        echo "</pre>";
        die();
    }

} catch (\Exception $e) {
    // Si ocurre cualquier otro error (conexión, configuración, etc.), muestra un mensaje claro.
    die('Error al conectar con WebPay: ' . htmlspecialchars($e->getMessage()));
}
?>
