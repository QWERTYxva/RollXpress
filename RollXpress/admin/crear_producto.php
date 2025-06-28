<?php
require_once 'seguridad.php';
// Dejamos las variables vacías para el formulario de creación
$producto = ['id' => '', 'nombre' => '', 'descripcion' => '', 'precio' => '', 'categoria' => 'roll', 'imagen' => '', 'popular' => 0, 'destacado' => 0];
$action_url = "guardar_producto.php";
$page_title = "Añadir Nuevo Producto";
include 'formulario_producto.php'; // Reutilizaremos el mismo formulario
?>