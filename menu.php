<?php
$page_title = "Nuestro Menú";
require_once 'db.php';
$stmt = $pdo->query("SELECT * FROM productos ORDER BY categoria, nombre");
$menu_items = $stmt->fetchAll();
include 'header.php';
?>

<main>
    <section class="section">
        <div class="container">
            <div class="section-panel animate-on-scroll">
                <div class="section-header">
                    <h1 class="section-title">Nuestro Menú</h1>
                    <p class="section-subtitle">Explora todas nuestras delicias, desde los rolls clásicos hasta los acompañamientos perfectos.</p>
                </div>
                
                <div class="menu-filters">
                    <button class="filter-btn active" data-filter="all">Todos</button>
                    <button class="filter-btn" data-filter="roll">Rolls</button>
                    <button class="filter-btn" data-filter="acompanamiento">Acompañamientos</button>
                    <button class="filter-btn" data-filter="bebestible">Bebestibles</button>
                </div>

                <div class="menu-grid grid">
                    <?php foreach ($menu_items as $item): ?>
                        <article class="menu-item animate-on-scroll" data-category="<?php echo $item['categoria']; ?>" data-id="<?php echo $item['id']; ?>" data-name="<?php echo htmlspecialchars($item['nombre']); ?>" data-price="<?php echo $item['precio']; ?>">
                            
                            <?php if ($item['popular']): ?>
                                <div class="menu-item-popular">Popular</div>
                            <?php endif; ?>

                            <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="Foto de <?php echo htmlspecialchars($item['nombre']); ?>" class="menu-item-image">
                            
                            <div class="menu-item-content">
                                <h3 class="menu-item-title"><?php echo htmlspecialchars($item['nombre']); ?></h3>
                                <p class="menu-item-description"><?php echo htmlspecialchars($item['descripcion']); ?></p>
                            </div>
                            
                            <div class="menu-item-footer">
                                <span class="menu-item-price">$<?php echo number_format($item['precio'], 0, ',', '.'); ?></span>
                                <button type="button" class="btn btn-primary add-to-cart-btn">Añadir</button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'footer.php';
?>