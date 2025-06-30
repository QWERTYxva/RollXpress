<?php
// C:/laragon/www/RollXpress/prueba_final_transbank.php
// Este es un script de prueba mínimo para aislar la conexión con Transbank.

// 1. Cargar únicamente el autoloader de Composer.
require_once __DIR__ . '/vendor/autoload.php';

// 2. Importar las clases necesarias.
use Transbank\Webpay\Options;
use Transbank\Webpay\WebpayPlus\Transaction;

echo "<h1>Prueba Final de Conexión a Transbank</h1>";
echo "<p>Intentando crear una transacción de prueba...</p>";
echo "<hr>";

// 3. Definir las credenciales de prueba.
$commerceCode = '597055555532';
$apiKey = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1B';

// 4. Datos de la transacción de prueba.
$buyOrder = 'ORDEN-PRUEBA-' . time();
$sessionId = 'SESION-PRUEBA-' . time();
$amount = 1000;
$returnUrl = 'http://rollxpress.test/pago_exitoso.php'; // Una URL de retorno de ejemplo.

try {
    // 5. Configurar y crear la transacción.
    $options = Options::forIntegration($commerceCode, $apiKey);
    $tx = new Transaction($options);
    $response = $tx->create($buyOrder, $sessionId, $amount, $returnUrl);

    echo "<h2>¡ÉXITO!</h2>";
    echo "<p>Se ha creado la transacción correctamente.</p>";
    echo "<p>Serás redirigido a Webpay en 5 segundos...</p>";
    echo '<p>Token: ' . htmlspecialchars($response->getToken()) . '</p>';
    echo '<p>URL: <a href="' . htmlspecialchars($response->getUrl()) . '?token_ws=' . htmlspecialchars($response->getToken()) . '">Ir a Webpay Manualmente</a></p>';
    
    // Redirección automática con JavaScript.
    echo '<script>setTimeout(function() { window.location.href = "' . $response->getUrl() . '?token_ws=' . $response->getToken() . '"; }, 5000);</script>';


} catch (Exception $e) {
    echo "<h2>ERROR</h2>";
    echo "<p>La conexión con Transbank ha fallado. Esto confirma un problema en tu entorno local (probablemente el Antivirus).</p>";
    echo "<p><strong>Mensaje del error:</strong></p>";
    echo "<pre style='background-color: #ffecec; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; color: #721c24;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>
