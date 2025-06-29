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

<h1>Gestión de Pedidos</h1>

<div class="pedidos-admin-container">
    <?php foreach ($pedidos as $pedido): ?>
        <div class="pedido-admin-card">
            <div class="card-header">
                <h4>Pedido #<?php echo $pedido['id']; ?></h4>
                <span><?php echo date("d/m/Y H:i", strtotime($pedido['fecha_pedido'])); ?></span>
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nombre_usuario']); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($pedido['telefono_usuario']); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion_entrega']); ?></p>
                <p><strong>Total:</strong> $<?php echo number_format($pedido['total'], 0, ',', '.'); ?></p>
                <hr>
                <strong>Detalles:</strong>
                <ul>
                    <?php
                    $detalles = json_decode($pedido['detalles_del_pedido'], true);
                    foreach ($detalles as $item) {
                        echo "<li>" . htmlspecialchars($item['cantidad']) . "x " . htmlspecialchars($item['nombre']) . "</li>";
                    }
                    ?>
                </ul>
            </div>
            <div class="card-footer">
                <form action="index.php" method="POST" class="form-estado">
                    <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                    <select name="estado">
                        <?php foreach ($estados_posibles as $estado): ?>
                            <option value="<?php echo $estado; ?>" <?php if ($pedido['estado'] == $estado) echo 'selected'; ?>>
                                <?php echo $estado; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="actualizar_estado">Actualizar</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
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