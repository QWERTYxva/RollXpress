<?php
// db.php - VERSIÓN CORRECTA

// 1. Cargamos nuestras variables de configuración. __DIR__ asegura que la ruta siempre sea correcta.
require_once __DIR__ . '/config.php';

// 2. Opciones de conexión para PDO. Son buenas prácticas de seguridad y manejo de errores.
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 3. Creamos la "cadena de conexión" (DSN) usando las variables que cargamos de config.php
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";

try {
    // 4. Creamos la instancia de PDO. Esta variable $pdo estará disponible en cualquier
    // archivo que incluya db.php
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Si la conexión falla, el script se detiene y muestra un error claro.
    // Esto es muy útil durante el desarrollo para saber exactamente qué falló.
    die("Error Crítico de Conexión a la Base de Datos: " . $e->getMessage());
}
?>