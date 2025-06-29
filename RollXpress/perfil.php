<?php
// perfil.php
session_start();
require_once 'db.php';

// 1. SEGURIDAD: Si no hay un usuario en la sesión, lo redirigimos al login.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['user_id'];
$mensaje_datos = ''; // Para mensajes de éxito/error del formulario de datos
$mensaje_pass = '';  // Para mensajes del formulario de contraseña

// 2. LÓGICA PARA PROCESAR LOS FORMULARIOS CUANDO SE ENVÍAN (MÉTODO POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // A. Si se envió el formulario de "actualizar datos"
    if (isset($_POST['actualizar_datos'])) {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion'] ?? '');

        // Validar que los campos no estén vacíos
        if (empty($nombre) || empty($email) || empty($telefono) || empty($direccion)) {
            $mensaje_datos = '<div class="form-message error">Todos los campos son obligatorios.</div>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje_datos = '<div class="form-message error">El formato del email no es válido.</div>';
        } else {
            // Actualizamos los datos en la base de datos
            $sql = "UPDATE usuarios SET nombre = ?, email = ?, telefono = ?, direccion = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$nombre, $email, $telefono, $direccion, $usuario_id])) {
                // Actualizamos el nombre en la sesión por si cambió
                $_SESSION['user_name'] = $nombre;
                $mensaje_datos = '<div class="form-message success">¡Datos actualizados con éxito!</div>';
            } else {
                $mensaje_datos = '<div class="form-message error">Hubo un error al actualizar tus datos.</div>';
            }
        }
    }

    // B. Si se envió el formulario de "cambiar contraseña"
    if (isset($_POST['cambiar_password'])) {
        $pass_actual = $_POST['password_actual'];
        $pass_nueva = $_POST['password_nueva'];
        $pass_confirmar = $_POST['password_confirmar'];

        if (empty($pass_actual) || empty($pass_nueva) || empty($pass_confirmar)) {
            $mensaje_pass = '<div class="form-message error">Debes rellenar todos los campos de contraseña.</div>';
        } elseif ($pass_nueva !== $pass_confirmar) {
            $mensaje_pass = '<div class="form-message error">Las contraseñas nuevas no coinciden.</div>';
        } else {
            // Verificamos la contraseña actual contra la de la base de datos
            $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $user = $stmt->fetch();

            if ($user && password_verify($pass_actual, $user['password'])) {
                // Si la contraseña actual es correcta, hasheamos y guardamos la nueva
                $nuevo_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
                $sql_update = "UPDATE usuarios SET password = ? WHERE id = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([$nuevo_hash, $usuario_id]);
                $mensaje_pass = '<div class="form-message success">¡Contraseña cambiada con éxito!</div>';
            } else {
                $mensaje_pass = '<div class="form-message error">La contraseña actual es incorrecta.</div>';
            }
        }
    }
}

// 3. OBTENER LOS DATOS DEL USUARIO PARA MOSTRARLOS EN EL FORMULARIO
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();


// --- Título de la página e inclusión del header ---
$page_title = "Mi Perfil";
include 'header.php';
?>

<main>
    <section class="section">
        <div class="container">
            <div class="section-panel animate-on-scroll">
                <div class="section-header">
                    <h1 class="section-title">Hola, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
                    <p class="section-subtitle">Aquí puedes gestionar tu información personal.</p>
                </div>

                <div class="profile-container">
                    <div class="profile-form">
                        <h2>Tus Datos</h2>
                        <?php echo $mensaje_datos; // Muestra el mensaje de éxito o error ?>
                        <form action="perfil.php" method="POST">
                            <div class="form-group">
                                <label for="nombre">Nombre Completo</label>
                                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" name="telefono" id="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="direccion">Dirección de Entrega</label>
                                <input type="text" name="direccion" id="direccion" value="<?php echo htmlspecialchars($usuario['direccion']); ?>" required>
                            </div>
                            <button type="submit" name="actualizar_datos" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>

                    <div class="profile-form">
                        <h2>Cambiar Contraseña</h2>
                        <?php echo $mensaje_pass; // Muestra el mensaje de éxito o error ?>
                        <form action="perfil.php" method="POST">
                            <div class="form-group">
                                <label for="password_actual">Contraseña Actual</label>
                                <input type="password" name="password_actual" id="password_actual" required>
                            </div>
                            <div class="form-group">
                                <label for="password_nueva">Nueva Contraseña</label>
                                <input type="password" name="password_nueva" id="password_nueva" required>
                            </div>
                             <div class="form-group">
                                <label for="password_confirmar">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmar" id="password_confirmar" required>
                            </div>
                            <button type="submit" name="cambiar_password" class="btn btn-primary">Cambiar Contraseña</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>

<?php
include 'footer.php';
?>