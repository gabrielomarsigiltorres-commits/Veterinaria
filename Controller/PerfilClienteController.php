<?php
// Controller/PerfilClienteController.php (Corregido para persistencia de datos)
session_start();

// 1. Seguridad: Solo permite clientes logueados
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../Modelo/ClienteModelo.php";

$redirect_view = "../Views/perfil_cliente.php";
$id_usuario_logueado = $_SESSION['usuario_id'];
$modelo = new ClienteModelo();
$perfil = $modelo->obtenerPerfil($id_usuario_logueado); // Carga datos actuales
$mensaje = "";

// --- Función de Subida de Imagen (Función auxiliar) ---
function subirFotoPerfil($archivo, $nombre_actual = null)
{
    if (!isset($archivo) || $archivo["error"] !== 0) {
        return [null, null];
    }
    
    $directorio_subidas = '../Views/uploads/';
    $info_archivo = pathinfo($archivo["name"]);
    $extension = strtolower($info_archivo['extension']);
    $tipos_permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $tipos_permitidos)) {
        return [false, "Error: Solo se permiten archivos JPG, JPEG, PNG y WEBP."];
    }

    $nombre_unico = uniqid('user_') . '_' . time() . '.' . $extension;
    $ruta_destino = $directorio_subidas . $nombre_unico;

    // Intentamos mover el archivo temporal
    if (move_uploaded_file($archivo["tmp_name"], $ruta_destino)) {
        // Eliminar la imagen anterior si existe y no es la predeterminada
        if ($nombre_actual && file_exists($directorio_subidas . $nombre_actual) && $nombre_actual != 'user_default.png') {
            unlink($directorio_subidas . $nombre_actual);
        }
        return [true, $nombre_unico];
    } else {
        return [false, "Error al mover el archivo subido."];
    }
}

// --- Lógica de Actualización (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';
    
    // 1. Recolección de datos
    $datos = [
        // Tabla 'usuarios'
        'nombres_completos' => trim($_POST['nombres_completos'] ?? ''),
        'password'          => trim($_POST['password'] ?? ''), 
        
        // Tabla 'cliente_perfil'
        'telefono'          => trim($_POST['telefono'] ?? ''),
        'dni'               => trim($_POST['dni'] ?? ''),
        'distrito'          => trim($_POST['distrito'] ?? ''),
        'provincia'         => trim($_POST['provincia'] ?? ''),
        'direccion_av'      => trim($_POST['direccion_av'] ?? ''),
        'foto_perfil'       => $perfil['foto_perfil'] // Valor actual por defecto
    ];

    // 2. Manejar Subida de Foto de Perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['size'] > 0) {
        list($subida_exitosa, $resultado_subida) = subirFotoPerfil($_FILES['foto_perfil'], $perfil['foto_perfil']);
        
        if ($subida_exitosa === true) {
            $datos['foto_perfil'] = $resultado_subida;
        } elseif ($subida_exitosa === false) {
            $msg_imagen = urlencode($resultado_subida);
            header("Location: {$redirect_view}?status=error&msg={$msg_imagen}");
            exit;
        }
    }
    
    // 3. Llamada al Modelo para la actualización transaccional
    try {
        if (empty($datos['password'])) {
            unset($datos['password']);
        }
        
        $resultado = $modelo->actualizarPerfil($id_usuario_logueado, $datos);

        if ($resultado) {
            // 4. Actualizar la sesión y forzar la recarga de datos
            $_SESSION['usuario_nombre'] = $datos['nombres_completos'];
            
            // La redirección fuerza la recarga de la vista y los datos frescos
            $msg = urlencode("✅ Perfil actualizado con éxito.");
            
            if ($accion === 'guardar_salir') {
                header("Location: ../Views/dashboard.php?status=success&msg={$msg}");
            } else { // 'actualizar_mantener'
                header("Location: {$redirect_view}?status=success&msg={$msg}");
            }
            exit;
        } else {
            // Si no se hicieron cambios
            $msg = urlencode("ℹ️ No se detectaron cambios.");
            header("Location: {$redirect_view}?status=info&msg={$msg}");
            exit;
        }

    } catch (Exception $e) {
        $error_msg = urlencode("Error al actualizar: " . $e->getMessage());
        header("Location: {$redirect_view}?status=error&msg={$error_msg}");
        exit;
    }
}

// Si es GET, o después de POST, se incluye la vista
require_once "../Views/perfil_cliente.php";
?>