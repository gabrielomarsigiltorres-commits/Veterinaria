<?php
// Views/perfil_cliente.php
// Esta vista es incluida por PerfilClienteController.php

// 1. 🏁 ¡SIEMPRE iniciar la sesión primero!
session_start();

// 2. 🛡️ Seguridad: Verificar si el usuario está logueado
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

// 3. Obtener el nombre del usuario para el encabezado
$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'Cliente';

// 4. Obtener mensajes del Controlador e inicializar $mensaje
$mensaje = ""; // CORRECCIÓN: Inicialización de variable indefinida

$status = $_GET['status'] ?? '';
$msg = $_GET['msg'] ?? '';

if (!empty($msg)) {
    if ($status === 'success') {
        $mensaje = "<div class='mensaje-exito'>✅ " . htmlspecialchars(urldecode($msg)) . "</div>";
    } elseif ($status === 'info') { 
        $mensaje = "<div class='mensaje-alerta'>⚠️ " . htmlspecialchars(urldecode($msg)) . "</div>";
    } else {
        $mensaje = "<div class='mensaje-error'>❌ " . htmlspecialchars(urldecode($msg)) . "</div>";
    }
}

// 5. Datos estáticos de ubicación para Perú/Lima
$provincias_lima = [
    'Lima' => 'Lima',
];

$distritos_lima = [
    'Ancón', 'Ate', 'Barranco', 'Breña', 'Carabayllo', 'Chaclacayo', 'Chorrillos', 'Cieneguilla',
    'Comas', 'El Agustino', 'Independencia', 'Jesús María', 'La Molina', 'La Victoria',
    'Lince', 'Los Olivos', 'Lurigancho-Chosica', 'Lurín', 'Magdalena del Mar', 'Miraflores',
    'Pachacámac', 'Pucusana', 'Pueblo Libre', 'Puente Piedra', 'Punta Hermosa', 'Punta Negra',
    'Rímac', 'San Bartolo', 'San Borja', 'San Isidro', 'San Juan de Lurigancho',
    'San Juan de Miraflores', 'San Luis', 'San Martín de Porres', 'San Miguel', 'Santa Anita',
    'Santa María del Mar', 'Santa Rosa', 'Santiago de Surco', 'Surquillo', 'Villa El Salvador',
    'Villa María del Triunfo'
];

// 6. Determinar la URL de la foto de perfil (usamos la variable $perfil que viene del controlador)
$foto_perfil_nombre = $perfil['foto_perfil'] ?? null;
$foto_perfil_url = $foto_perfil_nombre ? "uploads/" . htmlspecialchars($foto_perfil_nombre) : "../img/user_default.png";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Cliente</title>
    
    <link rel="stylesheet" href="css/registro.css"> 
    <link rel="stylesheet" href="css/dashboard.css">
    
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* [ESTILOS OMITIDOS POR BREVEDAD, SON LOS MISMOS QUE EN LA RESPUESTA ANTERIOR] */
        /* Nota: Debes mantener todos los estilos de la respuesta anterior en tu archivo */
        body { background-color: #ffffff; min-height: 100vh; display: flex; flex-direction: column; }
        .encabezado { position: sticky; top: 0; z-index: 1000; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .contenedor-encabezado { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; }
        main.contenido { flex-grow: 1; display: flex; justify-content: center; align-items: center; padding: 30px 20px; max-width: 100%; margin: 0 auto; }
        .tabla-section { background-color: #ffffff; max-width: 750px; width: 100%; margin: 0 auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .grid-form { display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
        @media (min-width: 600px) { .grid-form { grid-template-columns: 1fr 1fr; } }
        .grid-form-full { grid-column: 1 / -1; }
        .form-input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; font-size: 1em; }
        .botones-ordenados { display: flex; justify-content: space-between; gap: 20px; margin-top: 30px; flex-wrap: wrap; }
        .btn-accion { flex-grow: 1; text-align: center; padding: 12px 25px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; text-decoration: none; }
        .btn-guardar-salir { background-color: #38b6ab; color: white; }
        .btn-actualizar { background-color: #13b6ec; color: white; }
        .btn-volver { background-color: #e44d4d; color: white; }
        .perfil-foto-container { display: flex; flex-direction: column; align-items: center; margin-bottom: 20px; }
        #currentProfilePic { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #13b6ec; margin-bottom: 15px; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 2000; justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 12px; padding: 20px; max-width: 450px; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.3); text-align: center; }
        #modalImagePreview { display: block; max-width: 100%; height: auto; max-height: 300px; margin: 0 auto 15px; border-radius: 8px; }
        .modal-buttons { display: flex; justify-content: space-around; margin-top: 20px; gap: 15px; }
        .btn-confirmar { background-color: #38b6ab; color: white; }
        .btn-cambiar { background-color: #f4f4f4; color: #333; border: 1px solid #ddd; }
        .mensaje-alerta { background-color: #fff3cd; color: #856404; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: bold; border: 1px solid #ffeeba; }
        /* Estilos Responsivos para el Encabezado (del bloque anterior) */
        @media (max-width: 980px) {
            .contenedor-encabezado { flex-direction: column; align-items: flex-start !important; }
            .nav-principal { width: 100%; justify-content: flex-start !important; margin: 10px 0 !important; }
            .nav-principal ul { flex-direction: column; gap: 5px !important; }
            .perfil-usuario { width: 100%; justify-content: space-between; border-top: 1px solid #eee; padding-top: 10px; margin-top: 10px; }
        }
    </style>
</head>
<body>
    
    <header class="encabezado">
        <div class="contenedor-encabezado">
            
            <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                <img class="logo" src="../img/veterinarialogo.png" alt="Logo" style="width: 45px; height: 45px;">
                <h1 style="font-size: 1.2rem; color: #18a0d6; margin: 0;">Clínica Veterinaria del Norte S.A.C</h1>
            </div>
            
            <nav class="nav-principal" style="flex-grow: 1; display: flex; justify-content: center; margin: 0 20px;">
                <ul style="display: flex; list-style: none; padding: 0; margin: 0; gap: 20px;">
                    <li><a href="dashboard.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Menú principal</a></li>
                    <li><a href="reserva.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Calendaria servicios</a></li>
                    <li><a href="tienda.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Tienda</a></li>
                    <li><a href="contacto.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="perfil-usuario" style="display: flex; align-items: center; gap: 15px; flex-shrink: 0;">
                <a href="anuncio_cliente.php" class="campana" title="Ver anuncios importantes" style="text-decoration: none; font-size: 1.2rem;">🔔</a>
                
                <a href="perfil_cliente.php" title="Mi Perfil" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit;">
                    <img id="headerProfilePic" src="<?= $foto_perfil_url ?>" alt="Foto de Perfil" 
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #13b6ec;">
                    <span class="nombre-usuario" style="color: #333; font-weight: 600;"><?= htmlspecialchars($nombre_usuario); ?></span>
                </a>
                
                <a href="../Controller/cerrar_sesion.php" class="cerrar-sesion" style="text-decoration: none; color: #e44d4d; font-weight: 600;">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="contenido">
        
        <div class="tabla-section">
            
            <div class="bienvenida" style="text-align: center;">
                <h2 style="color: #13b6ec; margin-top: 0;">🛠️ Editar Mi Perfil</h2>
                <p>Actualiza tu información personal y de contacto.</p>
            </div>
            
            <?= $mensaje ?>

            <form action="../Controller/PerfilClienteController.php" method="POST" enctype="multipart/form-data">
                
                <div class="perfil-foto-container">
                    <img id="currentProfilePic" src="<?= $foto_perfil_url ?>" alt="Foto de Perfil Actual"> 
                    
                    <div class="form-group" style="width: 100%;">
                        <label for="foto_perfil" style="text-align: center;">📸 Cambiar Foto de Perfil:</label>
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/png, image/jpeg" onchange="handleFileSelect(event)">
                        <input type="hidden" id="foto_confirmada" name="foto_confirmada" value="false"> 
                    </div>
                </div>

                <div class="grid-form">
                    
                    <div class="grid-form-full">
                        <label for="nombres_completos">👤 Nombre Completo:</label>
                        <input type="text" id="nombres_completos" name="nombres_completos" required class="form-input"
                               value="<?= htmlspecialchars($perfil['nombres_completos'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="correo">📧 Correo Electrónico:</label>
                        <input type="email" id="correo" name="correo" required class="form-input" readonly style="background-color: #f0f0f0;"
                               value="<?= htmlspecialchars($perfil['correo'] ?? '') ?>"> 
                    </div>

                    <div>
                        <label for="telefono">📞 Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" required class="form-input"
                               value="<?= htmlspecialchars($perfil['telefono'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="dni">🆔 DNI:</label>
                        <input type="text" id="dni" name="dni" class="form-input" maxlength="8"
                               value="<?= htmlspecialchars($perfil['dni'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="provincia">🏙️ Provincia:</label>
                        <select id="provincia" name="provincia" required class="form-input">
                            <option value="">Seleccione Provincia</option>
                            <?php 
                            $current_provincia = $perfil['provincia'] ?? '';
                            foreach ($provincias_lima as $key => $name): ?>
                                <option value="<?= $key ?>" <?= ($current_provincia == $key) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="distrito">🏘️ Distrito:</label>
                        <select id="distrito" name="distrito" required class="form-input">
                            <option value="">Seleccione Distrito</option>
                            <?php 
                            $current_distrito = $perfil['distrito'] ?? '';
                            foreach ($distritos_lima as $name): ?>
                                <option value="<?= $name ?>" <?= ($current_distrito == $name) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="grid-form-full">
                        <label for="direccion_av">🛣️ Dirección (Av. / Calle):</label>
                        <input type="text" id="direccion_av" name="direccion_av" class="form-input"
                               value="<?= htmlspecialchars($perfil['direccion_av'] ?? '') ?>">
                    </div>
                    
                    <div class="grid-form-full">
                        <label for="password">🔑 Nueva Contraseña (Dejar vacío para no cambiar):</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="********">
                    </div>
                
                </div>
                
                <div class="botones-ordenados">
                    
                    <a href="dashboard.php" class="btn-accion btn-volver">
                        <i data-lucide="arrow-left" style="width: 18px;"></i>
                        Volver Atrás
                    </a>
                    
                    <button type="submit" name="accion" value="actualizar_mantener" class="btn-accion btn-actualizar">
                        <i data-lucide="refresh-cw" style="width: 18px;"></i>
                        Actualizar Información
                    </button>
                    
                    <button type="submit" name="accion" value="guardar_salir" class="btn-accion btn-guardar-salir">
                        <i data-lucide="save" style="width: 18px;"></i>
                        Guardar y Salir
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <div class="modal-overlay" id="confirmationModal">
        <div class="modal-content">
            <h4 style="margin-top: 0; color: #38b6ab;">Previsualización de Foto de Perfil</h4>
            <img id="modalImagePreview" src="#" alt="Vista Previa de la Foto">
            
            <p style="font-size: 0.9rem; color: #555;">¿Es esta la foto que deseas usar?</p>

            <div class="modal-buttons">
                <button type="button" class="btn-modal btn-cambiar" onclick="changeImage()">
                    Cambiar Foto
                </button>
                <button type="button" class="btn-modal btn-confirmar" onclick="confirmImage()">
                    <i data-lucide="check" style="width: 18px;"></i>
                    Confirmar Foto
                </button>
            </div>
        </div>
    </div>


    <footer class="footer">
        <p>© 2025 Clínica Veterinaria del Norte S.A.C. Todos los derechos reservados.</p>
    </footer>

    <script>
        lucide.createIcons();
        
        let tempFile = null; 
        
        // 1. Maneja la selección del archivo, previsualiza en modal y espera confirmación
        function handleFileSelect(event) {
            const input = event.target;
            const confirmationModal = document.getElementById('confirmationModal');
            const modalImagePreview = document.getElementById('modalImagePreview');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                tempFile = file; 
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    modalImagePreview.src = e.target.result;
                    confirmationModal.style.display = 'flex'; // Abre el modal
                };
                reader.readAsDataURL(file);

            } else {
                tempFile = null;
                input.value = '';
            }
        }

        // 2. Confirma la imagen, cierra el modal y la muestra en el formulario
        function confirmImage() {
            const currentProfilePic = document.getElementById('currentProfilePic');
            const headerProfilePic = document.getElementById('headerProfilePic');
            const confirmationModal = document.getElementById('confirmationModal');
            const modalImagePreview = document.getElementById('modalImagePreview');
            const fotoConfirmadaInput = document.getElementById('foto_confirmada');
            
            if (tempFile) {
                // Settear las imágenes de previsualización con la URL del modal
                currentProfilePic.src = modalImagePreview.src;
                headerProfilePic.src = modalImagePreview.src;
                
                // Marca que la foto ha sido confirmada
                fotoConfirmadaInput.value = 'true';

                confirmationModal.style.display = 'none'; // Cierra el modal
                
                // Mantenemos el archivo en el input[type=file] para que se envíe con el formulario
            }
        }

        // 3. Cancela la selección, cierra el modal y resetea el campo de archivo
        function changeImage() {
            const fileInput = document.getElementById('foto_perfil');
            const confirmationModal = document.getElementById('confirmationModal');
            
            // Resetea el input para permitir seleccionar otro archivo
            fileInput.value = ''; 
            tempFile = null;
            
            // Revertir a la foto actual (ya guardada en el perfil)
            const initialUrl = '<?= $foto_perfil_url ?>';

            document.getElementById('currentProfilePic').src = initialUrl;
            document.getElementById('headerProfilePic').src = initialUrl;

            document.getElementById('foto_confirmada').value = 'false';

            confirmationModal.style.display = 'none'; // Cierra el modal
        }

        // Cierra el modal de confirmación si se presiona ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape" && document.getElementById('confirmationModal').style.display === 'flex') {
                document.getElementById('confirmationModal').style.display = 'none';
            }
        });
    </script>
</body>
</html>