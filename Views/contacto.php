<?php
// ------------------- CONEXIÓN A LA BASE DE DATOS -------------------
require_once('../Modelo/conexion.php'); // Usa la conexión PDO centralizada

// (Se eliminó la lógica de recepción del formulario porque ya no existe)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Contacta a la Clínica Veterinaria del Norte | Citas y Consultas</title>
    <meta name="description" content="Ponte en contacto con la Clínica Veterinaria del Norte. Llámanos, escríbenos por WhatsApp o envíanos un formulario para agendar citas, consultas y emergencias." />
    <link rel="stylesheet" href="css/contacto.css">
</head>

<body class="pagina-contacto">

    <div class="contenedor-principal">
        <div class="layout-contenedor">

         <header class="encabezado">
        <div class="contenedor-encabezado">
            
            <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                <img class="logo" src="../img/veterinarialogo.png" alt="Logo" style="width: 45px; height: 45px;">
                <h1 style="font-size: 1.2rem; color: #18a0d6; margin: 0;">Clínica Veterinaria del Norte S.A.C</h1>
            </div>
            
            <nav class="nav-principal" style="flex-grow: 1; display: flex; justify-content: center; margin: 0 20px;">
                <ul style="display: flex; list-style: none; padding: 0; margin: 0; gap: 20px;">
                    <li><a href="dashboard.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Menú principal</a></li>
                    <li><a href="servicios.php" style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.2s;">Calendario servicios</a></li>
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

            <main class="contenido-principal">
                <div class="contenedor-contenido contenedor-limitado">
                    <div class="titulo-pagina">
                        <h1 class="titulo-principal">Ponte en Contacto con Nosotros</h1>
                        <p class="subtitulo-pagina">Elige la opción que mejor se adapte a tu consulta. Estamos listos para atender a tu mascota.</p>
                    </div>

                    <div class="layout-contacto">
                        <div class="columna-informacion">
                            <div class="bloque-whatsapp">
                                <h2 class="subtitulo-seccion">Consultas Rápidas y Urgencias</h2>
                                <p class="descripcion-seccion">Este canal es ideal para preguntas sencillas o para notificar una llegada de emergencia.</p>
                                <a class="boton-whatsapp" href="https://wa.me/51956369001?text=Hola, quiero una consulta" target="_blank" rel="noopener noreferrer">
                                    <svg class="icono-whatsapp" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.75 13.96c.25.13.36.26.34.42-.04.28-.27.53-.55.72-.28.19-.65.29-1.07.29-.44 0-1.12-.11-2.09-.54-1.39-.62-2.29-1.57-2.69-2.11-.08-.11-.56-.76-.56-1.44 0-.66.34-.98.46-1.12.1-.11.23-.19.36-.19.11 0 .22.01.32.02.13.01.27.02.37.28.11.26.38.94.41 1.01.04.09.06.2.02.29-.05.09-.08.14-.15.22-.09.09-.18.17-.26.27-.08.09-.17.18-.07.36.26.44 1.14 1.58 2.45 2.16.2.09.38.07.52-.07.11-.11.46-.54.59-.72.11-.16.29-.21.49-.13l1.55.73c.09.04.18.09.24.13zM12 2.18c5.42 0 9.82 4.4 9.82 9.82 0 1.73-.45 3.36-1.23 4.79l.81 2.95-3.03-.79C17.06 20.3 14.63 21 12 21c-5.42 0-9.82-4.4-9.82-9.82S6.58 2.18 12 2.18zM12 0C5.37 0 0 5.37 0 12c0 2.12.55 4.1 1.51 5.82L.09 23.36l5.71-1.49A11.94 11.94 0 0 0 12 24c6.63 0 12-5.37 12-12S18.63 0 12 0z"/>
                                    </svg>
                                    <span>Chatea con nosotros en WhatsApp</span>
                                </a>
                            </div>

                            <div class="bloque-info-contacto">
                                <h3 class="subtitulo-seccion">Información de Contacto</h3>
                                <div class="lista-info-contacto">
                                    <div class="info-item">
                                        <img src="https://cdn-icons-png.flaticon.com/512/535/535188.png" alt="Ubicación" class="icono-img">
                                        <p>AV. CONTINSUYO 464 - INDEPENDENCIA, Lima, Perú</p>
                                    </div>
                                    <div class="info-item">
                                        <img src="https://cdn-icons-png.flaticon.com/512/597/597177.png" alt="Teléfono" class="icono-img">
                                        <p>985 791 723</p>
                                    </div>
                                    <div class="info-item">
                                        <img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" alt="Correo electrónico" class="icono-img">
                                        <p>veterinariadelnorte01@gmail.com</p>
                                    </div>
                                    <div class="info-item">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828859.png" alt="Horario" class="icono-img">
                                        <p>SIEMPRE ABIERTA</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>
                </div>
            </main>

            <footer class="pie-de-pagina">
                <div class="contenedor-limitado">
                    <div class="pie-de-pagina-contenido">
                        <p>© 2025 Clínica Veterinaria del Norte S.A.C. Todos los derechos reservados.</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

</body>
</html>