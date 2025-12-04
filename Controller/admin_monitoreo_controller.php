<?php
// admin_monitoreo_controller.php

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. SEGURIDAD: Verificar si es admin
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'administrador') {
    // Si es una petición AJAX, devolver error JSON
    if (isset($_GET['action'])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }
    // Si es acceso directo, redirigir
    header("Location: ../Views/login.php");
    exit;
}

// 2. CONEXIÓN (Ajusta la ruta según tu estructura real)
// Basado en tu imagen, si esto está en Controller/, la conexión está en Modelo/
require_once __DIR__ . '/../Modelo/conexion.php'; 

// 3. MANEJO DE ACCIONES AJAX (API)
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    try {
        // --- A. Obtener Mascotas de un Cliente ---
        if ($action === 'get_pets') {
            $userId = intval($_GET['user_id']);
            $stmt = $conexion->prepare("SELECT id, nombre, raza, imagen FROM mascotas_cliente WHERE id_usuario = :uid");
            $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit;
        }

        // --- B. Obtener Detalles Completos de la Mascota ---
        if ($action === 'get_pet_details') {
            $petId = intval($_GET['pet_id']);
            
            // Info básica + Cita activa
            // Nota: Se asume que 'etapa_actual' fue creada en la BD como se indicó en pasos anteriores
            $stmtPet = $conexion->prepare("
                SELECT m.*, c.servicio, c.etapa_actual, c.id as id_cita 
                FROM mascotas_cliente m 
                LEFT JOIN citas c ON c.id_mascota = m.id AND c.estado != 'Completada' AND c.estado != 'Cancelada'
                WHERE m.id = :pid LIMIT 1
            ");
            $stmtPet->bindParam(':pid', $petId, PDO::PARAM_INT);
            $stmtPet->execute();
            $petData = $stmtPet->fetch(PDO::FETCH_ASSOC);

            // Historial Peso
            $stmtPeso = $conexion->prepare("SELECT * FROM historial_peso WHERE id_mascota = :pid ORDER BY fecha_peso ASC");
            $stmtPeso->bindParam(':pid', $petId, PDO::PARAM_INT);
            $stmtPeso->execute();
            $pesoData = $stmtPeso->fetchAll(PDO::FETCH_ASSOC);

            // Tratamiento Activo
            $stmtTrat = $conexion->prepare("SELECT * FROM tratamientos WHERE id_mascota = :pid AND activo = 1 LIMIT 1");
            $stmtTrat->bindParam(':pid', $petId, PDO::PARAM_INT);
            $stmtTrat->execute();
            $tratData = $stmtTrat->fetch(PDO::FETCH_ASSOC);

            // Vacunas
            $stmtVac = $conexion->prepare("SELECT * FROM vacunas_control WHERE id_mascota = :pid");
            $stmtVac->bindParam(':pid', $petId, PDO::PARAM_INT);
            $stmtVac->execute();
            $vacData = $stmtVac->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'pet' => $petData,
                'weights' => $pesoData,
                'treatment' => $tratData ?: null, // Enviar null si no hay tratamiento
                'vaccines' => $vacData
            ]);
            exit;
        }

        // --- C. Guardar Datos ---
        if ($action === 'save_data' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $petId = $input['pet_id'];

            // 1. Actualizar Etapa Cita
            if (!empty($input['cita_id'])) {
                $citaId = intval($input['cita_id']);
                $etapa = intval($input['etapa']);
                $stmtCita = $conexion->prepare("UPDATE citas SET etapa_actual = :etapa WHERE id = :cid");
                $stmtCita->execute([':etapa' => $etapa, ':cid' => $citaId]);
            }

            // 2. Agregar Peso Nuevo
            if (!empty($input['new_weight'])) {
                $peso = floatval($input['new_weight']);
                $stmtPeso = $conexion->prepare("INSERT INTO historial_peso (id_mascota, peso, fecha_peso) VALUES (:pid, :peso, NOW())");
                $stmtPeso->execute([':pid' => $petId, ':peso' => $peso]);
            }

            // 3. Guardar Tratamiento
            if (isset($input['treatment'])) {
                $t = $input['treatment'];
                // Desactivar anteriores
                $stmtDes = $conexion->prepare("UPDATE tratamientos SET activo = 0 WHERE id_mascota = :pid");
                $stmtDes->execute([':pid' => $petId]);
                
                if ($t['active']) {
                    $stmtInsT = $conexion->prepare("INSERT INTO tratamientos (id_mascota, medicamento, dosis, instrucciones, dias_progreso, activo) VALUES (:pid, :med, :dos, :ins, :prog, 1)");
                    $stmtInsT->execute([
                        ':pid' => $petId,
                        ':med' => $t['drug'],
                        ':dos' => $t['dose'],
                        ':ins' => $t['instructions'],
                        ':prog' => intval($t['progress'])
                    ]);
                }
            }

            // 4. Actualizar Vacunas Existentes
            if (isset($input['vaccines'])) {
                $stmtUpdVac = $conexion->prepare("UPDATE vacunas_control SET estado = :est, fecha_aplicacion = :fec WHERE id = :vid");
                foreach ($input['vaccines'] as $vac) {
                    $fecha = ($vac['status'] == 'completada') ? date('Y-m-d') : NULL;
                    $stmtUpdVac->execute([
                        ':est' => $vac['status'],
                        ':fec' => $fecha,
                        ':vid' => $vac['id']
                    ]);
                }
            }

            // 5. Nueva Vacuna
            if (!empty($input['new_vaccine'])) {
                $stmtNewVac = $conexion->prepare("INSERT INTO vacunas_control (id_mascota, nombre_vacuna, estado) VALUES (:pid, :nom, 'pendiente')");
                $stmtNewVac->execute([':pid' => $petId, ':nom' => $input['new_vaccine']]);
            }

            echo json_encode(['success' => true]);
            exit;
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// 4. CARGA INICIAL PARA LA VISTA (Solo si no es Ajax)
// Cargar lista de clientes para el select
try {
    $stmt_clientes = $conexion->prepare("SELECT id, nombres_completos FROM usuarios WHERE tipo_usuario = 'cliente' ORDER BY nombres_completos ASC");
    $stmt_clientes->execute();
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clientes = [];
    $error_carga = $e->getMessage();
}
?>