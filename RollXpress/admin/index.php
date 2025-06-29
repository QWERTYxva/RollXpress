<?php
// admin/index.php - Gestión de Pedidos
include 'header.php';

// Lógica para actualizar el estado de un pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_estado'])) {
    $pedido_id = $_POST['pedido_id'];
    $nuevo_estado = $_POST['estado'];
    $stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->execute([$nuevo_estado, $pedido_id]);
}

// Obtener todos los pedidos con la información del usuario
$sql = "SELECT p.*, u.nombre as nombre_usuario, u.telefono as telefono_usuario 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.fecha_pedido DESC";
$pedidos = $pdo->query($sql)->fetchAll();
$estados_posibles = ['Recibido', 'En preparación', 'En reparto', 'Completado', 'Cancelado'];
?>

<div class="admin-page-header">
    <h1>Gestión de Pedidos</h1>
</div>

<audio id="notificacion-sonido" src="notification.mp3" preload="auto"></audio>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Pedido ID</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-pedidos-body">
            <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td>#<?php echo $pedido['id']; ?></td>
                <td><?php echo htmlspecialchars($pedido['nombre_usuario']); ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($pedido['fecha_pedido'])); ?></td>
                <td>$<?php echo number_format($pedido['total'], 0); ?></td>
                <td>
                    <form action="index.php" method="POST" class="form-estado">
                        </form>
                </td>
                <td><a href="#" class="action-btn">Ver Detalles</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
/* Estilos rápidos para las tarjetas de pedido en el admin */
.pedidos-admin-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
.pedido-admin-card { background: #34495e; border-radius: 8px; overflow: hidden; }
.card-header, .card-body, .card-footer { padding: 1rem; }
.card-header { background: #2c3e50; display: flex; justify-content: space-between; align-items: center; }
.card-body ul { list-style: none; padding-left: 0; margin-top: 0.5rem; }
.card-footer form { display: flex; gap: 10px; }
.card-footer select, .card-footer button { padding: 8px; border: none; border-radius: 5px; }
.card-footer button { background: #1abc9c; color: white; cursor: pointer; }
</style>

<?php include 'footer.php'; ?>