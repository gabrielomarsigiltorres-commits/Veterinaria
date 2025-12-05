<?php
session_start();

// 1. Seguridad
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    header("Location: login.php");
    exit;
}

require '../Modelo/conexion.php'; 

// 2. Obtener DATOS PARA EL MODAL (Listas desplegables)
try {
    // A) Lista de Mascotas con sus Dueños (Para el Select)
    // Concatenamos nombre mascota + nombre dueño para facilitar la búsqueda visual
    $sqlMascotas = "SELECT m.id, m.nombre AS nombre_mascota, u.nombres_completos AS nombre_dueno 
                    FROM mascotas_cliente m 
                    JOIN usuarios u ON m.id_usuario = u.id 
                    ORDER BY u.nombres_completos ASC";
    $stmtM = $conexion->prepare($sqlMascotas);
    $stmtM->execute();
    $listaMascotas = $stmtM->fetchAll(PDO::FETCH_ASSOC);

    // B) Lista de Servicios (Para el Select)
    $sqlServicios = "SELECT nombre FROM servicios WHERE estado = 'Activo' ORDER BY nombre ASC";
    $stmtS = $conexion->prepare($sqlServicios);
    $stmtS->execute();
    $listaServicios = $stmtS->fetchAll(PDO::FETCH_ASSOC);

    // C) Lista de Citas (Tabla principal)
    $sqlCitas = "SELECT c.id, c.fecha_cita, c.hora_cita, c.servicio, c.estado, c.motivo,
                   m.nombre AS nombre_mascota, 
                   u.nombres_completos AS nombre_dueno, u.correo_electronico
            FROM citas c
            LEFT JOIN mascotas_cliente m ON c.id_mascota = m.id
            LEFT JOIN usuarios u ON c.id_usuario = u.id
            ORDER BY c.fecha_cita DESC, c.hora_cita ASC";
    $stmt = $conexion->prepare($sqlCitas);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}

// 3. Mensajes
$mensaje = '';
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    if ($msg == 'creada') $mensaje = '<div class="alerta exito">✅ Nueva cita programada con éxito.</div>';
    if ($msg == 'atendida') $mensaje = '<div class="alerta exito">✅ Cita marcada como ATENDIDA.</div>';
    if ($msg == 'cancelada') $mensaje = '<div class="alerta error">⚠ Cita CANCELADA.</div>';
    if ($msg == 'eliminada') $mensaje = '<div class="alerta error">🗑 Registro eliminado.</div>';
    if ($msg == 'error') $mensaje = '<div class="alerta error">❌ Error en la solicitud.</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Citas - Admin</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="css/dashboard_admin.css">
  <style>
      /* Estilos para el botón y modal */
      .boton-nuevo {
          background-color: #00A79D;
          color: white;
          padding: 8px 16px;
          border-radius: 6px;
          text-decoration: none;
          font-weight: bold;
          display: inline-flex;
          align-items: center;
          gap: 8px;
          font-size: 0.9rem;
          transition: background 0.3s;
          cursor: pointer;
          border: none;
      }
      .boton-nuevo:hover { background-color: #008f85; }

      /* Modal Backdrop */
      .modal-backdrop {
          display: none;
          position: fixed;
          top: 0; left: 0; width: 100%; height: 100%;
          background: rgba(0,0,0,0.5);
          z-index: 1000;
          justify-content: center;
          align-items: center;
      }
      .modal-backdrop.open { display: flex; }

      /* Modal Content */
      .modal-contenido {
          background: white;
          padding: 2rem;
          border-radius: 10px;
          width: 90%;
          max-width: 500px;
          box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      }
      .form-group { margin-bottom: 1rem; }
      .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; }
      .form-group input, .form-group select, .form-group textarea {
          width: 100%;
          padding: 0.75rem;
          border: 1px solid #d1d5db;
          border-radius: 0.5rem;
          font-size: 0.95rem;
      }
      .form-group input:focus, .form-group select:focus {
          border-color: #00A79D;
          outline: none;
          box-shadow: 0 0 0 3px rgba(0, 167, 157, 0.1);
      }
      .modal-footer {
          display: flex;
          justify-content: flex-end;
          gap: 1rem;
          margin-top: 1.5rem;
          padding-top: 1rem;
          border-top: 1px solid #e5e7eb;
      }
      .btn-cancelar {
          padding: 0.5rem 1rem;
          border: 1px solid #d1d5db;
          background: white;
          border-radius: 0.5rem;
          cursor: pointer;
      }
      .btn-guardar {
          padding: 0.5rem 1rem;
          background: #00A79D;
          color: white;
          border: none;
          border-radius: 0.5rem;
          font-weight: bold;
          cursor: pointer;
      }
  </style>
</head>
<body>
  <div class="contenedor">
    
    <?php include 'partials/admin_sidebar.php'; ?>

    <main class="contenido-principal">
      <header class="cabecera-principal">
        <h1>Gestión de Citas Médicas</h1>
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
            <h2>Programación de Citas</h2>
            <button onclick="abrirModalNuevaCita()" class="boton-nuevo">
                <i data-lucide="plus-circle"></i> Nueva Cita
            </button>
          </div>
          
          <div class="contenedor-tabla">
            <table>
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Paciente</th>
                  <th>Dueño</th>
                  <th>Servicio</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($citas)): ?>
                    <tr><td colspan="7" style="text-align:center; padding: 20px;">No hay citas registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($citas as $cita): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                        <td><?= date('h:i A', strtotime($cita['hora_cita'])) ?></td>
                        <td><strong><?= htmlspecialchars($cita['nombre_mascota'] ?? 'Desconocido') ?></strong></td>
                        <td>
                            <?= htmlspecialchars($cita['nombre_dueno'] ?? 'Desconocido') ?><br>
                            <span style="font-size: 0.8em; color: #666;"><?= htmlspecialchars($cita['correo_electronico'] ?? '') ?></span>
                        </td>
                        <td><?= htmlspecialchars($cita['servicio']) ?></td>
                        <td>
                            <?php 
                                $textoEstado = $cita['estado'];
                                $estadoClass = 'pendiente'; 

                                if($cita['estado'] == 'Confirmada' || $cita['estado'] == 'Atendida') {
                                    $textoEstado = 'Atendida'; 
                                    $estadoClass = 'completado'; 
                                }
                                elseif($cita['estado'] == 'Cancelada') {
                                    $textoEstado = 'Cancelada';
                                    $estadoClass = 'cancelado'; 
                                }
                            ?>
                            <span class="estado <?= $estadoClass ?>"><?= htmlspecialchars($textoEstado) ?></span>
                        </td>
                        <td class="acciones">
                            <a href="../Controller/admin_cita_controller.php?accion=atender&id=<?= $cita['id'] ?>" 
                               class="boton-accion" style="color: green; border-color: green;" title="Marcar Atendida">
                               <i data-lucide="check-circle"></i>
                            </a>
                            <a href="../Controller/admin_cita_controller.php?accion=cancelar&id=<?= $cita['id'] ?>" 
                               class="boton-accion" style="color: orange; border-color: orange;" title="Cancelar">
                               <i data-lucide="x-circle"></i>
                            </a>
                            <a href="../Controller/admin_cita_controller.php?accion=eliminar&id=<?= $cita['id'] ?>" 
                               class="boton-accion eliminar" onclick="return confirm('¿Eliminar registro?')" title="Eliminar">
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

  <div id="modalNuevaCita" class="modal-backdrop">
      <div class="modal-contenido">
          <h2 style="margin-bottom: 1.5rem; color: #00A79D; font-size: 1.5rem; font-weight: bold;">
              <i data-lucide="calendar-plus" style="vertical-align: middle; margin-right: 5px;"></i> 
              Registrar Nueva Cita
          </h2>
          
          <form action="../Controller/admin_cita_controller.php" method="POST">
              <input type="hidden" name="accion" value="crear">
              
              <div class="form-group">
                  <label for="id_mascota">Paciente (Mascota - Dueño)</label>
                  <select name="id_mascota" id="id_mascota" required>
                      <option value="">-- Seleccionar Paciente --</option>
                      <?php foreach ($listaMascotas as $m): ?>
                          <option value="<?= $m['id'] ?>">
                              <?= htmlspecialchars($m['nombre_mascota']) ?> (Dueño: <?= htmlspecialchars($m['nombre_dueno']) ?>)
                          </option>
                      <?php endforeach; ?>
                  </select>
              </div>

              <div class="form-group">
                  <label for="servicio">Servicio Médico</label>
                  <select name="servicio" id="servicio" required>
                      <option value="">-- Seleccionar Servicio --</option>
                      <?php foreach ($listaServicios as $s): ?>
                          <option value="<?= htmlspecialchars($s['nombre']) ?>">
                              <?= htmlspecialchars($s['nombre']) ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
              </div>

              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                  <div class="form-group">
                      <label for="fecha_cita">Fecha</label>
                      <input type="date" name="fecha_cita" id="fecha_cita" min="<?= date('Y-m-d') ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="hora_cita">Hora</label>
                      <input type="time" name="hora_cita" id="hora_cita" required>
                  </div>
              </div>

              <div class="form-group">
                  <label for="motivo">Motivo / Notas</label>
                  <textarea name="motivo" id="motivo" rows="3" placeholder="Detalles de la cita..."></textarea>
              </div>

              <div class="modal-footer">
                  <button type="button" onclick="cerrarModalNuevaCita()" class="btn-cancelar">Cancelar</button>
                  <button type="submit" class="btn-guardar">Guardar Cita</button>
              </div>
          </form>
      </div>
  </div>

  <script>
    lucide.createIcons();

    const modal = document.getElementById('modalNuevaCita');

    function abrirModalNuevaCita() {
        modal.classList.add('open');
    }

    function cerrarModalNuevaCita() {
        modal.classList.remove('open');
    }

    // Cerrar al hacer clic fuera
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            cerrarModalNuevaCita();
        }
    });
  </script>
</body>
</html>