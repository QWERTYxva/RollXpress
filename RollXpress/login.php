<?php
// login.php
session_start();

// Esta parte no cambia: si ya está logueado, lo mandamos a su perfil.
if (isset($_SESSION['user_id'])) {
    header("Location: perfil.php");
    exit();
}

require 'db.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // La lógica de procesar el login no cambia.
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'Email y contraseña son obligatorios.';
    } else {
        $sql = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            header("Location: perfil.php");
            exit();
        } else {
            $error = 'Email o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - RollXpress</title>
    <link rel="stylesheet" href="css/styleslogin.css">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body class="login-page">

    <div class="login-container">
        <a href="index.php"><img src="img/logo.svg" alt="Logo de RollXpress" class="logo"></a>
        <h1>Bienvenido de Vuelta</h1>
        <p class="login-subtitle">Ingresa tus datos para acceder a tu cuenta.</p>
        
        <?php
        // --- CAMBIO CLAVE AQUÍ ---
        // 1. Revisamos si existe un mensaje "flash" de éxito desde el registro.
        if (isset($_SESSION['flash_message'])) {
            // 2. Si existe, lo mostramos con una clase de éxito.
            echo '<div class="login-message success">' . htmlspecialchars($_SESSION['flash_message']) . '</div>';
            // 3. MUY IMPORTANTE: Lo borramos para que no se muestre de nuevo.
            unset($_SESSION['flash_message']);
        }
        // --- FIN DEL CAMBIO ---

        // El mensaje de error del login sigue funcionando como antes.
        if ($error): ?>
            <div class="login-message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="tu@email.com" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>

        <div class="login-footer">
            <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>

</body>
</html>