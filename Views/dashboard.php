<?php
// 1. 🏁 Iniciar sesión
session_start();

// 2. 🛡 Seguridad de la página
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'cliente') {
    header("Location: login.php");
    exit;
}

// 3. 👩‍💻 Obtenemos los datos del usuario de la sesión
$nombre_usuario = $_SESSION['usuario_nombre'];
$id_usuario_logueado = $_SESSION['usuario_id'];

// 4. 🗄 Incluimos la conexión a la BD (PDO)
require '../Modelo/conexion.php';

// 5. 🐶 Consultamos las mascotas del usuario logueado (con PDO)
$stmt = $conexion->prepare("SELECT * FROM mascotas_cliente WHERE id_usuario = ?");
$stmt->execute([$id_usuario_logueado]);
$mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link src="../img/veterinarialogo.png" rel="icon" type="image/png">
    <!-- Agregamos la librería de iconos de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        /* Estilos específicos para los botones de acceso rápido */
        
        .contenedor-accesos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }

        /* Estilo base IGUAL para todos los botones */
        .accesos-rapidos {
            display: flex;
            align-items: center;
            background-color: #ffffff !important; /* Forzamos blanco para todos */
            border-radius: 12px;
            padding: 15px 20px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            gap: 15px;
        }

        /* Efecto Hover: Cambia a celeste cuando pasas el mouse */
        .accesos-rapidos:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(37, 183, 219, 0.15);
            background-color: #e0f2fe !important; /* Fondo celeste suave al pasar el mouse */
            border-color: #bae6fd;
        }

        /* El cuadrito del icono */
        .icono-marco {
            background-color: #e0f2fe; /* Celeste claro por defecto */
            color: #0284c7;            /* Icono azul fuerte */
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background-color 0.3s;
        }

        /* Al pasar el mouse sobre el botón, el cuadrito se vuelve blanco para resaltar */
        .accesos-rapidos:hover .icono-marco {
            background-color: #ffffff;
        }
        
        /* Ajuste de tamaño de icono */
        .material-symbols-outlined {
            font-size: 24px;
        }
    </style>
</head>
<body>
    <!-- Header -->
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

    <!-- Main Content -->
    <main class="principal">
        <section class="seccion-bienvenida">
            <h1>BIENVENIDO, <?php echo htmlspecialchars($nombre_usuario); ?></h1>
        </section>

        <section class="seccion-companeros">
            <h3>Tus Compañeros</h3>
            <div class="contenedor-companeros">
                <?php
                if (count($mascotas) > 0) {
                    foreach ($mascotas as $mascota) {
                        $ruta_imagen = "img/gato-siames.jpeg"; // Imagen por defecto
                        if (!empty($mascota['imagen'])) {
                            $ruta_imagen = "uploads/" . htmlspecialchars($mascota['imagen']);
                        }
                ?>
                        <div class="tarjeta-companero">
                            <img class="imagen-companero" src="<?php echo $ruta_imagen; ?>" alt="<?php echo htmlspecialchars($mascota['nombre']); ?>">
                            <h4><?php echo htmlspecialchars($mascota['nombre']); ?></h4>
                            <p><?php echo htmlspecialchars($mascota['especie']); ?>, <?php echo htmlspecialchars($mascota['raza']); ?></p>
                            <button onclick="location.href='perfilmascota1.php?id=<?php echo $mascota['id']; ?>'">Ver Perfil Completo</button>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>No tienes mascotas registradas todavía.</p>";
                }
                ?>

                <!-- Tarjeta para añadir nueva mascota -->
                <div class="anadir-companero">
                   <a href="registrar_mascota_cliente.php" class="btn-anadir" style="text-decoration: none; color: var(--primary); font-weight: 600; font-size: 15px; text-align: center;">+ Añadir Nueva Mascota</a>
                </div>
            </div>
        </section>

        <section class="seccion-citas">
            <h3>Próximas Citas</h3>
            <div class="contenedor-citas">
                <div class="tarjeta-cita">
                    <p>Martes, 25 de Octubre - 10:00 AM</p>
                    <p>Consulta general para Max</p>
                    <p>Dr. Alan García</p>
                    <button>Reprogramar</button>
                    <button>Cancelar</button>
                </div>
                <div class="tarjeta-cita">
                    <p>Viernes, 28 de Octubre - 03:00 PM</p>
                    <p>Vacunación para Luna</p>
                    <p>Dr. Keiko Fujimori</p>
                    <button>Reprogramar</button>
                    <button>Cancelar</button>
                </div>
            </div>
        </section>

        <section class="seccion-recordatorios">
            <h3>Recordatorios Importantes</h3>
            <div class="contenedor-recordatorios">
                <div class="tarjeta-recordatorio">
                    <p>Vacuna anual de Max pendiente.</p>
                    <button>Agendar ahora</button>
                </div>
                <div class="tarjeta-recordatorio">
                    <p>Resultados de laboratorio de Luna disponibles.</p>
                    <button>Ver resultados</button>
                </div>
            </div>
        </section>

        <!-- Seccion Accesos Rápidos -->
        <section class="seccion-accesos">
            <h3>Accesos Rápidos</h3>
            <div class="contenedor-accesos">
                
                <!-- Botón 1: Reservar Cita (Ahora igual a los demás) -->
                <a class="accesos-rapidos" href="reserva.php">
                    <div class="icono-marco">
                        <span class="material-symbols-outlined">calendar_month</span>
                    </div>
                    <span>Reservar Nueva Cita</span>
                </a>

                <!-- Botón 2: Historial -->
                <a class="accesos-rapidos" href="historial-clinico.php">
                    <div class="icono-marco">
                        <span class="material-symbols-outlined">history</span>
                    </div>
                    <span>Ver Historial de Citas</span>
                </a>

                <!-- Botón 3: Tienda -->
                <a class="accesos-rapidos" href="tienda.php">
                    <div class="icono-marco">
                        <span class="material-symbols-outlined">storefront</span>
                    </div>
                    <span>Ir a la Tienda Online</span>
                </a>

                <!-- Botón 4: Contacto -->
                <a class="accesos-rapidos" href="contacto.php">
                    <div class="icono-marco">
                        <span class="material-symbols-outlined">call</span>
                    </div>
                    <span>Contactar a la Clínica</span>
                </a>

                <!-- Botón 5: Seguimiento -->
                <a class="accesos-rapidos" href="cliente_monitoreo.php">
                    <div class="icono-marco">
                        <span class="material-symbols-outlined">monitor_heart</span>
                    </div>
                    <span>Seguimiento</span>
                </a>

                <!-- Botón 6: Servicios -->
                <a class="accesos-rapidos" href="servicios.php">
                    <div class="icono-marco">
                        <span class="material-symbols-outlined">medical_services</span>
                    </div>
                    <span>Servicios Disponibles</span>
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="pie-pagina">
        <div class="contenedor-pie">
            <p>© 2025 Clínica Veterinaria del Norte S.A.C. Todos los derechos reservados.</p>
            <nav>
                <ul>
                    <li><a href="#">Términos de Servicio</a></li>
                    <li><a href="#">Política de Privacidad</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </footer>
</body>
</html>

//cambiar el dashboard
