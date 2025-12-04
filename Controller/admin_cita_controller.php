<?php
session_start();

// 1. Seguridad: Verificar sesión de administrador
// Usamos trim() para limpiar espacios en blanco
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    // Si falla la sesión, redirigir al login
    // Como estamos en /Controller, subimos a la raíz y entramos a Views
    header("Location: ../Views/login.php");
    exit;
}

require '../Modelo/conexion.php'; // Conexión PDO

// 2. Verificar parámetros
if (isset($_GET['id']) && isset($_GET['accion'])) {
    $id = $_GET['id'];
    $accion = $_GET['accion'];

    try {
        if ($accion == 'confirmar') {
            $sql = "UPDATE citas SET estado = 'Confirmada' WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id]);
            // Redirigir de vuelta a la vista de admin de citas
            header("Location: ../Views/citas_admin.php?msg=confirmada");
            exit;

        } elseif ($accion == 'cancelar') {
            $sql = "UPDATE citas SET estado = 'Cancelada' WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id]);
            header("Location: ../Views/citas_admin.php?msg=cancelada");
            exit;

        } elseif ($accion == 'eliminar') {
            $sql = "DELETE FROM citas WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id]);
            header("Location: ../Views/citas_admin.php?msg=eliminada");
            exit;
        }
        
    } catch (PDOException $e) {
        // Si hay error, redirigir con mensaje de error
        header("Location: ../Views/citas_admin.php?msg=error");
        exit;
    }

} else {
    // Si faltan parámetros, volver a la lista
    header("Location: ../Views/citas_admin.php");
    exit;
}
?>
