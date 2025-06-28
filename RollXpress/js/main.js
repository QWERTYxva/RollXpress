// js/main.js - Versión Final y Unificada

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

    let currentCart = []; // Variable para guardar el estado del carrito actual
    const userDropdownToggle = document.querySelector('.dropdown-toggle');
    const userDropdownMenu = document.querySelector('.dropdown-menu');

    if (userDropdownToggle && userDropdownMenu) {
        // Muestra/oculta el menú al hacer clic en el ícono
        userDropdownToggle.addEventListener('click', (e) => {
            e.preventDefault(); // Evita que el enlace '#' recargue la página
            userDropdownMenu.classList.toggle('active');
        });

        // Cierra el menú si se hace clic fuera de él
        document.addEventListener('click', (e) => {
            // Si el menú está activo y el clic NO fue en el ícono ni dentro del menú...
            if (userDropdownMenu.classList.contains('active') && !userDropdownToggle.contains(e.target)) {
                userDropdownMenu.classList.remove('active');
            }
        });
    }

    // Función para comunicarse con nuestra API de PHP
    async function cartApiRequest(action, data = {}) {
        try {
            const response = await fetch('carrito_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action, ...data })
            });
            return await response.json();
        } catch (error) {
            console.error('Error en la solicitud a la API del carrito:', error);
            return { success: false, message: 'Error de conexión.' };
        }
    }

    // Pide al servidor el carrito del usuario y lo muestra
    async function fetchCart() {
        const data = await cartApiRequest('get');
        if (data.success) {
            currentCart = data.cart;
            renderCart(currentCart);
        }
    }

    // Pide al servidor añadir un producto
    async function addToCart(producto_id, cantidad = 1) {
        await cartApiRequest('add', { producto_id, cantidad });
        await fetchCart();
        toggleCart();
    }

    // Pide al servidor actualizar la cantidad de un item
    async function updateCartItem(item_id, cantidad) {
        await cartApiRequest('update', { item_id, cantidad });
        await fetchCart();
    }

    // Dibuja el contenido del carrito en la barra lateral
    function renderCart(cart) {
        if (!cartBody) return;
        cartBody.innerHTML = ''; 

        if (!cart || cart.length === 0) {
            cartBody.innerHTML = '<p class="cart-empty-msg">Tu carrito está vacío.</p>';
        } else {
            cart.forEach(item => {
                const descriptionHTML = item.custom_description ? `<p class="cart-item-description">${item.custom_description}</p>` : '';
                const cartItemHTML = `
                    <div class="cart-item" data-item-id="${item.id}">
                        <div class="cart-item-info">
                            <h4>${item.nombre}</h4>
                            ${descriptionHTML}
                            <p class="cart-item-price">$${(item.precio * item.cantidad).toLocaleString('es-CL')}</p>
                        </div>
                        <div class="cart-item-quantity">
                            <button class="quantity-btn decrease-btn">-</button>
                            <span>${item.cantidad}</span>
                            <button class="quantity-btn increase-btn">+</button>
                        </div>
                        <button class="cart-item-remove">&times;</button>
                    </div>`;
                cartBody.innerHTML += cartItemHTML;
            });
        }

        const totalItems = cart ? cart.reduce((sum, item) => sum + parseInt(item.cantidad), 0) : 0;
        const totalPrice = cart ? cart.reduce((sum, item) => sum + (item.precio * item.cantidad), 0) : 0;
        
        if(cartItemCount) cartItemCount.textContent = totalItems;
        if(cartTotalPrice) cartTotalPrice.textContent = `$${totalPrice.toLocaleString('es-CL')}`;
        
        updateOrderButtonState();
    }

    // =========================================================================
    //  LÓGICA GENERAL DE LA PÁGINA E INTERACTIVIDAD
    // =========================================================================

    // Función para abrir/cerrar el carrito
    function toggleCart() {
        if(cartSidebar) cartSidebar.classList.toggle('is-open');
        if(cartOverlay) cartOverlay.classList.toggle('is-visible');
    }

    // --- NAVEGACIÓN MÓVIL ---
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('nav--visible');
            // NUEVO: Añadimos o quitamos una clase al body
            document.body.classList.toggle('no-scroll'); 
            
            const isVisible = navMenu.classList.contains('nav--visible');
            navToggle.setAttribute('aria-expanded', isVisible);
        });
    }

    // --- EVENTOS DE CLIC ---
    if (cartIcon) cartIcon.addEventListener('click', toggleCart);
    if (cartCloseBtn) cartCloseBtn.addEventListener('click', toggleCart);
    if (cartOverlay) cartOverlay.addEventListener('click', toggleCart);

    // Añadir al carrito desde los botones de producto
document.querySelectorAll('.add-to-cart-btn').forEach(button => {
    button.addEventListener('click', (e) => {
        // 1. PREVENIMOS LA ACCIÓN POR DEFECTO (LA REDIRECCIÓN)
        e.preventDefault();

        // 2. Verificamos si el usuario está logueado
        if (!document.body.classList.contains('logged-in')) {
            // Si no está logueado, ahí sí lo mandamos al login.
            window.location.href = 'login.php';
            return;
        }

        // 3. Si está logueado, ejecutamos la lógica del carrito
        const producto_id = e.target.closest('.menu-item').dataset.id;
        addToCartDB(producto_id); // Esta es tu función que habla con la API
    });
});

    // Acciones dentro del carrito (-, +, X)
    if(cartBody){
        cartBody.addEventListener('click', (e) => {
            const target = e.target;
            const cartItemDiv = target.closest('.cart-item');
            if (!cartItemDiv) return;

            const item_id = cartItemDiv.dataset.itemId;
            const item = currentCart.find(i => i.id == item_id);
            if (!item) return;

            if (target.classList.contains('increase-btn')) {
                updateCartItem(item_id, item.cantidad + 1);
            } else if (target.classList.contains('decrease-btn')) {
                updateCartItem(item_id, item.cantidad - 1);
            } else if (target.classList.contains('cart-item-remove')) {
                updateCartItem(item_id, 0); // La API lo elimina si la cantidad es 0
            }
        });
    }
    
    // --- FILTROS DE LA PÁGINA DE MENÚ ---
    const filtersContainer = document.querySelector('.menu-filters');
    if (filtersContainer) {
        const filterButtons = filtersContainer.querySelectorAll('.filter-btn');
        const menuItems = document.querySelectorAll('.menu-item');
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                const filterValue = button.dataset.filter;
                menuItems.forEach(item => {
                    item.classList.toggle('hidden', filterValue !== 'all' && item.dataset.category !== filterValue);
                });
            });
        });
    }

    // --- CREADOR DE SUSHI PERSONALIZADO ---
    const builderForm = document.querySelector('#sushi-builder-form');
    if(builderForm) {
        // ... (Aquí iría la lógica específica del creador de sushi si es necesario, 
        // por ahora, el botón "Añadir" es manejado por el sistema de carrito normal si se le añaden las clases correctas)
    }

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
    }a// js/main.js - Versión Final con Creador de Sushi Personalizado Integrado

document.addEventListener('DOMContentLoaded', () => {

    // =========================================================================
    //  SELECTORES GLOBALES Y LÓGICA DEL CARRITO (SIN CAMBIOS)
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

    let currentCart = [];

    async function cartApiRequest(action, data = {}) {
        // ... (código de la API sin cambios)
    }
    async function fetchCart() {
        // ... (código de fetchCart sin cambios)
    }
    async function addToCartDB(productData) { // <-- Modificamos esta función para aceptar objetos
        let payload;
        if (productData.target) { // Si es un evento de clic
             const menuItem = productData.target.closest('.menu-item');
             if (!menuItem) return;
             payload = { producto_id: menuItem.dataset.id, cantidad: 1 };
        } else { // Si es un objeto de producto (como el roll personalizado)
            payload = { custom_product: productData };
        }
        
        try {
            await fetch('carrito_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'add', ...payload })
            });
            await fetchCart();
            toggleCart();
        } catch (error) {
            console.error('Error al añadir al carrito:', error);
        }
    }
    async function updateCartItem(item_id, cantidad) {
        // ... (código de updateCartItem sin cambios)
    }
    function renderCart(cart) {
        // ... (código de renderCart sin cambios)
    }
    function toggleCart() {
        // ... (código de toggleCart sin cambios)
    }

    // =========================================================================
    //  LÓGICA GENERAL DE LA PÁGINA E INTERACTIVIDAD
    // =========================================================================

    // ... (Toda tu lógica para Navegación, Eventos del Carrito, Filtros, Animaciones, etc. va aquí sin cambios)

    // --- CREADOR DE SUSHI PERSONALIZADO (personalizado.php) ---
    const builderForm = document.querySelector('#sushi-builder-form');
    if (builderForm) {
        const summaryList = document.querySelector('#summary-list');
        const extrasPriceEl = document.querySelector('#extras-price');
        const finalPriceEl = document.querySelector('#final-price');
        const addCustomBtn = document.querySelector('#add-custom-to-cart');
        const basePrice = 4990; // Precio base de un roll personalizado

        const updateBuilderSummary = () => {
            let extrasPrice = 0;
            const selectedIngredients = [];
            const checkedInputs = builderForm.querySelectorAll('input:checked');

            summaryList.innerHTML = ''; // Limpiar resumen

            if (checkedInputs.length === 0) {
                summaryList.innerHTML = '<p class="summary-placeholder">Selecciona ingredientes para verlos aquí.</p>';
                addCustomBtn.disabled = true;
            } else {
                checkedInputs.forEach(input => {
                    const nombre = input.dataset.nombre;
                    const precio = parseInt(input.dataset.precio);
                    extrasPrice += precio;
                    selectedIngredients.push(nombre);

                    // Añadir al resumen visual
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'summary-item';
                    itemDiv.innerHTML = `<span>${nombre}</span> <span>+$${precio.toLocaleString('es-CL')}</span>`;
                    summaryList.appendChild(itemDiv);
                });
                addCustomBtn.disabled = false;
            }

            extrasPriceEl.textContent = `$${extrasPrice.toLocaleString('es-CL')}`;
            finalPriceEl.textContent = `$${(basePrice + extrasPrice).toLocaleString('es-CL')}`;
        };

        // Cada vez que cambia una selección en el formulario, actualizamos el resumen
        builderForm.addEventListener('change', updateBuilderSummary);

        // Evento para el botón "Añadir al Carrito"
        addCustomBtn.addEventListener('click', () => {
            const checkedInputs = builderForm.querySelectorAll('input:checked');
            if (checkedInputs.length === 0) return;

            const ingredientsText = Array.from(checkedInputs).map(input => input.dataset.nombre);
            let extrasPrice = 0;
            checkedInputs.forEach(input => {
                extrasPrice += parseInt(input.dataset.precio);
            });

            // Creamos el objeto del producto personalizado
            const customRoll = {
                id: `custom-${Date.now()}`, // ID único para que no se agrupe con otros rolls personalizados
                name: 'Roll Personalizado',
                price: basePrice + extrasPrice,
                quantity: 1,
                description: ingredientsText.join(', ') // La descripción son los ingredientes
            };
            
            // Llamamos a nuestra función principal para añadir al carrito, pasándole el objeto
            addToCartDB(customRoll);
        });

        updateBuilderSummary(); // Llamada inicial para establecer el estado del botón
    }

    // --- El resto de tu código (Copyright, Inicialización, etc.) ---
    const yearSpan = document.querySelector('#year');
    if (yearSpan) yearSpan.textContent = new Date().getFullYear();

    if (document.body.classList.contains('logged-in')) {
        fetchCart();
    } else {
        if(cartBody) renderCart([]);
    }

    document.body.classList.remove('is-loading');
});

    // --- BOTÓN DE WHATSAPP Y COPYRIGHT ---
    function updateOrderButtonState() {
        if (!generateOrderBtn || !direccionInput) return;
        generateOrderBtn.classList.toggle('btn-disabled', currentCart.length === 0 || direccionInput.value.trim() === "");
    }
    if (direccionInput) direccionInput.addEventListener('input', updateOrderButtonState);
    // (La lógica para generar el link de WhatsApp se puede añadir aquí si se mantiene esa función)

    const yearSpan = document.querySelector('#year');
    if (yearSpan) yearSpan.textContent = new Date().getFullYear();

    // =========================================================================
    //  INICIALIZACIÓN
    // =========================================================================
    // Solo intentamos cargar el carrito desde la BD si el usuario ha iniciado sesión.
    if (document.body.classList.contains('logged-in')) {
        fetchCart();
    } else {
        renderCart([]); // Muestra un carrito vacío para los invitados
    }
});