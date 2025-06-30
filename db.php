<?php
// C:/laragon/www/RollXpress/db.php (Versión para Gitpod)

$host = "127.0.0.1"; // En Gitpod, usa 127.0.0.1 en lugar de 'localhost' para forzar la conexión por red.
$port = 3306;
$db_name = "rollxpress";
$username = "root";
$password = ""; // La contraseña del usuario root en Gitpod también está vacía.
$charset = "utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=$charset";

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Si la conexión falla, muestra un error detallado.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
