<?php
// admin/header.php - VERSIÓN FINAL
session_start();
require_once '../db.php'; // La ruta cambia porque estamos dentro de la carpeta /admin

// Seguridad del Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - RollXpress</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css?v=2.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="admin-content">