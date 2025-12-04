<?php
// Incluimos el controlador específico del cliente
require_once '../Controller/cliente_monitoreo_controller.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mi Monitoreo - Cliente</title>
  
  <!-- Iconos Lucide -->
  <script src="https://unpkg.com/lucide@latest"></script>
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- CSS Global y Específico -->
  <link rel="stylesheet" href="css/cliente_monitoreo.css">
</head>
<body class="bg-slate-50">
  <div class="max-w-7xl mx-auto p-4 md:p-8">

    <!-- Cabecera Simple -->
    <header class="flex justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Mi Panel de Mascotas</h1>
            <p class="text-slate-500 text-sm">Monitoreo en tiempo real</p>
        </div>
        <div class="flex items-center gap-3 bg-blue-50 px-4 py-2 rounded-full">
            <i data-lucide="user" class="text-blue-600 w-5 h-5"></i>
            <span class="font-bold text-blue-900 text-sm">
                <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Cliente'); ?>
            </span>
        </div>
    </header>

    <main>
        <!-- 1. SELECTOR DE MI MASCOTA -->
        <div class="bg-white p-8 rounded-2xl shadow-lg border-b-4 border-blue-500 mb-8 transform transition-all hover:scale-[1.01]">
            <label class="block text-sm font-bold text-slate-500 uppercase mb-3 flex items-center gap-2">
                <i data-lucide="heart" class="w-4 h-4 text-red-500"></i> Selecciona a tu compañero
            </label>
            <div class="relative">
                <select id="selectMascota" class="w-full p-4 pl-12 border-2 border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none bg-slate-50 text-lg font-medium appearance-none cursor-pointer transition-colors">
                    <option value="">-- Elige una mascota para ver su estado --</option>
                    <?php if(!empty($misMascotas)): ?>
                        <?php foreach ($misMascotas as $mascota): ?>
                            <option value="<?= htmlspecialchars($mascota['id']) ?>">
                                <?= htmlspecialchars($mascota['nombre']) ?> (<?= htmlspecialchars($mascota['raza']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <i data-lucide="paw-print" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 w-6 h-6"></i>
                <i data-lucide="chevron-down" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5"></i>
            </div>
        </div>

        <!-- 2. PANTALLA DE VISUALIZACIÓN (Oculta al inicio) -->
        <div id="panelVisualizacion" class="hidden space-y-6 animate-fade-in">
            
            <!-- SECCIÓN A: Estado de la Cita (Barra de Progreso) -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-2 h-full bg-gradient-to-b from-blue-400 to-blue-600"></div>
                
                <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-3 text-xl">
                    <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    Estado del Servicio Actual
                </h3>
                
                <div id="citaContainer" class="hidden">
                    <div class="text-center mb-6">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Fase Actual</span>
                        <div id="txtEtapa" class="text-3xl md:text-5xl font-black text-blue-600 mt-1 transition-all duration-500">
                            Recepción
                        </div>
                    </div>

                    <!-- Barra Visual Decorativa -->
                    <div class="relative w-full bg-slate-100 h-6 rounded-full overflow-hidden shadow-inner mb-2">
                        <!-- Fondo con rayas -->
                        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIi8+CjxwYXRoIGQ9Ik0wIDBMOCA4Wk04IDBMMCA4WiIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utd2lkdGg9IjEiLz4KPC9zdmc+')]"></div>
                        
                        <div id="barraProgresoVisual" class="bg-gradient-to-r from-blue-500 to-cyan-400 h-full w-0 transition-all duration-1000 ease-out shadow-lg relative">
                            <div class="absolute right-0 top-0 h-full w-2 bg-white/30 animate-pulse"></div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between text-xs font-bold text-slate-400 px-2">
                        <span>Recepción</span>
                        <span>Listo para casa</span>
                    </div>
                </div>
                
                <div id="noCitaMsg" class="flex flex-col items-center justify-center py-8 text-slate-400">
                    <div class="bg-slate-50 p-4 rounded-full mb-3">
                        <i data-lucide="coffee" class="w-8 h-8 opacity-50"></i>
                    </div>
                    <p class="font-medium">Tu mascota está descansando en casa. No hay citas hoy.</p>
                </div>
            </div>

            <!-- SECCIÓN B: Grid de Datos Clínicos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Historial de Peso -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <i data-lucide="scale" class="text-orange-500 w-5 h-5"></i>
                        Historial de Peso
                    </h3>
                    <div class="bg-orange-50/50 rounded-xl p-4 min-h-[150px]">
                        <ul id="listaPesos" class="space-y-3 max-h-48 overflow-y-auto custom-scrollbar pr-2">
                            <!-- Se llena con JS -->
                        </ul>
                    </div>
                </div>

                <!-- Vacunas -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <i data-lucide="syringe" class="text-green-500 w-5 h-5"></i>
                        Calendario de Salud
                    </h3>
                    <div id="listaVacunas" class="space-y-3 max-h-60 overflow-y-auto custom-scrollbar pr-2 min-h-[150px]">
                        <!-- Se llena con JS -->
                    </div>
                </div>
            </div>

            <!-- SECCIÓN C: Tratamiento Activo (Si existe) -->
            <div id="panelTratamiento" class="hidden bg-white rounded-2xl shadow-lg border-l-4 border-red-500 overflow-hidden">
                <div class="bg-red-50 p-4 border-b border-red-100 flex items-center gap-3">
                    <div class="bg-white p-2 rounded-full shadow-sm text-red-500">
                        <i data-lucide="pill" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-red-900 text-lg">Tratamiento en Casa</h3>
                        <p class="text-red-600 text-xs font-medium uppercase tracking-wide">Receta Médica Activa</p>
                    </div>
                </div>

                <div class="p-6 md:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase block mb-1">Medicamento</span>
                            <p id="viewTratMed" class="text-xl font-bold text-gray-800">--</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase block mb-1">Dosis / Frecuencia</span>
                            <div class="flex items-start gap-2">
                                <i data-lucide="clock-3" class="w-5 h-5 text-slate-400 mt-0.5"></i>
                                <p id="viewTratDosis" class="text-lg text-gray-700">--</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-5 border border-blue-100">
                         <div class="flex justify-between items-end mb-2">
                            <span class="text-xs font-bold text-blue-800 uppercase">Progreso del tratamiento</span>
                            <span class="text-2xl font-black text-blue-600" id="viewDiasProgreso">0</span>
                        </div>
                        <!-- Barra progreso tratamiento (Read Only) -->
                        <div class="w-full bg-blue-200 h-3 rounded-full overflow-hidden">
                            <div id="barTratamiento" class="bg-blue-500 h-full rounded-full w-0 transition-all duration-700"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-blue-400 mt-1 uppercase font-bold">
                            <span>Día 1</span>
                            <span>Final</span>
                        </div>
                    </div>

                    <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 flex gap-4">
                        <i data-lucide="alert-circle" class="text-amber-500 w-6 h-6 flex-shrink-0"></i>
                        <div>
                            <span class="text-xs font-bold text-amber-700 uppercase block mb-1">Indicaciones Especiales</span>
                            <p id="viewTratInst" class="text-gray-700 italic">--</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center py-6">
                <p class="text-xs text-gray-400">La información se actualiza automáticamente cada vez que seleccionas la mascota.</p>
            </div>

        </div>
    </main>
  </div>

  <!-- JAVASCRIPT -->
  <script>
    lucide.createIcons();

    const etapasNombres = ["Recepción", "Lavado/Triaje", "Corte/Tratamiento", "Secado/Recuperación", "Listo para Recoger"];
    const controllerUrl = '../Controller/cliente_monitoreo_controller.php';

    // Evento de selección
    document.getElementById('selectMascota').addEventListener('change', function() {
        const petId = this.value;
        const panel = document.getElementById('panelVisualizacion');
        
        if(!petId) {
            panel.classList.add('hidden');
            return;
        }

        // Mostrar panel con animación
        panel.classList.remove('hidden');
        
        // Petición de datos
        fetch(`${controllerUrl}?action=get_pet_details&pet_id=${petId}`)
            .then(r => r.json())
            .then(data => renderizarDatos(data))
            .catch(err => {
                console.error(err);
                alert("Error al cargar datos.");
            });
    });

    function renderizarDatos(data) {
        const pet = data.pet;

        // 1. Cita (Progreso)
        const divCita = document.getElementById('citaContainer');
        const noCita = document.getElementById('noCitaMsg');
        
        if(pet.id_cita) {
            divCita.classList.remove('hidden');
            noCita.classList.add('hidden');
            
            const etapaIdx = parseInt(pet.etapa_actual);
            document.getElementById('txtEtapa').innerText = etapasNombres[etapaIdx] || "En proceso";
            
            // Calculo de barra visual
            const totalEtapas = etapasNombres.length - 1;
            const porcentaje = (etapaIdx / totalEtapas) * 100;
            document.getElementById('barraProgresoVisual').style.width = `${porcentaje}%`;

            // Cambio de color si está listo
            const textoEtapa = document.getElementById('txtEtapa');
            if(etapaIdx === totalEtapas) {
                textoEtapa.classList.remove('text-blue-600');
                textoEtapa.classList.add('text-green-500');
            } else {
                textoEtapa.classList.add('text-blue-600');
                textoEtapa.classList.remove('text-green-500');
            }

        } else {
            divCita.classList.add('hidden');
            noCita.classList.remove('hidden');
        }

        // 2. Peso
        const ulPesos = document.getElementById('listaPesos');
        ulPesos.innerHTML = '';
        if(data.weights.length > 0) {
            data.weights.forEach((w, index) => {
                // Destacar el más reciente
                const isFirst = index === 0;
                ulPesos.innerHTML += `
                    <li class="flex justify-between items-center ${isFirst ? 'bg-white shadow-sm border-l-4 border-orange-400 pl-3 py-2 rounded-r' : 'py-1 px-3 opacity-70'}">
                        <span class="text-sm text-gray-500">${w.fecha_peso}</span>
                        <span class="${isFirst ? 'font-bold text-slate-800 text-lg' : 'text-gray-600'}">${w.peso} kg</span>
                    </li>`;
            });
        } else {
            ulPesos.innerHTML = '<li class="text-center text-gray-400 text-sm py-4">No hay registros de peso recientes.</li>';
        }

        // 3. Vacunas
        const divVac = document.getElementById('listaVacunas');
        divVac.innerHTML = '';
        if(data.vaccines.length > 0) {
            data.vaccines.forEach(v => {
                const isDone = v.estado === 'completada';
                divVac.innerHTML += `
                    <div class="flex items-center gap-4 p-3 rounded-xl border ${isDone ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100'} mb-2">
                        <div class="p-2 rounded-full ${isDone ? 'bg-green-200 text-green-700' : 'bg-gray-200 text-gray-400'}">
                            <i data-lucide="${isDone ? 'check' : 'clock'}" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold ${isDone ? 'text-green-800' : 'text-gray-500'}">${v.nombre_vacuna}</p>
                            <p class="text-[10px] uppercase font-bold tracking-wide ${isDone ? 'text-green-600' : 'text-gray-400'}">${isDone ? 'Aplicada' : 'Pendiente'}</p>
                        </div>
                    </div>
                `;
            });
        } else {
            divVac.innerHTML = '<p class="text-gray-400 text-sm text-center">Calendario de vacunación al día.</p>';
        }

        // 4. Tratamiento
        const t = data.treatment;
        const panelTrat = document.getElementById('panelTratamiento');
        
        if(t && t.active) {
            panelTrat.classList.remove('hidden');
            document.getElementById('viewTratMed').innerText = t.medicamento;
            document.getElementById('viewTratDosis').innerText = t.dosis;
            document.getElementById('viewTratInst').innerText = t.instrucciones || "Sin instrucciones adicionales.";
            
            // Progreso dias
            const dias = parseInt(t.dias_progreso) || 0;
            document.getElementById('viewDiasProgreso').innerText = `Día ${dias}`;
            
            // Asumiendo un maximo de 14 dias para la barra visual (puedes ajustar esto)
            const porcentajeTrat = Math.min((dias / 14) * 100, 100);
            document.getElementById('barTratamiento').style.width = `${porcentajeTrat}%`;
            
        } else {
            panelTrat.classList.add('hidden');
        }

        // Reinicializar iconos para el contenido nuevo dinámico
        lucide.createIcons();
    }

    // Auto-actualizar cada 30 segundos si hay una mascota seleccionada
    setInterval(() => {
        const select = document.getElementById('selectMascota');
        if(select.value) {
            select.dispatchEvent(new Event('change'));
        }
    }, 30000);

  </script>
</body>
</html>