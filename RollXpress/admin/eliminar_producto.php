<?php
// admin/eliminar_producto.php
require_once 'seguridad.php';
require_once '../db.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: productos.php');
?>