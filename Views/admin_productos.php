<?php
session_start();
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    header("Location: login.php");
    exit;
}

require '../Modelo/conexion.php'; 

// --- LÓGICA DE OBTENCIÓN DE DATOS ---

// 1. Obtener Productos
$stmtProd = $conexion->prepare("SELECT * FROM productos ORDER BY id_producto DESC");
$stmtProd->execute();
$productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

// 2. Obtener Clientes
$stmtClientes = $conexion->prepare("SELECT id, nombres_completos, correo_electronico FROM usuarios WHERE tipo_usuario = 'cliente' ORDER BY nombres_completos ASC");
$stmtClientes->execute();
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

// 3. Obtener Historial de Ventas
$sqlVentas = "SELECT v.id_venta, v.fecha_venta, v.cantidad, v.total_venta, v.tipo_venta,
                     p.nombre AS nombre_producto, 
                     u.nombres_completos AS nombre_cliente
              FROM ventas v
              LEFT JOIN productos p ON v.id_producto = p.id_producto
              LEFT JOIN usuarios u ON v.id_usuario_cliente = u.id
              ORDER BY v.fecha_venta DESC";
$stmtVentas = $conexion->prepare($sqlVentas);
$stmtVentas->execute();
$historialVentas = $stmtVentas->fetchAll(PDO::FETCH_ASSOC);

// Mensajes
$mensaje = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'venta_ok') $mensaje = '<div class="alerta exito">✅ Venta registrada correctamente.</div>';
    if ($_GET['msg'] == 'error') $mensaje = '<div class="alerta error">❌ Error: ' . htmlspecialchars($_GET['info'] ?? 'Desconocido') . '</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos y Ventas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="css/dashboard_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        .alerta { padding: 1rem; margin-bottom: 1rem; border-radius: 0.5rem; font-weight: 500; }
        .alerta.exito { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alerta.error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        /* Modal Estilos */
        .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 50; align-items: center; justify-content: center; backdrop-filter: blur(2px); }
        .modal-backdrop.open { display: flex; }
        
        /* Tabs */
        .tab-btn { border-bottom: 2px solid transparent; color: #6b7280; }
        .tab-btn.active { border-bottom: 2px solid #00A79D; color: #00A79D; font-weight: bold; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Estilos Tarjeta Crédito */
        .input-card { background-image: url('https://img.icons8.com/ios/452/credit-card-front.png'); background-position: 10px center; background-repeat: no-repeat; background-size: 20px; padding-left: 40px; }
    </style>
</head>
<body class="font-display bg-gray-50 text-gray-800">
    <div class="contenedor flex min-h-screen">
        <?php include 'partials/admin_sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-10">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Tienda</h1>
                    <p class="text-gray-500">Administra productos y registra ventas presenciales.</p>
                </div>
                
                <button onclick="abrirModalVenta()" class="flex items-center justify-center gap-2 bg-[#00A79D] hover:bg-[#008f85] text-white px-6 py-3 rounded-lg font-bold shadow-lg transition-all transform hover:scale-105">
                    <i data-lucide="shopping-cart"></i>
                    Registrar Nueva Venta
                </button>
            </header>

            <?= $mensaje ?>

            <div class="mb-6 border-b border-gray-200">
                <div class="flex gap-8">
                    <button onclick="cambiarTab('inventario')" id="tab-inventario" class="tab-btn active pb-3 px-2 text-lg">
                        📦 Inventario de Productos
                    </button>
                    <button onclick="cambiarTab('ventas')" id="tab-ventas" class="tab-btn pb-3 px-2 text-lg">
                        💰 Historial de Ventas
                    </button>
                </div>
            </div>

            <div id="content-inventario" class="tab-content active">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($productos as $prod): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <?php 
                                            $imgUrl = !empty($prod['imagen_url']) ? 'uploads/'.$prod['imagen_url'] : '';
                                            if (!empty($imgUrl) && file_exists('uploads/'.$prod['imagen_url'])): 
                                        ?>
                                            <img src="<?= htmlspecialchars($imgUrl) ?>" class="h-10 w-10 rounded-full object-cover border">
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500"><i data-lucide="package"></i></div>
                                        <?php endif; ?>
                                        <div class="font-medium text-gray-900"><?= htmlspecialchars($prod['nombre']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">S/ <?= number_format($prod['precio'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold <?= $prod['stock'] < 5 ? 'text-red-600' : 'text-gray-700' ?>">
                                        <?= $prod['stock'] ?> un.
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($prod['stock'] > 0): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Disponible</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Agotado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="admin_producto_form.php?id=<?= $prod['id_producto'] ?>" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                                    <a href="../Controller/admin_producto_controlador.php?accion=eliminar&id=<?= $prod['id_producto'] ?>" 
                                       onclick="return confirm('¿Eliminar producto?')" 
                                       class="text-red-600 hover:text-red-900 font-medium ml-3">Eliminar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="admin_producto_form.php" class="inline-flex items-center gap-2 text-sm font-bold text-[#00A79D] hover:underline">
                        <i data-lucide="plus"></i> Agregar Nuevo Producto
                    </a>
                </div>
            </div>

            <div id="content-ventas" class="tab-content">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <?php if(empty($historialVentas)): ?>
                        <div class="p-10 text-center text-gray-500">
                            <i data-lucide="shopping-bag" class="mx-auto h-12 w-12 text-gray-300 mb-3"></i>
                            <p>No hay ventas registradas todavía.</p>
                        </div>
                    <?php else: ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tipo Venta</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($historialVentas as $venta): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= !empty($venta['nombre_cliente']) ? htmlspecialchars($venta['nombre_cliente']) : '<span class="text-gray-400 italic">Cliente General</span>' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= htmlspecialchars($venta['nombre_producto']) ?> 
                                    <span class="text-xs text-gray-400 ml-1">(x<?= $venta['cantidad'] ?>)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="store" class="h-4 w-4 text-green-500"></i>
                                        <?= htmlspecialchars(ucfirst($venta['tipo_venta'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-[#00A79D]">
                                    S/ <?= number_format($venta['total_venta'], 2) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>

    <div id="modalVenta" class="modal-backdrop overflow-y-auto">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-3xl transform transition-all my-10 p-0 overflow-hidden">
            
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="shopping-cart" class="text-[#00A79D]"></i> Nueva Venta
                </h3>
                <button onclick="cerrarModalVenta()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <form action="../Controller/admin_venta_controlador.php" method="POST" class="p-6">
                <input type="hidden" name="accion" value="registrar_venta">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="col-span-2">
                        <h4 class="text-sm font-bold text-gray-500 uppercase mb-3 border-b pb-1">Detalles de la Compra</h4>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Cliente (Opcional)</label>
                        <select name="id_usuario_cliente" class="w-full rounded-lg border-gray-300 focus:border-[#00A79D] focus:ring focus:ring-[#00A79D]/20">
                            <option value="">-- Cliente de Mostrador --</option>
                            <?php foreach ($clientes as $cli): ?>
                                <option value="<?= $cli['id'] ?>">
                                    <?= htmlspecialchars($cli['nombres_completos']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Producto</label>
                        <select id="selectProducto" name="id_producto" required onchange="calcularTotal()" class="w-full rounded-lg border-gray-300 focus:border-[#00A79D] focus:ring focus:ring-[#00A79D]/20">
                            <option value="" data-precio="0">-- Seleccionar --</option>
                            <?php foreach ($productos as $prod): ?>
                                <?php if($prod['stock'] > 0): ?>
                                <option value="<?= $prod['id_producto'] ?>" data-precio="<?= $prod['precio'] ?>" data-stock="<?= $prod['stock'] ?>">
                                    <?= htmlspecialchars($prod['nombre']) ?> - S/ <?= $prod['precio'] ?>
                                </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <p id="stockInfo" class="text-xs text-gray-500 mt-1">Stock disponible: -</p>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Cantidad</label>
                        <input type="number" id="inputCantidad" name="cantidad" value="1" min="1" required oninput="calcularTotal()" class="w-full rounded-lg border-gray-300 focus:border-[#00A79D] focus:ring focus:ring-[#00A79D]/20">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Total a Pagar</label>
                        <div class="w-full bg-gray-100 rounded-lg py-2 px-3 text-right">
                            <span id="displayTotal" class="text-xl font-black text-[#00A79D]">S/ 0.00</span>
                        </div>
                    </div>

                    <div class="col-span-2 mt-4">
                        <h4 class="text-sm font-bold text-gray-500 uppercase mb-3 border-b pb-1">Método de Pago</h4>
                    </div>

                    <div class="col-span-2">
                        <div class="grid grid-cols-3 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="metodo_pago" value="efectivo" class="peer sr-only" checked onchange="cambiarMetodoPago('efectivo')">
                                <div class="rounded-lg border-2 border-gray-200 p-4 hover:bg-gray-50 peer-checked:border-[#00A79D] peer-checked:bg-[#00A79D]/5 peer-checked:text-[#00A79D] transition-all text-center">
                                    <i data-lucide="banknote" class="mx-auto h-6 w-6 mb-2"></i>
                                    <span class="block text-sm font-bold">Efectivo</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="metodo_pago" value="yape" class="peer sr-only" onchange="cambiarMetodoPago('yape')">
                                <div class="rounded-lg border-2 border-gray-200 p-4 hover:bg-gray-50 peer-checked:border-[#732283] peer-checked:bg-[#732283]/5 peer-checked:text-[#732283] transition-all text-center">
                                    <i data-lucide="qr-code" class="mx-auto h-6 w-6 mb-2"></i>
                                    <span class="block text-sm font-bold">Yape</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="metodo_pago" value="tarjeta" class="peer sr-only" onchange="cambiarMetodoPago('tarjeta')">
                                <div class="rounded-lg border-2 border-gray-200 p-4 hover:bg-gray-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600 transition-all text-center">
                                    <i data-lucide="credit-card" class="mx-auto h-6 w-6 mb-2"></i>
                                    <span class="block text-sm font-bold">Tarjeta</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200" id="contenedor-pago">
                        
                        <div id="seccion-efectivo" class="text-center py-4">
                            <i data-lucide="check-circle" class="mx-auto h-10 w-10 text-green-500 mb-2"></i>
                            <p class="text-gray-700 font-medium">Pago en efectivo presencial.</p>
                            <p class="text-sm text-gray-500">Haz clic en "Confirmar Compra" una vez recibido el dinero.</p>
                        </div>

                        <div id="seccion-yape" class="hidden">
                            <div class="flex flex-col md:flex-row items-center gap-6">
                                <div class="w-40 h-40 bg-white p-2 rounded-lg border shadow-sm flex items-center justify-center">
                                    <img src="../img/QR YAPE.jpg" alt="QR Yape" class="w-full h-full object-contain"> 
                                    </div>
                                <div class="flex-1 w-full">
                                    <h5 class="font-bold text-[#732283] mb-2">Escanea para pagar</h5>
                                    <p class="text-sm text-gray-600 mb-4">A nombre de: <strong>Veterinaria del Norte</strong></p>
                                    
                                    <label class="block text-sm font-bold text-gray-700 mb-1">N° Operación / Referencia</label>
                                    <input type="text" name="ref_yape" placeholder="Ej: 1234567" class="w-full rounded-lg border-gray-300 focus:border-[#732283] focus:ring focus:ring-[#732283]/20">
                                </div>
                            </div>
                        </div>

                        <div id="seccion-tarjeta" class="hidden">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Número de Tarjeta</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="credit-card" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <input type="text" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500/20 font-mono">
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Titular de la Tarjeta</label>
                                    <input type="text" name="card_holder" placeholder="COMO APARECE EN LA TARJETA" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500/20 uppercase">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Vencimiento</label>
                                    <input type="text" name="card_expiry" placeholder="MM/AA" maxlength="5" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500/20 text-center">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">CVV</label>
                                    <input type="password" name="card_cvv" placeholder="123" maxlength="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500/20 text-center">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="cerrarModalVenta()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-6 py-2.5 bg-[#00A79D] text-white font-bold rounded-lg shadow hover:bg-[#008f85] transition-colors flex items-center gap-2">
                        Confirmar Compra <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // 1. Pestañas
        function cambiarTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById('content-' + tabName).classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        }

        // 2. Modal
        const modal = document.getElementById('modalVenta');
        function abrirModalVenta() { modal.classList.add('open'); }
        function cerrarModalVenta() { modal.classList.remove('open'); }
        modal.addEventListener('click', (e) => { if (e.target === modal) cerrarModalVenta(); });

        // 3. Calculadora de Total
        function calcularTotal() {
            const select = document.getElementById('selectProducto');
            const inputCant = document.getElementById('inputCantidad');
            const display = document.getElementById('displayTotal');
            const stockInfo = document.getElementById('stockInfo');

            const precioUnitario = parseFloat(select.options[select.selectedIndex].dataset.precio) || 0;
            const stockDisponible = parseInt(select.options[select.selectedIndex].dataset.stock) || 0;
            const cantidad = parseInt(inputCant.value) || 0;

            if(select.value !== "") {
                stockInfo.innerText = `Stock disponible: ${stockDisponible} unidades`;
                if(cantidad > stockDisponible) {
                    stockInfo.classList.add('text-red-600');
                    stockInfo.innerText += " (Insuficiente)";
                } else {
                    stockInfo.classList.remove('text-red-600');
                }
            } else {
                stockInfo.innerText = "Stock disponible: -";
            }

            const total = precioUnitario * cantidad;
            display.innerText = "S/ " + total.toFixed(2);
        }

        // 4. Lógica de Métodos de Pago (Mostrar/Ocultar secciones)
        function cambiarMetodoPago(metodo) {
            // Ocultar todas las secciones
            document.getElementById('seccion-efectivo').classList.add('hidden');
            document.getElementById('seccion-yape').classList.add('hidden');
            document.getElementById('seccion-tarjeta').classList.add('hidden');

            // Mostrar la seleccionada
            document.getElementById('seccion-' + metodo).classList.remove('hidden');
        }
    </script>
</body>
</html>