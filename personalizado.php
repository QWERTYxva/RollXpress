<?php
// personalizado.php - VERSIÓN FINAL
$page_title = "Crea tu Sushi";
require_once 'db.php';
session_start(); // Necesitamos la sesión para el header

// 1. Obtenemos todos los ingredientes de la base de datos
$stmt = $pdo->query("SELECT * FROM ingredientes ORDER BY tipo, nombre");
$todos_los_ingredientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Separamos los ingredientes por tipo en arrays diferentes
$ingredientes_por_tipo = [];
foreach ($todos_los_ingredientes as $ingrediente) {
    $ingredientes_por_tipo[$ingrediente['tipo']][] = $ingrediente;
}

include 'header.php';
?>

<main>
    <section class="section">
        <div class="container">
            <div class="section-panel animate-on-scroll">
                <div class="section-header">
                    <h1 class="section-title">Crea tu Propio Roll</h1>
                    <p class="section-subtitle">Elige tus ingredientes favoritos y nosotros lo hacemos realidad.</p>
                </div>
                
                <div class="sushi-builder-container">
                    <div class="builder-options">
                        <form id="sushi-builder-form">
                            <fieldset class="builder-group">
                                <legend>Elige tus Proteínas</legend>
                                <?php foreach ($ingredientes_por_tipo['proteina'] ?? [] as $ing): ?>
                                <div class="checkbox-card">
                                    <input type="checkbox" name="ingrediente[]" id="ing-<?php echo $ing['id']; ?>" value="<?php echo $ing['id']; ?>" data-nombre="<?php echo htmlspecialchars($ing['nombre']); ?>" data-precio="<?php echo $ing['precio_adicional']; ?>">
                                    <label for="ing-<?php echo $ing['id']; ?>">
                                        <span class="ingrediente-nombre"><?php echo htmlspecialchars($ing['nombre']); ?></span>
                                        <span class="ingrediente-precio">+ $<?php echo number_format($ing['precio_adicional'], 0); ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </fieldset>
                            
                            <fieldset class="builder-group">
                                <legend>Añade Vegetales y Otros</legend>
                                 <?php foreach ($ingredientes_por_tipo['vegetal'] ?? [] as $ing): ?>
                                <div class="checkbox-card">
                                    <input type="checkbox" name="ingrediente[]" id="ing-<?php echo $ing['id']; ?>" value="<?php echo $ing['id']; ?>" data-nombre="<?php echo htmlspecialchars($ing['nombre']); ?>" data-precio="<?php echo $ing['precio_adicional']; ?>">
                                    <label for="ing-<?php echo $ing['id']; ?>">
                                        <span class="ingrediente-nombre"><?php echo htmlspecialchars($ing['nombre']); ?></span>
                                        <span class="ingrediente-precio">+ $<?php echo number_format($ing['precio_adicional'], 0); ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </fieldset>

                            <fieldset class="builder-group">
                                <legend>Elige tu Topping o Cobertura (1 opción)</legend>
                                 <?php foreach ($ingredientes_por_tipo['topping'] ?? [] as $ing): ?>
                                <div class="checkbox-card">
                                    <input type="radio" name="topping" id="ing-<?php echo $ing['id']; ?>" value="<?php echo $ing['id']; ?>" data-nombre="<?php echo htmlspecialchars($ing['nombre']); ?>" data-precio="<?php echo $ing['precio_adicional']; ?>">
                                    <label for="ing-<?php echo $ing['id']; ?>">
                                        <span class="ingrediente-nombre"><?php echo htmlspecialchars($ing['nombre']); ?></span>
                                        <span class="ingrediente-precio">+ $<?php echo number_format($ing['precio_adicional'], 0); ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </fieldset>
                        </form>
                    </div>

                    <aside class="builder-summary">
                        <h3>Tu Roll Personalizado</h3>
                        <div id="summary-list">
                            <p class="summary-placeholder">Selecciona ingredientes para verlos aquí.</p>
                        </div>
                        <div class="summary-total">
                            <span>Precio Base:</span>
                            <span id="base-price">$4.990</span>
                        </div>
                        <div class="summary-total">
                            <span>Adicionales:</span>
                            <span id="extras-price">$0</span>
                        </div>
                        <hr style="border-color: rgba(255,255,255,0.1); margin: 1rem 0;">
                        <div class="summary-total final-total">
                            <span>Total:</span>
                            <span id="final-price">$4.990</span>
                        </div>
                        <button id="add-custom-to-cart" class="btn btn-primary btn-lg" disabled>Añadir al Carrito</button>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'footer.php';
?>