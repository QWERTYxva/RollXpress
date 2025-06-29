// js/main.js - VERSIÓN FINAL, COMPLETA Y UNIFICADA

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
    const direccionInput = document.querySelector('#cart-direccion-input');
    const checkoutBtn = document.querySelector('#checkout-btn');
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav');
    const userDropdownToggle = document.querySelector('.dropdown-toggle');
    const userDropdownMenu = document.querySelector('.dropdown-menu');
    
    let currentCart = []; // Variable global para guardar el estado del carrito

    // =========================================================================
    //  LÓGICA DEL CARRITO DE COMPRAS (CONEXIÓN CON LA BASE DE DATOS)
    // =========================================================================

    // Función central para todas las comunicaciones con nuestra API en PHP
    async function cartApiRequest(action, data = {}) {
        try {
            const response = await fetch('carrito_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action, ...data })
            });
            // Verificamos si la respuesta es JSON antes de procesarla
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return await response.json();
            } else {
                const text = await response.text();
                throw new Error("Respuesta inesperada del servidor: " + text);
            }
        } catch (error) {
            console.error('Error en la API del carrito:', error);
            alert('Error de conexión con el servidor del carrito.');
            return { success: false };
        }
    }
    // Pide al servidor el carrito del usuario y lo muestra
    async function fetchCart() {
        if (!document.body.classList.contains('logged-in')) {
            renderCart([]); // Muestra un carrito vacío si no hay sesión iniciada
            return;
        }
        const data = await cartApiRequest('get');
        if (data.success) {
            currentCart = data.cart;
            renderCart(currentCart);
        }
    }

    // Pide al servidor añadir un producto (estándar o personalizado)
    async function addToCartDB(productData) {
        let payload = (productData.isCustom) 
            ? { custom_product: productData } 
            : { producto_id: productData, cantidad: 1 };
        
        const result = await cartApiRequest('add', payload);
        if(result.success) {
            await fetchCart();
            toggleCart();
        } else {
            alert(result.message || 'Hubo un error al añadir el producto.');
        }
    }

    // Pide al servidor actualizar la cantidad de un item o eliminarlo
    async function updateCartItemDB(itemId, newQuantity) {
        await cartApiRequest('update', { item_id: itemId, cantidad: newQuantity });
        await fetchCart();
    }

    // Dibuja el contenido del carrito en la barra lateral
    function renderCart(cartData) {
        if (!cartBody) return;
        cartBody.innerHTML = ''; 

        if (!cartData || cartData.length === 0) {
            cartBody.innerHTML = '<p class="cart-empty-msg">Tu carrito está vacío.</p>';
        } else {
            cartData.forEach(item => {
                // LÓGICA CLAVE: Determina qué nombre y precio usar
                const esPersonalizado = parseInt(item.is_custom) === 1;
                const nombre = esPersonalizado ? item.custom_nombre : item.nombre;
                const precio = esPersonalizado ? item.custom_precio : item.precio;
                const descripcion = esPersonalizado ? item.custom_descripcion : '';
                
                const descriptionHTML = descripcion ? `<p class="cart-item-description">${descripcion}</p>` : '';
                
                const cartItemHTML = `
                    <div class="cart-item" data-item-id="${item.id}">
                        <div class="cart-item-info">
                            <h4>${nombre || 'Producto sin nombre'}</h4>
                            ${descriptionHTML}
                            <p class="cart-item-price">$${((precio || 0) * item.cantidad).toLocaleString('es-CL')}</p>
                        </div>
                        <div class="cart-item-quantity">
                            <button type="button" class="quantity-btn decrease-btn">-</button>
                            <span>${item.cantidad}</span>
                            <button type="button" class="quantity-btn increase-btn">+</button>
                        </div>
                        <button type="button" class="cart-item-remove">&times;</button>
                    </div>`;
                cartBody.innerHTML += cartItemHTML;
            });
        }
        const totalItems = cartData ? cartData.reduce((sum, item) => sum + parseInt(item.cantidad), 0) : 0;
        const totalPrice = cartData ? cartData.reduce((sum, item) => sum + (item.precio * item.cantidad), 0) : 0;
        
        if(cartItemCount) cartItemCount.textContent = totalItems;
        if(cartTotalPrice) cartTotalPrice.textContent = `$${totalPrice.toLocaleString('es-CL')}`;
        
        updateCheckoutButtonState();
    }
    
    // Función para abrir/cerrar la barra lateral del carrito
    function toggleCart() {
        if(cartSidebar) cartSidebar.classList.toggle('is-open');
        if(cartOverlay) cartOverlay.classList.toggle('is-visible');
    }

    // =========================================================================
    //  ASIGNACIÓN DE EVENTOS Y LÓGICAS DE PÁGINA
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

    // --- CARRITO DE COMPRAS Y CHECKOUT ---
    if (cartIcon) cartIcon.addEventListener('click', toggleCart);
    if (cartCloseBtn) cartCloseBtn.addEventListener('click', toggleCart);
    if (cartOverlay) cartOverlay.addEventListener('click', toggleCart);

    // Añadir al carrito (usando delegación de eventos)
    document.addEventListener('click', (e) => {
        if (e.target.matches('.add-to-cart-btn')) {
            e.preventDefault();
            e.stopPropagation();

            if (!document.body.classList.contains('logged-in')) {
                alert('Debes iniciar sesión para añadir productos al carrito.');
                window.location.href = 'login.php';
                return;
            }
            
            const menuItem = e.target.closest('.menu-item');
            if (menuItem && menuItem.dataset.id) {
                addToCartDB(menuItem.dataset.id);
            }
        }
    });

    // Acciones dentro del carrito (+, -, X)
    if(cartBody){
        cartBody.addEventListener('click', (e) => {
            const cartItemDiv = e.target.closest('.cart-item');
            if (!cartItemDiv) return;
            const itemId = cartItemDiv.dataset.itemId;
            const item = currentCart.find(i => i.id == itemId);
            if (!item) return;

            if (e.target.classList.contains('increase-btn')) updateCartItemDB(itemId, item.cantidad + 1);
            else if (e.target.classList.contains('decrease-btn')) updateCartItemDB(itemId, item.cantidad - 1);
            else if (e.target.classList.contains('cart-item-remove')) updateCartItemDB(itemId, 0);
        });
    }
    
    // Botón Finalizar Pedido
    function updateCheckoutButtonState() {
        if (!checkoutBtn || !direccionInput) return;
        checkoutBtn.disabled = !currentCart || currentCart.length === 0 || direccionInput.value.trim() === "";
    }
    if (direccionInput) direccionInput.addEventListener('input', updateCheckoutButtonState);
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', async () => {
            const direccion = direccionInput.value.trim();
            if (checkoutBtn.disabled || !direccion) return;

            checkoutBtn.disabled = true;
            checkoutBtn.textContent = 'Procesando...';

            const data = await cartApiRequest('checkout', { direccion });

            if (data.success && data.pedido_id) {
                window.location.href = `seguimiento.php?id=${data.pedido_id}`;
            } else {
                alert('Error al finalizar el pedido: ' + (data.message || 'Inténtalo de nuevo.'));
                checkoutBtn.disabled = false;
                checkoutBtn.textContent = 'Finalizar Pedido';
            }
        });
    }

    // --- FILTROS DE LA PÁGINA DE MENÚ ---
    const filtersContainer = document.querySelector('.menu-filters');
    if (filtersContainer) {
        filtersContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('filter-btn')) {
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
    const builderForm = document.querySelector('#sushi-builder-form');
    if (builderForm) {
        const summaryList = document.querySelector('#summary-list');
        const extrasPriceEl = document.querySelector('#extras-price');
        const finalPriceEl = document.querySelector('#final-price');
        const addCustomBtn = document.querySelector('#add-custom-to-cart');
        const basePrice = 4990;

        const updateBuilderSummary = () => {
            let extrasPrice = 0;
            const checkedInputs = builderForm.querySelectorAll('input:checked');
            summaryList.innerHTML = '';
            
            if (checkedInputs.length > 0) {
                checkedInputs.forEach(input => {
                    extrasPrice += parseInt(input.dataset.precio);
                    summaryList.innerHTML += `<div class="summary-item"><span>${input.dataset.nombre}</span><span>+$${parseInt(input.dataset.precio).toLocaleString('es-CL')}</span></div>`;
                });
                addCustomBtn.disabled = false;
            } else {
                summaryList.innerHTML = '<p class="summary-placeholder">Selecciona ingredientes.</p>';
                addCustomBtn.disabled = true;
            }
            extrasPriceEl.textContent = `$${extrasPrice.toLocaleString('es-CL')}`;
            finalPriceEl.textContent = `$${(basePrice + extrasPrice).toLocaleString('es-CL')}`;
        };

        builderForm.addEventListener('change', updateBuilderSummary);
        
        addCustomBtn.addEventListener('click', () => {
            if (!document.body.classList.contains('logged-in')) {
                alert('Debes iniciar sesión para añadir productos al carrito.');
                window.location.href = 'login.php';
                return;
            }
            const checkedInputs = builderForm.querySelectorAll('input:checked');
            const ingredientsText = Array.from(checkedInputs).map(input => input.dataset.nombre);
            const extrasPrice = Array.from(checkedInputs).reduce((sum, input) => sum + parseInt(input.dataset.precio), 0);
            
            const customRoll = {
                isCustom: true,
                nombre: 'Roll Personalizado',
                precio: basePrice + extrasPrice,
                cantidad: 1,
                custom_description: ingredientsText.join(', ')
            };
            addToCartDB(customRoll); 
        });
        updateBuilderSummary();
    }
    
    // --- ANIMACIONES Y COPYRIGHT ---
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

    const yearSpan = document.querySelector('#year');
    if (yearSpan) yearSpan.textContent = new Date().getFullYear();

    // =========================================================================
    //  INICIALIZACIÓN
    // =========================================================================
    fetchCart(); // Carga el carrito del usuario al iniciar la página.
    document.body.classList.remove('is-loading'); // Activa la animación de entrada.
});