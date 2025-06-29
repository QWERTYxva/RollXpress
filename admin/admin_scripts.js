// admin/admin_scripts.js
document.addEventListener('DOMContentLoaded', () => {
    
    // Solo ejecutamos esto si estamos en la página de gestión de pedidos
    const tablaPedidosBody = document.querySelector('#tabla-pedidos-body');
    if (!tablaPedidosBody) return;

    let ultimoPedidoId = 0;
    let ultimoUsuarioId = 0;
    const notificacionSonido = document.querySelector('#notificacion-sonido');

    // Función para obtener las actualizaciones
    async function checkForUpdates() {
        try {
            // Le pasamos los últimos IDs que tenemos para que la API busque a partir de ahí
            const response = await fetch(`api_dashboard.php?ultimo_pedido_id=${ultimoPedidoId}&ultimo_usuario_id=${ultimoUsuarioId}`);
            const data = await response.json();

            // Si hay nuevos pedidos, los añadimos a la tabla
            if (data.nuevos_pedidos && data.nuevos_pedidos.length > 0) {
                // Actualizamos el ID del último pedido visto
                ultimoPedidoId = data.nuevos_pedidos[0].id; 
                
                // Reproducimos un sonido de notificación
                if(notificacionSonido) notificacionSonido.play();

                data.nuevos_pedidos.reverse().forEach(pedido => {
                    const newRow = document.createElement('tr');
                    newRow.className = 'new-order-row'; // Clase para destacar la nueva fila
                    newRow.innerHTML = `
                        <td>#${pedido.id}</td>
                        <td>${pedido.nombre_usuario}</td>
                        <td>${pedido.fecha_pedido}</td>
                        <td>$${parseInt(pedido.total).toLocaleString('es-CL')}</td>
                        <td>${pedido.estado}</td>
                        <td><a href="#" class="action-btn">Ver</a></td>
                    `;
                    tablaPedidosBody.prepend(newRow); // Añadimos la nueva fila al PRINCIPIO de la tabla
                });
            }

            // Si hay nuevos usuarios, mostramos una alerta (se puede mejorar a una notificación toast)
            if (data.nuevos_usuarios && data.nuevos_usuarios.length > 0) {
                 ultimoUsuarioId = data.nuevos_usuarios[0].id;
                 data.nuevos_usuarios.forEach(usuario => {
                     alert(`¡Nuevo usuario registrado!\nNombre: ${usuario.nombre}\nEmail: ${usuario.email}`);
                 });
            }

        } catch (error) {
            console.error("Error al buscar actualizaciones:", error);
        }
    }

    // Inicializamos el estado actual de los IDs de la tabla ya cargada
    const primeraFila = tablaPedidosBody.querySelector('tr');
    if (primeraFila) {
        ultimoPedidoId = parseInt(primeraFila.querySelector('td').textContent.replace('#',''));
    }
    // (Podríamos hacer lo mismo para el último ID de usuario si lo tuviéramos en la página)

    // Preguntamos al servidor por actualizaciones cada 15 segundos (15000 milisegundos)
    setInterval(checkForUpdates, 15000);
});