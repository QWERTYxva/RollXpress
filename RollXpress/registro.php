<?php
// registro.php

// NUEVO: Iniciamos la sesión al principio de todo para poder usar $_SESSION.
session_start();

require 'db.php';
$error = '';
// Ya no necesitamos la variable $success aquí.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    if (empty($nombre) || empty($email) || empty($telefono) || empty($password) || empty($password_confirm)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Este correo electrónico ya está registrado.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO usuarios (nombre, email, password, telefono) VALUES (?, ?, ?, ?)";
            $stmt_insert = $pdo->prepare($sql_insert);

            if ($stmt_insert->execute([$nombre, $email, $hashed_password, $telefono])) {
                // --- CAMBIO CLAVE AQUÍ ---
                // 1. Guardamos el mensaje de éxito en la sesión.
                $_SESSION['flash_message'] = "¡Cuenta creada exitosamente! Ya puedes iniciar sesión.";
                
                // 2. Redirigimos al usuario a la página de login.
                header("Location: login.php");
                
                // 3. Detenemos la ejecución del script actual.
                exit();
                // --- FIN DEL CAMBIO ---
            } else {
                $error = 'Hubo un error al crear la cuenta. Inténtalo de nuevo.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - RollXpress</title>
    <link rel="stylesheet" href="css/stylesregister.css">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body class="register-page">
    <div class="register-container">
        <a href="index.php"><img src="img/logo.svg" alt="Logo de RollXpress" class="logo"></a>
        <h1>Crea tu Cuenta</h1>
        <p class="register-subtitle">Únete a la familia RollXpress para pedidos más rápidos.</p>
        
        <?php if ($error): ?>
            <div class="register-message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Tu nombre y apellido" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="tu@email.com" required>
            </div>
            <div class="form-group">
                <label for="telefono" class="form-label">Número de Teléfono</label>
                <input type="tel" id="telefono" name="telefono" class="form-input" placeholder="Ej: 912345678" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Crea una contraseña segura" required>
            </div>
            <div class="form-group">
                <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-input" placeholder="Vuelve a escribir la contraseña" required>
            </div>
            <button type="submit" class="btn-register">Crear Cuenta</button>
        </form>

        <div class="register-footer">
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión</a></p>
        </div>
    </div>
</body>
</html>