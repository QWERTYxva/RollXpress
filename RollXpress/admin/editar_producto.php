<?php
// admin/editar_producto.php
require_once 'seguridad.php';
require_once '../db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: productos.php'); exit(); }

// Obtenemos los datos del producto existente para rellenar el formulario
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

$page_title = "Editar Producto";
include 'formulario_producto.php';
?>