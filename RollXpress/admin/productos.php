<?php
// admin/productos.php - VERSIÓN ESTRUCTURALMENTE CORRECTA
include 'header.php'; // Incluye seguridad, conexión a DB y el header del admin

// Hacemos una consulta para obtener todos los productos
$stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
$productos = $stmt->fetchAll();
?>

<div class="admin-page-header">
    <h1>Gestión de Productos</h1>
    <a href="crear_producto.php" class="btn-add"><i class="fas fa-plus"></i> Añadir Nuevo Producto</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Categoría</th>
                <th>Destacado</th>
                <th>Popular</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?php echo $producto['id']; ?></td>
                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                <td>$<?php echo number_format($producto['precio'], 0); ?></td>
                <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                <td>
                    <span class="status-badge <?php echo $producto['destacado'] ? 'status-yes' : 'status-no'; ?>">
                        <?php echo $producto['destacado'] ? 'Sí' : 'No'; ?>
                    </span>
                </td>
                <td>
                    <span class="status-badge <?php echo $producto['popular'] ? 'status-yes' : 'status-no'; ?>">
                        <?php echo $producto['popular'] ? 'Sí' : 'No'; ?>
                    </span>
                </td>
                <td class="actions-cell">
                    <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="action-btn edit"><i class="fas fa-pencil-alt"></i> Editar</a>
                    <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="action-btn delete" onclick="return confirm('¿Estás seguro?');"><i class="fas fa-trash-alt"></i> Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>