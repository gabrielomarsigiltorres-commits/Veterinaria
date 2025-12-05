<?php
session_start();
require_once __DIR__ . '/../Modelo/conexion.php';
require_once __DIR__ . '/../Modelo/ServicioModelo.php';

class ServicioController {
    
    private $model;

    public function __construct() {
        $this->model = new ServicioModelo();
    }

    // ✅ MÉTODO PÚBLICO: Para mostrar en la web del cliente
    public function listarPublico() {
        return $this->model->listarPublico();
    }

    // ✅ MÉTODO PRIVADO: Lógica de subida de imágenes
    private function subirImagen($archivo) {
        if (isset($archivo) && $archivo['error'] === UPLOAD_ERR_OK) {
            $nombreOriginal = basename($archivo['name']);
            $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($extension, $permitidos)) {
                $nombreFinal = 'serv_' . uniqid() . '.' . $extension;
                // Ajuste de ruta: asumiendo que el controller está en Controller/ y uploads en Views/uploads/
                $rutaDestino = __DIR__ . '/../Views/uploads/' . $nombreFinal;

                if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                    return $nombreFinal;
                }
            }
        }
        return null;
    }

    // ✅ MÉTODO ADMIN: Maneja Crear, Editar, Eliminar (Mantiene tu lógica original)
    public function procesarSolicitudAdmin() {
        global $conexion; // Usamos la conexión del require 'conexion.php'

        // 1. Seguridad: Solo admin
        if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
            header("Location: ../Views/login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // --- CREAR SERVICIO ---
            if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
                $nombre = $_POST['nombre'];
                $descripcion = $_POST['descripcion'];
                $precio = $_POST['precio'];
                $categoria = $_POST['categoria'];
                
                $imagen_url = 'default_service.png';
                if (!empty($_FILES['imagen']['name'])) {
                    $subida = $this->subirImagen($_FILES['imagen']);
                    if ($subida) $imagen_url = $subida;
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
                    $stmtActual = $conexion->prepare("SELECT imagen_url FROM servicios WHERE id = ?");
                    $stmtActual->execute([$id]);
                    $servicioActual = $stmtActual->fetch(PDO::FETCH_ASSOC);
                    $imagen_url = $servicioActual['imagen_url'];

                    if (!empty($_FILES['imagen']['name'])) {
                        $nuevaImagen = $this->subirImagen($_FILES['imagen']);
                        if ($nuevaImagen) {
                            if ($imagen_url && $imagen_url !== 'default_service.png' && file_exists(__DIR__ . "/../Views/uploads/" . $imagen_url)) {
                                unlink(__DIR__ . "/../Views/uploads/" . $imagen_url);
                            }
                            $imagen_url = $nuevaImagen;
                        }
                    }

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
                $stmtImg = $conexion->prepare("SELECT imagen_url FROM servicios WHERE id = ?");
                $stmtImg->execute([$id]);
                $img = $stmtImg->fetchColumn();

                if ($img && $img !== 'default_service.png' && file_exists(__DIR__ . "/../Views/uploads/" . $img)) {
                    unlink(__DIR__ . "/../Views/uploads/" . $img);
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
    }
}

// 🔥 AUTO-EJECUCIÓN PARA EL ADMIN:
// Si detectamos que se está enviando un formulario o pidiendo eliminar, instanciamos y procesamos.
if (isset($_POST['accion']) || (isset($_GET['accion']) && $_GET['accion'] === 'eliminar')) {
    $controller = new ServicioController();
    $controller->procesarSolicitudAdmin();
}
?>