<?php
// admin/seguridad.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario no ha iniciado sesión, lo redirigimos a la página de login
if (!isset($_SESSION['user_id'])) {
    // Usamos ../ para "subir" un nivel de la carpeta admin a la raíz
    header("Location: ../login.php");
    exit();
}
// En un futuro, aquí podrías verificar si el usuario tiene rol de "administrador"
?>