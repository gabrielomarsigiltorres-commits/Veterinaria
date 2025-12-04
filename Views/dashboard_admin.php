<?php
session_start();
// Seguridad: Verificar si el usuario es administrador
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Conexión con PDO
require '../Modelo/conexion.php'; // Debe devolver un objeto $conexion de tipo PDO

// --- Carga de Estadísticas ---
$hoy = date('Y-m-d');

try {
    // Citas de hoy
    $stmt_citas_hoy = $conexion->prepare("SELECT COUNT(*) FROM citas WHERE fecha_cita = ?");
    $stmt_citas_hoy->execute([$hoy]);
    $citas_hoy = $stmt_citas_hoy->fetchColumn();

    // Total Clientes
    $stmt_clientes = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE tipo_usuario = 'cliente'");
    $stmt_clientes->execute();
    $clientes_total = $stmt_clientes->fetchColumn();

    // Total Mascotas
    $stmt_mascotas_total = $conexion->prepare("SELECT COUNT(*) FROM mascotas_cliente");
    $stmt_mascotas_total->execute();
    $mascotas_total = $stmt_mascotas_total->fetchColumn();

    // Productos con stock bajo (<= 10)
    $stmt_stock = $conexion->prepare("
        SELECT p.id_producto, p.nombre, p.stock 
        FROM productos p
        WHERE p.stock <= 10 
        ORDER BY p.stock ASC 
        LIMIT 5
    ");
    $stmt_stock->execute();
    $productos_bajos = $stmt_stock->fetchAll(PDO::FETCH_ASSOC);
    $productos_stock_bajo_count = count($productos_bajos);

    // Próximas citas
    $stmt_citas_lista = $conexion->prepare("
        SELECT c.id, c.fecha_cita, c.hora_cita, c.servicio, c.estado, 
               m.nombre AS mascota, u.nombres_completos AS dueno 
        FROM citas c
        JOIN mascotas_cliente m ON c.id_mascota = m.id
        JOIN usuarios u ON c.id_usuario = u.id
        WHERE c.fecha_cita >= CURDATE()
        ORDER BY c.fecha_cita ASC, c.hora_cita ASC
        LIMIT 5
    ");
    $stmt_citas_lista->execute();
    $citas = $stmt_citas_lista->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar el dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Admin - Clínica Veterinaria</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="css/dashboard_admin.css">
</head>
<body>
  <div class="contenedor">
    <?php include 'partials/admin_sidebar.php'; ?>

    <main class="contenido-principal">
      <header class="cabecera-principal">
        <h1>Dashboard Principal</h1>
        <div class="info-usuario">
          <img src="../img/logo.jpg" alt="Admin" class="avatar">
          <div>
            <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
            <p class="rol-usuario">Administrador</p>
          </div>
        </div>
      </header>

      <section class="contenido-pagina">
        <div class="estadisticas">
          <div class="tarjeta-stat">
            <i data-lucide="calendar-clock" class="icono-stat"></i>
            <h3>Citas de Hoy</h3>
            <p class="valor"><?= $citas_hoy ?></p>
            <span class="info-extra positivo">en tiempo real</span>
          </div>
          <div class="tarjeta-stat">
            <i data-lucide="users" class="icono-stat"></i>
            <h3>Total Clientes</h3>
            <p class="valor"><?= $clientes_total ?></p>
            <span class="info-extra positivo">registrados</span>
          </div>
          <div class="tarjeta-stat">
            <i data-lucide="paw-print" class="icono-stat"></i>
            <h3>Total Mascotas</h3>
            <p class="valor"><?= $mascotas_total ?></p>
            <span class="info-extra positivo">en el sistema</span>
          </div>
          <div class="tarjeta-stat">
            <i data-lucide="alert-circle" class="icono-stat"></i>
            <h3>Stock Bajo</h3>
            <p class="valor"><?= $productos_stock_bajo_count ?></p>
            <span class="info-extra <?= ($productos_stock_bajo_count > 0) ? 'negativo' : 'positivo'; ?>">productos urgentes</span>
          </div>
        </div>

        <div class="layout-columnas">
          <!-- Columna Izquierda: Próximas Citas -->
          <div class="columna-izquierda">
            <div class="tarjeta-tabla">
              <div class="tarjeta-cabecera">
                <h2>Próximas Citas</h2>
                <a href="reserva.php" class="boton-nuevo">
                  <i data-lucide="plus"></i> Nueva Cita
                </a>
              </div>
              <div class="contenedor-tabla">
                <table>
                  <thead>
                    <tr>
                      <th>Mascota</th>
                      <th>Dueño</th>
                      <th>Fecha y Hora</th>
                      <th>Servicio</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($citas)): ?>
                      <tr><td colspan="5">No hay citas programadas.</td></tr>
                    <?php else: ?>
                      <?php foreach ($citas as $c): ?>
                      <tr>
                        <td><strong><?= htmlspecialchars($c['mascota']) ?></strong></td>
                        <td><?= htmlspecialchars($c['dueno']) ?></td>
                        <td><?= date('d/m/y', strtotime($c['fecha_cita'])) ?> <?= date('h:i A', strtotime($c['hora_cita'])) ?></td>
                        <td><?= htmlspecialchars($c['servicio']) ?></td>
                        <td>
                          <span class="estado <?= strtolower(htmlspecialchars($c['estado'])); ?>">
                            <?= htmlspecialchars($c['estado']) ?>
                          </span>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Columna Derecha: Alertas de Stock -->
          <div class="columna-derecha">
            <div class="tarjeta-tabla">
              <div class="tarjeta-cabecera">
                <h2>Alertas de Stock Bajo</h2>
                <a href="admin_productos.php" class="boton-nuevo-secundario">Gestionar Productos</a>
              </div>
              <ul class="lista-simple">
                <?php if (empty($productos_bajos)): ?>
                  <li class="item-lista">
                    <span>¡Excelente! No hay productos con bajo stock.</span>
                  </li>
                <?php else: ?>
                  <?php foreach ($productos_bajos as $p): ?>
                  <li class="item-lista">
                    <span><?= htmlspecialchars($p['nombre']) ?></span>
                    <span class="stock-valor <?= ($p['stock'] <= 5) ? 'negativo' : 'pendiente'; ?>">
                      <?= htmlspecialchars($p['stock']) ?> Unid.
                    </span>
                  </li>
                  <?php endforeach; ?>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
  <script>lucide.createIcons();</script>
</body>
</html>
