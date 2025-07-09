<?php
// footer.php
?>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid grid">

                <div class="footer-about">
                    <img src="img/logo.svg" alt="Logo de RollXpress en blanco" class="footer-logo">
                    <p class="footer-text">
                        Sushi fresco y de calidad, preparado por expertos para ofrecerte la mejor experiencia gastronómica en San Bernardo.
                    </p>
                </div>

                <div class="footer-links-container">
                    <h3 class="footer-title">Enlaces Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="index.php" class="footer-link">Inicio</a></li>
                        <li><a href="menu.php" class="footer-link">Nuestro Menú</a></li>
                        <li><a href="contacto.php" class="footer-link">Contacto</a></li>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h3 class="footer-title">Contacto</h3>
                    <ul class="footer-contact-list">
                        <li class="footer-contact-item">
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                            <span>Gran Av. José Miguel Carrera 8790, La Cisterna</span>
                        </li>
                        <li class="footer-contact-item">
                            <i class="fab fa-whatsapp" aria-hidden="true"></i>
                            <a href="https://wa.me/56982684951" class="footer-link">+56 9 8268 4951</a>
                        </li>
                        <li class="footer-contact-item">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            <span>Mar a Dom: 13:00 - 22:30 hrs</span>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="footer-bottom">
                <p class="footer-copyright">&copy; <?php echo date('Y'); ?> RollXpress. Todos los derechos reservados.</p>
            </div>

        </div>
    </footer>

    <aside class="cart-sidebar">
    <div class="cart-header">
      <h3>Tu Pedido</h3>
      <button class="cart-close-btn" aria-label="Cerrar carrito">&times;</button>
    </div>
    <div class="cart-body">
      <p class="cart-empty-msg">Tu carrito está vacío.</p>
    </div>
    <div class="cart-footer">
    <div class="cart-total">
        <strong>Total:</strong>
        <span id="cart-total-price">$0</span>
    </div>
    <a href="checkout.php" id="go-to-checkout-btn" class="btn btn-primary btn-block">
        Ir a Pagar
    </a>
</div>
</aside>

<div class="cart-overlay"></div>
    <script src="js/main.js?v=2.2"></script>

</body>
</html>