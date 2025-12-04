<?php
session_start();
// Seguridad: Verificar si el usuario es administrador
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}
// Ruta al modelo (usa PDO)
require '../Modelo/conexion.php';

try {
    // --- Cargar Categorías ---
    $stmt_categorias = $conexion->prepare("SELECT * FROM categorias ORDER BY nombre_categoria ASC");
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

    // --- Cargar Productos (con JOIN) ---
    $stmt_productos = $conexion->prepare("
        SELECT p.*, c.nombre_categoria 
        FROM productos p 
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        ORDER BY p.nombre ASC
    ");
    $stmt_productos->execute();
    $productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

// --- Lógica para Mensajes de Feedback ---
$mensaje = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';

    switch ($status) {
        case 'creado':
            $mensaje = '<div class="alerta exito">Producto añadido exitosamente.</div>';
            break;
        case 'actualizado':
            $mensaje = '<div class="alerta exito">Producto actualizado exitosamente.</div>';
            break;
        case 'eliminado':
            $mensaje = '<div class="alerta exito">Producto eliminado exitosamente.</div>';
            break;
        case 'venta_ok':
            $mensaje = '<div class="alerta exito">Venta registrada y stock actualizado.</div>';
            break;
        case 'no_stock':
            $mensaje = '<div class="alerta error">Error: No hay suficiente stock (' . $msg . ')</div>';
            break;
        case 'error':
            $mensaje = '<div class="alerta error">Ocurrió un error: ' . $msg . '</div>';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gestión de Productos - Admin</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="css/dashboard_admin.css">
</head>
<body>
  <div class="contenedor">

    <?php include 'partials/admin_sidebar.php'; ?>

    <main class="contenido-principal">
      <header class="cabecera-principal">
        <h1>Gestión de Productos</h1>
        <div class="info-usuario">
          <img src="../img/logo.jpg" alt="Admin" class="avatar">
          <div>
            <p class="nombre-usuario"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin'); ?></p>
            <p class="rol-usuario">Administrador</p>
          </div>
        </div>
      </header>

      <section class="contenido-pagina">
        <?= $mensaje; ?>

        <!-- 1. Punto de Venta Rápido -->
        <div class="tarjeta-tabla" style="margin-bottom: 24px;">
          <div class="tarjeta-cabecera">
            <h2>Punto de Venta Rápido (Local)</h2>
          </div>
          <form action="../Controller/admin_venta_controlador.php" method="POST" class="formulario-venta-rapida">
            <input type="hidden" name="accion" value="venta_rapida">
            
            <div class="campo-grupo">
              <label for="producto_id_venta">Producto:</label>
              <select name="producto_id" id="producto_id_venta" required>
                <option value="">Seleccione un producto...</option>
                <?php foreach ($productos as $prod): ?>
                  <option value="<?= htmlspecialchars($prod['id_producto']) ?>">
                    <?= htmlspecialchars($prod['nombre']) ?> (Stock: <?= htmlspecialchars($prod['stock']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="campo-grupo">
              <label for="cantidad_venta">Cantidad:</label>
              <input type="number" name="cantidad" id="cantidad_venta" value="1" min="1" required>
            </div>

            <button type="submit" class="boton-nuevo">
              <i data-lucide="shopping-cart"></i> Registrar Venta
            </button>
          </form>
        </div>

        <!-- 2. Tabla de Productos -->
        <div class="tarjeta-tabla">
          <div class="tarjeta-cabecera">
            <h2>Inventario de Productos</h2>
            <a href="admin_producto_form.php" class="boton-nuevo">
              <i data-lucide="plus"></i> Añadir Producto
            </a>
          </div>

          <div class="contenedor-tabla">
            <table>
              <thead>
                <tr>
                  <th>Imagen</th>
                  <th>Nombre</th>
                  <th>Descripción</th>
                  <th>Categoría</th>
                  <th>Precio</th>
                  <th>Stock</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($productos)): ?>
                  <tr><td colspan="7">No hay productos registrados. <a href="admin_producto_form.php">Añadir uno</a>.</td></tr>
                <?php else: ?>
                  <?php foreach ($productos as $producto): ?>
                  <tr>
                    <td>
                      <img src="uploads/<?= htmlspecialchars($producto['imagen_url'] ?? 'default_product.png'); ?>" 
                           alt="<?= htmlspecialchars($producto['nombre']) ?>" 
                           class="imagen-tabla">
                    </td>
                    <td><strong><?= htmlspecialchars($producto['nombre']) ?></strong></td>
                    <td class="descripcion-corta">
                      <?= htmlspecialchars(substr($producto['descripcion'], 0, 70)) . (strlen($producto['descripcion']) > 70 ? '...' : '') ?>
                    </td>
                    <td><?= htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría') ?></td>
                    <td>S/ <?= number_format($producto['precio'], 2) ?></td>
                    <td>
                      <?php 
                        $stock_clase = 'completado';
                        if ($producto['stock'] <= 10 && $producto['stock'] > 0) $stock_clase = 'pendiente';
                        if ($producto['stock'] == 0) $stock_clase = 'cancelado';
                      ?>
                      <span class="estado <?= $stock_clase; ?>">
                        <?= htmlspecialchars($producto['stock']); ?>
                        <?= ($producto['stock'] == 1) ? 'unidad' : 'unidades'; ?>
                      </span>
                    </td>
                    <td class="acciones">
                      <a href="admin_producto_form.php?id=<?= $producto['id_producto']; ?>" class="boton-accion editar">Editar</a>
                      <a href="../Controller/admin_producto_controlador.php?accion=eliminar&id=<?= $producto['id_producto']; ?>" 
                         class="boton-accion eliminar" 
                         onclick="return confirm('¿Seguro que deseas eliminar este producto?')">Eliminar</a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
