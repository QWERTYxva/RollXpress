<?php
// Este archivo es incluido por crear_producto.php y editar_producto.php
// No se puede acceder a él directamente.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="admin_styles.css?v=1.1">
</head>
<body class="body-form">
    <div class="form-container">
        <h1><?php echo $page_title; ?></h1>
        <form action="guardar_producto.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['id']); ?>">
            
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label>Descripción:</label>
                <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Precio:</label>
                <input type="number" name="precio" step="10" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
            </div>
            <div class="form-group">
                <label>Categoría:</label>
                <select name="categoria" required>
                    <option value="roll" <?php if($producto['categoria'] == 'roll') echo 'selected'; ?>>Roll</option>
                    <option value="acompanamiento" <?php if($producto['categoria'] == 'acompanamiento') echo 'selected'; ?>>Acompañamiento</option>
                    <option value="bebestible" <?php if($producto['categoria'] == 'bebestible') echo 'selected'; ?>>Bebestible</option>
                </select>
            </div>
            <div class="form-group">
                <label>Ruta de Imagen (ej: img/sushi/mi-roll.webp):</label>
                <input type="text" name="imagen" value="<?php echo htmlspecialchars($producto['imagen']); ?>">
            </div>
            <div class="form-group-checkbox">
                <input type="checkbox" name="popular" id="popular" value="1" <?php if($producto['popular']) echo 'checked'; ?>>
                <label for="popular">Marcar como Popular</label>
            </div>
            <div class="form-group-checkbox">
                <input type="checkbox" name="destacado" id="destacado" value="1" <?php if($producto['destacado']) echo 'checked'; ?>>
                <label for="destacado">Marcar como Destacado (aparece en la página de inicio)</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Guardar Producto</button>
                <a href="productos.php" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>