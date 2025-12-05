<?php
// Controller/MascotaClienteController.php
session_start();

// 1. Seguridad: Solo permite clientes logueados
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../Modelo/MascotaModelo.php"; 

$redirect_url = "../Views/registrar_mascota_cliente.php";
$id_usuario_logueado = $_SESSION['usuario_id'];

// --- Función de Subida de Imagen (Adaptada del admin_producto_controlador.php) ---
function subirImagen($archivo)
{
    if (isset($archivo) && $archivo["error"] == 0) {
        $directorio_subidas = '../Views/uploads/';
        $info_archivo = pathinfo($archivo["name"]);
        $extension = strtolower($info_archivo['extension']);
        $tipos_permitidos = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $tipos_permitidos)) {
            return [false, "Error: Solo se permiten archivos JPG, JPEG, PNG y WEBP."];
        }

        $nombre_unico = uniqid('pet_') . '_' . time() . '.' . $extension;
        $ruta_destino = $directorio_subidas . $nombre_unico;

        if (move_uploaded_file($archivo["tmp_name"], $ruta_destino)) {
            return [true, $nombre_unico];
        } else {
            return [false, "Error al mover el archivo subido."];
        }
    }
    return [null, null];
}

// --- Lógica Principal ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Manejar Subida de Imagen
    $nombre_imagen = NULL;
    
    if (isset($_FILES['imagen_mascota']) && $_FILES['imagen_mascota']['size'] > 0) {
        list($subida_exitosa, $resultado_subida) = subirImagen($_FILES['imagen_mascota']);
    
        if ($subida_exitosa === true) {
            $nombre_imagen = $resultado_subida;
        } elseif ($subida_exitosa === false) {
            $msg_imagen = urlencode($resultado_subida);
            header("Location: {$redirect_url}?status=error&msg={$msg_imagen}");
            exit;
        }
    } else {
         header("Location: {$redirect_url}?status=error&msg=" . urlencode("Debe subir una foto de la mascota."));
         exit;
    }


    // 2. Recolección y Validación de Datos
    $nombre = trim($_POST['nombre'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
    $raza = trim($_POST['raza'] ?? '');
    $sexo = $_POST['sexo'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $direccion = trim($_POST['direccion'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $alergias = trim($_POST['alergias'] ?? '');

    if (empty($nombre) || empty($especie) || empty($sexo) || empty($fecha_nacimiento)) {
        header("Location: {$redirect_url}?status=error&msg=" . urlencode("Por favor, complete todos los campos obligatorios."));
        exit;
    }

    // 3. Cálculo de Edad (Usando PHP DateTime)
    $fecha_nac = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;

    // 4. Llamada al Modelo
    try {
        $modelo = new MascotaModelo();
        
        $resultado = $modelo->registrarMascotaCliente(
            $id_usuario_logueado, 
            $nombre, 
            $especie, 
            $raza, 
            $sexo, 
            $fecha_nacimiento, 
            $edad, 
            $direccion, 
            $contacto, 
            $correo, 
            $alergias,
            $nombre_imagen
        );

        if ($resultado) {
            // Redirección exitosa de vuelta al Dashboard del cliente
            header("Location: ../Views/dashboard.php?status=success&msg=" . urlencode("🐾 Mascota registrada con éxito."));
            exit;
        } else {
            throw new Exception("La inserción en la base de datos falló.");
        }

    } catch (Exception $e) {
        $error_msg = urlencode("Error de BD: " . $e->getMessage());
        // Redirige de nuevo a la vista con el mensaje de error
        header("Location: {$redirect_url}?status=error&msg={$error_msg}");
        exit;
    }
}

// Si se accede por GET sin acción, simplemente redirige a la vista del formulario
header("Location: {$redirect_url}");
exit;
?>