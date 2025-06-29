<?php
// admin/api_dashboard.php
require_once 'seguridad.php'; // Usamos la misma seguridad del panel
require_once '../db.php';

// Leemos el último ID que el JavaScript nos envió para saber desde dónde buscar
$ultimoPedidoId = $_GET['ultimo_pedido_id'] ?? 0;
$ultimoUsuarioId = $_GET['ultimo_usuario_id'] ?? 0;

// Buscamos pedidos MÁS NUEVOS que el último que vimos
$stmt_pedidos = $pdo->prepare("SELECT p.*, u.nombre as nombre_usuario FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id > ? ORDER BY p.id DESC");
$stmt_pedidos->execute([$ultimoPedidoId]);
$nuevos_pedidos = $stmt_pedidos->fetchAll();

// Buscamos usuarios MÁS NUEVOS que el último que vimos
$stmt_usuarios = $pdo->prepare("SELECT id, nombre, email FROM usuarios WHERE id > ? AND is_admin = 0 ORDER BY id DESC");
$stmt_usuarios->execute([$ultimoUsuarioId]);
$nuevos_usuarios = $stmt_usuarios->fetchAll();

// Devolvemos los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode([
    'nuevos_pedidos' => $nuevos_pedidos,
    'nuevos_usuarios' => $nuevos_usuarios
]);
?>