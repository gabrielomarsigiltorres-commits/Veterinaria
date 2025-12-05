<?php
session_start();

// 1. Seguridad: Verificar admin
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    header("Location: ../Views/login.php");
    exit;
}

require '../Modelo/conexion.php'; 

// 2. Procesar Acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // --- REGISTRAR NUEVA VENTA ---
    if ($accion == 'registrar_venta') {
        $id_usuario_cliente = !empty($_POST['id_usuario_cliente']) ? $_POST['id_usuario_cliente'] : null;
        $id_producto = $_POST['id_producto'];
        $cantidad = (int)$_POST['cantidad'];
        $metodo_pago_seleccionado = $_POST['metodo_pago']; // 'efectivo', 'yape', 'tarjeta'
        
        $tipo_venta = 'local'; 
        $id_usuario_admin = $_SESSION['usuario_id']; // ID del admin logueado (Asegúrate de tener esto en sesión)
        
        // Determinar "Referencia" o "Detalle" según el método
        // Nota: Como tu BD no tiene campo 'metodo_pago' ni 'detalle', 
        // guardaremos todo concatenado en un log o simplemente asumimos que si es local se procesó.
        // Pero para ser ordenados, intentaremos guardar algo si fuera posible.
        
        // LOGICA DE DATOS ADICIONALES (Simulada para guardar contexto)
        $referencia_interna = "";
        if ($metodo_pago_seleccionado == 'yape') {
            $referencia_interna = "Yape Ref: " . ($_POST['ref_yape'] ?? 'Sin ref');
        } elseif ($metodo_pago_seleccionado == 'tarjeta') {
            // Solo guardamos los ultimos 4 digitos por seguridad
            $num_tarjeta = $_POST['card_number'] ?? '0000';
            $last4 = substr(str_replace(' ', '', $num_tarjeta), -4);
            $referencia_interna = "Tarjeta ****" . $last4;
        } else {
            $referencia_interna = "Pago Efectivo";
        }

        try {
            $conexion->beginTransaction();

            // A) Verificar Stock y Precio
            $stmt = $conexion->prepare("SELECT stock, precio FROM productos WHERE id_producto = ?");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                throw new Exception("El producto no existe.");
            }

            if ($producto['stock'] < $cantidad) {
                throw new Exception("Stock insuficiente. Solo quedan " . $producto['stock'] . " unidades.");
            }

            // B) Calcular Total
            $precio_unitario = $producto['precio'];
            $total_venta = $precio_unitario * $cantidad;

            // C) Insertar Venta
            // Usamos la estructura de tu BD actual:
            // id_producto, id_usuario_cliente, id_usuario_admin, cantidad, precio_unitario, total_venta, tipo_venta, fecha_venta
            
            // Nota: Si agregaste columnas 'metodo_pago' o 'referencia' a tu BD, agrégalos aquí.
            // Si no, se guarda lo básico.
            $sqlVenta = "INSERT INTO ventas (id_producto, id_usuario_cliente, id_usuario_admin, cantidad, precio_unitario, total_venta, tipo_venta, fecha_venta) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmtVenta = $conexion->prepare($sqlVenta);
            
            $stmtVenta->execute([
                $id_producto, 
                $id_usuario_cliente, 
                $id_usuario_admin, 
                $cantidad, 
                $precio_unitario, 
                $total_venta, 
                $tipo_venta
            ]);

            // D) Descontar Stock
            $sqlUpdate = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->execute([$cantidad, $id_producto]);

            $conexion->commit();
            header("Location: ../Views/admin_productos.php?msg=venta_ok");
            exit;

        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            $error = urlencode($e->getMessage());
            header("Location: ../Views/admin_productos.php?msg=error&info=$error");
            exit;
        }
    }
}

// Redirección por defecto
header("Location: ../Views/admin_productos.php");
exit;
?>