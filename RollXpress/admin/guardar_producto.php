<?php
require_once 'seguridad.php';
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $imagen = $_POST['imagen'];
    $popular = isset($_POST['popular']) ? 1 : 0;
    $destacado = isset($_POST['destacado']) ? 1 : 0;

    if (empty($id)) {
        // CREAR nuevo producto
        $sql = "INSERT INTO productos (nombre, descripcion, precio, categoria, imagen, popular, destacado) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $precio, $categoria, $imagen, $popular, $destacado]);
    } else {
        // ACTUALIZAR producto existente
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria = ?, imagen = ?, popular = ?, destacado = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $precio, $categoria, $imagen, $popular, $destacado, $id]);
    }
    header('Location: index.php');
}
?>