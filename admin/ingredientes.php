<?php
// admin/ingredientes.php
include 'header.php';

// Obtenemos todos los ingredientes ordenados por tipo
$stmt = $pdo->query("SELECT * FROM ingredientes ORDER BY tipo, nombre");
$ingredientes = $stmt->fetchAll();
?>

<div class="admin-page-header">
    <h1>Gestión de Ingredientes</h1>
    <a href="crear_ingrediente.php" class="btn-add"><i class="fas fa-plus"></i> Añadir Nuevo Ingrediente</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Precio Adicional</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ingredientes as $ingrediente): ?>
            <tr>
                <td><?php echo $ingrediente['id']; ?></td>
                <td><?php echo htmlspecialchars($ingrediente['nombre']); ?></td>
                <td><?php echo htmlspecialchars($ingrediente['tipo']); ?></td>
                <td>$<?php echo number_format($ingrediente['precio_adicional'], 0); ?></td>
                <td class="actions-cell">
                    <a href="editar_ingrediente.php?id=<?php echo $ingrediente['id']; ?>" class="action-btn edit"><i class="fas fa-pencil-alt"></i> Editar</a>
                    <a href="eliminar_ingrediente.php?id=<?php echo $ingrediente['id']; ?>" class="action-btn delete" onclick="return confirm('¿Estás seguro?');"><i class="fas fa-trash-alt"></i> Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>