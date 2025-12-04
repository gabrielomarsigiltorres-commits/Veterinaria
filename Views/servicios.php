<?php
require_once "../Controller/ServicioController.php";
$controller = new ServicioController();
$servicios = $controller->listarPublico();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios Veterinarios</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="../css/dashboard_admin.css">

    <style>
        .card-servicio {
            transition: 0.2s ease;
        }
        .card-servicio:hover {
            transform: scale(1.03);
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- ENCABEZADO DEL CLIENTE -->
    <header class="bg-blue-600 text-white py-6 shadow-lg">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold">Promociones de servicios  Veterinarios</h1>
            <a href="dashboard.php" class="bg-white text-blue-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100">
              Regresar   
        </a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-10">

        <h2 class="text-2xl font-bold mb-6 text-gray-700">Promociones del establecimiento</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <?php foreach ($servicios as $s): ?>
            <div class="card-servicio bg-white p-6 rounded-xl shadow-lg">
                
                <h3 class="text-xl font-bold text-blue-600 mb-2">
                    <?= htmlspecialchars($s['nombre']) ?>
                </h3>

                <span class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                    <?= htmlspecialchars($s['categoria']) ?>
                </span>

                <p class="mt-4 text-gray-700">
                    <?= htmlspecialchars($s['descripcion']) ?>
                </p>

                <p class="mt-4 font-bold text-green-600 text-lg">
                    S/ <?= number_format($s['precio'], 2) ?>
                </p>

                <p class="text-sm mt-2 text-gray-500">
                    Disponible desde: <?= htmlspecialchars($s['fecha_inicio']) ?>
                </p>

            </div>
            <?php endforeach; ?>

        </div>

    </main>

</body>
</html>
