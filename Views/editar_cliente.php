<?php
session_start();
// Asegurar que solo el administrador pueda acceder
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require '../Modelo/conexion.php'; 

$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    header("Location: clientes.php?status=error&msg=ID_cliente_no_especificado");
    exit;
}

// Carga los datos del usuario
$stmt = $conexion->prepare("SELECT id, nombres_completos, correo_electronico, tipo_usuario FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: clientes.php?status=error&msg=Cliente_no_encontrado");
    exit;
}

$page_title = 'Editar Cliente: ' . htmlspecialchars($usuario['nombres_completos']);

// Obtener el estado y mensaje de la URL para mostrar alertas
$status = $_GET['status'] ?? '';
$msg = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/dashboard_admin.css"> 
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="contenedor">
        
        <?php include 'partials/admin_sidebar.php'; ?>

        <main class="contenido-principal">
            
            <header class="cabecera-principal">
                <h1><?= $page_title ?></h1>
                <div class="info-usuario">
                    <img src="../img/logo.jpg" alt="Admin" class="avatar"> 
                    <div>
                        <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
                        <p class="rol-usuario">Administrador</p>
                    </div>
                    </div>
            </header>
                
            
            <section class="contenido-pagina">

                <?php if (!empty($msg)): ?>
                    <?php 
                        $clase_alerta = ($status === 'success') ? 'alerta-exito' : 'alerta-error';
                        $msg_decodificada = htmlspecialchars(urldecode($msg));
                    ?>
                    <div class="alerta <?= $clase_alerta ?>">
                        <p><?= $msg_decodificada ?></p>
                    </div>
                <?php endif; ?>
                
                <a href="clientes.php" class="boton-accion cancelar" style="margin-bottom: 20px;">
                    <i data-lucide="arrow-left"></i> Volver a la Lista de Clientes
                </a>

                <div class="tarjeta-tabla" style="max-width: 700px; margin: 0 auto;">
                    
                    <div class="tarjeta-cabecera">
                        <h2>Información del Cliente</h2>
                    </div>

                    <form action="../Controller/admin_cliente_controlador.php" method="POST">
                        
                        <div class="formulario-crud">

                            <input type="hidden" name="accion" value="actualizar">
                            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id']) ?>">
                            
                            <div class="grupo-formulario-doble"> 
                                <div class="grupo-formulario">
                                    <label for="nombres_completos">Nombre Completo:</label>
                                    <input type="text" id="nombres_completos" name="nombres_completos" required 
                                        value="<?= htmlspecialchars($usuario['nombres_completos']) ?>">
                                </div>
                                
                                <div class="grupo-formulario">
                                    <label for="correo_electronico">Correo Electrónico:</label>
                                    <input type="email" id="correo_electronico" name="correo_electronico" required 
                                        value="<?= htmlspecialchars($usuario['correo_electronico']) ?>">
                                </div>
                            </div>
                            
                            <div class="grupo-formulario">
                                <label for="tipo_usuario">Rol del Cliente:</label>
                                <select id="tipo_usuario" name="tipo_usuario" required>
                                    <?php $current_tipo = $usuario['tipo_usuario']; ?>
                                    <option value="cliente" <?= $current_tipo === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                    <option value="administrador" <?= $current_tipo === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                                </select>
                            </div>
                            
                            <div class="acciones-formulario" style="border-top: none; padding-top: 10px;">
                                <button type="submit" class="boton-nuevo" style="width: 100%;">
                                    <i data-lucide="save"></i> Guardar Cambios del Cliente
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </section>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>