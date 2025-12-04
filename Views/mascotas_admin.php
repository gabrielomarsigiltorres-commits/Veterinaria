<?php
session_start();
// 1. Verificar la sesión del administrador
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require '../Modelo/conexion.php'; 

// Inicializar variables de estado y filtros
$status = $_GET['status'] ?? '';
$msg = $_GET['msg'] ?? '';

// --- INICIO LÓGICA DE FILTROS ---

$buscar = $_GET['buscar'] ?? '';
$filtro_especie = $_GET['filtro_especie'] ?? '';

// Obtener todas las especies disponibles para el filtro
try {
    $stmt_especies = $conexion->query("SELECT DISTINCT especie FROM mascotas_cliente WHERE especie IS NOT NULL ORDER BY especie ASC");
    $especies_disponibles = $stmt_especies->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // Si falla la consulta, la lista de especies estará vacía.
    $especies_disponibles = []; 
}

// 2. Construcción de la Consulta para obtener mascotas
$sql = "SELECT 
            m.id AS id_mascota,
            m.nombre AS nombre_mascota,
            m.especie,
            m.raza,
            m.fecha_nacimiento, 
            m.imagen,
            u.id AS id_usuario,
            u.nombres_completos AS nombre_dueno
        FROM 
            mascotas_cliente m
        LEFT JOIN 
            usuarios u ON m.id_usuario = u.id
        WHERE 1=1 "; // Inicia la cláusula WHERE

$parametros = [];

// Aplicar filtro de búsqueda por nombre de mascota o dueño
if (!empty($buscar)) {
    $sql .= " AND (m.nombre LIKE :buscar OR u.nombres_completos LIKE :buscar_dueno) ";
    $parametros[':buscar'] = '%' . $buscar . '%';
    $parametros[':buscar_dueno'] = '%' . $buscar . '%'; // Usar el mismo valor de búsqueda
}

// Aplicar filtro por especie
if (!empty($filtro_especie) && $filtro_especie !== 'Todos') {
    $sql .= " AND m.especie = :filtro_especie ";
    $parametros[':filtro_especie'] = $filtro_especie;
}

$sql .= " ORDER BY m.id DESC";

// --- FIN LÓGICA DE FILTROS ---

try {
    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);
    $mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar mascotas: " . $e->getMessage());
}

// Función auxiliar para calcular la edad (se mantiene)
if (!function_exists('calcularEdad')) {
    function calcularEdad($fecha_nacimiento) {
        if (empty($fecha_nacimiento)) return 'N/A';
        
        $fecha_nacimiento = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $diferencia = $hoy->diff($fecha_nacimiento);
        
        if ($diferencia->y > 0) {
            return $diferencia->y . ' año' . ($diferencia->y > 1 ? 's' : '');
        } elseif ($diferencia->m > 0) {
            return $diferencia->m . ' mes' . ($diferencia->m > 1 ? 'es' : '');
        } else {
            return $diferencia->d . ' día' . ($diferencia->d != 1 ? 's' : '');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mascotas - Admin</title>
    <link rel="stylesheet" href="css/dashboard_admin.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> 
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        .imagen-mascota {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #ccc;
        }
        .boton-accion.historial {
            background-color: #3498db;
            color: white;
        }
        .boton-accion.editar {
            background-color: #f39c12;
            color: white;
        }
        /* Estilos para el contenedor de filtros */
        .contenedor-filtros {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .contenedor-filtros input, .contenedor-filtros select, .contenedor-filtros button {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .contenedor-filtros button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        
        <?php include 'partials/admin_sidebar.php'; ?>
        
        <main class="contenido-principal">
            <header class="cabecera-principal">
                <h1>Gestión de Mascotas</h1>
                <div class="info-usuario">
                    <img src="../img/logo.jpg" alt="Admin" class="avatar">
                    <div>
                        <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador'); ?></p>
                        <p class="rol-usuario">Administrador</p>
                    </div>
                </div>
            </header>
            
            <?php
            // Muestra mensajes de estado
            if (!empty($msg)): 
                $clase_alerta = ($status === 'success') ? 'alerta-exito' : 'alerta-error';
            ?>
                <div class="alerta <?= $clase_alerta ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 5px; font-weight: bold;">
                    <p><?= htmlspecialchars(urldecode($msg)) ?></p>
                </div>
            <?php endif; ?>
            
            <section class="contenido-pagina">
                
                <form action="mascotas_admin.php" method="GET" class="contenedor-filtros">
                    
                    <input type="text" name="buscar" placeholder="Buscar por nombre o dueño" value="<?= htmlspecialchars($buscar) ?>" style="flex-grow: 1;">
                    
                    <label for="filtro_especie" style="margin: 0;">Filtrar por Especie:</label>
                    <select name="filtro_especie" id="filtro_especie">
                        <option value="Todos" <?= $filtro_especie === 'Todos' ? 'selected' : '' ?>>Todas</option>
                        <?php foreach ($especies_disponibles as $especie): ?>
                            <option value="<?= htmlspecialchars($especie) ?>" <?= $filtro_especie === $especie ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($especie)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit">Buscar/Filtrar</button>
                    
                    <?php if (!empty($buscar) || !empty($filtro_especie)): ?>
                        <a href="mascotas_admin.php" class="boton-accion" style="background-color: #dc3545;">Limpiar Filtros</a>
                    <?php endif; ?>
                </form>
                
                <div class="tarjeta-tabla">
                    
                    <div class="tarjeta-cabecera">
                        <h2>Listado de Mascotas</h2>
                        <a href="clientes.php" class="boton-nuevo"> 
                            <i data-lucide="plus"></i> Añadir Mascota (Seleccionar Dueño)
                        </a>
                    </div>

                    <div class="contenedor-tabla">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 5%;">ID</th>
                                    <th style="width: 5%;">FOTO</th>
                                    <th style="width: 20%;">NOMBRE</th>
                                    <th style="width: 15%;">ESPECIE</th>
                                    <th style="width: 10%;">RAZA</th>
                                    <th style="width: 10%;">EDAD</th>
                                    <th style="width: 20%;">DUEÑO</th>
                                    <th style="width: 15%;">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($mascotas) > 0): ?>
                                    <?php foreach ($mascotas as $mascota): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($mascota['id_mascota']) ?></td>
                                            <td>
                                                <?php 
                                                // La ruta de la imagen debe ser relativa a 'Views/'
                                                $imagen_path = !empty($mascota['imagen']) ? 'uploads/' . htmlspecialchars($mascota['imagen']) : 'assets/img/default_pet.png';
                                                ?>
                                                <img src="<?= $imagen_path ?>" alt="<?= htmlspecialchars($mascota['nombre_mascota']) ?>" 
                                                     class="imagen-mascota"
                                                     onerror="this.onerror=null; this.src='assets/img/default_pet.png';">
                                            </td>
                                            <td><strong><?= htmlspecialchars($mascota['nombre_mascota']) ?></strong></td>
                                            <td><?= htmlspecialchars($mascota['especie']) ?></td>
                                            <td><?= htmlspecialchars($mascota['raza'] ?? 'N/A') ?></td>
                                            <td><?= calcularEdad($mascota['fecha_nacimiento']) ?></td>
                                            <td>
                                                <a href="cliente_detalle.php?id=<?= htmlspecialchars($mascota['id_usuario']) ?>">
                                                    <?= htmlspecialchars($mascota['nombre_dueno'] ?? 'N/A') ?>
                                                </a>
                                            </td>
                                            <td class="acciones">
                                                <a href="#" 
                                                   class="boton-accion historial btn-historial" 
                                                   data-toggle="modal" 
                                                   data-target="#historialModal" 
                                                   data-id="<?= htmlspecialchars($mascota['id_mascota']) ?>"
                                                >
                                                    Historial
                                                </a>
                                                <a href="mascota_detalle.php?id=<?= htmlspecialchars($mascota['id_mascota']) ?>" class="boton-accion editar">
                                                    Editar
                                                </a>
                                                <a href="../Controller/admin_mascota_controlador.php?accion=eliminar&id=<?= htmlspecialchars($mascota['id_mascota']) ?>" 
                                                     class="boton-accion eliminar" 
                                                     onclick="return confirm('¿Seguro que deseas eliminar a <?= htmlspecialchars($mascota['nombre_mascota']) ?>?')">Eliminar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="sin-datos">No hay mascotas registradas que coincidan con los filtros.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div class="modal fade" id="historialModal" tabindex="-1" role="dialog" aria-labelledby="historialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="p-5 text-center">
                    <p style="font-size: 1.2em;">Cargando historial...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        lucide.createIcons();

        // SCRIPT AJAX para cargar el historial 
        $(document).ready(function() {
            $('#historialModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var idMascota = button.data('id');

                var modal = $(this);
                var modalContent = modal.find('.modal-content');

                // Mostrar mensaje de carga
                modalContent.html('<div class="p-5 text-center"><p style="font-size: 1.2em;">Cargando historial...</p></div>');

                // Llamada AJAX
                $.ajax({
                    url: '../Controller/fetch_historial_data.php', 
                    type: 'POST',
                    data: { id_mascota: idMascota },
                    success: function(response) {
                        modalContent.html(response);
                    },
                    error: function() {
                        modalContent.html('<div class="alert alert-danger m-3">Error al cargar el historial clínico. Por favor, inténtelo de nuevo.</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>