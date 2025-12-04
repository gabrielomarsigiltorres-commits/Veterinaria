<?php
// ------------------- CONEXIÓN A LA BASE DE DATOS -------------------
require_once('../Modelo/conexion.php'); // Usa la conexión PDO centralizada

// ------------------- GUARDAR DATOS DEL FORMULARIO -------------------
$mensaje_exito = "";
$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre  = trim($_POST['full-name'] ?? '');
    $correo  = trim($_POST['email'] ?? '');
    $asunto  = trim($_POST['subject'] ?? '');
    $mensaje = trim($_POST['message'] ?? '');

    if (!empty($nombre) && !empty($correo) && !empty($asunto) && !empty($mensaje)) {
        try {
            $sql = "INSERT INTO mensajes_contacto (nombre_completo, correo_electronico, asunto, mensaje)
                    VALUES (:nombre, :correo, :asunto, :mensaje)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':nombre'  => $nombre,
                ':correo'  => $correo,
                ':asunto'  => $asunto,
                ':mensaje' => $mensaje
            ]);

            $mensaje_exito = "✅ Tu mensaje se envió correctamente. ¡Gracias por contactarnos!";
        } catch (PDOException $e) {
            $mensaje_error = "❌ Error al enviar el mensaje: " . $e->getMessage();
        }
    } else {
        $mensaje_error = "⚠️ Por favor, completa todos los campos antes de enviar.";
    }
}
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

            <!-- CABECERA -->
            <header class="cabecera-sitio">
                <div class="contenedor-limitado">
                    <div class="cabecera-contenido">
                        <div class="logo-contenedor">
                            <span class="logo-icono">🐾</span>
                            <h2 class="logo-texto">Clínica Veterinaria del Norte</h2>
                        </div>
                        <nav class="navegacion-principal" aria-label="Navegación del Sitio">
                            <a href="dashboard.php">Inicio</a>
                            <a href="mascotas.php">Mascotas</a>
                            <a href="citas.php">Citas</a>
                            <a href="tienda.php">Tienda</a>
                            <a href="contacto.php" aria-current="page">Contacto</a>
                        </nav>
                        <div class="cabecera-accion">
                            <button class="boton-primario"><span>Agendar Cita</span></button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="contenido-principal">
                <div class="contenedor-contenido contenedor-limitado">
                    <div class="titulo-pagina">
                        <h1 class="titulo-principal">Ponte en Contacto con Nosotros</h1>
                        <p class="subtitulo-pagina">Elige la opción que mejor se adapte a tu consulta. Estamos listos para atender a tu mascota.</p>
                    </div>

                    <!-- Mensajes de respuesta -->
                    <?php if ($mensaje_exito): ?>
                        <div class="mensaje-exito"><?= htmlspecialchars($mensaje_exito); ?></div>
                    <?php elseif ($mensaje_error): ?>
                        <div class="mensaje-error"><?= htmlspecialchars($mensaje_error); ?></div>
                    <?php endif; ?>

                    <div class="layout-contacto">
                        <!-- COLUMNA IZQUIERDA -->
                        <div class="columna-informacion">
                            <div class="bloque-whatsapp">
                                <h2 class="subtitulo-seccion">Consultas Rápidas y Urgencias</h2>
                                <p class="descripcion-seccion">Este canal es ideal para preguntas sencillas o para notificar una llegada de emergencia.</p>
                                <a class="boton-whatsapp" href="https://wa.me/51956369001?text=Hola, quiero una consulta" target="_blank" rel="noopener noreferrer">
                                    <svg class="icono-whatsapp" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.75 13.96c..." />
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

                        <!-- COLUMNA DERECHA: FORMULARIO -->
                        <div class="columna-formulario">
                            <h2 class="subtitulo-seccion">Para Consultas Detalladas o Solicitudes</h2>
                            <p class="descripcion-seccion">Usa este formulario para agendar citas, solicitar información específica o enviar comentarios.</p>

                            <form action="contacto.php" method="POST" class="formulario-contacto">
                                <div class="campo-formulario">
                                    <label for="full-name">Nombre Completo</label>
                                    <input id="full-name" name="full-name" placeholder="Tu nombre y apellido" type="text" required />
                                </div>
                                <div class="campo-formulario">
                                    <label for="email">Correo Electrónico</label>
                                    <input id="email" name="email" placeholder="tu@email.com" type="email" required />
                                </div>
                                <div class="campo-formulario">
                                    <label for="subject">Asunto</label>
                                    <input id="subject" name="subject" placeholder="Ej: Consulta sobre vacunación" type="text" required />
                                </div>
                                <div class="campo-formulario">
                                    <label for="message">Mensaje</label>
                                    <textarea id="message" name="message" placeholder="Escribe aquí tu consulta..." rows="5" required></textarea>
                                </div>
                                <div>
                                    <button class="boton-primario" type="submit">
                                        <span>Enviar Mensaje</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>

            <!-- PIE DE PÁGINA -->
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
