<?php
require_once 'seguridad.php';
require_once '../db.php';
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit(); }

$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

$action_url = "guardar_producto.php";
$page_title = "Editar Producto";
include 'formulario_producto.php'; // Reutilizamos el mismo formulario
?>