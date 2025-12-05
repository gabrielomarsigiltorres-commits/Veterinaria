<?php
// Views/registrar_mascota_cliente.php

// 1. 🏁 ¡SIEMPRE iniciar la sesión primero!
session_start();

// 2. 🛡️ Seguridad: Verificar si el usuario está logueado
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

// 3. Obtener el nombre del usuario para el encabezado
$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'Cliente';

// 4. Obtener mensajes del Controlador
$mensaje = "";
$status = $_GET['status'] ?? '';
$msg = $_GET['msg'] ?? '';

if (!empty($msg)) {
    if ($status === 'success') {
        $mensaje = "<div class='mensaje-exito'>✅ " . htmlspecialchars(urldecode($msg)) . "</div>";
    } else {
        $mensaje = "<div class='mensaje-error'>❌ " . htmlspecialchars(urldecode($msg)) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar a tu consentido</title>
    
    <link rel="stylesheet" href="css/registro.css"> 
    <link rel="stylesheet" href="css/dashboard.css">
    
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* ==================================== */
        /* 1. Estilos Estructurales y de Fondo */
        /* ==================================== */
        body {
            background-color: #ffffff; /* Fondo blanco solicitado */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Encabezado Fijo */
        .encabezado {
            position: sticky; 
            top: 0; 
            z-index: 1000;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }

        /* Contenido principal para centrado vertical y horizontal del formulario */
        main.contenido {
            flex-grow: 1; 
            display: flex;
            justify-content: center; 
            align-items: center; 
            padding: 30px 20px;
            max-width: 100%;
            margin: 0 auto;
        }
        
        .tabla-section {
            background-color: #ffffff;
            max-width: 650px;
            width: 100%;
            margin: 0 auto;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
        }

        /* ==================================== */
        /* 2. Estilos de Formulario y Botones */
        /* ==================================== */
        .form-group label {
            display: block;
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        .form-input:focus {
            border-color: #38b6ab; 
            box-shadow: 0 0 5px rgba(56, 182, 171, 0.5);
            outline: none;
        }
        .grid-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem; 
            margin-bottom: 1.5rem;
        }
        .grid-form-double {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem; 
        }
        @media (min-width: 768px) {
            .grid-form-double {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        /* Estilos de mensaje (Exito/Error) */
        .mensaje-exito { background-color: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: bold; }
        .mensaje-error { background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: bold; }
        
        /* Botones Ordenados (Volver y Registrar) */
        .botones-ordenados {
            display: flex;
            justify-content: space-between; 
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap; 
        }
        
        .btn-submit, .btn-cancelar {
            flex-grow: 1; 
            text-align: center;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%; 
        }
        
        /* Colores Solicitados: Verde/Teal para Registrar, Rojo para Volver */
        .btn-submit { 
            background-color: #38b6ab; /* Verde/Teal */
            color: white;
        }
        .btn-submit:hover { 
            background-color: #2d9b91; 
        }

        .btn-cancelar { 
            background-color: #e44d4d; /* Rojo */
            color: white;
            text-decoration: none;
        }
        .btn-cancelar:hover { 
            background-color: #c93737; 
        }

        @media (min-width: 600px) {
            .btn-submit, .btn-cancelar {
                width: auto; 
            }
        }

        /* ==================================== */
        /* 4. Vista Previa y Modal de Confirmación */
        /* ==================================== */
        
        /* Vista Previa Circular en el Formulario (Tamaño Mediano) */
        #finalPreviewContainer {
            margin-top: 15px;
            display: none; /* Oculto por defecto */
            align-items: center;
            gap: 20px;
        }
        #finalPreview {
            width: 100px;
            height: 100px;
            border-radius: 50%; /* Círculo */
            object-fit: cover;
            border: 3px solid #38b6ab;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            cursor: pointer;
        }
        #finalPreviewText {
            font-size: 0.9rem;
            color: #555;
            font-weight: 500;
        }


        /* Modal Overlay (Ventana de Confirmación) */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        
        /* Modal Content (Sub-ventana de Previsualización) */
        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 20px;
            max-width: 450px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            text-align: center;
        }
        
        #modalImagePreview {
            display: block;
            max-width: 100%;
            height: auto;
            max-height: 300px;
            margin: 0 auto 15px;
            border-radius: 8px;
        }

        .modal-buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            gap: 15px;
        }

        .btn-modal {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-confirmar {
            background-color: #38b6ab; /* Teal */
            color: white;
        }
        .btn-confirmar:hover {
            background-color: #2d9b91;
        }

        .btn-cambiar {
            background-color: #f4f4f4; /* Gris */
            color: #333;
            border: 1px solid #ddd;
        }
        .btn-cambiar:hover {
            background-color: #eaeaea;
        }
    </style>
</head>
<body>
    
    <header class="encabezado">
        <div class="contenedor-encabezado">
            <img class="logo" src="../img/veterinarialogo.png" alt="Logo">
            <h1>Clínica Veterinaria del Norte S.A.C</h1>
            <nav class="nav-principal">
                <ul>
                    <li><a href="perfilmascota1.php">Mascotas</a></li>
                    <li><a href="reserva.php">Citas</a></li>
                    <li><a href="tienda.php">Tienda</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </nav>
            <div class="perfil-usuario">
                <a href="anuncio_cliente.php" class="campana" title="Ver anuncios importantes">🔔</a>
                <span class="nombre-usuario">👤 <?php echo htmlspecialchars($nombre_usuario); ?></span>
                <a href="../Controller/cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="contenido">
        
        <div> 
            <div class="bienvenida">
                <h2 style="color: #13b6ec;">🐶 ¡Registra a tu consentido! 😼</h2>
                <p>Ayúdanos a conocer a tu mascota ingresando sus datos para brindarle el mejor cuidado.</p>
            </div>

            <div class="tabla-section">
                
                <h3 style="color: #13b6ec; margin-bottom: 25px; text-align: center;">Datos de la Mascota y Contacto</h3>

                <?= $mensaje ?>

                <form method="POST" action="../Controller/MascotaClienteController.php" enctype="multipart/form-data">
                    
                    <div class="grid-form">
                        
                        <div>
                            <label for="nombre">🐾 Nombre de la Mascota:</label>
                            <input type="text" id="nombre" name="nombre" required class="form-input">
                        </div>

                        <div class="grid-form-double">
                            <div>
                                <label for="especie">🦴 Especie:</label>
                                <input type="text" id="especie" name="especie" required class="form-input">
                            </div>
                            <div>
                                <label for="raza">🐕 Raza:</label>
                                <input type="text" id="raza" name="raza" class="form-input">
                            </div>
                        </div>

                        <div class="grid-form-double">
                            <div>
                                <label for="sexo">⚧ Sexo:</label>
                                <select id="sexo" name="sexo" required class="form-input">
                                    <option value="">Seleccione...</option>
                                    <option value="Macho">Macho</option>
                                    <option value="Hembra">Hembra</option>
                                </select>
                            </div>
                            <div>
                                <label for="fecha_nacimiento">🎂 Fecha de Nacimiento:</label>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required onchange="calcularEdad()" class="form-input">
                            </div>
                        </div>

                        <div>
                            <label for="edad">🧮 Edad (años):</label>
                            <input type="text" id="edad" name="edad_display" placeholder="Se calculará automáticamente" readonly class="form-input bg-gray-100">
                        </div>

                        <div>
                            <label for="direccion">🏡 Dirección:</label>
                            <input type="text" id="direccion" name="direccion" required class="form-input">
                        </div>

                        <div class="grid-form-double">
                            <div>
                                <label for="contacto">📞 Número de Contacto:</label>
                                <input type="text" id="contacto" name="contacto" required class="form-input">
                            </div>
                            <div>
                                <label for="correo">📧 Correo Electrónico:</label>
                                <input type="email" id="correo" name="correo" required class="form-input">
                            </div>
                        </div>

                        <div>
                            <label for="alergias">💊 Alergias:</label>
                            <textarea id="alergias" name="alergias" rows="3" class="form-input" placeholder="Describa si su mascota tiene alguna alergia..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="imagen_mascota">📸 Foto de la Mascota:</label>
                            
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <input type="file" id="imagen_mascota" name="imagen_mascota" accept="image/png, image/jpeg" required onchange="handleFileSelect(event)">
                                <div id="finalPreviewContainer">
                                    <img id="finalPreview" src="#" alt="Foto de Mascota Confirmada" title="Click para ver en grande">
                                    <span id="finalPreviewText">Foto cargada</span>
                                </div>
                            </div>
                            
                            <input type="hidden" id="imagen_confirmada_src" name="imagen_confirmada_src" value="">
                        </div>
                    
                    </div>
                    
                    <div class="botones-ordenados">
                        <a href="dashboard.php" class="btn-cancelar">
                            <i data-lucide="x" style="width: 18px;"></i>
                            Volver
                        </a>
                        <button type="submit" class="btn-submit">
                            <i data-lucide="plus" style="width: 18px;"></i>
                            Registrar Mascota
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <div class="modal-overlay" id="confirmationModal">
        <div class="modal-content">
            <h4 style="margin-top: 0; color: #38b6ab;">Previsualización de Foto</h4>
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
        
        let tempImageSrc = null;

        // 🧮 Función para calcular edad automáticamente
        function calcularEdad() {
            const fechaNac = document.getElementById("fecha_nacimiento").value;
            const edadInput = document.getElementById("edad");

            if (fechaNac) {
                const hoy = new Date();
                const nacimiento = new Date(fechaNac);
                let edad = hoy.getFullYear() - nacimiento.getFullYear();
                const m = hoy.getMonth() - nacimiento.getMonth();

                if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
                    edad--;
                }
                edadInput.value = edad + " años";
            } else {
                edadInput.value = "";
            }
        }
        
        // 1. Maneja la selección del archivo, previsualiza en modal y espera confirmación
        function handleFileSelect(event) {
            const reader = new FileReader();
            const modalImagePreview = document.getElementById('modalImagePreview');
            const confirmationModal = document.getElementById('confirmationModal');
            
            if (event.target.files && event.target.files[0]) {
                reader.onload = function(){
                    if(reader.readyState == 2){
                        tempImageSrc = reader.result; // Almacena temporalmente la URL de datos
                        
                        // Muestra la imagen en el modal
                        modalImagePreview.src = tempImageSrc;
                        confirmationModal.style.display = 'flex'; // Abre el modal
                    }
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        // 2. Confirma la imagen, cierra el modal y la muestra en el formulario
        function confirmImage() {
            const finalPreview = document.getElementById('finalPreview');
            const finalPreviewContainer = document.getElementById('finalPreviewContainer');
            const confirmationModal = document.getElementById('confirmationModal');

            if (tempImageSrc) {
                finalPreview.src = tempImageSrc;
                finalPreviewContainer.style.display = 'flex'; // Muestra el círculo de previsualización
                confirmationModal.style.display = 'none'; // Cierra el modal
            }
        }

        // 3. Cancela la selección, cierra el modal y resetea el campo de archivo
        function changeImage() {
            const input = document.getElementById('imagen_mascota');
            const confirmationModal = document.getElementById('confirmationModal');
            
            // Resetea el input para permitir seleccionar otro archivo
            input.value = ''; 
            tempImageSrc = null;
            
            // Oculta la previsualización final (en caso de que ya estuviera visible)
            document.getElementById('finalPreviewContainer').style.display = 'none';

            confirmationModal.style.display = 'none'; // Cierra el modal
        }

        // Si se presiona ESC, cierra el modal
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape" && document.getElementById('confirmationModal').style.display === 'flex') {
                // Si el usuario cancela sin confirmar, el archivo sigue seleccionado
                // Aquí solo cerramos la ventana de confirmación.
                document.getElementById('confirmationModal').style.display = 'none';
            }
        });

    </script>
</body>
</html>