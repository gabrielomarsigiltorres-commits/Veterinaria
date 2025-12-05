<?php
require_once "../Controller/ServicioController.php";
$controller = new ServicioController();
$servicios = $controller->listarPublico();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios Veterinarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/dashboard_admin.css">

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

    <header class="bg-blue-600 text-white py-6 shadow-lg">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold">Promociones de servicios Veterinarios</h1>
            <a href="dashboard.php" class="bg-white text-blue-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100">
                Regresar
            </a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold mb-6 text-gray-700">Promociones del establecimiento</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(!empty($servicios)): ?>
                <?php foreach ($servicios as $s): ?>
                    <div class="card-servicio bg-white p-6 rounded-xl shadow-lg flex flex-col-reverse md:flex-row items-start gap-4">

                        <div class="flex-grow">
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
                                Disponible desde: <?= htmlspecialchars($s['fecha_inicio'] ?? $s['fecha_registro'] ?? 'Hoy') ?>
                            </p>

                            <a href="reserva.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 mt-4 inline-block">Reservar Cita</a>
                        </div>

                        <?php 
                            $img = !empty($s['imagen_url']) ? $s['imagen_url'] : 'default_service.png';
                            $rutaImagen = "uploads/" . $img;
                        ?>
                        <div class="flex-shrink-0 w-32 h-32 md:w-40 md:h-40 overflow-hidden rounded-lg shadow-md bg-gray-200">
                            <img src="<?= htmlspecialchars($rutaImagen); ?>"
                                 alt="<?= htmlspecialchars($s['nombre']); ?>"
                                 class="w-full h-full object-cover"
                                 onerror="this.src='img/veterinarialogo.png';"> </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-10">
                    <p class="text-gray-500 text-xl">No hay servicios activos disponibles por el momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>