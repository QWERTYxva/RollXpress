<?php
// admin/crear_producto.php
require_once 'seguridad.php';
// Preparamos un array vacío porque es un producto nuevo
$producto = ['id' => '', 'nombre' => '', 'descripcion' => '', 'precio' => '', 'categoria' => 'roll', 'imagen' => 'img/sushi/', 'popular' => 0, 'destacado' => 0];
$page_title = "Añadir Nuevo Producto";
include 'formulario_producto.php';
?>