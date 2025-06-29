<?php
require_once 'seguridad.php';
require_once '../db.php';
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: ingredientes.php'); exit(); }

$stmt = $pdo->prepare("SELECT * FROM ingredientes WHERE id = ?");
$stmt->execute([$id]);
$ingrediente = $stmt->fetch();

$page_title = "Editar Ingrediente";
include 'formulario_ingrediente.php';
?>