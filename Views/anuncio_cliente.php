<?php
// ======================================================================
// 1. CONFIGURACIÓN, CONEXIÓN Y DATOS DE SESIÓN
// ======================================================================
$host = 'localhost'; 
$db   = 'veterinaria';
$user = 'root'; // 🚨 ¡REEMPLAZAR con tu usuario!
$pass = ''; // 🚨 ¡REEMPLAZAR con tu contraseña!

// ⚠️ SIMULACIÓN DE ID DE CLIENTE: DEBE REEMPLAZARSE CON EL ID REAL DEL USUARIO LOGGEADO
$id_cliente_actual = 1; 

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
// 2. LÓGICA POST: MARCAR COMO LEÍDO
// ======================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'mark_read') {
    $anuncioIdToRead = $_POST['anuncio_id'] ?? null;

    if ($anuncioIdToRead && is_numeric($anuncioIdToRead)) {
        try {
            // Inserta o actualiza el registro de lectura
            $stmt = $pdo->prepare("
                INSERT INTO lecturas_anuncios (id_anuncio, id_cliente) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE fecha_lectura = NOW()
            ");
            $stmt->execute([$anuncioIdToRead, $id_cliente_actual]);

        } catch (\PDOException $e) {
            // Manejar error
        }
    }
    // Redirigir manteniendo filtros y búsqueda
    $redirectUrl = 'anuncio_cliente.php?id=' . $anuncioIdToRead . '&filter=' . ($_POST['current_filter'] ?? 'todos') . '&search=' . urlencode($_POST['current_search'] ?? '');
    header('Location: ' . $redirectUrl);
    exit;
}


// ======================================================================
// 3. LÓGICA GET: FILTROS, BÚSQUEDA Y LISTA DE ANUNCIOS
// ======================================================================

$anuncioId = $_GET['id'] ?? null;
$searchTerm = $_GET['search'] ?? ''; 
$filterType = $_GET['filter'] ?? 'todos'; 

$anuncioSeleccionado = null;
$anuncios = [];
$params = [];

// Consulta base
$sqlBase = "
    SELECT 
        a.id, 
        a.titulo, 
        a.prioridad, 
        DATE_FORMAT(a.fecha_creacion, '%d de %M, %Y') as fecha_formato,
        l.id_lectura IS NOT NULL as leido
    FROM anuncios a
    LEFT JOIN lecturas_anuncios l ON a.id = l.id_anuncio AND l.id_cliente = :cliente_id
    WHERE a.estado IN ('Publicado', 'Programado')
";

$whereClauses = [];
$params['cliente_id'] = $id_cliente_actual;

// --- Aplicar Filtros (No Leído / Urgente) ---
if ($filterType == 'no_leido') {
    $whereClauses[] = "l.id_lectura IS NULL";
} elseif ($filterType == 'urgente') {
    $whereClauses[] = "a.prioridad = 'Urgente'";
}

// --- Aplicar Búsqueda por Palabra Clave ---
if (!empty($searchTerm)) {
    // Lógica SQL para buscar en el TÍTULO O CONTENIDO
    $whereClauses[] = "(a.titulo LIKE :search_term OR a.contenido LIKE :search_term)";
    $params['search_term'] = '%' . $searchTerm . '%'; 
}

// Construir la consulta final
if (!empty($whereClauses)) {
    $sqlBase .= " AND " . implode(' AND ', $whereClauses);
}

$sqlBase .= " ORDER BY a.fecha_creacion DESC";

$stmt = $pdo->prepare($sqlBase);
$stmt->execute($params);
$anuncios = $stmt->fetchAll();


// --- Obtener detalles del anuncio seleccionado para el panel derecho ---
if ($anuncioId && is_numeric($anuncioId)) {
    $stmt = $pdo->prepare("SELECT *, l.id_lectura IS NOT NULL as leido FROM anuncios a LEFT JOIN lecturas_anuncios l ON a.id = l.id_anuncio AND l.id_cliente = ? WHERE a.id = ? AND a.estado IN ('Publicado', 'Programado')");
    $stmt->execute([$id_cliente_actual, $anuncioId]);
    $anuncioSeleccionado = $stmt->fetch();
}

// Si no hay selección, carga el primer anuncio de la lista
if (!$anuncioSeleccionado && !empty($anuncios)) {
    $anuncioId = $anuncios[0]['id'];
    $stmt = $pdo->prepare("SELECT *, l.id_lectura IS NOT NULL as leido FROM anuncios a LEFT JOIN lecturas_anuncios l ON a.id = l.id_anuncio AND l.id_cliente = ? WHERE a.id = ?");
    $stmt->execute([$id_cliente_actual, $anuncioId]);
    $anuncioSeleccionado = $stmt->fetch();
}


?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Bandeja de Anuncios - Cliente</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="stylesheet" href="cliente.css"> 
    
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

    <script>
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
                    },
                },
            },
        }
    </script>
</head>
<body class="font-display bg-background-light text-[#333333]">

<div class="flex flex-col min-h-screen">
    <header class="w-full bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="../img/veterinarialogo.png" alt="Logo Veterinaria" class="rounded-full size-8">
                <span class="text-lg font-bold text-gray-800">Clínica Veterinaria del Norte S.A.C</span>
            </div>
            <div class="flex items-center space-x-4">
                
            </div>
        </div>
    </header>
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col gap-1 mb-8">
            <p class="text-3xl font-bold leading-tight tracking-tight text-gray-900">Bandeja de Anuncios</p>
            <p class="text-base font-normal text-gray-500">Revisa los últimos comunicados de la clínica.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 bg-white rounded-xl shadow-lg border border-gray-200 p-4">
                
                <form method="GET" action="anuncio_cliente.php" class="mb-4">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute inset-y-0 left-3 flex items-center text-gray-400">search</span>
                        <input class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm focus:ring-primary focus:border-primary" 
                               placeholder="Buscar por palabra clave" type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>"/>
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filterType); ?>">
                        <button type="submit" class="hidden"></button> 
                    </div>
                </form>

                <div class="flex space-x-2 mb-4">
                    <a href="?filter=todos&search=<?php echo urlencode($searchTerm); ?>" class="px-3 py-1 text-sm font-semibold rounded-full <?php echo $filterType == 'todos' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">Todos</a>
                    <a href="?filter=no_leido&search=<?php echo urlencode($searchTerm); ?>" class="px-3 py-1 text-sm font-semibold rounded-full <?php echo $filterType == 'no_leido' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">No Leído</a>
                    <a href="?filter=urgente&search=<?php echo urlencode($searchTerm); ?>" class="px-3 py-1 text-sm font-semibold rounded-full <?php echo $filterType == 'urgente' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">Urgente</a>
                </div>

                <div class="space-y-2">
                    <?php if (empty($anuncios)): ?>
                        <p class="text-center text-gray-500 py-4">No hay anuncios disponibles.</p>
                    <?php else: ?>
                        <?php foreach ($anuncios as $anuncio): 
                            // Enlaces mantienen el ID de anuncio, filtro y búsqueda para persistencia
                            $linkParams = "?id=" . $anuncio['id'] . "&filter=" . $filterType . "&search=" . urlencode($searchTerm);
                            $isActive = $anuncio['id'] == $anuncioId ? 'bg-blue-50 border-blue-400' : 'hover:bg-gray-50 border-gray-200';
                            $readStatus = $anuncio['leido'] ? 'font-normal text-gray-500' : 'font-semibold text-gray-800';
                            
                            $tagClass = match($anuncio['prioridad']) {
                                'Urgente' => 'bg-red-500 text-white',
                                'Importante' => 'bg-orange-400 text-white',
                                'Informativo' => 'bg-blue-400 text-white',
                                default => 'bg-gray-400 text-white',
                            };
                            ?>
                            <a href="<?php echo $linkParams; ?>" class="relative block p-3 rounded-lg border-l-4 <?php echo $isActive; ?> transition duration-150">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full <?php echo $tagClass; ?>"><?php echo htmlspecialchars(strtoupper($anuncio['prioridad'])); ?></span>
                                <p class="text-sm mt-1 <?php echo $readStatus; ?>"><?php echo htmlspecialchars($anuncio['titulo']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($anuncio['fecha_formato']); ?></p>
                                <?php if (!$anuncio['leido']): ?>
                                    <span class="absolute right-3 top-3 w-2 h-2 bg-primary rounded-full"></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <?php if ($anuncioSeleccionado): 
                    $tagClassDetail = match($anuncioSeleccionado['prioridad']) {
                        'Urgente' => 'bg-red-500 text-white',
                        'Importante' => 'bg-orange-400 text-white',
                        'Informativo' => 'bg-blue-400 text-white',
                        default => 'bg-gray-400 text-white',
                    };
                    $fechaDetalle = date('d \d\e F, Y - H:i A', strtotime($anuncioSeleccionado['fecha_creacion'] ?? 'now'));
                ?>
                    <div class="mb-6">
                        <span class="text-sm font-bold px-3 py-1 rounded-full <?php echo $tagClassDetail; ?>"><?php echo htmlspecialchars(strtoupper($anuncioSeleccionado['prioridad'])); ?></span>
                        <h1 class="text-3xl font-extrabold text-gray-900 mt-3 mb-2"><?php echo htmlspecialchars($anuncioSeleccionado['titulo']); ?></h1>
                        <p class="text-xs text-gray-500">De: Clínica Veterinaria del Norte | Fecha: <?php echo $fechaDetalle; ?></p>
                    </div>

                    <div class="prose max-w-none text-gray-700 leading-relaxed mb-8">
                        <?php 
                        // Muestra el contenido del anuncio
                        $contenido_html = nl2br(htmlspecialchars($anuncioSeleccionado['contenido'])); 
                        echo $contenido_html;
                        ?>
                    </div>

                    
                    
                    <div class="flex justify-between items-center mt-8 pt-4 border-t border-gray-200">
                        <?php if (!$anuncioSeleccionado['leido']): ?>
                            <form method="POST" action="anuncio_cliente.php" class="inline">
                                <input type="hidden" name="action" value="mark_read">
                                <input type="hidden" name="anuncio_id" value="<?php echo $anuncioSeleccionado['id']; ?>">
                                <input type="hidden" name="current_filter" value="<?php echo htmlspecialchars($filterType); ?>">
                                <input type="hidden" name="current_search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                                <button type="submit" class="flex items-center justify-center gap-2 rounded-lg h-10 px-5 bg-primary text-white text-sm font-bold hover:bg-primary/90">
                                    <span class="material-symbols-outlined">done_all</span>
                                    Marcar como leído
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-sm font-semibold text-green-600 flex items-center gap-1">
                                <span class="material-symbols-outlined">check_circle</span>
                                Anuncio leído
                            </span>
                        <?php endif; ?>
                        
                        <a href="dashboard.php" class="text-gray-600 hover:text-gray-800 text-sm font-semibold">
                            <span class="material-symbols-outlined align-middle">arrow_back</span> Regresar al Dashboard
                        </a>
                    </div>

                <?php else: ?>
                    <p class="text-center text-gray-500 py-10">Selecciona un anuncio de la lista para ver los detalles.</p>
                <?php endif; ?>
            </div>
            
        </div>
    </main>
    </div>
</body>
</html>