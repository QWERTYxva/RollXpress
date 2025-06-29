<?php
// db.php - Versión Robusta con Reporte de Errores

$host = 'localhost'; 
$dbname = 'rollxpress';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Opciones de conexión
$options = [
    // La línea más importante: hace que PDO lance excepciones si hay un error SQL
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Devuelve los resultados como arrays asociativos
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    // Intentamos crear la conexión
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Si la conexión falla, detiene todo y muestra el error.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>