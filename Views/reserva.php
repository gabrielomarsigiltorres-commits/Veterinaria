<?php
// 1. 🏁 Iniciar sesión SIEMPRE al principio
session_start();

// 2. 🛡 Seguridad: Verificar si el usuario está logueado como CLIENTE
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'cliente') {
    header("Location: login.php");
    exit;
}

require '../Modelo/conexion.php'; // Conexión PDO

$id_usuario_logueado = $_SESSION['usuario_id'];
$mensaje = "";

// 3. 🐶 Cargar mascotas (para el select)
try {
    $stmt = $conexion->prepare("SELECT id, nombre FROM mascotas_cliente WHERE id_usuario = ?");
    $stmt->execute([$id_usuario_logueado]);
    $mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje = "<div class='alert-message error'>Error al cargar mascotas: " . $e->getMessage() . "</div>";
}

// 4. 🏥 Cargar SERVICIOS dinámicamente desde la BD (Igual que en el Admin)
$listaServicios = [];
try {
    // Seleccionamos solo los activos
    $stmtServicios = $conexion->query("SELECT * FROM servicios WHERE estado = 'Activo' ORDER BY id DESC");
    $listaServicios = $stmtServicios->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si falla, la lista queda vacía y no rompe la página
    error_log("Error al cargar servicios: " . $e->getMessage());
}

// 5. 🧠 Procesar el formulario de Reserva (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_mascota = $_POST['id_mascota'] ?? '';
    $servicio = $_POST['servicio'] ?? '';
    $fecha_cita = $_POST['fecha_cita'] ?? '';
    $hora_cita = $_POST['hora_cita'] ?? '';
    $motivo = $_POST['motivo'] ?? '';

    if (empty($id_mascota) || empty($fecha_cita) || empty($hora_cita) || empty($servicio)) {
        $mensaje = "<div class='alert-message error'>Por favor, selecciona un servicio, fecha, hora y mascota.</div>";
    } else {
        try {
            $sql_insert = "INSERT INTO citas (id_usuario, id_mascota, servicio, fecha_cita, hora_cita, motivo, estado) 
                           VALUES (?, ?, ?, ?, ?, ?, 'Pendiente')";
            $stmt = $conexion->prepare($sql_insert);
            
            if ($stmt->execute([$id_usuario_logueado, $id_mascota, $servicio, $fecha_cita, $hora_cita, $motivo])) {
                $mensaje = "<div class='alert-message success'>✅ ¡Cita solicitada con éxito! Espera la confirmación.</div>";
            }
        } catch (PDOException $e) {
            $mensaje = "<div class='alert-message error'>❌ Error al guardar la cita: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Reservar Cita - Clínica Veterinaria</title>
    <link rel="stylesheet" href="css/reserva.css">
    <style>
        .alert-message { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; text-align: center; }
        .alert-message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<header class="main-header">
    <div class="header-content-simple">
        <div class="logo-group">
            <a href="../INICIO.PHP"><img src="../img/veterinarialogo.png" class="logo-image" alt="Logo"></a>
            <span class="header-title-text">Veterinaria del Norte</span>
        </div>
        <a href="dashboard.php" style="text-decoration: none; color: #007bff; font-weight: bold;">Volver al Panel</a>
    </div>
</header>

<div class="layout-wrapper">
    <div class="layout-container">
        <form method="POST" action="">
            
            <div class="content-header">
                <p class="title">Reserva tu Cita</p>
                <p class="subtitle">Selecciona el servicio y horario para tu mascota.</p>
            </div>

            <?= $mensaje ?>

            <h2 class="section-title">1. Selecciona el Servicio</h2>
            <div class="service-grid">
                
                <?php if (count($listaServicios) > 0): ?>
                    <?php foreach ($listaServicios as $svc): ?>
                        <?php 
                            // Lógica de la imagen:
                            // Las imágenes se guardan en Views/uploads según el controlador del admin.
                            // Como estamos en Views/reserva.php, la ruta relativa es 'uploads/nombre_archivo'.
                            $imgNombre = !empty($svc['imagen_url']) ? $svc['imagen_url'] : 'default_service.png';
                            $rutaImagen = "uploads/" . htmlspecialchars($imgNombre);
                            
                            // Si quisieras un fallback si el archivo no existe físicamente (opcional):
                            if (!file_exists("uploads/" . $imgNombre)) {
                                // Puedes poner una imagen por defecto o dejar la rota, 
                                // pero mantendremos la ruta generada por la BD.
                            }
                        ?>
                        
                        <div class="service-card" data-service="<?= htmlspecialchars($svc['nombre']) ?>">
                            <div class="card-image" style="background-image: url('<?= $rutaImagen ?>'); background-size: cover; background-position: center;"></div>
                            <div><p class="card-title"><?= htmlspecialchars($svc['nombre']) ?></p></div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #666; font-style: italic;">No hay servicios activos disponibles en este momento.</p>
                <?php endif; ?>

            </div>

            <div id="step-2-container" class="step-container">
                <h2 class="section-title">2. Fecha y Hora</h2>
                <div class="datetime-selection-simple">
                    <div class="form-group datetime-group">
                        <label class="form-label">Fecha:</label>
                        <input type="date" name="fecha_cita" id="cita-date" class="form-input date-input" required>
                    </div>
                    <div class="form-group datetime-group">
                        <label class="form-label">Horario:</label>
                        <div class="time-grid-simple">
                            <button type="button" class="time-slot" data-time="09:00">09:00 AM</button>
                            <button type="button" class="time-slot" data-time="10:00">10:00 AM</button>
                            <button type="button" class="time-slot" data-time="11:00">11:00 AM</button>
                            <button type="button" class="time-slot" data-time="15:00">03:00 PM</button>
                            <button type="button" class="time-slot" data-time="16:00">04:00 PM</button>
                            <button type="button" class="time-slot" data-time="17:00">05:00 PM</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="step-3-container" class="step-container">
                <h2 class="section-title">3. Detalles Finales</h2>
                <div class="confirmation-section">
                    <div class="form-column">
                        <div class="form-group">
                            <label class="form-label">Mascota:</label>
                            <select name="id_mascota" id="id_mascota" class="form-input" required>
                                <option value="">-- Selecciona --</option>
                                <?php foreach($mascotas as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Motivo (Opcional):</label>
                            <textarea name="motivo" class="form-textarea" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="summary-column">
                        <h3 class="summary-title">Resumen</h3>
                        <p><strong>Servicio:</strong> <span id="summary-service">-</span></p>
                        <p><strong>Fecha:</strong> <span id="summary-date">-</span></p>
                        <p><strong>Hora:</strong> <span id="summary-time">-</span></p>
                        
                        <input type="hidden" name="servicio" id="hidden-service" required>
                        <input type="hidden" name="hora_cita" id="hidden-time" required>
                        
                        <button type="submit" class="confirm-button">Confirmar Reserva</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Selectores actualizados para elementos dinámicos
    const serviceGrid = document.querySelector('.service-grid'); // Contenedor padre
    const timeSlots = document.querySelectorAll('.time-slot');
    const dateInput = document.getElementById('cita-date');
    
    const summaryService = document.getElementById('summary-service');
    const summaryDate = document.getElementById('summary-date');
    const summaryTime = document.getElementById('summary-time');
    
    const hiddenService = document.getElementById('hidden-service');
    const hiddenTime = document.getElementById('hidden-time');
    
    const step2 = document.getElementById('step-2-container');
    const step3 = document.getElementById('step-3-container');

    // DELEGACIÓN DE EVENTOS para Servicios Dinámicos
    // Como los divs se crean con PHP, es seguro usar querySelectorAll directamente,
    // pero verificamos que existan elementos.
    const serviceCards = document.querySelectorAll('.service-card');

    serviceCards.forEach(card => {
        card.addEventListener('click', () => {
            // Quitar clase selected a todos
            serviceCards.forEach(c => c.classList.remove('selected-service'));
            // Agregar clase al clickeado
            card.classList.add('selected-service');
            
            // Obtener nombre del servicio del dataset
            const service = card.dataset.service;
            
            // Actualizar resumen y input oculto
            summaryService.textContent = service;
            hiddenService.value = service;
            
            // Mostrar siguiente paso
            step2.classList.add('active'); 
        });
    });

    // Selección de Fecha
    dateInput.addEventListener('change', (e) => {
        summaryDate.textContent = e.target.value;
    });

    // Selección de Hora
    timeSlots.forEach(slot => {
        slot.addEventListener('click', () => {
            timeSlots.forEach(s => s.classList.remove('selected-slot'));
            slot.classList.add('selected-slot');
            
            const time = slot.dataset.time;
            summaryTime.textContent = time;
            hiddenTime.value = time;
            
            step3.classList.add('active'); 
        });
    });
});
</script>

</body>
</html>