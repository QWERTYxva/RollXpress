<?php
// nav.php - Versión Final con Menú de Usuario Desplegable y Carrito

$pagina_actual = basename($_SERVER['PHP_SELF']);
?>

<header class="header">
    <div class="container header-container">
        <a href="index.php" class="logo-link">
            <img src="img/logo.svg" alt="Logo de RollXpress" class="logo">
        </a>
        
        <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">
            <span class="hamburger"></span>
        </button>
        
        <nav class="nav">
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link <?php if($pagina_actual == 'index.php') echo 'active'; ?>">Inicio</a></li>
                <li><a href="menu.php" class="nav-link <?php if($pagina_actual == 'menu.php') echo 'active'; ?>">Menú</a></li>
                <li><a href="contacto.php" class="nav-link <?php if($pagina_actual == 'contacto.php') echo 'active'; ?>">Contacto</a></li>
                
                <li class="nav-divider"></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item-dropdown">
                        <a href="#" class="dropdown-toggle" aria-label="Menú de usuario">
                            <i class="fas fa-user-circle"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="perfil.php" class="dropdown-item">
                                <i class="fas fa-user-cog"></i>
                                <span>Mi Perfil</span>
                            </a>
                            <a href="pedidos.php" class="dropdown-item">
                                <i class="fas fa-receipt"></i>
                                <span>Mis Pedidos</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item dropdown-item-logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Cerrar Sesión</span>
                            </a>
                        </div>
                    </li>

                    <li>
                        <div class="cart-icon-container" aria-label="Ver carrito de compras" tabindex="0">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="cart-item-count">0</span>
                        </div>
                    </li>

                <?php else: ?>
                    <li><a href="login.php" class="nav-link <?php if($pagina_actual == 'login.php') echo 'active'; ?>">Iniciar Sesión</a></li>
                    <li><a href="registro.php" class="nav-link nav-link-btn">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>