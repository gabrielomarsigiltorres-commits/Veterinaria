<?php
session_start();
// Seguridad
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Ruta al modelo
require '../Modelo/conexion.php'; 

// Variables por defecto para 'Crear'
$modo_edicion = false;
$id_producto = null;
$producto = [
    'nombre' => '',
    'descripcion' => '',
    'precio' => '',
    'stock' => '',
    'id_categoria' => '', 
    'imagen_url' => 'default_product.png'
];
$titulo_pagina = "Añadir Nuevo Producto";

// --- Cargar Categorías (usando PDO) ---
try {
    $stmt_categorias = $conexion->prepare("SELECT * FROM categorias ORDER BY nombre_categoria ASC");
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar categorías: " . $e->getMessage());
}

// --- Verificar si estamos en modo EDICIÓN ---
if (isset($_GET['id'])) {
    $id_producto = (int) $_GET['id'];
    $modo_edicion = true;
    $titulo_pagina = "Editar Producto";

    try {
        $stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            header("Location: admin_productos.php?status=error&msg=Producto no encontrado");
            exit;
        }
    } catch (PDOException $e) {
        die("Error al cargar producto: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($titulo_pagina); ?></title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="css/dashboard_admin.css">
</head>
<body>
  <div class="contenedor">
    
    <?php include 'partials/admin_sidebar.php'; ?>

    <main class="contenido-principal">
      <header class="cabecera-principal">
        <h1><?= htmlspecialchars($titulo_pagina); ?></h1>
        <div class="info-usuario">
          <img src="../img/logo.jpg" alt="Admin" class="avatar">
          <div>
            <p class="nombre-usuario"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin'); ?></p>
            <p class="rol-usuario">Administrador</p>
          </div>
        </div>
      </header>

      <section class="contenido-pagina">
        <div class="tarjeta-tabla">
          <div class="tarjeta-cabecera">
            <h2>Detalles del Producto</h2>
          </div>

          <form action="../Controller/admin_producto_controlador.php" 
                method="POST" 
                enctype="multipart/form-data" 
                class="formulario-crud">

            <?php if ($modo_edicion): ?>
              <input type="hidden" name="id_producto" value="<?= htmlspecialchars($id_producto); ?>">
              <input type="hidden" name="accion" value="actualizar">
            <?php else: ?>
              <input type="hidden" name="accion" value="crear">
            <?php endif; ?>

            <div class="grupo-formulario">
              <label for="nombre">Nombre del Producto</label>
              <input type="text" id="nombre" name="nombre" 
                     value="<?= htmlspecialchars($producto['nombre']); ?>" required>
            </div>

            <div class="grupo-formulario">
              <label for="descripcion">Descripción</label>
              <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <div class="grupo-formulario-doble">
              <div class="campo-corto">
                <label for="precio">Precio (S/)</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" 
                       value="<?= htmlspecialchars($producto['precio']); ?>" required>
              </div>
              <div class="campo-corto">
                <label for="stock">Stock (Unidades)</label>
                <input type="number" id="stock" name="stock" min="0" 
                       value="<?= htmlspecialchars($producto['stock']); ?>" required>
              </div>
            </div>

            <div class="grupo-formulario">
              <label for="categoria">Categoría</label>
              <select id="categoria" name="id_categoria" required>
                <option value="">-- Seleccionar categoría --</option>
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?= $cat['id_categoria']; ?>" 
                          <?= ($producto['id_categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($cat['nombre_categoria']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="grupo-formulario">
              <label for="imagen">Imagen del Producto (Opcional)</label>
              <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png, image/webp">
              
              <?php if ($modo_edicion && $producto['imagen_url'] && $producto['imagen_url'] != 'default_product.png'): ?>
                <p class="info-imagen-actual">
                  Imagen actual: 
                  <img src="uploads/<?= htmlspecialchars($producto['imagen_url']); ?>" alt="Imagen actual" width="50">
                  <span><?= htmlspecialchars($producto['imagen_url']); ?></span>
                  <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($producto['imagen_url']); ?>">
                </p>
              <?php endif; ?>
            </div>

            <div class="acciones-formulario">
              <a href="admin_productos.php" class="boton-accion cancelar">Cancelar</a>
              <button type="submit" class="boton-nuevo">
                <i data-lucide="save"></i>
                <?= $modo_edicion ? 'Actualizar Producto' : 'Guardar Producto'; ?>
              </button>
            </div>
          </form>
        </div>
      </section>
    </main>
  </div>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
