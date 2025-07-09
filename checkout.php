<?php
// checkout.php - VERSIÓN FINAL Y SINCRONIZADA

session_start();
require_once 'db.php';

// Si no hay un usuario en la sesión, lo redirigimos al login.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout');
    exit();
}

$usuario_id = $_SESSION['user_id'];

// Obtenemos los items del carrito para el usuario.
$stmt = $pdo->prepare(
    "SELECT ci.id, ci.is_custom, ci.cantidad, ci.custom_nombre, ci.custom_precio, p.nombre, p.precio
    FROM carrito_items ci
    LEFT JOIN productos p ON ci.producto_id = p.id
    WHERE ci.usuario_id = ?"
);
$stmt->execute([$usuario_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si el carrito está vacío, no tiene sentido estar aquí.
if (empty($cart_items)) {
    header('Location: menu.php?mensaje=carrito_vacio');
    exit();
}

// --- PASO CLAVE: CALCULAR EL TOTAL ---
$total = 0;
foreach ($cart_items as $item) {
    $precio_item = ($item['is_custom'] == 1) ? $item['custom_precio'] : $item['precio'];
    $total += $precio_item * $item['cantidad'];
}

// --- PASO CLAVE: GUARDAR EL TOTAL EN LA SESIÓN DE FORMA SEGURA ---
$_SESSION['monto_total_a_pagar'] = $total;

$page_title = "Finalizar Compra";
include 'header.php';
?>

<main>
    <section class="section">
        <div class="container">
            <div class="section-panel animate-on-scroll" style="max-width: 800px; margin: auto;">
                <div class="section-header">
                    <h1 class="section-title">Finalizar Compra</h1>
                    <p class="section-subtitle">Revisa tu pedido final antes de confirmarlo.</p>
                </div>

                 <?php if (isset($_GET['error'])): ?>
                    <div class="login-message error" style="text-align:center;">
                        Error al procesar el pago: <?= htmlspecialchars($_GET['message'] ?? 'Inténtalo de nuevo.') ?>
                    </div>
                <?php endif; ?>

                <div class="checkout-grid">
                    <div class="order-summary">
                        <h3>Resumen de tu Pedido</h3>
                        <?php foreach ($cart_items as $item): ?>
                            <?php
                                $es_personalizado = $item['is_custom'] == 1;
                                $nombre_item = $es_personalizado ? ($item['custom_nombre'] ?: 'Roll Personalizado') : $item['nombre'];
                                $precio_item = $es_personalizado ? $item['custom_precio'] : $item['precio'];
                                $subtotal = $precio_item * $item['cantidad'];
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
                        <p>Serás redirigido a la página segura para completar tu pago.</p>

                        <form action="iniciar_pago.php" method="POST">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-credit-card"></i> Pagar $<?php echo number_format($total, 0, ',', '.'); ?> con Flow
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>