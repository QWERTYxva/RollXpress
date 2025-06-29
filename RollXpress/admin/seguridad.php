<?php
// admin/seguridad.php - Versión Mejorada

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario ha iniciado sesión Y si la variable de sesión 'is_admin' es verdadera.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Si no cumple las condiciones, lo redirigimos al login del admin.
    header('Location: login.php');
    exit();
}