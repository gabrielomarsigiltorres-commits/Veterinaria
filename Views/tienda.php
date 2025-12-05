<?php
session_start();
require '../Modelo/conexion.php';

// 1. Obtener categorías
try {
    $stmt_cat = $conexion->prepare("SELECT * FROM categorias ORDER BY nombre_categoria ASC");
    $stmt_cat->execute();
    $categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo silencioso o log
}

// 2. Obtener productos (solo con stock)
try {
    $sql = "SELECT p.*, c.nombre_categoria 
            FROM productos p
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            WHERE p.stock > 0
            ORDER BY p.nombre ASC";
    $stmt_prod = $conexion->prepare($sql);
    $stmt_prod->execute();
    $productos = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo silencioso
}

// Datos del usuario para el mensaje de WhatsApp (si está logueado)
$nombre_cliente = $_SESSION['usuario_nombre'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tienda - Veterinaria del Norte</title>
    <meta name="description" content="Tienda online de productos veterinarios." />
    <link rel="stylesheet" href="css/tienda.css">
    <script src="https://unpkg.com/lucide@latest"></script> <!-- Íconos -->
    
    <style>
        /* Estilos Específicos para el Carrito Flotante y Modal */
        .boton-flotante-carrito {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #18a0d6;
            color: white;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            cursor: pointer;
            z-index: 1000;
            transition: transform 0.2s;
        }
        .boton-flotante-carrito:hover { transform: scale(1.1); }
        .contador-carrito {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff4757;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 50%;
            border: 2px solid white;
        }

        /* Estilos del Modal del Carrito */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.activo { display: flex; }
        
        .modal-carrito {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }
        
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; }
        .modal-header h2 { margin: 0; font-size: 1.5rem; color: #333; }
        .btn-cerrar { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #777; }
        
        .carrito-items { flex: 1; overflow-y: auto; margin-bottom: 20px; }
        .item-carrito { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px dashed #eee; padding-bottom: 10px; }
        .item-info h4 { margin: 0 0 5px; font-size: 1rem; }
        .item-info span { font-size: 0.85rem; color: #666; }
        .item-controles { display: flex; align-items: center; gap: 10px; }
        .btn-eliminar { color: #ff4757; cursor: pointer; background: none; border: none; }
        
        .resumen-carrito { background: #f8f9fa; padding: 15px; border-radius: 10px; }
        .fila-resumen { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.95rem; }
        .total { font-weight: bold; font-size: 1.2rem; color: #18a0d6; border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px; }
        
        .btn-whatsapp {
            background-color: #25D366;
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: 15px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .btn-whatsapp:hover { background-color: #20ba5a; }
        
        .carrito-vacio { text-align: center; padding: 30px; color: #777; }
    </style>
</head>

<body>

    <div class="contenedor-principal">

        <!-- HEADER -->
       <header class="encabezado">
        <div class="contenedor-encabezado">
            
            <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                <img class="logo" src="../img/veterinarialogo.png" alt="Logo" style="width: 45px; height: 45px;">
                <h1 style="font-size: 1.2rem; color: #18a0d6; margin: 0;">Clínica Veterinaria del Norte S.A.C</h1>
            </div>
            
            <nav class="nav-principal" style="flex-grow: 1; display: flex; justify-content: center; margin: 0 20px;">
                <ul style="display: flex; list-style: none; padding: 0; margin: 0; gap: 20px;">
                    <li><a href="dashboard.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Menú principal</a></li>
                    <li><a href="servicios.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Calendario servicios</a></li>
                    <li><a href="tienda.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Tienda</a></li>
                    <li><a href="contacto.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="perfil-usuario" style="display: flex; align-items: center; gap: 15px; flex-shrink: 0;">
                <a href="anuncio_cliente.php" class="campana" title="Ver anuncios importantes" style="text-decoration: none; font-size: 1.2rem;">🔔</a>
                
                <a href="perfil_cliente.php" title="Mi Perfil" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit;">
                    <img id="headerProfilePic" src="<?= $foto_perfil_url ?>" alt="Foto de Perfil" 
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #13b6ec;">
                    <span class="nombre-usuario" style="color: #333; font-weight: 600;"><?= htmlspecialchars($nombre_usuario); ?></span>
                </a>
                
                <a href="../Controller/cerrar_sesion.php" class="cerrar-sesion" style="text-decoration: none; color: #e44d4d; font-weight: 600;">Cerrar Sesión</a>
            </div>
        </div>
    </header>

        <!-- MAIN -->
        <main class="contenedor-limitado">
            <section class="introduccion-tienda">
                <h1>Tienda de Mascotas</h1>
                <p>Selecciona tus productos y envíanos tu pedido por WhatsApp.</p>
            </section>

            <div class="layout-tienda">
                
                <!-- Filtros -->
                <aside class="barra-lateral-filtros">
                    <div class="filtros-contenido">
                        <h2>Categorías</h2>
                        <div class="espacio-filtros">
                            <label><input type="radio" name="cat" value="all" checked onchange="filtrarProductos('all')"> Todos</label>
                            <?php foreach ($categorias as $cat): ?>
                                <label>
                                    <input type="radio" name="cat" value="<?= $cat['id_categoria'] ?>" onchange="filtrarProductos(<?= $cat['id_categoria'] ?>)">
                                    <?= htmlspecialchars($cat['nombre_categoria']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>

                <!-- Lista de Productos -->
                <section class="seccion-productos">
                    <div class="cuadricula-productos" id="gridProductos">
                        <?php if (empty($productos)): ?>
                            <p>No hay productos disponibles.</p>
                        <?php else: ?>
                            <?php foreach ($productos as $prod): ?>
                                <article class="producto-tarjeta" data-categoria="<?= $prod['id_categoria'] ?>">
                                    <img class="imagen-producto" 
                                         src="uploads/<?= htmlspecialchars($prod['imagen_url'] ? $prod['imagen_url'] : 'default_product.png'); ?>" 
                                         alt="<?= htmlspecialchars($prod['nombre']) ?>">
                                    
                                    <div class="detalle-producto">
                                        <p class="nombre-prod"><?= htmlspecialchars($prod['nombre']) ?></p>
                                        <p class="desc-prod"><?= htmlspecialchars(substr($prod['descripcion'], 0, 60)) ?>...</p>
                                        <p class="precio-prod">S/ <?= number_format($prod['precio'], 2) ?></p>
                                        <p style="font-size: 0.8rem; color: #888;">Stock: <?= $prod['stock'] ?></p>
                                    </div>
                                    
                                    <!-- Datos para JS -->
                                    <button class="boton-anadir-carrito" 
                                            onclick="agregarAlCarrito(
                                                <?= $prod['id_producto'] ?>, 
                                                '<?= htmlspecialchars($prod['nombre']) ?>', 
                                                <?= $prod['precio'] ?>, 
                                                <?= $prod['stock'] ?>
                                            )">
                                        <i data-lucide="shopping-cart" style="width: 16px;"></i> Agregar
                                    </button>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>

        <!-- BOTÓN FLOTANTE CARRITO -->
        <div class="boton-flotante-carrito" onclick="abrirCarrito()">
            <i data-lucide="shopping-cart"></i>
            <span class="contador-carrito" id="contadorCarrito">0</span>
        </div>

    </div>

    <!-- MODAL CARRITO (COMPROBANTE PREVIO) -->
    <div class="modal-overlay" id="modalCarrito">
        <div class="modal-carrito">
            <div class="modal-header">
                <h2>Tu Pedido</h2>
                <button class="btn-cerrar" onclick="cerrarCarrito()">&times;</button>
            </div>
            
            <div class="carrito-items" id="listaCarrito">
                <!-- Aquí se inyectan los productos con JS -->
            </div>

            <div class="resumen-carrito" id="resumenCarrito">
                <div class="fila-resumen total">
                    <span>Total a Pagar:</span>
                    <span id="totalCarrito">S/ 0.00</span>
                </div>
                
                <!-- Campo opcional para nombre si no está logueado -->
                <?php if (empty($nombre_cliente)): ?>
                    <input type="text" id="nombreClienteInput" placeholder="Tu Nombre Completo" style="width:100%; padding:10px; margin-top:10px; border:1px solid #ddd; border-radius:5px;">
                <?php else: ?>
                    <input type="hidden" id="nombreClienteInput" value="<?= htmlspecialchars($nombre_cliente) ?>">
                    <p style="margin-top:10px; font-size:0.9rem; color:#666;">Cliente: <strong><?= htmlspecialchars($nombre_cliente) ?></strong></p>
                <?php endif; ?>

                <button class="btn-whatsapp" onclick="enviarPedidoWhatsApp()">
                    <i data-lucide="message-circle"></i> Enviar Pedido por WhatsApp
                </button>
                <p style="font-size: 0.8rem; color: #888; text-align: center; margin-top: 10px;">
                    Al enviar, recibirás las instrucciones de pago y entrega.
                </p>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        lucide.createIcons();

        // --- LÓGICA DEL CARRITO DE COMPRAS ---
        let carrito = [];

        // 1. Agregar Producto
        function agregarAlCarrito(id, nombre, precio, stockMax) {
            const existe = carrito.find(item => item.id === id);
            if (existe) {
                if (existe.cantidad < stockMax) {
                    existe.cantidad++;
                } else {
                    alert("¡Has alcanzado el stock máximo disponible!");
                    return;
                }
            } else {
                carrito.push({ id, nombre, precio, cantidad: 1, stockMax });
            }
            actualizarInterfaz();
            // Efecto visual simple
            const btn = event.target;
            const textoOriginal = btn.innerHTML;
            btn.innerHTML = "¡Añadido!";
            setTimeout(() => btn.innerHTML = textoOriginal, 1000);
        }

        // 2. Actualizar UI (Contador y Modal)
        function actualizarInterfaz() {
            // Actualizar contador flotante
            const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
            document.getElementById('contadorCarrito').textContent = totalItems;

            // Actualizar lista en el modal
            const lista = document.getElementById('listaCarrito');
            lista.innerHTML = '';
            let totalPrecio = 0;

            if (carrito.length === 0) {
                lista.innerHTML = '<div class="carrito-vacio"><i data-lucide="shopping-bag" style="width:48px; height:48px; margin-bottom:10px;"></i><p>Tu carrito está vacío</p></div>';
                document.getElementById('resumenCarrito').style.display = 'none';
                lucide.createIcons();
                return;
            }

            document.getElementById('resumenCarrito').style.display = 'block';

            carrito.forEach((item, index) => {
                const subtotal = item.precio * item.cantidad;
                totalPrecio += subtotal;

                const div = document.createElement('div');
                div.className = 'item-carrito';
                div.innerHTML = `
                    <div class="item-info">
                        <h4>${item.nombre}</h4>
                        <span>S/ ${item.precio.toFixed(2)} x ${item.cantidad} unid.</span>
                    </div>
                    <div class="item-controles">
                        <span style="font-weight:bold;">S/ ${subtotal.toFixed(2)}</span>
                        <button class="btn-eliminar" onclick="eliminarItem(${index})">
                            <i data-lucide="trash-2" style="width:18px;"></i>
                        </button>
                    </div>
                `;
                lista.appendChild(div);
            });

            document.getElementById('totalCarrito').textContent = 'S/ ' + totalPrecio.toFixed(2);
            lucide.createIcons();
        }

        // 3. Eliminar Item
        function eliminarItem(index) {
            carrito.splice(index, 1);
            actualizarInterfaz();
        }

        // 4. Filtros de Categoría
        function filtrarProductos(catId) {
            const tarjetas = document.querySelectorAll('.producto-tarjeta');
            tarjetas.forEach(card => {
                if (catId === 'all' || card.dataset.categoria == catId) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // 5. Modal Controls
        function abrirCarrito() {
            document.getElementById('modalCarrito').classList.add('activo');
            actualizarInterfaz(); // Asegura que se renderice al abrir
        }
        function cerrarCarrito() {
            document.getElementById('modalCarrito').classList.remove('activo');
        }

        // 6. ENVIAR A WHATSAPP (Generador de Comprobante)
        function enviarPedidoWhatsApp() {
            if (carrito.length === 0) return;

            const nombreCliente = document.getElementById('nombreClienteInput').value;
            if (!nombreCliente.trim()) {
                alert("Por favor, ingresa tu nombre para el pedido.");
                return;
            }

            // Número del Administrador (Cámbialo por el real)
            const telefonoAdmin = "51956369001"; 

            let mensaje = `Hola, soy *${nombreCliente}*. Quisiera realizar el siguiente pedido:%0A%0A`;
            let total = 0;

            carrito.forEach(item => {
                const sub = item.precio * item.cantidad;
                total += sub;
                mensaje += `▪️ ${item.nombre} (x${item.cantidad}) - S/ ${sub.toFixed(2)}%0A`;
            });

            mensaje += `%0A*TOTAL A PAGAR: S/ ${total.toFixed(2)}*%0A`;
            mensaje += `%0A¿Podría brindarme los datos para realizar el pago y confirmar mi compra?`;

            const url = `https://wa.me/${telefonoAdmin}?text=${mensaje}`;
            window.open(url, '_blank');
        }
    </script>
</body>
</html>