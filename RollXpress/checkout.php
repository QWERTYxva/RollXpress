<?php
// checkout.php
session_start();
require_once 'db.php';

// Seguridad: Si no hay sesión o el carrito está vacío, no pueden estar aquí.
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }

$usuario_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT ci.*, p.nombre, p.precio, p.imagen FROM carrito_items ci JOIN productos p ON ci.producto_id = p.id WHERE ci.usuario_id = ?");
$stmt->execute([$usuario_id]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) { header('Location: menu.php'); exit(); }

$page_title = "Finalizar Compra";
include 'header.php';
?>

<main>
    <section class="section">
        <div class="container">
            <div class="section-panel animate-on-scroll">
                <div class="section-header">
                    <h1 class="section-title">Finalizar Compra</h1>
                    <p class="section-subtitle">Revisa tu pedido y elige un método de pago para completar la compra.</p>
                </div>

                <div class="checkout-grid">
                    <div class="order-summary">
                        <h3>Resumen de tu Pedido</h3>
                        <?php 
                        $total = 0;
                        foreach ($cart_items as $item): 
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total += $subtotal;
                        ?>
                            <div class="summary-item">
                                <span class="summary-item-name"><?php echo $item['cantidad']; ?>x <?php echo htmlspecialchars($item['nombre']); ?></span>
                                <span class="summary-item-price">$<?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="summary-total final-total">
                            <span>Total a Pagar:</span>
                            <span>$<?php echo number_format($total, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <div class="payment-options">
                        <h3>Método de Pago</h3>
                        <div class="payment-method">
                            <p><strong>Nota Importante:</strong></p>
                            <p>Actualmente, esta es una simulación de checkout. Al hacer clic en "Realizar Pedido", tu orden se guardará en nuestro sistema y será procesada. En un futuro, aquí se integrará WebPay.</p>
                            <p>Por favor, asegúrate de que tu dirección de entrega en tu perfil sea la correcta.</p>
                        </div>
                        <button id="confirm-checkout-btn" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-lock"></i> Realizar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>