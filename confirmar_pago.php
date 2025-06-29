<?php
session_start();
require_once 'db.php';
require_once 'vendor/autoload.php';

use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$usuario_id = $_SESSION['user_id'];
$token = $_POST['token_ws'] ?? $_GET['token_ws'] ?? null;

if (!$token) {
    die("Error: No se recibió el token de confirmación de WebPay. La transacción no puede ser verificada.");
}

WebpayPlus::configureForIntegration(
    '597055555532', 
    '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1B'
);

try {
    $response = (new Transaction)->commit($token);
    if ($response->isApproved()) {
        $pdo->beginTransaction();

        try {
            // a. Obtenemos los items del carrito para guardarlos en el pedido
            $stmt_cart = $pdo->prepare("SELECT ci.cantidad, p.nombre, p.precio FROM carrito_items ci JOIN productos p ON ci.producto_id = p.id WHERE ci.usuario_id = ?");
            $stmt_cart->execute([$usuario_id]);
            $items_pedido = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);
            
            // b. Verificamos que el monto pagado en WebPay coincida con el total del carrito
            $total_calculado = 0;
            foreach ($items_pedido as $item) {
                $total_calculado += $item['precio'] * $item['cantidad'];
            }

            if ((int)$total_calculado !== (int)$response->getAmount()) {
                // Si los montos no coinciden, es una alerta de seguridad. No guardamos el pedido.
                throw new Exception("El monto pagado no coincide con el total del carrito.");
            }

            // c. Creamos los detalles del pedido en formato JSON para guardarlos
            $detalles_del_pedido = json_encode($items_pedido);

            // d. Obtenemos la dirección del usuario desde la tabla usuarios
            $stmt_user = $pdo->prepare("SELECT direccion FROM usuarios WHERE id = ?");
            $stmt_user->execute([$usuario_id]);
            $direccion_entrega = $stmt_user->fetchColumn();

            // e. Insertamos el pedido final en la tabla `pedidos`
            $sql_pedido = "INSERT INTO pedidos (usuario_id, detalles_del_pedido, total, direccion_entrega, estado) VALUES (?, ?, ?, ?, 'Recibido')";
            $stmt_pedido = $pdo->prepare($sql_pedido);
            $stmt_pedido->execute([$usuario_id, $detalles_del_pedido, $total_calculado, $direccion_entrega]);
            $pedido_id = $pdo->lastInsertId();

            // f. Vaciamos el carrito del usuario, ya que la compra se completó
            $stmt_delete = $pdo->prepare("DELETE FROM carrito_items WHERE usuario_id = ?");
            $stmt_delete->execute([$usuario_id]);

            // g. Si todo salió bien, confirmamos los cambios en la base de datos
            $pdo->commit();

            // h. Redirigimos al usuario a la página de seguimiento del pedido que acaba de crear
            header("Location: seguimiento.php?id=" . $pedido_id . "&status=success");
            exit();

        } catch (Exception $e) {
            // Si algo falló durante el guardado en la base de datos, revertimos todo.
            $pdo->rollBack();
            die("Error crítico al guardar tu pedido. Por favor, contacta a soporte. Error: " . $e->getMessage());
        }

    } else {
        // Si el pago fue rechazado, anulado por el usuario o falló en WebPay.
        // Lo devolvemos a la página de checkout con un mensaje de error.
        header("Location: checkout.php?status=failed");
        exit();
    }

} catch (Exception $e) {
    // Si hubo un error al comunicarse con los servidores de Transbank.
    die('Error al confirmar la transacción con WebPay: ' . $e->getMessage());
}

?>