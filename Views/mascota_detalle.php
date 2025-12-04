<?php
session_start();
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

$id_mascota = $_GET['id'] ?? 0;
require '../Modelo/conexion.php'; 

// 1. Obtener datos de la mascota y el nombre del cliente
$stmt = $conexion->prepare("
    SELECT mc.*, u.nombres_completos AS nombre_cliente
    FROM mascotas_cliente mc
    JOIN usuarios u ON mc.id_usuario = u.id
    WHERE mc.id = :id_mascota
");
$stmt->execute([':id_mascota' => $id_mascota]);
$mascota = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mascota) {
    header("Location: clientes.php?status=error&msg=mascota_no_encontrada");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Mascota - <?= htmlspecialchars($mascota['nombre']); ?></title>
    <link rel="stylesheet" href="css/dashboard_admin.css"> 
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="contenedor">
        <?php include 'partials/admin_sidebar.php'; ?>
        <main class="contenido-principal">
            
            <header class="cabecera-principal">
                <h1>Detalle de Mascota: <?= htmlspecialchars($mascota['nombre']); ?></h1>
                <div class="info-usuario">
                    <img src="../img/logo.jpg" alt="Admin" class="avatar"> 
                    <div>
                        <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
                        <p class="rol-usuario">Administrador</p>
                    </div>
                    </div>
            </header>
            
            <section class="contenido-pagina">
                
                <a href="cliente_detalle.php?id=<?= $mascota['id_usuario'] ?>" class="boton-accion cancelar" style="margin-bottom: 20px;">
                    <i data-lucide="arrow-left"></i> Volver a Cliente (<?= htmlspecialchars($mascota['nombre_cliente']); ?>)
                </a>

                <div class="tarjeta-tabla" style="max-width: 800px; margin: 0 auto;">
                    <div class="tarjeta-cabecera">
                        <h2>Información de <?= htmlspecialchars($mascota['nombre']); ?></h2>
                        <a href="registrar_mascota.php?id=<?= $mascota['id'] ?>" class="boton-accion editar">
                            <i data-lucide="edit"></i> Editar Mascota
                        </a>
                    </div>
                    <div class="tarjeta-cuerpo" style="padding: 24px;">
                        <div style="display: flex; gap: 30px; align-items: flex-start;">
                            <img src="uploads/<?= htmlspecialchars($mascota['imagen'] ?? 'default.jpg') ?>" 
                                 alt="<?= htmlspecialchars($mascota['nombre']) ?>" class="imagen-tabla" style="width: 150px; height: 150px; border-radius: 8px;">
                            
                            <div class="lista-detalles" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div style="font-size: 0.95rem;"><strong>Cliente:</strong> <span><?= htmlspecialchars($mascota['nombre_cliente']) ?></span></div>
                                <div style="font-size: 0.95rem;"><strong>ID Mascota:</strong> <span><?= htmlspecialchars($mascota['id']) ?></span></div>
                                <div style="font-size: 0.95rem;"><strong>Especie:</strong> <span><?= htmlspecialchars($mascota['especie']) ?></span></div>
                                <div style="font-size: 0.95rem;"><strong>Raza:</strong> <span><?= htmlspecialchars($mascota['raza']) ?></span></div>
                                <div style="font-size: 0.95rem;"><strong>Sexo:</strong> <span><?= htmlspecialchars($mascota['sexo']) ?></span></div>
                                <div style="font-size: 0.95rem;"><strong>Edad:</strong> <span><?= htmlspecialchars($mascota['edad']) ?> años</span></div>
                                <div style="font-size: 0.95rem;"><strong>Fecha Nacimiento:</strong> <span><?= htmlspecialchars($mascota['fecha_nacimiento']) ?></span></div>
                                <div style="font-size: 0.95rem; grid-column: span 2;"><strong>Alergias:</strong> <span><?= htmlspecialchars($mascota['alergias'] ?? 'Ninguna registrada') ?></span></div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>