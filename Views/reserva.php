<?php
// 1. 🏁 Iniciar sesión SIEMPRE al principio
session_start();

// 2. 🛡 Seguridad: Verificar si el usuario está logueado como CLIENTE
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'cliente') {
    // Si no es cliente (o no está logueado), mandar al login
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

// 4. 🧠 Procesar el formulario (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_mascota = $_POST['id_mascota'] ?? '';
    $servicio = $_POST['servicio'] ?? '';
    $fecha_cita = $_POST['fecha_cita'] ?? '';
    $hora_cita = $_POST['hora_cita'] ?? '';
    $motivo = $_POST['motivo'] ?? '';

    if (empty($id_mascota) || empty($fecha_cita) || empty($hora_cita)) {
        $mensaje = "<div class='alert-message error'>Por favor, completa fecha, hora y mascota.</div>";
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
        <!-- action="" envía los datos a la misma URL actual, evitando problemas de ruta -->
        <form method="POST" action="">
            
            <div class="content-header">
                <p class="title">Reserva tu Cita</p>
                <p class="subtitle">Selecciona el servicio y horario para tu mascota.</p>
            </div>

            <?= $mensaje ?>

            <!-- PASO 1: SERVICIOS -->
            <h2 class="section-title">1. Selecciona el Servicio</h2>
            <div class="service-grid">
                <div class="service-card" data-service="Consulta General">
                    <div class="card-image" style="background-image: url('img/general.jpg');"></div>
                    <div><p class="card-title">Consulta General</p></div>
                </div>
                <div class="service-card" data-service="Vacunación">
                    <div class="card-image" style="background-image: url('img/vacunacion.webp');"></div>
                    <div><p class="card-title">Vacunación</p></div>
                </div>
                 <div class="service-card" data-service="Baño y Corte">
                    <div class="card-image" style="background-image: url('img/spa_canino.jpeg');"></div>
                    <div><p class="card-title">Baño y Corte</p></div>
                </div>
            </div>

            <!-- PASO 2: FECHA Y HORA -->
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
                            <!-- Los botones actualizan el input oculto mediante JS -->
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

            <!-- PASO 3: CONFIRMACIÓN -->
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
                        
                        <!-- Inputs ocultos para enviar al PHP -->
                        <input type="hidden" name="servicio" id="hidden-service">
                        <input type="hidden" name="hora_cita" id="hidden-time">
                        
                        <button type="submit" class="confirm-button">Confirmar Reserva</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const serviceCards = document.querySelectorAll('.service-card');
    const timeSlots = document.querySelectorAll('.time-slot');
    const dateInput = document.getElementById('cita-date');
    
    const summaryService = document.getElementById('summary-service');
    const summaryDate = document.getElementById('summary-date');
    const summaryTime = document.getElementById('summary-time');
    
    const hiddenService = document.getElementById('hidden-service');
    const hiddenTime = document.getElementById('hidden-time');
    
    const step2 = document.getElementById('step-2-container');
    const step3 = document.getElementById('step-3-container');

    // Selección de Servicio
    serviceCards.forEach(card => {
        card.addEventListener('click', () => {
            serviceCards.forEach(c => c.classList.remove('selected-service'));
            card.classList.add('selected-service');
            
            const service = card.dataset.service;
            summaryService.textContent = service;
            hiddenService.value = service;
            
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
