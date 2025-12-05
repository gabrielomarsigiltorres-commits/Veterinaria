<?php
session_start();
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    header("Location: login.php");
    exit;
}
// Array de meses para el selector
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
$anioActual = date('Y');
$mesActual = date('n');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Mensuales - Admin</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="css/dashboard_admin.css">
    <style>
        .card-reporte {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s;
        }
        .card-reporte:hover {
            transform: translateY(-5px);
            border-color: #00A79D;
        }
        .icon-container {
            width: 60px;
            height: 60px;
            background: #f0fdfa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            color: #00A79D;
        }
        .btn-generar {
            background-color: #00A79D;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 1rem;
            width: 100%;
            justify-content: center;
        }
        .btn-generar:hover { background-color: #008f85; }
        
        .filtros {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: flex-end;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .grupo-input { display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .grupo-input label { font-weight: 600; color: #374151; font-size: 0.9rem; }
        .grupo-input select {
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <?php include 'partials/admin_sidebar.php'; ?>

        <main class="contenido-principal">
            <header class="cabecera-principal">
                <h1>Reportes y Estadísticas</h1>
                <div class="info-usuario">
                    <img src="../img/logo.jpg" alt="Admin" class="avatar">
                    <div>
                        <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin'); ?></p>
                        <p class="rol-usuario">Administrador</p>
                    </div>
                </div>
            </header>

            <section class="contenido-pagina">
                
                <form id="formFiltros" class="filtros">
                    <div class="grupo-input">
                        <label>Mes del Reporte</label>
                        <select name="mes" id="mes">
                            <?php foreach($meses as $num => $nombre): ?>
                                <option value="<?= $num ?>" <?= $num == $mesActual ? 'selected' : '' ?>><?= $nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grupo-input">
                        <label>Año</label>
                        <select name="anio" id="anio">
                            <?php for($y = 2024; $y <= date('Y'); $y++): ?>
                                <option value="<?= $y ?>" <?= $y == $anioActual ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </form>

                <div class="grid-reportes" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                    
                    <div class="card-reporte">
                        <div class="icon-container">
                            <i data-lucide="shopping-bag" size="32"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Reporte Mensual de Ventas</h3>
                        <p class="text-gray-500 mb-4">Genera un PDF con el detalle de todos los productos vendidos, clientes y totales del mes seleccionado.</p>
                        <button type="button" onclick="generarReporte('ventas')" class="btn-generar">
                            <i data-lucide="file-text"></i> Descargar PDF Ventas
                        </button>
                    </div>

                    <div class="card-reporte">
                        <div class="icon-container">
                            <i data-lucide="calendar" size="32"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Reporte de Citas Médicas</h3>
                        <p class="text-gray-500 mb-4">Obtén un listado completo de citas programadas, atendidas y canceladas durante el mes.</p>
                        <button type="button" onclick="generarReporte('citas')" class="btn-generar">
                            <i data-lucide="file-text"></i> Descargar PDF Citas
                        </button>
                    </div>

                </div>
            </section>
        </main>
    </div>

    <script>
        lucide.createIcons();

        function generarReporte(tipo) {
            const mes = document.getElementById('mes').value;
            const anio = document.getElementById('anio').value;
            
            let url = '';
            if(tipo === 'ventas') {
                url = `../Controller/reporte_ventas_pdf.php?mes=${mes}&anio=${anio}`;
            } else if (tipo === 'citas') {
                url = `../Controller/reporte_citas_pdf.php?mes=${mes}&anio=${anio}`;
            }

            // Abrir en nueva pestaña
            window.open(url, '_blank');
        }
    </script>
</body>
</html>