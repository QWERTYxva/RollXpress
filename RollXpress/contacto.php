<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluimos los archivos de la librería PHPMailer
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'header.php';

$mensaje_usuario = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strip_tags(trim($_POST["nombre"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $asunto = strip_tags(trim($_POST["asunto"]));
    $mensaje = trim($_POST["mensaje"]);

    if (empty($nombre) || empty($asunto) || empty($mensaje) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje_usuario = "Por favor, completa todos los campos correctamente.";
    } else {
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor SMTP (pide estos datos a tu proveedor de hosting, ej. Bluehost)
            $mail->isSMTP();
            $mail->Host       = 'smtp.tudominio.com';  // Ej: mail.rollxpress.cl
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tu-correo@tudominio.com'; // Ej: contacto@rollxpress.cl
            $mail->Password   = 'tu-contraseña-del-correo'; // La contraseña de ese correo
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Remitente y Destinatario
            $mail->setFrom('tu-correo@tudominio.com', 'Sitio Web RollXpress'); // Quien envía
            $mail->addAddress('tu-correo-receptor@dominio.com', 'RollXpress Admin'); // Quien recibe
            $mail->addReplyTo($email, $nombre); // Para poder responder directamente al cliente

            // Contenido del Email
            $mail->isHTML(true); // Permite usar HTML en el cuerpo
            $mail->Subject = "Nuevo Mensaje de Contacto: $asunto";
            $mail->Body    = "<h2>Has recibido un nuevo mensaje desde el sitio web:</h2>
                              <p><b>Nombre:</b> $nombre</p>
                              <p><b>Email:</b> $email</p>
                              <hr>
                              <p><b>Mensaje:</b><br>" . nl2br($mensaje) . "</p>";
            $mail->AltBody = "Nombre: $nombre\nEmail: $email\n\nMensaje:\n$mensaje";

            $mail->send();
            $mensaje_usuario = '¡Gracias! Tu mensaje ha sido enviado exitosamente.';
        } catch (Exception $e) {
            $mensaje_usuario = "Hubo un error al enviar el mensaje. Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<main>
    <section class="section">
        <div class="container">
            <div class="section-panel animate-on-scroll">
                <div class="section-header">
                    <h1 class="section-title">Ponte en Contacto</h1>
                    <p class="section-subtitle">¿Tienes dudas o sugerencias? Escríbenos. Estamos aquí para ayudarte.</p>
                </div>

                <div class="contact-grid">
                    
                    <div class="contact-info">
                        <h3>Nuestros Canales</h3>
                        <p>Si prefieres, puedes contactarnos directamente a través de estos medios. Para pedidos, te recomendamos usar nuestro carrito de compras.</p>
                        
                        <ul class="contact-details">
                            <li><i class="fas fa-map-marker-alt"></i> Av. Portales 345, San Bernardo</li>
                            <li><i class="fab fa-whatsapp"></i> <a href="https://wa.me/56982684951">+56 9 8268 4951</a></li>
                            <li><i class="fas fa-envelope"></i> <a href="mailto:contacto@rollxpress.cl">contacto@rollxpress.cl</a></li>
                            <li><i class="fas fa-clock"></i> Mar a Dom: 13:00 - 22:30 hrs</li>
                        </ul>
                    </div>

                    <div class="contact-form">
                        <h3>Envíanos un Mensaje</h3>
                        
                        <?php if ($mensaje_usuario): ?>
                            <div class="form-message"><?php echo $mensaje_usuario; ?></div>
                        <?php endif; ?>

                        <form action="contacto.php" method="POST">
                            <div class="form-group">
                                <label for="nombre">Tu Nombre</label>
                                <input type="text" name="nombre" id="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Tu Correo Electrónico</label>
                                <input type="email" name="email" id="email" required>
                            </div>
                            <div class="form-group">
                                <label for="asunto">Asunto</label>
                                <input type="text" name="asunto" id="asunto" required>
                            </div>
                            <div class="form-group">
                                <label for="mensaje">Tu Mensaje</label>
                                <textarea name="mensaje" id="mensaje" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
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