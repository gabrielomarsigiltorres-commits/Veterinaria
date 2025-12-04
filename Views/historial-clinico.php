<?php
session_start();
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit;
}

require '../Modelo/conexion.php'; // PDO
$id_usuario = $_SESSION['usuario_id'];

// Obtener citas con nombre de mascota
try {
    $sql = "SELECT c.*, m.nombre AS nombre_mascota 
            FROM citas c
            JOIN mascotas_cliente m ON c.id_mascota = m.id
            WHERE c.id_usuario = ?
            ORDER BY c.fecha_cita DESC, c.hora_cita ASC";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id_usuario]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historial de Citas</title>
  <link rel="stylesheet" href="css/historial.css">
</head>
<body>
  <header class="header">
    <div class="header-left">
      <img src="../img/veterinarialogo.png" alt="Logo" class="logo">
      <span class="title">Veterinaria del Norte</span>
    </div>
    <div class="header-right">
      <button class="btn-secondary"><a href="dashboard.php" class="btn-link">Volver</a></button>
    </div>
  </header>

  <main class="container">
    <section class="historial">
      <h3>Tus Citas Agendadas</h3>
      
      <?php if (empty($citas)): ?>
        <p style="text-align:center; color:#666; padding:20px;">No tienes citas registradas aún.</p>
      <?php else: ?>
        <div class="linea-tiempo">
          <?php foreach ($citas as $cita): ?>
            <div class="evento">
              <!-- Icono según estado -->
              <div class="icono-evento <?= ($cita['estado'] == 'Confirmada') ? 'vacuna' : (($cita['estado'] == 'Cancelada') ? 'cirugia' : 'consulta') ?>">
                <?= ($cita['estado'] == 'Confirmada') ? '✓' : (($cita['estado'] == 'Cancelada') ? '✕' : '🕒') ?>
              </div>
              
              <div class="contenido">
                <h4><?= htmlspecialchars($cita['servicio']) ?></h4>
                <p><strong>Mascota:</strong> <?= htmlspecialchars($cita['nombre_mascota']) ?></p>
                <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?> - <?= date('h:i A', strtotime($cita['hora_cita'])) ?></p>
                <p><strong>Estado:</strong> 
                   <span style="color: <?= ($cita['estado'] == 'Confirmada') ? 'green' : (($cita['estado'] == 'Cancelada') ? 'red' : 'orange') ?>;">
                     <?= htmlspecialchars($cita['estado']) ?>
                   </span>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </section>
  </main>
</body>
</html>
