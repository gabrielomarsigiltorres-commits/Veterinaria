<?php
// ======================================================================
// 1. CONFIGURACIÓN Y CONEXIÓN A LA BASE DE DATOS
// ======================================================================
$host = 'localhost'; 
$db   = 'veterinaria'; // Nombre de tu base de datos
$user = 'root'; // ¡REEMPLAZAR!
$pass = ''; // ¡REEMPLAZAR!

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Error de conexión a la Base de Datos: " . $e->getMessage()); 
}


// ======================================================================
// 2. LÓGICA DE PROCESAMIENTO (POST: CREAR, EDITAR Y ELIMINAR)
// ======================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- LÓGICA PARA ELIMINAR ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $idToDelete = $_POST['id'] ?? null;
        if ($idToDelete && is_numeric($idToDelete)) {
            $stmt = $pdo->prepare("DELETE FROM anuncios WHERE id = ?");
            $stmt->execute([$idToDelete]);
        }
        // Redirigir para refrescar la lista
        header('Location: anuncios_admin.php');
        exit;
    }

    // --- LÓGICA PARA GUARDAR (CREAR O EDITAR) ---
    $id = $_POST['id'] ?? null;
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $prioridad = $_POST['prioridad'] ?? 'Informativo';
    $estado = $_POST['estado'] ?? 'Borrador';

    if (!empty($titulo) && !empty($contenido)) {
        if ($id && is_numeric($id)) {
            // EDICIÓN (UPDATE)
            $stmt = $pdo->prepare("
                UPDATE anuncios SET 
                    titulo = ?, 
                    contenido = ?, 
                    prioridad = ?, 
                    estado = ? 
                WHERE id = ?
            ");
            $stmt->execute([$titulo, $contenido, $prioridad, $estado, $id]);
        } else {
            // CREACIÓN (INSERT)
            $stmt = $pdo->prepare("
                INSERT INTO anuncios (titulo, contenido, prioridad, estado, vistas, fecha_creacion) 
                VALUES (?, ?, ?, ?, 0, NOW())
            ");
            $stmt->execute([$titulo, $contenido, $prioridad, $estado]);
        }
    }
    
    // Redirigir para limpiar el POST y cerrar el modal
    header('Location: anuncios_admin.php');
    exit;
}


// ======================================================================
// 3. LÓGICA DE LECTURA (GET), FILTROS Y PREPARACIÓN DEL MODAL
// ======================================================================
$paginaActual = 'anuncios_admin.php'; 

$searchTerm = $_GET['search'] ?? '';
$anuncioIdToEdit = $_GET['edit'] ?? null;
$modalOpenClass = ($anuncioIdToEdit) ? 'modal-open' : '';

$anuncioEditado = null;
$whereClause = '';
$params = [];

if (!empty($searchTerm)) {
    $whereClause = ' WHERE titulo LIKE ?';
    $params[] = '%' . $searchTerm . '%';
}

$stmt = $pdo->prepare("SELECT * FROM anuncios" . $whereClause . " ORDER BY id DESC");
$stmt->execute($params);
$anunciosFiltrados = $stmt->fetchAll();

if ($anuncioIdToEdit && is_numeric($anuncioIdToEdit)) {
    $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE id = ?");
    $stmt->execute([$anuncioIdToEdit]);
    $anuncioEditado = $stmt->fetch();
    
    if (!$anuncioEditado) {
        $modalOpenClass = '';
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Gestión de Anuncios - Clínica Veterinaria del Norte</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="stylesheet" href="Anuncios.css"> 
    <link rel="stylesheet" href="css/dashboard_admin.css"> 
    
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#00A79D",
                        "background-light": "#F8F9FA",
                        "background-dark": "#101d22",
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }
        .modal-backdrop.modal-open {
            display: flex;
        }
        .modal-container {
            background-color: white;
            width: 100%;
            max-width: 32rem;
            border-radius: 0.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .dark .modal-container {
            background-color: #101d22;
            border: 1px solid #374151;
        }
    </style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-[#333333] dark:text-gray-200">
<div class="contenedor">
<?php include 'partials/admin_sidebar.php'; ?>
<main class="flex-1 p-6 lg:p-10">
<div class="mx-auto max-w-7xl">
<div class="flex flex-wrap items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<p class="text-3xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white">Gestión de Anuncios</p>
<p class="text-base font-normal leading-normal text-gray-500 dark:text-gray-400">Crea, edita y gestiona las comunicaciones para los usuarios.</p>
</div>
<button onclick="abrirModalCrear()" type="button" class="flex items-center justify-center gap-2 overflow-hidden rounded-lg h-11 px-5 bg-primary text-white text-sm font-bold leading-normal tracking-wide shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-background-dark cursor-pointer">
<span class="material-symbols-outlined">add_circle</span>
<span class="truncate">Crear Nuevo Anuncio</span>
</button>
</div>
<div class="grid grid-cols-1 gap-6 mt-8 sm:grid-cols-2 lg:grid-cols-3">
    <div class="flex flex-col gap-2 rounded-lg p-6 bg-white dark:bg-background-dark border border-[#E0E0E0] dark:border-gray-700">
        <p class="text-base font-medium leading-normal text-gray-600 dark:text-gray-300">Total de Anuncios Publicados</p>
        <?php 
            $stmt = $pdo->query("SELECT COUNT(*) FROM anuncios WHERE estado = 'Publicado'");
            $totalPublicados = $stmt->fetchColumn();
        ?>
        <p class="text-3xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white"><?php echo $totalPublicados; ?></p>
        <p class="text-sm font-medium leading-normal text-green-600 dark:text-green-500">+12% este mes</p>
    </div>
    <div class="flex flex-col gap-2 rounded-lg p-6 bg-white dark:bg-background-dark border border-[#E0E0E0] dark:border-gray-700">
        <p class="text-base font-medium leading-normal text-gray-600 dark:text-gray-300">Vistas Totales </p>
        <p class="text-3xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white">0</p>
        <p class="text-sm font-medium leading-normal text-green-600 dark:text-green-500">0</p>
    </div>
    <div class="flex flex-col gap-2 rounded-lg p-6 bg-white dark:bg-background-dark border border-[#E0E0E0] dark:border-gray-700">
        <p class="text-base font-medium leading-normal text-gray-600 dark:text-gray-300">Anuncio Más Popular </p>
        <p class="text-xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white truncate">Campaña de Vacunación</p>
        <p class="text-sm font-medium leading-normal text-green-600 dark:text-green-500">+21% en vistas</p>
    </div>
</div>
<div class="mt-10">
    <h2 class="text-2xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white">Todos los Anuncios</h2>
    <div class="mt-4">
        <form method="GET" action="anuncios_admin.php">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <input class="block w-full rounded-lg border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark py-2.5 pl-10 pr-24 text-sm placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary" placeholder="Buscar por título..." type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>"/>
                <button type="submit" class="absolute inset-y-0 right-0 px-4 text-sm font-semibold text-white bg-primary rounded-r-lg hover:bg-primary/90">Buscar</button>
            </div>
        </form>
        </div>
</div>
<div class="mt-6 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark">
                <table class="min-w-full divide-y divide-[#E0E0E0] dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6" scope="col">Título</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white" scope="col">Prioridad</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white" scope="col">Estado</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white" scope="col">Vistas</th>
                            <th class="relative py-3.5 pl-3 pr-4 sm:pr-6" scope="col"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php if (empty($anunciosFiltrados)): ?>
                            <tr>
                                <td colspan="5" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-500 text-center sm:pl-6">
                                    <?php echo !empty($searchTerm) ? 'No se encontraron anuncios que coincidan con "' . htmlspecialchars($searchTerm) . '"' : 'No hay anuncios registrados.'; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($anunciosFiltrados as $anuncio): ?>
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6"><?php echo htmlspecialchars($anuncio['titulo']); ?></td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <?php 
                                            $colorClass = match($anuncio['prioridad']) {
                                                'Urgente' => 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300',
                                                'Importante' => 'bg-orange-100 dark:bg-orange-900/50 text-orange-800 dark:text-orange-300',
                                                'Informativo' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300',
                                                default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                            };
                                        ?>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?php echo $colorClass; ?>"><?php echo htmlspecialchars($anuncio['prioridad']); ?></span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300"><?php echo htmlspecialchars($anuncio['estado']); ?></td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300"><?php echo htmlspecialchars($anuncio['vistas']); ?></td>
                                    
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex items-center justify-end gap-3">
                                            <a class="text-primary hover:text-primary/80 font-bold" href="?edit=<?php echo $anuncio['id']; ?>">Editar</a>
                                            
                                            <form method="POST" action="anuncios_admin.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este anuncio? Esta acción no se puede deshacer.');" style="display:inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $anuncio['id']; ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold bg-transparent border-none cursor-pointer">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</main>
</div>

<div id="modalAnuncio" class="modal-backdrop <?php echo $modalOpenClass; ?>">
    <div class="modal-container">
        <div class="flex items-center justify-between p-4 border-b border-[#E0E0E0] dark:border-gray-700">
            <h3 id="modalTitulo" class="text-xl font-bold text-gray-900 dark:text-white">
                <?php echo $anuncioEditado ? 'Editar Anuncio: ' . htmlspecialchars($anuncioEditado['titulo']) : 'Crear Nuevo Anuncio'; ?>
            </h3>
            <button type="button" onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i data-lucide="x" class="lucide-icon size-6"></i>
            </button>
        </div>
        
        <form id="formAnuncio" class="p-6" method="POST" action="anuncios_admin.php">
            <input type="hidden" id="anuncioId" name="id" value="<?php echo htmlspecialchars($anuncioEditado['id'] ?? ''); ?>">
            
            <div class="space-y-4">
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                    <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($anuncioEditado['titulo'] ?? ''); ?>" 
                           class="mt-1 block w-full rounded-lg border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark py-2.5 px-3 text-sm focus:ring-primary focus:border-primary" required>
                </div>
                
                <div>
                    <label for="contenido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contenido</label>
                    <textarea id="contenido" name="contenido" rows="4" 
                              class="mt-1 block w-full rounded-lg border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark py-2.5 px-3 text-sm focus:ring-primary focus:border-primary" required><?php echo htmlspecialchars($anuncioEditado['contenido'] ?? ''); ?></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="prioridad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                        <select id="prioridad" name="prioridad" class="mt-1 block w-full rounded-lg border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark py-2.5 px-3 text-sm focus:ring-primary focus:border-primary">
                            <option value="Informativo" <?php echo ($anuncioEditado['prioridad'] ?? '') == 'Informativo' ? 'selected' : ''; ?>>Informativo</option>
                            <option value="Importante" <?php echo ($anuncioEditado['prioridad'] ?? '') == 'Importante' ? 'selected' : ''; ?>>Importante</option>
                            <option value="Urgente" <?php echo ($anuncioEditado['prioridad'] ?? '') == 'Urgente' ? 'selected' : ''; ?>>Urgente</option>
                        </select>
                    </div>
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                        <select id="estado" name="estado" class="mt-1 block w-full rounded-lg border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark py-2.5 px-3 text-sm focus:ring-primary focus:border-primary">
                            <option value="Borrador" <?php echo ($anuncioEditado['estado'] ?? '') == 'Borrador' ? 'selected' : ''; ?>>Borrador</option>
                            <option value="Programado" <?php echo ($anuncioEditado['estado'] ?? '') == 'Programado' ? 'selected' : ''; ?>>Programado</option>
                            <option value="Publicado" <?php echo ($anuncioEditado['estado'] ?? '') == 'Publicado' ? 'selected' : ''; ?>>Publicado</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-6">
                <button type="button" onclick="cerrarModal()" class="h-11 px-5 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-bold leading-normal text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" class="h-11 px-5 flex items-center justify-center rounded-lg bg-primary text-white text-sm font-bold leading-normal tracking-wide shadow-sm hover:bg-primary/90">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('modalAnuncio');
    const form = document.getElementById('formAnuncio');
    const modalTitle = document.getElementById('modalTitulo');
    const inputId = document.getElementById('anuncioId');

    function abrirModalCrear() {
        form.reset(); 
        inputId.value = ''; 
        modalTitle.innerText = 'Crear Nuevo Anuncio'; 
        modal.classList.add('modal-open'); 
    }

    function cerrarModal() {
        modal.classList.remove('modal-open');
        const url = new URL(window.location);
        if (url.searchParams.has('edit')) {
            url.searchParams.delete('edit');
            window.history.pushState({}, '', url);
        }
    }

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModal();
        }
    });
</script>

</body>
</html>