<?php
// admin/usuarios.php
include 'header.php'; // Incluye seguridad, conexión a DB y el header del admin

// Esta consulta SQL es la magia de esta página.
// 1. Selecciona todos los datos de los usuarios.
// 2. Se une (LEFT JOIN) con la tabla de pedidos para poder contar cuántos pedidos ha hecho cada usuario.
// 3. Cuenta los pedidos (COUNT) y suma el total gastado (SUM).
// 4. Agrupa los resultados por usuario para que las cuentas sean correctas.
// 5. Ordena para mostrar los clientes con más pedidos primero.
$sql = "
    SELECT 
        u.id, 
        u.nombre, 
        u.email, 
        u.telefono, 
        u.fecha_registro,
        COUNT(p.id) AS total_pedidos,
        SUM(p.total) AS gasto_total
    FROM 
        usuarios u
    LEFT JOIN 
        pedidos p ON u.id = p.usuario_id
    WHERE
        u.is_admin = 0 -- Mostramos solo a los clientes, no a otros administradores
    GROUP BY 
        u.id
    ORDER BY 
        total_pedidos DESC, gasto_total DESC
";

$usuarios = $pdo->query($sql)->fetchAll();

// Define qué consideramos un "cliente frecuente". Puedes cambiar este número.
define('PEDIDOS_PARA_SER_FRECUENTE', 3); 
?>

<div class="admin-page-header">
    <h1>Gestión de Clientes</h1>
    <p style="margin:0; color: #ffffff;">Visualiza la información y la frecuencia de compra de tus clientes registrados.</p>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Pedidos Totales</th>
                <th>Gasto Total</th>
                <th>Tipo de Cliente</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No hay clientes registrados todavía.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['id']; ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                    <td><strong><?php echo $usuario['total_pedidos']; ?></strong></td>
                    <td>$<?php echo number_format($usuario['gasto_total'] ?? 0, 0); ?></td>
                    <td>
                        <?php if ($usuario['total_pedidos'] >= PEDIDOS_PARA_SER_FRECUENTE): ?>
                            <span class="status-badge status-frequent">Frecuente</span>
                        <?php else: ?>
                            <span class="status-badge status-regular">Regular</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>