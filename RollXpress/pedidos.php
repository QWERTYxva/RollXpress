<?php
// pedidos.php
session_start();
require_once 'db.php';

// Seguridad: Si no hay un usuario en la sesión, lo redirigimos al login.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['user_id'];

// Obtenemos todos los pedidos del usuario, ordenados del más reciente al más antiguo.
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC");
$stmt->execute([$usuario_id]);
$pedidos = $stmt->fetchAll();

// --- Título de la página e inclusión del header ---
$page_title = "Mis Pedidos";
include 'header.php';
?>

<main>
    <section class="section">
        <div class="container">
            <div class="section-panel animate-on-scroll">
                <div class="section-header">
                    <h1 class="section-title">Historial de Pedidos</h1>
                    <p class="section-subtitle">Aquí puedes ver todas las compras que has realizado.</p>
                </div>

                <div class="pedidos-container">
                    <?php if (empty($pedidos)): ?>
                        <p style="text-align: center;">Aún no has realizado ningún pedido.</p>
                    <?php else: ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <div class="pedido-card">
                                <div class="pedido-header">
                                    <div>
                                        <h3>Pedido #<?php echo $pedido['id']; ?></h3>
                                        <p>Fecha: <?php echo date("d/m/Y H:i", strtotime($pedido['fecha_pedido'])); ?></p>
                                    </div>
                                    <div class="pedido-status <?php echo strtolower($pedido['estado']); ?>">
                                        <?php echo htmlspecialchars($pedido['estado']); ?>
                                    </div>
                                </div>
                                <div class="pedido-body">
                                    <h4>Detalles del Pedido:</h4>
                                    <ul>
                                        <?php 
                                        $detalles = json_decode($pedido['detalles_del_pedido'], true);
                                        foreach ($detalles as $item):
                                        ?>
                                            <li><?php echo $item['cantidad']; ?>x <?php echo htmlspecialchars($item['nombre']); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="pedido-footer">
                                    <strong>Total: $<?php echo number_format($pedido['total'], 0, ',', '.'); ?></strong>
                                    <a href="#" class="btn btn-primary">Ver Detalles</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>
</main>

<?php
include 'footer.php';
?>