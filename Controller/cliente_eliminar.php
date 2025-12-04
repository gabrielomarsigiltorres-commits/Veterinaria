<?php
// 1. Iniciar sesión y verificar admin
session_start();
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../Views/login.php");
    exit;
}

// 2. Obtener ID del cliente
$id = $_GET['id'] ?? 0;
if ($id <= 0) {
    header("Location: ../Views/admin/clientes.php?msg=error_id");
    exit;
}

// 3. Conexión a la base de datos (PDO)
require '../Modelo was/conexion.php';

try {
    // Iniciar transacción
    $conexion->beginTransaction();

    // 4.1 Eliminar citas relacionadas con las mascotas del cliente
    $sql_citas = "DELETE FROM citas WHERE id_mascota IN (SELECT id FROM mascotas_cliente WHERE id_usuario = ?)";
    $stmt_citas = $conexion->prepare($sql_citas);
    $stmt_citas->execute([$id]);

    // 4.2 Eliminar mascotas del cliente
    $sql_mascotas = "DELETE FROM mascotas_cliente WHERE id_usuario = ?";
    $stmt_mascotas = $conexion->prepare($sql_mascotas);
    $stmt_mascotas->execute([$id]);

    // 4.3 Eliminar el cliente (usuario)
    $sql_usuario = "DELETE FROM usuarios WHERE id = ? AND tipo_usuario = 'cliente'";
    $stmt_usuario = $conexion->prepare($sql_usuario);
    $stmt_usuario->execute([$id]);

    if ($stmt_usuario->rowCount() === 1) {
        $conexion->commit();
        $mensaje = "Cliente eliminado correctamente.";
    } else {
        throw new Exception("Cliente no encontrado o ya eliminado.");
    }

} catch (Exception $e) {
    // Si hay algún error, revertimos los cambios
    $conexion->rollBack();
    $mensaje = "Error: " . $e->getMessage();
}

// 5. Redirección con mensaje
header("Location: ../Views/admin/clientes.php?msg=" . urlencode($mensaje));
exit;
?>
