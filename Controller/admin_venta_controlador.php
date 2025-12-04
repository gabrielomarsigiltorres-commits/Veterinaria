<?php
session_start();

// Seguridad: Solo administradores pueden ejecutar esto
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: ../Views/login.php");
    exit;
}

// Conexión con PDO (ruta al modelo)
require '../Modelo/conexion.php';

// Página a la que regresaremos
$redirect_url = "../Views/admin_productos.php";

// Verificar que sea una venta rápida
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'venta_rapida') {

    $id_producto = (int)$_POST['producto_id'];
    $cantidad_vendida = (int)$_POST['cantidad'];
    $id_admin = (int)$_SESSION['usuario_id']; // Rastrear quién hizo la venta

    if ($cantidad_vendida <= 0 || $id_producto <= 0) {
        header("Location: $redirect_url?status=error&msg=" . urlencode("Datos de venta inválidos."));
        exit;
    }

    try {
        // Iniciar transacción con PDO
        $conexion->beginTransaction();

        // 1. Obtener el stock actual y bloquear la fila para evitar concurrencia
        $stmt_select = $conexion->prepare("SELECT stock, precio FROM productos WHERE id_producto = ? FOR UPDATE");
        $stmt_select->execute([$id_producto]);
        $producto = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            throw new Exception("Producto no encontrado.");
        }

        $stock_actual = (int)$producto['stock'];
        $precio_unitario = (float)$producto['precio'];

        // 2. Verificar si hay stock suficiente
        if ($stock_actual < $cantidad_vendida) {
            // Revertimos la transacción y enviamos error
            $conexion->rollBack();
            header("Location: $redirect_url?status=no_stock&msg=" . urlencode("Solo quedan {$stock_actual} unidades."));
            exit;
        }

        // 3. Actualizar el stock
        $nuevo_stock = $stock_actual - $cantidad_vendida;
        $stmt_update = $conexion->prepare("UPDATE productos SET stock = ? WHERE id_producto = ?");
        $stmt_update->execute([$nuevo_stock, $id_producto]);

        // 4. Registrar la venta en la tabla 'ventas'
        $total_venta = $precio_unitario * $cantidad_vendida;
        $stmt_venta = $conexion->prepare("INSERT INTO ventas (id_producto, id_usuario_admin, cantidad, precio_unitario, total_venta, tipo_venta, fecha_venta) 
                                          VALUES (?, ?, ?, ?, ?, 'local', NOW())");
        $stmt_venta->execute([$id_producto, $id_admin, $cantidad_vendida, $precio_unitario, $total_venta]);

        // 5. Confirmar la transacción
        $conexion->commit();

        header("Location: $redirect_url?status=venta_ok");
        exit;

    } catch (Exception $e) {
        // Si algo falla, revertir todo
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        header("Location: $redirect_url?status=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

// Si no se reconoce ninguna acción
header("Location: $redirect_url");
exit;
?>
