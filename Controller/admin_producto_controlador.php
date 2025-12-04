<?php
session_start();

// Seguridad: Solo administradores pueden ejecutar esto
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: ../Views/login.php");
    exit;
}

// Conexión con PDO
require '../Modelo/conexion.php';

// --- Función para Subir Imagen ---
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

        if ($archivo["size"] > 2 * 1024 * 1024) {
            return [false, "Error: El archivo es demasiado grande (máx 2MB)."];
        }

        $nombre_unico = "prod_" . uniqid() . '_' . time() . '.' . $extension;
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
$redirect_url = "../Views/admin_productos.php";

// === CREAR PRODUCTO ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'crear') {

    $nombre_imagen = 'default_product.png';
    list($subida_exitosa, $resultado_subida) = subirImagen($_FILES['imagen']);

    if ($subida_exitosa === true) {
        $nombre_imagen = $resultado_subida;
    } elseif ($subida_exitosa === false) {
        header("Location: $redirect_url?status=error&msg=" . urlencode($resultado_subida));
        exit;
    }

    try {
        $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, id_categoria, imagen_url) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nombre'],
            $_POST['descripcion'],
            $_POST['precio'],
            $_POST['stock'],
            $_POST['id_categoria'],
            $nombre_imagen
        ]);

        header("Location: $redirect_url?status=creado");
        exit;

    } catch (PDOException $e) {
        header("Location: $redirect_url?status=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

// === ACTUALIZAR PRODUCTO ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'actualizar') {

    $id_producto = $_POST['id_producto'];
    $nombre_imagen = $_POST['imagen_actual'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        list($exito, $resultado) = subirImagen($_FILES['imagen']);
        if ($exito) {
            if ($nombre_imagen && $nombre_imagen != 'default_product.png' && file_exists("../Views/uploads/" . $nombre_imagen)) {
                unlink("../Views/uploads/" . $nombre_imagen);
            }
            $nombre_imagen = $resultado;
        } else {
            header("Location: $redirect_url?status=error&msg=" . urlencode($resultado));
            exit;
        }
    }

    try {
        $stmt = $conexion->prepare("UPDATE productos 
                                    SET nombre = ?, descripcion = ?, precio = ?, stock = ?, id_categoria = ?, imagen_url = ? 
                                    WHERE id_producto = ?");
        $stmt->execute([
            $_POST['nombre'],
            $_POST['descripcion'],
            $_POST['precio'],
            $_POST['stock'],
            $_POST['id_categoria'],
            $nombre_imagen,
            $id_producto
        ]);

        header("Location: $redirect_url?status=actualizado");
        exit;

    } catch (PDOException $e) {
        header("Location: $redirect_url?status=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

// === ELIMINAR PRODUCTO ===
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {

    $id_producto = (int)$_GET['id'];

    try {
        // Obtener imagen actual
        $stmt_img = $conexion->prepare("SELECT imagen_url FROM productos WHERE id_producto = ?");
        $stmt_img->execute([$id_producto]);
        $img = $stmt_img->fetch(PDO::FETCH_ASSOC);

        if ($img && $img['imagen_url'] && $img['imagen_url'] != 'default_product.png' && file_exists("../Views/uploads/" . $img['imagen_url'])) {
            unlink("../Views/uploads/" . $img['imagen_url']);
        }

        // Eliminar producto
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);

        header("Location: $redirect_url?status=eliminado");
        exit;

    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if (strpos($msg, 'foreign key constraint') !== false) {
            header("Location: $redirect_url?status=error&msg=" . urlencode("No se puede eliminar el producto porque tiene ventas registradas."));
        } else {
            header("Location: $redirect_url?status=error&msg=" . urlencode($msg));
        }
        exit;
    }
}

// === ACCIÓN NO RECONOCIDA ===
header("Location: $redirect_url");
exit;
?>
