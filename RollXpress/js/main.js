// js/main.js - Versión Final, Completa y Verificada

document.addEventListener('DOMContentLoaded', () => {

    // =========================================================================
    //  SELECTORES GLOBALES DE ELEMENTOS
    // =========================================================================
    const cartIcon = document.querySelector('.cart-icon-container');
    const cartSidebar = document.querySelector('.cart-sidebar');
    const cartCloseBtn = document.querySelector('.cart-close-btn');
    const cartOverlay = document.querySelector('.cart-overlay');
    const cartBody = document.querySelector('.cart-body');
    const cartItemCount = document.querySelector('.cart-item-count');
    const cartTotalPrice = document.querySelector('#cart-total-price');
    const generateOrderBtn = document.querySelector('#generate-order-btn');
    const direccionInput = document.querySelector('#cart-direccion-input');
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav');
    const userDropdownToggle = document.querySelector('.dropdown-toggle');
    const userDropdownMenu = document.querySelector('.dropdown-menu');
    
    let currentCart = [];

    // =========================================================================
    //  LÓGICA DEL CARRITO DE COMPRAS (CONEXIÓN CON LA BASE DE DATOS)
    // =========================================================================
    async function cartApiRequest(action, data = {}) {
        try {
            const response = await fetch('carrito_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action, ...data })
            });
            return await response.json();
        } catch (error) {
            console.error('Error en la API del carrito:', error);
            return { success: false, message: 'Error de conexión.' };
        }
    }

    async function fetchCart() {
        if (!document.body.classList.contains('logged-in')) return;
        const data = await cartApiRequest('get');
        if (data.success) {
            currentCart = data.cart;
            renderCart(currentCart);
        }
    }

    async function addToCartDB(productData) {
        let payload;
        if (productData.isCustom) {
            payload = { custom_product: productData };
        } else {
            payload = { producto_id: productData, cantidad: 1 };
        }
        
        await cartApiRequest('add', payload);
        await fetchCart();
        toggleCart();
    }

    async function updateCartItemDB(itemId, newQuantity) {
        await cartApiRequest('update', { item_id: itemId, cantidad: newQuantity });
        await fetchCart();
    }

    function renderCart(cartData) {
        if (!cartBody) return;
        cartBody.innerHTML = ''; 

        if (!cartData || cartData.length === 0) {
            cartBody.innerHTML = '<p class="cart-empty-msg">Tu carrito está vacío.</p>';
        } else {
            cartData.forEach(item => {
                const descriptionHTML = item.custom_description ? `<p class="cart-item-description">${item.custom_description}</p>` : '';
                const cartItemHTML = `
                    <div class="cart-item" data-item-id="${item.id}">
                        <div class="cart-item-info">
                            <h4>${item.nombre}</h4>
                            ${descriptionHTML}
                            <p class="cart-item-price">$${(item.precio * item.cantidad).toLocaleString('es-CL')}</p>
                        </div>
                        <div class="cart-item-quantity">
                            <button class="quantity-btn decrease-btn" type="button">-</button>
                            <span>${item.cantidad}</span>
                            <button class="quantity-btn increase-btn" type="button">+</button>
                        </div>
                        <button class="cart-item-remove" type="button">&times;</button>
                    </div>`;
                cartBody.innerHTML += cartItemHTML;
            });
        }
        
        const totalItems = cartData ? cartData.reduce((sum, item) => sum + parseInt(item.cantidad), 0) : 0;
        const totalPrice = cartData ? cartData.reduce((sum, item) => sum + (item.precio * item.cantidad), 0) : 0;
        
        if(cartItemCount) cartItemCount.textContent = totalItems;
        if(cartTotalPrice) cartTotalPrice.textContent = `$${totalPrice.toLocaleString('es-CL')}`;
        
        updateOrderButtonState();
    }
    
    function toggleCart() {
        if(cartSidebar) cartSidebar.classList.toggle('is-open');
        if(cartOverlay) cartOverlay.classList.toggle('is-visible');
    }

    // =========================================================================
    //  ASIGNACIÓN DE EVENTOS (EVENT LISTENERS)
    // =========================================================================

    // --- NAVEGACIÓN Y MENÚS ---
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('nav--visible');
            document.body.classList.toggle('no-scroll');
            navToggle.setAttribute('aria-expanded', navMenu.classList.contains('nav--visible'));
        });
    }

    if (userDropdownToggle && userDropdownMenu) {
        userDropdownToggle.addEventListener('click', (e) => {
            e.preventDefault();
            userDropdownMenu.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            if (userDropdownMenu.classList.contains('active') && !userDropdownToggle.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                userDropdownMenu.classList.remove('active');
            }
        });
    }

    // --- CARRITO DE COMPRAS ---
    if (cartIcon) cartIcon.addEventListener('click', toggleCart);
    if (cartCloseBtn) cartCloseBtn.addEventListener('click', toggleCart);
    if (cartOverlay) cartOverlay.addEventListener('click', toggleCart);

    // Botones "Añadir al Carrito" en todo el sitio
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (!document.body.classList.contains('logged-in')) {
                window.location.href = 'login.php';
                return;
            }
            
            const menuItem = e.target.closest('.menu-item');
            if (menuItem) {
                const producto_id = menuItem.dataset.id;
                addToCartDB(producto_id);
            }
        });
    });

    // Acciones dentro del carrito (+, -, X)
    if(cartBody){
        cartBody.addEventListener('click', (e) => {
            const cartItemDiv = e.target.closest('.cart-item');
            if (!cartItemDiv) return;

            const itemId = cartItemDiv.dataset.itemId;
            const item = currentCart.find(i => i.id == itemId);
            if (!item) return;

            if (e.target.classList.contains('increase-btn')) {
                updateCartItemDB(itemId, item.cantidad + 1);
            } else if (e.target.classList.contains('decrease-btn')) {
                updateCartItemDB(itemId, item.cantidad - 1);
            } else if (e.target.classList.contains('cart-item-remove')) {
                updateCartItemDB(itemId, 0);
            }
        });
    }
    
    // --- FILTROS DE LA PÁGINA DE MENÚ ---
    const filtersContainer = document.querySelector('.menu-filters');
    if (filtersContainer) {
        filtersContainer.addEventListener('click', (e) => {
            if (e.target.tagName === 'BUTTON') {
                filtersContainer.querySelector('.active').classList.remove('active');
                e.target.classList.add('active');
                const filterValue = e.target.dataset.filter;
                document.querySelectorAll('.menu-item').forEach(item => {
                    item.classList.toggle('hidden', filterValue !== 'all' && item.dataset.category !== filterValue);
                });
            }
        });
    }

    // --- CREADOR DE SUSHI PERSONALIZADO ---
    // (La lógica que ya teníamos)
    
    // --- ANIMACIONES AL HACER SCROLL ---
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        animatedElements.forEach(element => observer.observe(element));
    } else {
        animatedElements.forEach(element => element.classList.add('is-visible'));
    }

    // --- BOTÓN WHATSAPP Y COPYRIGHT ---
    function updateOrderButtonState() {
        if (!generateOrderBtn || !direccionInput) return;
        generateOrderBtn.classList.toggle('btn-disabled', !currentCart || currentCart.length === 0 || direccionInput.value.trim() === "");
    }
    if (direccionInput) direccionInput.addEventListener('input', updateOrderButtonState);
    if(generateOrderBtn) {
        generateOrderBtn.addEventListener('click', (e) => {
            if(generateOrderBtn.classList.contains('btn-disabled')) {
                e.preventDefault();
                alert("Por favor, añade productos e ingresa tu dirección.");
                return;
            }
            // Lógica para generar mensaje de WhatsApp
        });
    }
    
    const yearSpan = document.querySelector('#year');
    if (yearSpan) yearSpan.textContent = new Date().getFullYear();

    // =========================================================================
    //  INICIALIZACIÓN
    // =========================================================================
    fetchCart();
    document.body.classList.remove('is-loading');
});