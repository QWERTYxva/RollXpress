<!DOCTYPE html>
<html lang="es">
<head>
    <title><?php echo $page_title; ?></title>
    <style> body { font-family: sans-serif; padding: 20px; max-width: 600px; margin: auto; } .form-group { margin-bottom: 15px; } label { display: block; margin-bottom: 5px; } input, textarea, select { width: 100%; padding: 8px; } </style>
</head>
<body>
    <h1><?php echo $page_title; ?></h1>
    <form action="<?php echo $action_url; ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
        </div>
        <div class="form-group">
            <label>Descripción:</label>
            <textarea name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Precio:</label>
            <input type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
        </div>
        <div class="form-group">
            <label>Categoría:</label>
            <select name="categoria">
                <option value="roll" <?php if($producto['categoria'] == 'roll') echo 'selected'; ?>>Roll</option>
                <option value="acompanamiento" <?php if($producto['categoria'] == 'acompanamiento') echo 'selected'; ?>>Acompañamiento</option>
                <option value="bebestible" <?php if($producto['categoria'] == 'bebestible') echo 'selected'; ?>>Bebestible</option>
            </select>
        </div>
        <div class="form-group">
            <label>Ruta de Imagen (ej: img/sushi/mi-roll.webp):</label>
            <input type="text" name="imagen" value="<?php echo htmlspecialchars($producto['imagen']); ?>">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="popular" value="1" <?php if($producto['popular']) echo 'checked'; ?>> Marcar como Popular</label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="destacado" value="1" <?php if($producto['destacado']) echo 'checked'; ?>> Marcar como Destacado (para la página de inicio)</label>
        </div>
        <button type="submit">Guardar Producto</button>
    </form>
</body>
</html>