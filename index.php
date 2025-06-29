<?php
// index.php - VERSIÓN CORREGIDA FINAL

$page_title = "Inicio";

// =================================================================
// ESTA ES LA PARTE CORREGIDA Y MÁS IMPORTANTE
// 1. Nos conectamos a la base de datos.
require_once 'db.php';

// 2. Hacemos una consulta SQL para obtener solo los productos marcados como 'destacado = 1'.
$stmt = $pdo->query("SELECT * FROM productos WHERE destacado = 1 ORDER BY nombre");
$productos_destacados = $stmt->fetchAll();
// Ya no usamos 'menu_data.php' ni 'array_filter'. La base de datos hace el trabajo.
// =================================================================

// 3. Incluimos el header.
include 'header.php';
?>
<main>
    <section class="hero">
        <div class="hero-background"></div>
        <div class="container hero-container">
            <div class="hero-content animate-hero-content">
                <h1 class="hero-title animate-hero-title">El Verdadero Sabor del Sushi en La Cisterna</h1>
                <p class="hero-text animate-hero-text">Hecho al momento con ingredientes frescos y la pasión de nuestros chefs. Pide ahora y vive la experiencia RollXpress.</p>
                <a href="menu.php" class="btn btn-primary btn-lg animate-hero-btn">Ver Nuestro Menú</a>
            </div>
        </div>
    </section>

    <section id="favoritos" class="section">
        <div class="container">
            <div class="section-panel animate-on-scroll">
                <div class="section-header">
                    <h2 class="section-title">Favoritos de la Casa</h2>
                    <p class="section-subtitle">Los rolls más pedidos por nuestros clientes. ¡No te los puedes perder!</p>
                </div>
                <div class="menu-grid grid">
                    <?php foreach ($productos_destacados as $producto): ?>
                        <article class="menu-item" data-id="<?php echo $producto['id']; ?>" data-name="<?php echo htmlspecialchars($producto['nombre']); ?>" data-price="<?php echo $producto['precio']; ?>">
                            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Foto de <?php echo htmlspecialchars($producto['nombre']); ?>" class="menu-item-image">
                            <div class="menu-item-content">
                                <h3 class="menu-item-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <p class="menu-item-description"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            </div>
                            <div class="menu-item-footer">
                                <span class="menu-item-price">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></span>
                                <button class="btn btn-primary add-to-cart-btn">Añadir</button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

   <section class="section">
    <div class="container">
        <div class="section-panel animate-on-scroll">
            <div class="section-header">
                <h2 class="section-title">Tu Sushi en 3 Simples Pasos</h2>
            </div>
            
            <div class="steps-container">

                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3 class="step-title">Crea o Elige</h3>
                        <p class="step-text">Diseña tu roll perfecto desde cero o elige uno de nuestros clásicos del menú.</p>
                        <a href="personalizado.php" class="btn btn-primary" style="margin-top: 1rem;">Crear mi Roll</a>
                    </div>
                </div>

                <div class="step-connector"></div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3 class="step-title">Pide por WhatsApp</h3>
                        <p class="step-text">Usa el carrito para generar tu pedido y envíalo a nuestro WhatsApp con un solo clic.</p>
                    </div>
                </div>

                <div class="step-connector"></div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3 class="step-title">Disfruta en Casa</h3>
                        <p class="step-text">Relájate mientras preparamos tu orden. Te la llevaremos fresca y lista para disfrutar.</p>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </section>

    <section class="section">
    <div class="container">
        <div class="section-panel animate-on-scroll">
            <div class="section-header">
                <h2 class="section-title">#RollXpress</h2>
                <p class="section-subtitle">Síguenos en Instagram para ver nuestras últimas creaciones y promociones.</p>
            </div>
            
            <div class="gallery-grid">
                <a href="https://www.instagram.com/rollxpress._/" target="_blank" class="gallery-item">
                    <img src="img/sushi-hero.webp" alt="Foto de sushi 1">
                    <div class="gallery-item-overlay"><i class="fab fa-instagram"></i></div>
                </a>
                <a href="https://instagram.com/rollxpress._/" target="_blank" class="gallery-item">
                    <img src="img/sushi-hero.webp" alt="Foto de sushi 2">
                    <div class="gallery-item-overlay"><i class="fab fa-instagram"></i></div>
                </a>
                <a href="https://instagram.com/rollxpress._/" target="_blank" class="gallery-item">
                    <img src="img/sushi-hero.webp" alt="Foto de sushi 3">
                    <div class="gallery-item-overlay"><i class="fab fa-instagram"></i></div>
                </a>
                <a href="https://instagram.com/rollxpress._/" target="_blank" class="gallery-item">
                    <img src="img/sushi-hero.webp" alt="Foto de sushi 4">
                    <div class="gallery-item-overlay"><i class="fab fa-instagram"></i></div>
                </a>
            </div>
             <div style="text-align: center; margin-top: 2rem;">
                <a href="https://instagram.com/rollxpress._/" target="_blank" class="btn btn-primary">Ver más en Instagram</a>
            </div>
        </div>
    </div>
</section>
</main>

<?php
include 'footer.php';
?>