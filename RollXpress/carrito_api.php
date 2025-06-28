<?php
// carrito_api.php

session_start();
require_once 'db.php';

// Verificamos que el usuario haya iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit();
}

$usuario_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'get':
        // Obtiene todos los items del carrito para el usuario, uniéndolos con la tabla de productos para tener todos los detalles
        $stmt = $pdo->prepare("SELECT ci.*, p.nombre, p.precio, p.imagen FROM carrito_items ci JOIN productos p ON ci.producto_id = p.id WHERE ci.usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $cart_items = $stmt->fetchAll();
        echo json_encode(['success' => true, 'cart' => $cart_items]);
        break;

    case 'add':
        $producto_id = $data['producto_id'] ?? 0;
        if ($producto_id > 0) {
            // Revisa si el producto ya está en el carrito para este usuario
            $stmt = $pdo->prepare("SELECT * FROM carrito_items WHERE usuario_id = ? AND producto_id = ?");
            $stmt->execute([$usuario_id, $producto_id]);
            $existing_item = $stmt->fetch();

            if ($existing_item) {
                // Si ya existe, simplemente aumenta la cantidad
                $new_quantity = $existing_item['cantidad'] + 1;
                $stmt = $pdo->prepare("UPDATE carrito_items SET cantidad = ? WHERE id = ?");
                $stmt->execute([$new_quantity, $existing_item['id']]);
            } else {
                // Si no existe, lo inserta
                $stmt = $pdo->prepare("INSERT INTO carrito_items (usuario_id, producto_id, cantidad) VALUES (?, ?, 1)");
                $stmt->execute([$usuario_id, $producto_id]);
            }
            echo json_encode(['success' => true, 'message' => 'Producto añadido al carrito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de producto no válido.']);
        }
        break;

    case 'update':
        $item_id = $data['item_id'] ?? 0;
        $cantidad = $data['cantidad'] ?? 1;
        if ($item_id > 0) {
            if ($cantidad > 0) {
                $stmt = $pdo->prepare("UPDATE carrito_items SET cantidad = ? WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$cantidad, $item_id, $usuario_id]);
            } else {
                // Si la cantidad es 0 o menos, lo eliminamos
                $stmt = $pdo->prepare("DELETE FROM carrito_items WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$item_id, $usuario_id]);
            }
            echo json_encode(['success' => true, 'message' => 'Carrito actualizado.']);
        } else {
             echo json_encode(['success' => false, 'message' => 'ID de item no válido.']);
        }
        break;

    case 'remove':
         $item_id = $data['item_id'] ?? 0;
         if ($item_id > 0) {
            $stmt = $pdo->prepare("DELETE FROM carrito_items WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$item_id, $usuario_id]);
            echo json_encode(['success' => true, 'message' => 'Producto eliminado del carrito.']);
         } else {
            echo json_encode(['success' => false, 'message' => 'ID de item no válido.']);
         }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        break;
}
?>