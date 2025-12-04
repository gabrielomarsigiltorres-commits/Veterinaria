<?php
session_start();
require '../Modelo/conexion.php'; 

// Control de acceso
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../Views/login.php");
    exit;
}

// 1. Obtener la acción: puede venir por GET (eliminar) o POST (guardar/actualizar)
$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

// Variables de redirección por defecto
$status = 'error';
$msg = 'Acción no válida o faltan datos esenciales.';
$redirect_view = 'clientes.php'; 
$id_usuario_error = ''; 

// --- Lógica Principal del Controlador ---
switch ($accion) {
    
    // ===================================================================
    // ACCIÓN: GUARDAR (CREAR NUEVO CLIENTE) - SIN HASHING
    // ===================================================================
    case 'guardar':
        $nombres_completos = trim($_POST['nombres_completos'] ?? '');
        $correo_electronico = trim($_POST['correo_electronico'] ?? '');
        $password = $_POST['password'] ?? ''; // Contraseña en texto plano
        $tipo_usuario = 'cliente';

        if (empty($nombres_completos) || empty($correo_electronico) || empty($password)) {
            $msg = 'Faltan campos obligatorios para guardar el cliente.';
            $redirect_view = 'cliente_form.php'; 
            break; 
        }

        try {
            // Verificar si el correo ya existe
            $stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE correo_electronico = ?");
            $stmt->execute([$correo_electronico]);
            if ($stmt->fetchColumn() > 0) {
                $msg = 'Error: El correo electrónico ya está registrado.';
                $redirect_view = 'cliente_form.php';
                break;
            }

            // **IMPORTANTE:** Usamos la contraseña SIN hashear ($password) y la columna 'contrasena'
            $sql = "INSERT INTO usuarios (nombres_completos, correo_electronico, contrasena, tipo_usuario) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            
            if ($stmt->execute([$nombres_completos, $correo_electronico, $password, $tipo_usuario])) {
                $status = 'success';
                $msg = '✅ Cliente guardado exitosamente.';
            } else {
                $msg = 'Error desconocido al insertar el cliente.';
            }

        } catch (PDOException $e) {
            $msg = 'Error de BD: ' . $e->getMessage();
            $redirect_view = 'cliente_form.php';
        }
        break;


    // ===================================================================
    // ACCIÓN: ACTUALIZAR (EDICIÓN) - SIN HASHING
    // ===================================================================
    case 'actualizar':
        $id_usuario = $_POST['id_usuario'] ?? null;
        $nombres_completos = trim($_POST['nombres_completos'] ?? '');
        $correo_electronico = trim($_POST['correo_electronico'] ?? ''); 
        $password = $_POST['password'] ?? ''; // Contraseña en texto plano
        $tipo_usuario = $_POST['tipo_usuario'] ?? 'cliente';
        
        if (empty($id_usuario) || empty($nombres_completos) || empty($correo_electronico)) {
            $msg = 'Error: Faltan datos esenciales (ID, nombre o correo) para actualizar.';
            break; 
        }

        $id_usuario_error = $id_usuario; 
        $redirect_view = "cliente_form.php?id={$id_usuario}"; 

        try {
            $sql = "UPDATE usuarios SET nombres_completos = ?, correo_electronico = ?, tipo_usuario = ?";
            $parametros = [$nombres_completos, $correo_electronico, $tipo_usuario];

            if (!empty($password)) {
                // **IMPORTANTE:** Usamos la columna 'contrasena' y la variable $password SIN hashear
                $sql .= ", contrasena = ?";
                $parametros[] = $password; 
            }
            
            $sql .= " WHERE id = ?";
            $parametros[] = $id_usuario;

            $stmt = $conexion->prepare($sql);
            $stmt->execute($parametros);
            
            $status = 'success';
            $msg = '✅ Cliente actualizado exitosamente.';
            $redirect_view = 'clientes.php'; 
            $id_usuario_error = ''; 
            
        } catch (Exception $e) {
            if ($e instanceof PDOException && $e->getCode() == '23000') {
                 $msg = 'Error: El correo electrónico ya está registrado por otro usuario.';
            } else {
                 $msg = 'Error al actualizar: ' . $e->getMessage();
            }
        }
        break;


    // ===================================================================
    // ACCIÓN: ELIMINAR 
    // ===================================================================
    case 'eliminar':
        $id_usuario = $_GET['id'] ?? null;
        $redirect_view = 'clientes.php';

        if (empty($id_usuario) || !is_numeric($id_usuario)) {
            $msg = 'ID de cliente no válido para eliminar.';
            break;
        }

        try {
            // Eliminar registros relacionados (mascotas)
            $stmt_mascotas = $conexion->prepare("DELETE FROM mascotas_cliente WHERE id_usuario = ?");
            $stmt_mascotas->execute([$id_usuario]);
            
            // Eliminar el usuario (cliente)
            $sql = "DELETE FROM usuarios WHERE id = ? AND tipo_usuario = 'cliente'";
            $stmt = $conexion->prepare($sql);
            
            if ($stmt->execute([$id_usuario])) {
                $status = 'success';
                $msg = '🗑️ Cliente eliminado exitosamente.';
            } else {
                $msg = 'Error al eliminar el cliente o el cliente no existe.';
            }

        } catch (PDOException $e) {
            $msg = 'Error de BD al eliminar: ' . $e->getMessage();
        }
        break;

    // ===================================================================
    // ACCIÓN POR DEFECTO
    // ===================================================================
    default:
        $redirect_view = 'clientes.php';
        break;
}

// 5. Redirección final
$url_redireccion = "../Views/{$redirect_view}";

// Si es un error de edición o creación, lo incluimos en la URL
if ($status === 'error' && !empty($id_usuario_error)) {
    // Si la vista ya incluye el ID, simplemente agregamos status y msg
    $url_redireccion .= "&status=" . urlencode($status) . "&msg=" . urlencode($msg);
} else {
    // Si es cualquier otra vista (clientes.php o el formulario sin ID), lo añadimos normalmente
    $url_redireccion .= "?status=" . urlencode($status) . "&msg=" . urlencode($msg);
}

header("Location: " . $url_redireccion);
exit;
?>