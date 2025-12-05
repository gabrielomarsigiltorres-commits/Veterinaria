<?php
session_start();
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    header("Location: login.php");
    exit;
}

require '../Modelo/conexion.php';

// Obtener Servicios
$sql = "SELECT * FROM servicios ORDER BY id DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensajes
$mensaje = '';
if (isset($_GET['msg'])) {
    $m = $_GET['msg'];
    if ($m == 'creado') $mensaje = '<div class="alerta exito">✅ Servicio creado correctamente.</div>';
    if ($m == 'actualizado') $mensaje = '<div class="alerta exito">✅ Servicio actualizado.</div>';
    if ($m == 'eliminado') $mensaje = '<div class="alerta error">🗑 Servicio eliminado.</div>';
    if ($m == 'error') $mensaje = '<div class="alerta error">❌ Ocurrió un error.</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Servicios - Admin</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="css/dashboard_admin.css">
  <style>
      /* Estilos específicos para esta vista */
      .modal-backdrop { display: none; position: fixed; inset:0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
      .modal-backdrop.open { display: flex; }
      .modal-contenido { background: white; padding: 2rem; border-radius: 10px; width: 90%; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto; }
      
      .form-group { margin-bottom: 1rem; }
      .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; }
      .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; }
      
      .boton-nuevo { background-color: #00A79D; color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
      .boton-nuevo:hover { background-color: #008f85; }
      
      .img-preview { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 1px solid #ddd; }
  </style>
</head>
<body>
  <div class="contenedor">
    <?php include 'partials/admin_sidebar.php'; ?>

    <main class="contenido-principal">
      <header class="cabecera-principal">
        <h1>Gestión de Servicios</h1>
        <div class="info-usuario">
            <img src="../img/logo.jpg" alt="Admin" class="avatar">
            <div>
                <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin'); ?></p>
                <p class="rol-usuario">Administrador</p>
            </div>
        </div>
      </header>

      <section class="contenido-pagina">
        <?= $mensaje ?>

        <div class="tarjeta-tabla">
          <div class="tarjeta-cabecera" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Lista de Servicios</h2>
            <button onclick="abrirModalCrear()" class="boton-nuevo">
                <i data-lucide="plus-circle"></i> Nuevo Servicio
            </button>
          </div>
          
          <div class="contenedor-tabla">
            <table>
              <thead>
                <tr>
                  <th>Imagen</th> <th>Nombre</th>
                  <th>Categoría</th>
                  <th>Precio</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($servicios)): ?>
                    <tr><td colspan="6" class="text-center p-4">No hay servicios registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($servicios as $srv): ?>
                    <tr>
                        <td>
                            <?php 
                                $rutaImg = 'uploads/' . ($srv['imagen_url'] ?? 'default_service.png');
                                if (!file_exists($rutaImg)) $rutaImg = '../img/logo.jpg'; // Fallback
                            ?>
                            <img src="<?= $rutaImg ?>" alt="Foto" class="img-preview">
                        </td>
                        <td><strong><?= htmlspecialchars($srv['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($srv['categoria']) ?></td>
                        <td>S/ <?= number_format($srv['precio'], 2) ?></td>
                        <td>
                            <span class="estado <?= strtolower($srv['estado']) == 'activo' ? 'completado' : 'cancelado' ?>">
                                <?= htmlspecialchars($srv['estado']) ?>
                            </span>
                        </td>
                        <td class="acciones">
                            <button onclick='abrirModalEditar(<?= json_encode($srv) ?>)' class="boton-accion" style="color: #3b82f6; border-color: #3b82f6;">
                                <i data-lucide="edit"></i>
                            </button>
                            <a href="../Controller/ServicioController.php?accion=eliminar&id=<?= $srv['id'] ?>" 
                               class="boton-accion eliminar" onclick="return confirm('¿Eliminar este servicio?')" style="color: #ef4444; border-color: #ef4444;">
                               <i data-lucide="trash-2"></i>
                            </a>
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

  <div id="modalServicio" class="modal-backdrop">
      <div class="modal-contenido">
          <h2 id="modalTitulo" style="margin-bottom: 1.5rem; color: #00A79D; font-size: 1.5rem; font-weight: bold;">
              Nuevo Servicio
          </h2>
          
          <form id="formServicio" action="../Controller/ServicioController.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="accion" id="accion" value="crear">
              <input type="hidden" name="id" id="id_servicio">

              <div class="form-group">
                  <label>Nombre del Servicio</label>
                  <input type="text" name="nombre" id="nombre" required>
              </div>

              <div class="form-group">
                  <label>Imagen del Servicio</label>
                  <input type="file" name="imagen" id="imagen" accept="image/*">
                  <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">Deja vacío para mantener la actual (al editar).</p>
              </div>

              <div class="form-group">
                  <label>Categoría</label>
                  <select name="categoria" id="categoria" required>
                      <option value="Hospitalización">Hospitalización</option>
                      <option value="Anestesia Inhalatoria">Anestesia Inhalatoria</option>
                      <option value="Medicina Regenerativa">Medicina Regenerativa</option>
                      <option value="Medicina Preventiva">Medicina Preventiva</option>
                      <option value="Imagenología Ecografía">Imagenología Ecografía</option>
                      <option value="Imagenología RX">Imagenología RX</option>
                      <option value="Cirugías">Cirugías</option>
                      <option value="Spa Canino">Spa Canino</option>
                  </select>
              </div>

              <div class="form-group">
                  <label>Precio (S/)</label>
                  <input type="number" step="0.01" name="precio" id="precio" required>
              </div>

              <div class="form-group">
                  <label>Descripción</label>
                  <textarea name="descripcion" id="descripcion" rows="3"></textarea>
              </div>

              <div class="form-group" id="groupEstado" style="display:none;">
                  <label>Estado</label>
                  <select name="estado" id="estado">
                      <option value="Activo">Activo</option>
                      <option value="Inactivo">Inactivo</option>
                  </select>
              </div>

              <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                  <button type="button" onclick="cerrarModal()" style="padding: 0.5rem 1rem; background: white; border: 1px solid #ddd; border-radius: 0.5rem; cursor: pointer;">Cancelar</button>
                  <button type="submit" style="padding: 0.5rem 1rem; background: #00A79D; color: white; border: none; border-radius: 0.5rem; font-weight: bold; cursor: pointer;">Guardar</button>
              </div>
          </form>
      </div>
  </div>

  <script>
    lucide.createIcons();
    const modal = document.getElementById('modalServicio');
    const form = document.getElementById('formServicio');
    const titulo = document.getElementById('modalTitulo');
    
    // Abrir para Crear
    function abrirModalCrear() {
        form.reset();
        document.getElementById('accion').value = 'crear';
        document.getElementById('id_servicio').value = '';
        document.getElementById('groupEstado').style.display = 'none'; // Al crear siempre es activo por defecto
        titulo.innerText = 'Nuevo Servicio';
        modal.classList.add('open');
    }

    // Abrir para Editar
    function abrirModalEditar(data) {
        form.reset();
        document.getElementById('accion').value = 'editar';
        document.getElementById('id_servicio').value = data.id;
        
        document.getElementById('nombre').value = data.nombre;
        document.getElementById('categoria').value = data.categoria;
        document.getElementById('precio').value = data.precio;
        document.getElementById('descripcion').value = data.descripcion;
        document.getElementById('estado').value = data.estado;
        
        document.getElementById('groupEstado').style.display = 'block'; // Mostrar selector estado
        titulo.innerText = 'Editar Servicio';
        modal.classList.add('open');
    }

    function cerrarModal() {
        modal.classList.remove('open');
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) cerrarModal();
    });
  </script>
</body>
</html>