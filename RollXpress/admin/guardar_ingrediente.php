<?php
require_once 'seguridad.php';
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $precio_adicional = $_POST['precio_adicional'];

    if (empty($id)) {
        // CREAR
        $sql = "INSERT INTO ingredientes (nombre, tipo, precio_adicional) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $tipo, $precio_adicional]);
    } else {
        // ACTUALIZAR
        $sql = "UPDATE ingredientes SET nombre = ?, tipo = ?, precio_adicional = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $tipo, $precio_adicional, $id]);
    }
    header('Location: ingredientes.php');
}
?>