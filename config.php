<?php

// --- Configuración de la Base de Datos ---
$db_host = "127.0.0.1";          // Host de la base de datos (127.0.0.1 para Laragon)
$db_name = "rollxpress";         // Nombre de tu base de datos
$db_user = "root";               // Usuario de la base de datos
$db_pass = "";                   // Contraseña (usualmente vacía en Laragon)
$db_charset = "utf8mb4";         // Juego de caracteres (muy recomendado)

// --- 2. Credenciales de la Pasarela de Pago (Flow) ---
define('FLOW_API_KEY', 'TU_API_KEY_AQUI');
define('FLOW_SECRET_KEY', 'TU_SECRET_KEY_AQUI');

// --- 3. Modo de la aplicación ---
// Cambia a 'true' cuando pases a producción para usar las credenciales reales de Flow
$is_production = false;

if ($is_production) {
    define('FLOW_API_URL', 'https://www.flow.cl/api');
} else {
    // URL de Sandbox para hacer pruebas sin dinero real
    define('FLOW_API_URL', 'https://sandbox.flow.cl/api');
}

// Habilitar la visualización de errores de PHP mientras desarrollas.
// En producción, esto debería estar en Off (0).
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>