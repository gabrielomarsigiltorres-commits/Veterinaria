<?php
session_start();
// 1. Verificar la sesión del administrador
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php"); 
    exit;
}

$id = $_GET['id'] ?? 0;
require '../Modelo/conexion.php'; 

// Obtener datos del cliente
$stmt = $conexion->prepare("
    SELECT id, nombres_completos, correo_electronico 
    FROM usuarios 
    WHERE id = :id AND tipo_usuario = 'cliente'
");
$stmt->execute([':id' => $id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    header("Location: clientes.php?status=error&msg=cliente_no_encontrado");
    exit;
}

// Obtener las mascotas del cliente
$stmt_m = $conexion->prepare("SELECT id, nombre, especie, raza, sexo, edad, imagen FROM mascotas_cliente WHERE id_usuario = :id");
$stmt_m->execute([':id' => $id]);
$mascotas = $stmt_m->fetchAll(PDO::FETCH_ASSOC);

// Variable para el nombre del admin
// Usamos nombres_completos si existe, si no, usuario_nombre, o Administrador
$nombre_admin = $_SESSION['nombres_completos'] ?? $_SESSION['usuario_nombre'] ?? 'Administrador';
// Usaremos la variable que usas en el dashboard: $_SESSION['usuario_nombre']
$nombre_admin_display = $_SESSION['usuario_nombre'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Cliente - <?= htmlspecialchars($cliente['nombres_completos']); ?></title>
    <link rel="stylesheet" href="css/dashboard_admin.css"> 
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="contenedor">
        
        <?php include 'partials/admin_sidebar.php'; ?>

        <main class="contenido-principal">
            
            <header class="cabecera-principal">
                <h1>Detalle del Cliente: <?= htmlspecialchars($cliente['nombres_completos']); ?></h1>
                
                <div class="info-usuario">
                    <img src="../img/logo.jpg" alt="Admin" class="avatar">
                    <div>
                        <p class="nombre-usuario"><?php echo htmlspecialchars($nombre_admin_display); ?></p>
                        <p class="rol-usuario">Administrador</p>
                    </div>
                </div>
                </header>
                
            
            <section class="contenido-pagina">
                
                <a href="clientes.php" class="boton-accion cancelar" style="margin-bottom: 20px;">
                    <i data-lucide="arrow-left"></i> Volver a Clientes
                </a>

                <div class="layout-columnas"> 
                    
                    <div class="tarjeta-tabla columna-izquierda"> 
                        <div class="tarjeta-cabecera">
                            <h2>Datos Personales</h2>
                            <a href="editar_cliente.php?id=<?= $cliente['id'] ?>" class="boton-accion editar"> 
                                <i data-lucide="edit"></i> Editar
                            </a>
                        </div>
                        <div class="tarjeta-cuerpo" style="padding: 24px;">
                            
                            <div class="lista-detalles" style="display: flex; flex-direction: column; gap: 10px;">
                                <div style="font-size: 1.1rem; margin-bottom: 10px;"><strong>Nombre:</strong> <span><?= htmlspecialchars($cliente['nombres_completos']) ?></span></div>
                                <div style="font-size: 0.95rem;"><strong>ID Cliente:</strong> <span><?= htmlspecialchars($cliente['id']) ?></span></div>
                                <div style="font-size: 0.95rem;"><strong>Correo:</strong> <span><?= htmlspecialchars($cliente['correo_electronico']) ?></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="tarjeta-tabla columna-derecha">
                        <div class="tarjeta-cabecera">
                            <h2>Mascotas Registradas</h2>
                            <a href="registrar_mascota.php?cliente_id=<?= $cliente['id'] ?>" class="boton-nuevo">
                                <i data-lucide="plus"></i> Añadir
                            </a>
                        </div>
                        <div class="contenedor-tabla">
                            <?php if (count($mascotas) > 0): ?>
                                <?php foreach ($mascotas as $m): ?>
                                    <div class="item-lista">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <img src="uploads/<?= htmlspecialchars($m['imagen'] ?? 'default.jpg') ?>" 
                                                     alt="<?= htmlspecialchars($m['nombre']) ?>" class="imagen-tabla">
                                            <div class="info-mascota">
                                                <strong><?= htmlspecialchars($m['nombre']) ?></strong> 
                                                <small style="color: var(--color-texto-secundario);">(<?= htmlspecialchars($m['raza']) ?>)</small>
                                                <br><small style="color: var(--color-texto-secundario);">Edad: <?= htmlspecialchars($m['edad']) ?> años</small>
                                            </div>
                                        </div>
                                        <div class="acciones-mascota">
                                            <a href="mascota_detalle.php?id=<?= $m['id'] ?>" class="boton-accion editar small">Ver Detalle</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="sin-datos" style="padding: 20px; text-align: center; color: var(--color-texto-secundario);">Este cliente no tiene mascotas registradas.</p>
                            <?php endif; ?>
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