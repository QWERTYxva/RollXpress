<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin RollXpress</title>
    <link rel="stylesheet" href="admin_styles.css?v=1.0">
</head>
<body class="body-form">
    <div class="form-container">
        <h1><?php echo htmlspecialchars($page_title); ?></h1>
        
        <form action="guardar_ingrediente.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($ingrediente['id']); ?>">
            
            <div class="form-group">
                <label for="nombre">Nombre del Ingrediente:</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($ingrediente['nombre']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="tipo">Tipo de Ingrediente:</label>
                <select name="tipo" id="tipo" required>
                    <option value="base" <?php if($ingrediente['tipo'] == 'base') echo 'selected'; ?>>Base</option>
                    <option value="proteina" <?php if($ingrediente['tipo'] == 'proteina') echo 'selected'; ?>>Proteína</option>
                    <option value="vegetal" <?php if($ingrediente['tipo'] == 'vegetal') echo 'selected'; ?>>Vegetal</option>
                    <option value="topping" <?php if($ingrediente['tipo'] == 'topping') echo 'selected'; ?>>Topping</option>
                    <option value="salsa" <?php if($ingrediente['tipo'] == 'salsa') echo 'selected'; ?>>Salsa</option>
                </select>
            </div>

            <div class="form-group">
                <label for="precio">Precio Adicional:</label>
                <input type="number" name="precio_adicional" id="precio" step="10" value="<?php echo htmlspecialchars($ingrediente['precio_adicional']); ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Guardar Ingrediente</button>
                <a href="ingredientes.php" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>