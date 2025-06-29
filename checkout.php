<?php
// checkout.php - VERSIÓN FINAL Y CORREGIDA

session_start();
require_once 'db.php';

// Seguridad: Si no hay sesión, redirigimos al login.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['user_id'];

// ======================================================
//     LA CORRECCIÓN ESTÁ EN ESTA CONSULTA SQL
//     Cambiamos JOIN por LEFT JOIN para incluir siempre todos los items del carrito
// ======================================================
$stmt = $pdo->prepare(
    "SELECT 
        ci.id, ci.is_custom, ci.cantidad, 
        ci.custom_nombre, ci.custom_precio, 
        p.nombre, p.precio 
    FROM carrito_items ci 
    LEFT JOIN productos p ON ci.producto_id = p.id 
    WHERE ci.usuario_id = ?"
);
$stmt->execute([$usuario_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si después de la consulta correcta, el carrito sigue vacío, entonces redirigimos.
if (empty($cart_items)) {
    header('Location: menu.php');
    exit();
}

$page_title = "Finalizar Compra";
include 'header.php';
?>

<main>
    <section class="section">
        <div class="container">
            <div class="section-panel animate-on-scroll">
                <div class="section-header">
                    <h1 class="section-title">Finalizar Compra</h1>
                    <p class="section-subtitle">Revisa tu pedido final antes de confirmarlo.</p>
                </div>

                <div class="checkout-grid">
                    <div class="order-summary">
                        <h3>Resumen de tu Pedido</h3>
                        <?php 
                        $total = 0;
                        foreach ($cart_items as $item): 
                            // Lógica para determinar el nombre y precio correctos
                            $es_personalizado = $item['is_custom'] == 1;
                            $nombre_item = $es_personalizado ? $item['custom_nombre'] : $item['nombre'];
                            $precio_item = $es_personalizado ? $item['custom_precio'] : $item['precio'];
                            $subtotal = $precio_item * $item['cantidad'];
                            $total += $subtotal;
                        ?>
                            <div class="summary-item">
                                <span class="summary-item-name"><?php echo $item['cantidad']; ?>x <?php echo htmlspecialchars($nombre_item); ?></span>
                                <span class="summary-item-price">$<?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <hr style="border-color: rgba(255,255,255,0.1); margin: 1.5rem 0;">
                        <div class="summary-total final-total">
                            <span>Total a Pagar:</span>
                            <span>$<?php echo number_format($total, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <div class="payment-options">
                        <h3>Método de Pago</h3>
                        <p>Serás redirigido a la página segura de WebPay para completar tu pago.</p>
                        
                        <form action="iniciar_pago.php" method="POST">
                            <input type="hidden" name="monto" value="<?php echo $total; ?>">
                            <input type="hidden" name="orden_compra" value="rx-<?php echo time(); ?>"> <button type="submit" id="pay-with-webpay-btn" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-lock"></i> Pagar con WebPay
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>