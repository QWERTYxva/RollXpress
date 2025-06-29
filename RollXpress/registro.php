<?php
// registro.php - Versión con campo de Dirección
require_once 'db.php';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']); // Nuevo campo

    if (empty($nombre) || empty($email) || empty($password) || empty($telefono) || empty($direccion)) {
        $mensaje = '<div class="form-message error">Todos los campos son obligatorios.</div>';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $mensaje = '<div class="form-message error">Este correo electrónico ya está registrado.</div>';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Query actualizado para incluir la dirección
            $sql = "INSERT INTO usuarios (nombre, email, password, telefono, direccion) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$nombre, $email, $hashed_password, $telefono, $direccion])) {
                header('Location: login.php?registro=exitoso');
                exit();
            } else {
                $mensaje = '<div class="form-message error">Hubo un error al crear tu cuenta.</div>';
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
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="form-input" placeholder="Ej: Av. Siempre Viva 123" required>
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