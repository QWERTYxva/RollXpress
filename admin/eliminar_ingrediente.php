<?php
require_once 'seguridad.php';
require_once '../db.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM ingredientes WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: ingredientes.php');
?>