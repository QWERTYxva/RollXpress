<?php
require_once 'seguridad.php'; // Primero, la seguridad
require_once '../db.php'; // Incluimos la conexión a la base de datos

// Hacemos una consulta para obtener todos los productos
$stmt = $pdo->query("SELECT * FROM productos ORDER BY categoria, nombre");
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Panel de Administración - Menú</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        a { text-decoration: none; color: #007bff; }
        .btn-add { display: inline-block; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Gestión de Menú</h1>
    <a href="crear_producto.php" class="btn-add">Añadir Nuevo Producto</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Categoría</th>
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
                    <a href="editar_producto.php?id=<?php echo $producto['id']; ?>">Editar</a> |
                    <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>