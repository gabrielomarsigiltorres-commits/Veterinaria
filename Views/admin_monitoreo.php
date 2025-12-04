<?php
// Incluimos el controlador al inicio para manejar sesión y datos
require_once '../Controller/admin_monitoreo_controller.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gestión de Monitoreo - Admin</title>
  
  <!-- Iconos Lucide -->
  <script src="https://unpkg.com/lucide@latest"></script>
  
  <!-- Tailwind CSS (Solo para el panel interno) -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- CSS Global del Dashboard -->
  <link rel="stylesheet" href="css/dashboard_admin.css">
  
  <!-- CSS Específico de esta vista -->
  <link rel="stylesheet" href="css/admin_monitoreo.css">
</head>
<body>
  <div class="contenedor">

    <!-- Sidebar -->
    <?php include 'partials/admin_sidebar.php'; ?>

    <main class="contenido-principal">
      <header class="cabecera-principal">
        <h1>Gestión de Monitoreo Veterinario</h1>
        <div class="info-usuario">
          <img src="../img/logo.jpg" alt="Admin" class="avatar">
          <div>
            <p class="nombre-usuario"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin'); ?></p>
            <p class="rol-usuario">Administrador</p>
          </div>
        </div>
      </header>

      <section class="contenido-pagina">
        
        <!-- INICIO DEL PANEL DE MONITOREO -->
        <div class="tailwind-scope full-width-panel">
            
            <!-- 1. SELECTORES DE USUARIO Y MASCOTA -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 w-full">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4"></i> 1. Seleccionar Cliente
                    </label>
                    <select id="selectUsuario" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50">
                        <option value="">-- Buscar Cliente --</option>
                        <!-- Cargamos clientes desde el Controlador -->
                        <?php if(!empty($clientes)): ?>
                            <?php foreach ($clientes as $cli): ?>
                                <option value="<?= htmlspecialchars($cli['id']) ?>">
                                    <?= htmlspecialchars($cli['nombres_completos']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 flex items-center gap-2">
                        <i data-lucide="paw-print" class="w-4 h-4"></i> 2. Seleccionar Paciente
                    </label>
                    <select id="selectMascota" disabled class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-gray-100 disabled:opacity-50 cursor-not-allowed">
                        <option value="">-- Primero elija cliente --</option>
                    </select>
                </div>
            </div>

            <!-- 2. PANTALLA DE EDICIÓN (Ocupa todo el ancho) -->
            <div id="panelEdicion" class="hidden space-y-6 animate-fade-in w-full">
                
                <!-- SECCIÓN A: Cita en Curso (Barra de Progreso Extendida) -->
                <div class="bg-white p-8 rounded-xl border-l-4 border-blue-600 shadow-sm relative overflow-hidden">
                    <h3 class="font-bold text-blue-900 mb-6 flex items-center gap-2 text-2xl">
                        <i data-lucide="activity" class="w-6 h-6"></i> Monitoreo de Cita (Tiempo Real)
                    </h3>
                    
                    <div id="citaContainer" class="hidden">
                        <!-- Controles de la Barra -->
                        <div class="flex items-center justify-between gap-10">
                            <button onclick="cambiarEtapa(-1)" class="px-8 py-4 bg-gray-100 rounded-xl hover:bg-gray-200 text-gray-700 font-bold transition-colors shadow-sm">
                                <i data-lucide="chevron-left" class="inline w-5 h-5"></i> Anterior
                            </button>
                            
                            <div class="text-center flex-1">
                                <div class="text-sm text-gray-400 uppercase font-bold tracking-wider mb-2">Fase Actual del Servicio</div>
                                <div id="txtEtapa" class="text-4xl font-extrabold text-blue-600 mb-4">Recepción</div>
                                
                                <!-- Inputs ocultos -->
                                <input type="hidden" id="inputEtapa" value="0">
                                <input type="hidden" id="inputCitaId" value="0">
                                
                                <!-- Barra Visual -->
                                <div class="w-full bg-gray-200 h-4 rounded-full overflow-hidden shadow-inner">
                                    <div id="barraProgresoVisual" class="bg-blue-600 h-full w-0 transition-all duration-500 ease-out shadow-lg"></div>
                                </div>
                                
                                <div class="flex justify-between text-xs text-gray-400 mt-2 px-1">
                                    <span>Inicio</span>
                                    <span>Finalizado</span>
                                </div>
                            </div>
                            
                            <button onclick="cambiarEtapa(1)" class="px-8 py-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow-lg font-bold transition-colors flex items-center gap-2 transform hover:scale-105 transition-transform">
                                Siguiente Etapa <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="noCitaMsg" class="flex flex-col items-center justify-center py-10 text-gray-400 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                        <i data-lucide="calendar-x" class="w-16 h-16 mb-2 opacity-50"></i>
                        <p class="text-lg italic">La mascota no tiene citas activas hoy.</p>
                    </div>
                </div>

                <!-- SECCIÓN B: Datos Clínicos (Grid Amplio) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Control de Peso -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm h-full">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 text-lg">
                            <div class="p-2 bg-orange-100 rounded-lg text-orange-600"><i data-lucide="scale" class="w-6 h-6"></i></div>
                            Registro de Peso (kg)
                        </h3>
                        <div class="flex gap-4 mb-4">
                            <input type="number" id="nuevoPeso" step="0.1" placeholder="Ej. 12.5" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-200 outline-none text-lg">
                            <button onclick="agregarPesoDOM()" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors shadow-md">
                                <i data-lucide="plus" class="w-6 h-6"></i>
                            </button>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 min-h-[150px]">
                            <ul id="listaPesos" class="text-base text-gray-600 space-y-3 max-h-48 overflow-y-auto custom-scrollbar">
                                <li class="text-center text-gray-400 italic mt-10">Sin historial reciente</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Control de Vacunas -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm h-full">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 text-lg">
                            <div class="p-2 bg-green-100 rounded-lg text-green-600"><i data-lucide="syringe" class="w-6 h-6"></i></div>
                            Vacunas y Desparasitación
                        </h3>
                        <div id="listaVacunas" class="space-y-3 max-h-56 overflow-y-auto mb-4 pr-2 min-h-[150px]">
                            <!-- Se llena con JS -->
                        </div>
                        <div class="flex gap-3 border-t pt-4">
                            <input type="text" id="nuevaVacunaNombre" placeholder="Nombre de nueva vacuna..." class="flex-1 text-sm border border-gray-300 rounded-lg px-4 py-3 outline-none focus:border-green-500">
                            <button onclick="agregarVacunaDOM()" class="bg-green-50 text-green-700 px-6 rounded-lg text-sm font-bold border border-green-200 hover:bg-green-100 shadow-sm">
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN C: Tratamiento Activo -->
                <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-center mb-8 border-b pb-4">
                        <h3 class="font-bold text-gray-800 flex items-center gap-3 text-xl">
                            <div class="p-2 bg-red-100 rounded-lg text-red-600"><i data-lucide="pill" class="w-6 h-6"></i></div>
                            Tratamiento y Recetas Médicas
                        </h3>
                        <label class="flex items-center cursor-pointer bg-gray-50 px-4 py-2 rounded-full border border-gray-200 hover:bg-gray-100 transition-colors shadow-sm">
                            <input type="checkbox" id="chkTratamiento" class="mr-3 w-5 h-5 text-red-600 rounded focus:ring-red-500" onchange="toggleTratamiento()">
                            <span class="text-base font-bold text-gray-700">Habilitar Tratamiento</span>
                        </label>
                    </div>
                    
                    <div id="formTratamiento" class="space-y-6 opacity-50 pointer-events-none transition-all duration-300">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase mb-2 block tracking-wide">Medicamento</label>
                                <input id="tratMed" placeholder="Ej. Amoxicilina 500mg" class="border border-gray-300 p-4 rounded-lg text-base w-full focus:border-blue-500 outline-none shadow-sm">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase mb-2 block tracking-wide">Dosis / Frecuencia</label>
                                <input id="tratDosis" placeholder="Ej. 1 pastilla cada 8 horas por 7 días" class="border border-gray-300 p-4 rounded-lg text-base w-full focus:border-blue-500 outline-none shadow-sm">
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                            <div class="flex justify-between mb-4">
                                <label class="text-sm font-bold text-blue-800 uppercase">Progreso del Tratamiento</label>
                                <span class="text-sm font-bold text-blue-600 bg-white px-3 py-1 rounded shadow-sm border border-blue-100">Día <span id="lblProgreso" class="text-lg">0</span></span>
                            </div>
                            <input type="range" id="tratProgreso" min="0" max="14" value="0" class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600" oninput="document.getElementById('lblProgreso').innerText = this.value">
                            <div class="flex justify-between text-xs text-blue-400 mt-2 font-medium">
                                <span>Inicio del Tratamiento</span>
                                <span>Fin Estimado</span>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase mb-2 block tracking-wide">Instrucciones Adicionales para el Cliente</label>
                            <textarea id="tratInst" placeholder="Ej. Dar con comida para evitar molestias estomacales. Mantener hidratada a la mascota." rows="3" class="w-full border border-gray-300 p-4 rounded-lg text-base focus:border-blue-500 outline-none resize-none shadow-sm"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botón de Acción Principal -->
                <div class="pt-4 pb-8">
                    <button onclick="guardarCambios()" class="w-full bg-slate-800 text-white py-5 rounded-xl font-bold text-xl shadow-lg hover:bg-slate-700 hover:shadow-2xl transition-all flex justify-center items-center gap-4 transform hover:-translate-y-1">
                        <i data-lucide="save" class="w-8 h-8"></i> GUARDAR TODOS LOS CAMBIOS
                    </button>
                </div>

            </div>
        </div>
      </section>
    </main>
  </div>

  <!-- JAVASCRIPT LÓGICA -->
  <script>
    // Inicializar Iconos Lucide
    lucide.createIcons();

    // Variables Globales
    const etapasNombres = ["Recepción", "Lavado/Triaje", "Corte/Tratamiento", "Secado/Recuperación", "Listo"];
    let currentPetId = null;
    const controllerUrl = '../Controller/admin_monitoreo_controller.php'; // Ruta relativa al controlador

    // --- 1. CARGA DE MASCOTAS ---
    document.getElementById('selectUsuario').addEventListener('change', function() {
        const userId = this.value;
        const selectMascota = document.getElementById('selectMascota');
        
        // Reset UI
        selectMascota.innerHTML = '<option>Cargando...</option>';
        selectMascota.disabled = true;
        selectMascota.classList.add('cursor-not-allowed', 'bg-gray-100');
        document.getElementById('panelEdicion').classList.add('hidden');

        if(!userId) return;

        // Petición al Controlador
        fetch(`${controllerUrl}?action=get_pets&user_id=${userId}`)
            .then(r => r.json())
            .then(pets => {
                selectMascota.innerHTML = '<option value="">-- Seleccionar Mascota --</option>';
                if(pets.length > 0) {
                    selectMascota.disabled = false;
                    selectMascota.classList.remove('cursor-not-allowed', 'bg-gray-100');
                    selectMascota.classList.add('bg-white');
                    pets.forEach(p => {
                        selectMascota.innerHTML += `<option value="${p.id}">${p.nombre} (${p.raza})</option>`;
                    });
                } else {
                    selectMascota.innerHTML = '<option value="">Sin mascotas registradas</option>';
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error de conexión con el servidor.");
            });
    });

    // --- 2. CARGA DE DATOS DE LA MASCOTA ---
    document.getElementById('selectMascota').addEventListener('change', function() {
        const petId = this.value;
        if(!petId) return;

        currentPetId = petId;
        document.getElementById('panelEdicion').classList.remove('hidden');

        fetch(`${controllerUrl}?action=get_pet_details&pet_id=${petId}`)
            .then(r => r.json())
            .then(data => cargarInterfaz(data))
            .catch(err => console.error(err));
    });

    function cargarInterfaz(data) {
        const pet = data.pet;
        
        // A. Cita
        const divCita = document.getElementById('citaContainer');
        if(pet.id_cita) {
            divCita.classList.remove('hidden');
            document.getElementById('noCitaMsg').classList.add('hidden');
            document.getElementById('inputCitaId').value = pet.id_cita;
            actualizarVisualesEtapa(pet.etapa_actual);
        } else {
            divCita.classList.add('hidden');
            document.getElementById('noCitaMsg').classList.remove('hidden');
        }

        // B. Peso
        const ulPesos = document.getElementById('listaPesos');
        ulPesos.innerHTML = '';
        if(data.weights.length > 0) {
            data.weights.forEach(w => {
                ulPesos.innerHTML += `
                    <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                        <span class="text-gray-500">${w.fecha_peso}</span>
                        <span class="font-bold text-gray-800 text-lg">${w.peso} kg</span>
                    </li>`;
            });
        } else {
            ulPesos.innerHTML = '<li class="text-center text-gray-400 italic mt-4">Sin historial reciente</li>';
        }

        // C. Tratamiento
        const t = data.treatment;
        const chk = document.getElementById('chkTratamiento');
        
        if(t) {
            chk.checked = true;
            toggleTratamiento(true);
            document.getElementById('tratMed').value = t.medicamento;
            document.getElementById('tratDosis').value = t.dosis;
            document.getElementById('tratInst').value = t.instrucciones;
            document.getElementById('tratProgreso').value = t.dias_progreso;
            document.getElementById('lblProgreso').innerText = t.dias_progreso;
        } else {
            chk.checked = false;
            toggleTratamiento(false);
            document.getElementById('tratMed').value = '';
            document.getElementById('tratDosis').value = '';
            document.getElementById('tratInst').value = '';
            document.getElementById('tratProgreso').value = 0;
            document.getElementById('lblProgreso').innerText = 0;
        }

        // D. Vacunas
        const divVac = document.getElementById('listaVacunas');
        divVac.innerHTML = '';
        data.vaccines.forEach(v => {
            const isDone = v.estado === 'completada';
            divVac.innerHTML += `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-transparent hover:border-green-200 transition-colors shadow-sm">
                    <span class="text-base ${isDone ? 'line-through text-gray-400' : 'text-gray-800 font-bold'}">${v.nombre_vacuna}</span>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <span class="text-xs uppercase font-bold ${isDone ? 'text-green-600 bg-green-100 px-2 py-1 rounded' : 'text-gray-400 bg-gray-200 px-2 py-1 rounded'}">${isDone ? 'Aplicada' : 'Pendiente'}</span>
                        <input type="checkbox" class="vac-check w-5 h-5 text-green-600 rounded focus:ring-green-500" data-id="${v.id}" ${isDone ? 'checked' : ''}>
                    </label>
                </div>
            `;
        });
    }

    // --- 3. FUNCIONES DE INTERFAZ ---

    function cambiarEtapa(delta) {
        let actual = parseInt(document.getElementById('inputEtapa').value);
        let nuevo = actual + delta;
        if(nuevo >= 0 && nuevo < etapasNombres.length) {
            actualizarVisualesEtapa(nuevo);
        }
    }

    function actualizarVisualesEtapa(num) {
        document.getElementById('inputEtapa').value = num;
        document.getElementById('txtEtapa').innerText = etapasNombres[num];
        // Actualizar barra visual
        const porcentaje = (num / (etapasNombres.length - 1)) * 100;
        document.getElementById('barraProgresoVisual').style.width = porcentaje + '%';
    }

    function toggleTratamiento(forceState = null) {
        const chk = document.getElementById('chkTratamiento');
        const form = document.getElementById('formTratamiento');
        const isActive = forceState !== null ? forceState : chk.checked;
        
        if(isActive) {
            form.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            form.classList.add('opacity-50', 'pointer-events-none');
        }
    }

    function agregarVacunaDOM() {
        const nombre = document.getElementById('nuevaVacunaNombre').value;
        if(!nombre) return;
        
        const divVac = document.getElementById('listaVacunas');
        divVac.innerHTML += `
             <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg animate-pulse">
                <span class="text-base font-bold text-yellow-800">${nombre} <span class="text-xs font-normal">(Guardar para confirmar)</span></span>
                <input type="checkbox" disabled checked title="Se creará como pendiente" class="w-5 h-5">
            </div>
        `;
        document.getElementById('nuevaVacunaNombre').value = '';
    }
    
    function agregarPesoDOM() {
        const peso = document.getElementById('nuevoPeso').value;
        if(!peso) return;
        const ulPesos = document.getElementById('listaPesos');
        const today = new Date().toISOString().slice(0, 10);
        
        // Añadir visualmente para feedback inmediato
        ulPesos.innerHTML = `
            <li class="flex justify-between items-center border-b border-green-200 bg-green-50 p-2 rounded animate-pulse">
                <span class="text-green-700">Hoy (Pendiente de guardar)</span>
                <span class="font-bold text-green-800 text-lg">${peso} kg</span>
            </li>` + ulPesos.innerHTML;
    }

    // --- 4. GUARDAR CAMBIOS (Fetch API) ---

    function guardarCambios() {
        if(!currentPetId) return;

        // Recolectar vacunas
        const vacunas = [];
        document.querySelectorAll('.vac-check').forEach(chk => {
            vacunas.push({ id: chk.dataset.id, status: chk.checked ? 'completada' : 'pendiente' });
        });

        const data = {
            pet_id: currentPetId,
            cita_id: document.getElementById('inputCitaId').value,
            etapa: document.getElementById('inputEtapa').value,
            new_weight: document.getElementById('nuevoPeso').value,
            new_vaccine: document.getElementById('nuevaVacunaNombre').value,
            vaccines: vacunas,
            treatment: {
                active: document.getElementById('chkTratamiento').checked,
                drug: document.getElementById('tratMed').value,
                dose: document.getElementById('tratDosis').value,
                instructions: document.getElementById('tratInst').value,
                progress: document.getElementById('tratProgreso').value
            }
        };

        fetch(`${controllerUrl}?action=save_data`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if(res.success) {
                // Feedback Visual Épico
                const btn = document.querySelector('button[onclick="guardarCambios()"]');
                const originalText = btn.innerHTML;
                const originalClass = btn.className;
                
                btn.className = "w-full bg-green-600 text-white py-5 rounded-xl font-bold text-xl shadow-lg flex justify-center items-center gap-4";
                btn.innerHTML = '<i data-lucide="check-circle" class="w-8 h-8"></i> ¡CAMBIOS GUARDADOS!';
                lucide.createIcons();

                setTimeout(() => {
                    // Recargar datos para limpiar inputs y confirmar visualmente
                    document.getElementById('selectMascota').dispatchEvent(new Event('change'));
                    document.getElementById('nuevoPeso').value = '';
                    document.getElementById('nuevaVacunaNombre').value = '';
                    
                    btn.className = originalClass;
                    btn.innerHTML = originalText;
                    lucide.createIcons();
                }, 1500);
            } else {
                alert("Hubo un problema al guardar: " + (res.error || "Error desconocido"));
            }
        })
        .catch(err => {
            alert("Error de red al guardar.");
            console.error(err);
        });
    }
  </script>
</body>
</html>