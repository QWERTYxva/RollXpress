<?php
// admin/login.php
session_start();
require_once '../db.php'; // Subimos un nivel para encontrar db.php

// Si el usuario ya está logueado como admin, lo redirigimos al panel
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Por favor, ingresa tu correo y contraseña.';
    } else {
        // Buscamos un usuario que coincida con el email Y que sea administrador (is_admin = 1)
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND is_admin = 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        // Verificamos si el admin existe y si la contraseña es correcta
        if ($admin && password_verify($password, $admin['password'])) {
            // Si todo es correcto, creamos la sesión de admin
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['nombre'];
            $_SESSION['is_admin'] = true; // ¡La variable clave de seguridad!

            header('Location: index.php'); // Redirigimos al panel principal
            exit();
        } else {
            $error = 'Credenciales incorrectas o no tienes permiso de administrador.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Panel de Administración</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css?v=1.1">
</head>
<body class="login-body">

<div class="login-box">
    <img src="../img/logo.svg" alt="Logo" class="login-logo">
    <h2>Acceso al Panel</h2>
    
    <?php if ($error): ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="input-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="input-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Ingresar</button>
    </form>
</div>

</body>
</html>