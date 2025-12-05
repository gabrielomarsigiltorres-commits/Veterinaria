<?php
session_start();
require_once '../Modelo/conexion.php';

// 1. Seguridad: Solo admin
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    header("Location: ../Views/login.php");
    exit;
}

// --- FUNCIÓN HELPER PARA SUBIR IMAGEN ---
function subirImagen($archivo) {
    if (isset($archivo) && $archivo['error'] === UPLOAD_ERR_OK) {
        $nombreOriginal = basename($archivo['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($extension, $permitidos)) {
            // Nombre único para evitar sobrescribir
            $nombreFinal = 'serv_' . uniqid() . '.' . $extension;
            $rutaDestino = '../Views/uploads/' . $nombreFinal;

            if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                return $nombreFinal;
            }
        }
    }
    return null; // Si falla o no hay imagen
}

// 2. Manejo de Acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- CREAR SERVICIO ---
    if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $categoria = $_POST['categoria'];
        
        // Manejo de Imagen
        $imagen_url = 'default_service.png'; // Imagen por defecto
        if (!empty($_FILES['imagen']['name'])) {
            $subida = subirImagen($_FILES['imagen']);
            if ($subida) {
                $imagen_url = $subida;
            }
        }

        try {
            $sql = "INSERT INTO servicios (nombre, descripcion, precio, categoria, imagen_url, estado, fecha_registro) 
                    VALUES (?, ?, ?, ?, ?, 'Activo', CURDATE())";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $precio, $categoria, $imagen_url]);
            
            header("Location: ../Views/servicios_admin.php?msg=creado");
            exit;
        } catch (PDOException $e) {
            header("Location: ../Views/servicios_admin.php?msg=error");
            exit;
        }
    }

    // --- EDITAR SERVICIO ---
    if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $categoria = $_POST['categoria'];
        $estado = $_POST['estado'];

        try {
            // 1. Obtener imagen actual por si no se sube una nueva
            $stmtActual = $conexion->prepare("SELECT imagen_url FROM servicios WHERE id = ?");
            $stmtActual->execute([$id]);
            $servicioActual = $stmtActual->fetch(PDO::FETCH_ASSOC);
            $imagen_url = $servicioActual['imagen_url'];

            // 2. Si subieron nueva imagen, procesarla y borrar la anterior (si no es default)
            if (!empty($_FILES['imagen']['name'])) {
                $nuevaImagen = subirImagen($_FILES['imagen']);
                if ($nuevaImagen) {
                    // Borrar vieja si existe y no es la default
                    if ($imagen_url && $imagen_url !== 'default_service.png' && file_exists("../Views/uploads/" . $imagen_url)) {
                        unlink("../Views/uploads/" . $imagen_url);
                    }
                    $imagen_url = $nuevaImagen;
                }
            }

            // 3. Actualizar
            $sql = "UPDATE servicios SET nombre=?, descripcion=?, precio=?, categoria=?, estado=?, imagen_url=? WHERE id=?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $precio, $categoria, $estado, $imagen_url, $id]);

            header("Location: ../Views/servicios_admin.php?msg=actualizado");
            exit;

        } catch (PDOException $e) {
            header("Location: ../Views/servicios_admin.php?msg=error");
            exit;
        }
    }
}

// --- ELIMINAR SERVICIO (GET) ---
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        // Borrar imagen asociada antes de borrar el registro
        $stmtImg = $conexion->prepare("SELECT imagen_url FROM servicios WHERE id = ?");
        $stmtImg->execute([$id]);
        $img = $stmtImg->fetchColumn();

        if ($img && $img !== 'default_service.png' && file_exists("../Views/uploads/" . $img)) {
            unlink("../Views/uploads/" . $img);
        }

        $stmt = $conexion->prepare("DELETE FROM servicios WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: ../Views/servicios_admin.php?msg=eliminado");
        exit;
    } catch (PDOException $e) {
        header("Location: ../Views/servicios_admin.php?msg=error");
        exit;
    }
}
?>