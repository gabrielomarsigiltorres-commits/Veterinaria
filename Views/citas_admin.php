<?php
session_start();

// 1. Seguridad: Verificar si es administrador
// Usamos trim() para limpiar posibles espacios en blanco en el rol
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    // Si falla la verificación, redirigir al login.
    // Como estamos en /Views/, el login está en la misma carpeta.
    header("Location: login.php");
    exit;
}

require '../Modelo/conexion.php'; // Conexión PDO

// 2. Obtener todas las citas
try {
    // CORRECCIÓN: Usamos 'u.correo_electronico' en lugar de 'u.telefono'
    // Usamos LEFT JOIN para evitar errores si se borró un usuario/mascota
    $sql = "SELECT c.id, c.fecha_cita, c.hora_cita, c.servicio, c.estado, c.motivo,
                   m.nombre AS nombre_mascota, 
                   u.nombres_completos AS nombre_dueno, u.correo_electronico
            FROM citas c
            LEFT JOIN mascotas_cliente m ON c.id_mascota = m.id
            LEFT JOIN usuarios u ON c.id_usuario = u.id
            ORDER BY c.fecha_cita DESC, c.hora_cita ASC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar citas: " . $e->getMessage());
}

// 3. Mensajes de Feedback
$mensaje = '';
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    if ($msg == 'confirmada') $mensaje = '<div class="alerta exito">✅ Cita confirmada correctamente.</div>';
    if ($msg == 'cancelada') $mensaje = '<div class="alerta error">⚠ Cita cancelada.</div>';
    if ($msg == 'eliminada') $mensaje = '<div class="alerta error">🗑 Registro eliminado.</div>';
    if ($msg == 'error') $mensaje = '<div class="alerta error">❌ Ocurrió un error al procesar la solicitud.</div>';
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
</head>
<body>
  <div class="contenedor">
    
    <!-- Barra Lateral -->
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
          <div class="tarjeta-cabecera">
            <h2>Programación de Citas</h2>
          </div>
          
          <div class="contenedor-tabla">
            <table>
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Paciente</th>
                  <th>Dueño / Contacto</th>
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
                        <!-- Fecha formateada -->
                        <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                        <!-- Hora formateada AM/PM -->
                        <td><?= date('h:i A', strtotime($cita['hora_cita'])) ?></td>
                        
                        <td>
                            <strong><?= htmlspecialchars($cita['nombre_mascota'] ?? 'Desconocido') ?></strong>
                        </td>
                        <td>
                            <?= htmlspecialchars($cita['nombre_dueno'] ?? 'Desconocido') ?><br>
                            <span style="font-size: 0.8em; color: #666;">📧 <?= htmlspecialchars($cita['correo_electronico'] ?? '-') ?></span>
                        </td>
                        <td><?= htmlspecialchars($cita['servicio']) ?></td>
                        <td>
                            <?php 
                                $estadoClass = 'pendiente'; 
                                if($cita['estado'] == 'Confirmada') $estadoClass = 'completado'; 
                                if($cita['estado'] == 'Cancelada') $estadoClass = 'cancelado'; 
                            ?>
                            <span class="estado <?= $estadoClass ?>"><?= htmlspecialchars($cita['estado']) ?></span>
                        </td>
                        <td class="acciones">
                            <!-- Botón Confirmar -->
                            <a href="../Controller/admin_cita_controlador.php?accion=confirmar&id=<?= $cita['id'] ?>" 
                               class="boton-accion" style="color: green; border-color: green;" title="Confirmar">
                               <i data-lucide="check"></i>
                            </a>
                            
                            <!-- Botón Cancelar -->
                            <a href="../Controller/admin_cita_controlador.php?accion=cancelar&id=<?= $cita['id'] ?>" 
                               class="boton-accion" style="color: orange; border-color: orange;" title="Cancelar">
                               <i data-lucide="x"></i>
                            </a>

                            <!-- Botón Eliminar -->
                            <a href="../Controller/admin_cita_controlador.php?accion=eliminar&id=<?= $cita['id'] ?>" 
                               class="boton-accion eliminar" onclick="return confirm('¿Eliminar este registro permanentemente?')" title="Eliminar">
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
  <script>lucide.createIcons();</script>
</body>
</html>
