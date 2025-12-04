<?php
session_start();

// 1. Verificar la sesión del administrador
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../Views/login.php");
    exit;
}

require '../Modelo/conexion.php'; 

// 2. Obtener la acción: POST para guardar/actualizar, GET para eliminar
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// Variables para datos del formulario (POST)
$id_usuario = $_POST['id_usuario'] ?? null;
$nombre = $_POST['nombre'] ?? '';
$edad = $_POST['edad'] ?? null;
$especie = $_POST['especie'] ?? '';
$raza = $_POST['raza'] ?? '';
$sexo = $_POST['sexo'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
$alergias = $_POST['alergias'] ?? '';

// Variables para edición/eliminación (usa GET para eliminar)
$id_mascota = $_POST['id_mascota'] ?? $_GET['id'] ?? null; 
$imagen_actual = $_POST['imagen_actual'] ?? null;

// Ruta de destino para la imagen (Relativa al controlador)
// Asume que el directorio 'uploads' está dentro de 'Views'
$target_dir = "../Views/uploads/";
$nombre_imagen = $imagen_actual; 

// Inicializar variables de estado y redirección
$status = 'error';
$msg = 'Acción no válida.';
// RUTA CORREGIDA: Apunta a mascotas_admin.php (resuelve el 404 anterior)
$redirect_view = 'mascotas_admin.php'; 

// 3. Manejar la subida de archivos (solo si se sube una nueva imagen)
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    // Generar un nombre único para evitar colisiones
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombre_imagen_nuevo = time() . '_' . uniqid() . '.' . $ext;
    $target_file = $target_dir . $nombre_imagen_nuevo;

    // Asegurar que el directorio exista
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Mover el archivo subido al directorio de uploads
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
        $nombre_imagen = $nombre_imagen_nuevo;

        // Si estamos editando y se subió una nueva imagen, eliminar la anterior
        if ($accion === 'actualizar' && $imagen_actual && file_exists($target_dir . $imagen_actual)) {
            unlink($target_dir . $imagen_actual);
        }
    } else {
        // Manejo de error si no se pudo mover el archivo
        $msg = 'Error al subir la imagen.';
        $redirect_id = $id_mascota ? "id={$id_mascota}" : "cliente_id={$id_usuario}";
        header("Location: ../Views/mascota_form.php?" . $redirect_id . "&status=error&msg=" . urlencode($msg));
        exit;
    }
}


// 4. Ejecutar la acción en la base de datos
try {
    if ($accion === 'guardar') {
        // Acción de REGISTRAR NUEVA MASCOTA
        if (!$id_usuario || empty($nombre) || empty($especie)) {
            throw new Exception("Faltan campos esenciales (Dueño, Nombre, Especie) para el registro.");
        }
        
        $sql = "INSERT INTO mascotas_cliente (id_usuario, nombre, especie, raza, sexo, fecha_nacimiento, edad, alergias, imagen) 
                 VALUES (:id_usuario, :nombre, :especie, :raza, :sexo, :fecha_nacimiento, :edad, :alergias, :imagen)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':nombre' => $nombre,
            ':especie' => $especie,
            ':raza' => $raza,
            ':sexo' => $sexo,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':edad' => $edad,
            ':alergias' => $alergias,
            ':imagen' => $nombre_imagen
        ]);
        
        $status = 'success';
        $msg = 'Mascota registrada exitosamente.';
        $redirect_view = "cliente_detalle.php?id=" . $id_usuario; 

    } elseif ($accion === 'actualizar') {
        // Acción de EDITAR MASCOTA EXISTENTE
        if (!$id_mascota) {
            throw new Exception("ID de mascota no especificado para actualizar.");
        }
        
        $sql = "UPDATE mascotas_cliente SET
                    nombre = :nombre,
                    especie = :especie,
                    raza = :raza,
                    sexo = :sexo,
                    fecha_nacimiento = :fecha_nacimiento,
                    edad = :edad,
                    alergias = :alergias,
                    imagen = :imagen
                WHERE id = :id_mascota";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':especie' => $especie,
            ':raza' => $raza,
            ':sexo' => $sexo,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':edad' => $edad,
            ':alergias' => $alergias,
            ':imagen' => $nombre_imagen,
            ':id_mascota' => $id_mascota
        ]);
        
        $status = 'success';
        $msg = 'Mascota actualizada exitosamente.';
        $redirect_view = "mascotas_admin.php"; 
        
    } elseif ($accion === 'eliminar') {
        // ACCIÓN: ELIMINAR MASCOTA (Viene por GET)
        if (!$id_mascota) {
            throw new Exception("ID de mascota no especificado para eliminar.");
        }

        // 1. Obtener la imagen antes de eliminar (para el archivo físico)
        $stmt_data = $conexion->prepare("SELECT imagen FROM mascotas_cliente WHERE id = ?");
        $stmt_data->execute([$id_mascota]);
        $data_a_eliminar = $stmt_data->fetch(PDO::FETCH_ASSOC);

        if (!$data_a_eliminar) {
            throw new Exception("Mascota no encontrada para eliminar.");
        }
        $imagen_a_eliminar = $data_a_eliminar['imagen'];
        
        // 2. Eliminar la mascota de la BD
        $sql = "DELETE FROM mascotas_cliente WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_mascota]);
        
        // 3. Eliminar el archivo físico (si existe)
        $image_path = $target_dir . $imagen_a_eliminar;
        if (!empty($imagen_a_eliminar) && file_exists($image_path)) {
            unlink($image_path);
        }

        $status = 'success';
        $msg = '🗑️ Mascota eliminada exitosamente.';
        $redirect_view = "mascotas_admin.php"; 

    } else {
          $status = 'error';
          $msg = 'Acción no válida.';
          $redirect_view = "mascotas_admin.php"; 
    }

} catch (Exception $e) {
    $status = 'error';
    $msg = 'Error: ' . $e->getMessage();
    
    // Redirección en caso de error para volver al formulario
    if ($accion === 'guardar' && $id_usuario) {
        $redirect_view = "mascota_form.php?cliente_id=" . $id_usuario;
    } elseif ($accion === 'actualizar' && $id_mascota) {
        $redirect_view = "mascota_form.php?id=" . $id_mascota;
    } else {
        $redirect_view = "mascotas_admin.php"; 
    }
}

// 5. Redireccionamiento final
$url_redireccion = "../Views/{$redirect_view}";

// Agregar status y msg a la URL
$url_redireccion .= (strpos($url_redireccion, '?') === false ? '?' : '&') . "status=" . urlencode($status) . "&msg=" . urlencode($msg);

header("Location: " . $url_redireccion);
exit;
?>