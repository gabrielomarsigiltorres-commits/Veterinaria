<?php
// File: Controller/fetch_historial_data.php (Código mejorado para presentación)

session_start();
// 1. Verificar sesión y ID de mascota (Seguridad)
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador' || !isset($_POST['id_mascota'])) {
    http_response_code(403);
    echo "<div style='color: red; padding: 15px; border: 1px solid red; border-radius: 5px;'>Acceso denegado o ID de mascota no proporcionado.</div>";
    exit;
}

require '../Modelo/conexion.php'; 

$id_mascota = $_POST['id_mascota'];

// Funciones auxiliares para el formato
function getIconoServicio($servicio) {
    $servicio = strtolower($servicio);
    if (strpos($servicio, 'vacuna') !== false) return '💉 Vacunación';
    if (strpos($servicio, 'cirugía') !== false || strpos($servicio, 'operatorio') !== false) return '⚕️ Cirugía/Post-Operatorio';
    if (strpos($servicio, 'control') !== false || strpos($servicio, 'consulta') !== false) return '🩺 Consulta/Control';
    return '📋 Otros Servicios';
}

function formatearFecha($fecha) {
    if (empty($fecha)) return 'N/A';
    $date = new DateTime($fecha);
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    return $date->format('d') . ' de ' . $meses[$date->format('n') - 1] . ' de ' . $date->format('Y');
}

// 2. Consultar citas y nombre de la mascota
try {
    // Consulta para citas
    $sql = "
        SELECT 
            c.servicio, 
            c.fecha_cita, 
            c.hora_cita, 
            c.motivo, 
            c.estado,
            m.nombre AS nombre_mascota
        FROM citas c
        JOIN mascotas_cliente m ON c.id_mascota = m.id
        WHERE c.id_mascota = :id_mascota
        ORDER BY c.fecha_cita DESC, c.hora_cita DESC
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id_mascota' => $id_mascota]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para el nombre de la mascota
    $stmt_mascota = $conexion->prepare("SELECT nombre FROM mascotas_cliente WHERE id = :id_mascota");
    $stmt_mascota->execute([':id_mascota' => $id_mascota]);
    $mascota_data = $stmt_mascota->fetch(PDO::FETCH_ASSOC);
    $nombre_mascota = $mascota_data['nombre'] ?? 'Desconocida';

} catch (PDOException $e) {
    echo "<div style='color: red; padding: 15px; border: 1px solid red; border-radius: 5px;'>Error al obtener historial: " . $e->getMessage() . "</div>";
    exit;
}

// 3. Generar el contenido HTML del modal (Diseño de tarjetas mejorado)
?>
<div class="modal-header" style="border-bottom: 2px solid #007bff; background-color: #f8f9fa;">
    <h5 class="modal-title" id="historialModalLabel" style="font-weight: 700; color: #007bff;">Historial Clínico de **<?= htmlspecialchars($nombre_mascota); ?>**</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="border: none; background: none; font-size: 1.5rem;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" style="padding: 10px 20px;">
    <?php if (!empty($citas)): ?>
        <?php foreach ($citas as $cita): ?>
            <?php
                $servicio_tipo_icon = getIconoServicio($cita['servicio']);
                
                // Colores de estado
                $estado = htmlspecialchars($cita['estado']);
                $estado_color = 'gray';
                if ($estado === 'Completada') $estado_color = '#28a745'; // Verde
                if ($estado === 'Pendiente') $estado_color = '#ffc107'; // Amarillo
                if ($estado === 'Cancelada') $estado_color = '#dc3545'; // Rojo
            ?>
            <div class="historial-tarjeta" style="
                border: 1px solid #ddd; 
                border-radius: 8px; 
                padding: 15px; 
                margin-bottom: 15px; 
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            ">
                
                <h4 style="margin-top: 0; color: #007bff; font-weight: 600; font-size: 1.15rem;">
                    <?= $servicio_tipo_icon; ?>: **<?= htmlspecialchars($cita['servicio']); ?>**
                </h4>
                
                <hr style="margin: 8px 0; border-top: 1px dashed #eee;">

                <p style="font-size: 0.9em; color: #555; margin-bottom: 5px;">
                    🗓️ **Fecha:** <span style="font-weight: 600;"><?= formatearFecha($cita['fecha_cita']); ?></span> 
                    a las 
                    <span style="font-weight: 600;"><?= date('h:i A', strtotime($cita['hora_cita'])); ?></span>
                </p>

                <?php if (!empty($cita['motivo'])): ?>
                    <div style="background-color: #f1f8ff; border-left: 3px solid #007bff; padding: 10px; margin: 10px 0; border-radius: 4px;">
                        <p style="margin: 0; font-size: 0.9em;">
                            **📝 Notas:** <?= nl2br(htmlspecialchars($cita['motivo'])); ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <p style="margin-top: 10px; font-size: 0.9em;">
                    **Estado:** <span style="font-weight: bold; color: <?= $estado_color ?>; padding: 2px 8px; border-radius: 3px; background-color: <?= $estado_color ?>15; border: 1px solid <?= $estado_color ?>40;">
                        <?= $estado; ?>
                    </span>
                </p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 4px; padding: 15px; margin-top: 10px; text-align: center;">
            <p style="margin: 0;">⚠️ No hay citas ni historial clínico registrado para esta mascota.</p>
        </div>
    <?php endif; ?>
</div>