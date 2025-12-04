<?php
session_start();
// 1. Verificar la sesión del administrador
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require '../Modelo/conexion.php'; 

// --- INICIO LÓGICA DE FILTROS ---
$buscar = $_GET['buscar'] ?? '';
$parametros = [];

// 2. Construcción de la Consulta para obtener clientes con el conteo de mascotas
$sql = "
    SELECT 
        u.id, 
        u.nombres_completos, 
        u.correo_electronico, 
        (SELECT COUNT(m.id) FROM mascotas_cliente m WHERE m.id_usuario = u.id) AS total_mascotas
    FROM usuarios u
    WHERE u.tipo_usuario = 'cliente' 
";

// Aplicar filtro de búsqueda por nombre o correo
if (!empty($buscar)) {
    $sql .= " AND (u.nombres_completos LIKE :buscar_nombre OR u.correo_electronico LIKE :buscar_correo) ";
    $parametros[':buscar_nombre'] = '%' . $buscar . '%';
    $parametros[':buscar_correo'] = '%' . $buscar . '%';
}

$sql .= " ORDER BY u.nombres_completos ASC";

// --- FIN LÓGICA DE FILTROS ---

try {
    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros); // Ejecutar con los parámetros de búsqueda
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo de error si la base de datos falla por otra razón
    die("Error al cargar clientes: " . $e->getMessage());
}

// Obtener el estado y mensaje de la URL (si existen)
$status = $_GET['status'] ?? '';
$msg = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - Admin</title>
    <link rel="stylesheet" href="css/dashboard_admin.css"> 
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        /* Estilos específicos para el contenedor de filtros, usando variables del CSS global */
        .filtro-clientes-form {
            margin-bottom: 24px;
            background: var(--color-tarjeta);
            padding: 16px 20px;
            border-radius: 8px;
            border: 1px solid var(--color-borde);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .filtro-clientes-form .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filtro-clientes-form input[type="text"] {
            flex-grow: 1;
            /* Reutiliza estilos de input del formulario CRUD */
            padding: 10px 12px;
            border: 1px solid var(--color-borde);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
        }
        /* Ajuste de altura y estilos para el botón de buscar y limpiar */
        .filtro-clientes-form .boton-nuevo,
        .filtro-clientes-form .limpiar-filtro {
            height: 42px; /* Para alinear visualmente con el input */
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            font-weight: 600;
        }
        /* Estilo para el botón de limpiar (usa color de error) */
        .filtro-clientes-form .limpiar-filtro {
            background-color: var(--color-error); 
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px; /* Debe coincidir con boton-nuevo */
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .filtro-clientes-form .limpiar-filtro:hover {
            background-color: #c82333; 
        }
    </style>
</head>
<body>
    <div class="contenedor">
        
        <?php include 'partials/admin_sidebar.php'; ?>
        
        <main class="contenido-principal">
            <header class="cabecera-principal">
                <h1>Gestión de Clientes</h1>
                <div class="info-usuario">
                    <img src="../img/logo.jpg" alt="Admin" class="avatar">
                    <div>
                        <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
                        <p class="rol-usuario">Administrador</p>
                    </div>
                </div>
            </header>
            <?php
            if (!empty($msg)): 
                // Determinar la clase CSS basada en el estado
                $clase_alerta = ($status === 'success') ? 'alerta-exito' : 'alerta-error';
            ?>
                <div class="alerta <?= $clase_alerta ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 5px; font-weight: bold;">
                    <p><?= htmlspecialchars(urldecode($msg)) ?></p>
                </div>
            <?php endif; ?>
            
            <section class="contenido-pagina">
                
                <form action="clientes.php" method="GET" class="filtro-clientes-form">
                    <div class="input-group">
                        
                        <input type="text" name="buscar" placeholder="Buscar por Nombre o Correo Electrónico" value="<?= htmlspecialchars($buscar) ?>">
                        
                        <button type="submit" class="boton-nuevo">
                            <i data-lucide="search" style="width: 18px; margin-right: 4px;"></i> Buscar
                        </button>
                        
                        <?php if (!empty($buscar)): ?>
                            <a href="clientes.php" class="limpiar-filtro">
                                Limpiar Filtros
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                
                <div class="tarjeta-tabla">
                    
                    <div class="tarjeta-cabecera">
                        <h2>Listado de Clientes</h2>
                        <a href="cliente_form.php" class="boton-nuevo">
                            <i data-lucide="plus"></i> Añadir Cliente
                        </a>
                    </div>

                    <div class="contenedor-tabla">
                        <table>
                            <thead>
                                <tr>
                                    <th>NOMBRE COMPLETO</th>
                                    <th>ID / REFERENCIA</th>
                                    <th>CONTACTO (Correo)</th>
                                    <th>MASCOTAS</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($clientes) > 0): ?>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($cliente['nombres_completos']) ?></strong></td>
                                            
                                            <td><?= htmlspecialchars($cliente['id']) ?></td>
                                            
                                            <td><?= htmlspecialchars($cliente['correo_electronico']) ?></td>
                                            
                                            <td><?= htmlspecialchars($cliente['total_mascotas']) ?></td>
                                            
                                            <td class="acciones">
                                                <a href="cliente_detalle.php?id=<?= $cliente['id'] ?>" class="boton-accion editar">Editar</a>
                                                <a href="../Controller/admin_cliente_controlador.php?accion=eliminar&id=<?= $cliente['id']; ?>" 
                                                     class="boton-accion eliminar" 
                                                     onclick="return confirm('¿Seguro que deseas eliminar este cliente?')">Eliminar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="sin-datos">No hay clientes registrados que coincidan con la búsqueda.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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