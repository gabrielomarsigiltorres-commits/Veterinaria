<?php
session_start();

// 1. Seguridad: Verificar sesión de administrador
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    header("Location: ../Views/login.php");
    exit;
}

require '../Modelo/conexion.php'; 

// 2. Verificar parámetros o envío de formulario
if (isset($_REQUEST['accion'])) {
    $accion = $_REQUEST['accion'];
    $id = $_REQUEST['id'] ?? null;

    try {
        // --- CASO 1: CREAR NUEVA CITA (Desde el Modal) ---
        if ($accion == 'crear') {
            // Recibimos datos del formulario
            $id_mascota = $_POST['id_mascota'];
            $servicio = $_POST['servicio'];
            $fecha_cita = $_POST['fecha_cita'];
            $hora_cita = $_POST['hora_cita'];
            $motivo = $_POST['motivo'];
            $estado = 'Pendiente'; // Por defecto

            // Paso A: Buscar automáticamente el ID del dueño (id_usuario) basado en la mascota
            $stmtDueño = $conexion->prepare("SELECT id_usuario FROM mascotas_cliente WHERE id = ?");
            $stmtDueño->execute([$id_mascota]);
            $mascotaData = $stmtDueño->fetch(PDO::FETCH_ASSOC);

            if (!$mascotaData || !$mascotaData['id_usuario']) {
                // Si la mascota no tiene dueño asignado o no existe
                header("Location: ../Views/citas_admin.php?msg=error&info=Mascota_sin_dueno");
                exit;
            }
            $id_usuario = $mascotaData['id_usuario'];

            // Paso B: Insertar la cita
            $sql = "INSERT INTO citas (id_usuario, id_mascota, servicio, fecha_cita, hora_cita, motivo, estado, fecha_creacion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id_usuario, $id_mascota, $servicio, $fecha_cita, $hora_cita, $motivo, $estado]);

            header("Location: ../Views/citas_admin.php?msg=creada");
            exit;
        }

        // --- CASO 2: CONFIRMAR / ATENDER ---
        // TRUCO: Guardamos 'Confirmada' en la BD, pero visualmente es 'Atendida'
        elseif ($accion == 'atender' || $accion == 'confirmar') {
            if(!$id) throw new Exception("ID faltante");
            $sql = "UPDATE citas SET estado = 'Confirmada' WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id]);
            header("Location: ../Views/citas_admin.php?msg=atendida");
            exit;
        }

        // --- CASO 3: CANCELAR ---
        elseif ($accion == 'cancelar') {
            if(!$id) throw new Exception("ID faltante");
            $sql = "UPDATE citas SET estado = 'Cancelada' WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id]);
            header("Location: ../Views/citas_admin.php?msg=cancelada");
            exit;
        }

        // --- CASO 4: ELIMINAR ---
        elseif ($accion == 'eliminar') {
            if(!$id) throw new Exception("ID faltante");
            $sql = "DELETE FROM citas WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id]);
            header("Location: ../Views/citas_admin.php?msg=eliminada");
            exit;
        }
        
    } catch (Exception $e) {
        header("Location: ../Views/citas_admin.php?msg=error");
        exit;
    }

} else {
    header("Location: ../Views/citas_admin.php");
    exit;
}
?>