<?php
session_start();
// Asegurar que solo el administrador pueda acceder
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
require '../Modelo/conexion.php'; 

// 1. Detección de Modo: ¿Es edición o registro?
$is_editing = isset($_GET['id']) && $_GET['id'] > 0;
$mascota = []; // Inicializar array para datos de la mascota
$cliente_id = null;
$nombre_cliente = 'Cliente';

// --- Lógica de Carga de Datos (Existente) ---
if ($is_editing) {
    $id_mascota = $_GET['id'];
    $stmt = $conexion->prepare("SELECT * FROM mascotas_cliente WHERE id = :id");
    $stmt->execute([':id' => $id_mascota]);
    $mascota = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mascota) {
        header("Location: clientes.php?status=error&msg=mascota_no_encontrada");
        exit;
    }
    $cliente_id = $mascota['id_usuario'];
} else {
    $cliente_id = $_GET['cliente_id'] ?? null;
    if (!$cliente_id) {
        header("Location: clientes.php?status=error&msg=cliente_no_especificado");
        exit;
    }
}

// 2. Obtener el nombre del cliente (Existente)
if ($cliente_id) {
    $stmt_c = $conexion->prepare("SELECT nombres_completos FROM usuarios WHERE id = :id");
    $stmt_c->execute([':id' => $cliente_id]);
    $cliente = $stmt_c->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        header("Location: clientes.php?status=error&msg=cliente_no_encontrado");
        exit;
    }
    $nombre_cliente = $cliente['nombres_completos'];
}


// 3. Título dinámico (Existente)
$page_title = $is_editing ? 'Editar Mascota: ' . htmlspecialchars($mascota['nombre']) : 'Registrar Nueva Mascota';
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
                
                <a href="cliente_detalle.php?id=<?= htmlspecialchars($cliente_id) ?>" class="boton-accion cancelar" style="margin-bottom: 20px;">
                    <i data-lucide="arrow-left"></i> Volver a Cliente: <?= htmlspecialchars($nombre_cliente); ?>
                </a>

                <div class="tarjeta-tabla" style="max-width: 600px; margin: 0 auto;">
                    
                    <div class="tarjeta-cabecera">
                        <h2>Datos de la Mascota</h2>
                    </div>

                    <form action="../Controller/admin_mascota_controlador.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="formulario-crud">

                            <input type="hidden" name="accion" value="<?= $is_editing ? 'actualizar' : 'guardar' ?>">
                            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($cliente_id) ?>">
                            
                            <?php if ($is_editing): ?>
                                <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id']) ?>">
                                <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($mascota['imagen'] ?? '') ?>">
                            <?php endif; ?>

                            <div class="grupo-formulario-doble"> 
                                <div class="grupo-formulario">
                                    <label for="nombre">Nombre:</label>
                                    <input type="text" id="nombre" name="nombre" required 
                                        value="<?= htmlspecialchars($mascota['nombre'] ?? '') ?>">
                                </div>
                                
                                <div class="grupo-formulario">
                                    <label for="edad">Edad (años):</label>
                                    <input type="number" id="edad" name="edad" required min="0"
                                        value="<?= htmlspecialchars($mascota['edad'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="grupo-formulario-doble">
                                <div class="grupo-formulario">
                                    <label for="especie">Especie:</label>
                                    <select id="especie" name="especie" required>
                                        <?php $current_especie = $mascota['especie'] ?? ''; ?>
                                        <option value="">Seleccione especie</option>
                                        <option value="Perro" <?= $current_especie === 'Perro' ? 'selected' : '' ?>>Perro</option>
                                        <option value="Gato" <?= $current_especie === 'Gato' ? 'selected' : '' ?>>Gato</option>
                                        <option value="Ave" <?= $current_especie === 'Ave' ? 'selected' : '' ?>>Ave</option>
                                        <option value="Otro" <?= $current_especie === 'Otro' ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>

                                <div class="grupo-formulario">
                                    <label for="raza">Raza:</label>
                                    <input type="text" id="raza" name="raza" required 
                                        value="<?= htmlspecialchars($mascota['raza'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="grupo-formulario-doble">
                                <div class="grupo-formulario">
                                    <label>Sexo:</label>
                                    <?php $current_sexo = $mascota['sexo'] ?? ''; ?>
                                    <div class="radio-group-custom"> 
                                        <input type="radio" id="macho" name="sexo" value="Macho" required 
                                            <?= $current_sexo === 'Macho' ? 'checked' : '' ?>>
                                        <label for="macho">Macho</label>

                                        <input type="radio" id="hembra" name="sexo" value="Hembra" 
                                            <?= $current_sexo === 'Hembra' ? 'checked' : '' ?>>
                                        <label for="hembra">Hembra</label>
                                    </div>
                                </div>
                                
                                <div class="grupo-formulario">
                                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" 
                                        value="<?= htmlspecialchars($mascota['fecha_nacimiento'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="grupo-formulario">
                                <label for="alergias">Alergias / Notas importantes:</label>
                                <textarea id="alergias" name="alergias" rows="3"><?= htmlspecialchars($mascota['alergias'] ?? '') ?></textarea>
                            </div>

                            <div class="grupo-formulario">
                                <label for="imagen">Foto de la Mascota:</label>
                                <input type="file" id="imagen" name="imagen" accept="image/*">
                                
                                <?php if ($is_editing && ($mascota['imagen'] ?? null)): ?>
                                    <div class="info-imagen-actual">
                                        <img src="uploads/<?= htmlspecialchars($mascota['imagen']) ?>" alt="Foto actual" style="width: 50px; height: 50px; object-fit: cover;">
                                        <span>(Deje vacío si no desea cambiar la imagen actual)</span>
                                    </div>
                                <?php else: ?>
                                    <small style="color: var(--color-error); font-size: 0.8rem;">(Es obligatorio subir una foto al registrar)</small>
                                <?php endif; ?>
                            </div>

                            <div class="acciones-formulario" style="border-top: none; padding-top: 0;">
                                <button type="submit" class="boton-nuevo" style="width: 100%;">
                                    <i data-lucide="<?= $is_editing ? 'save' : 'plus' ?>"></i> 
                                    <?= $is_editing ? 'Guardar Cambios' : 'Registrar Mascota' ?>
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