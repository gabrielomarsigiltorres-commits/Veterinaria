<?php
session_start();
// 1. Verificar la sesión del administrador
if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// 📌 Variable para marcar el enlace activo en el sidebar
$pagina_activa = 'clientes'; 

require '../Modelo/conexion.php'; 

// Inicializar variables para el formulario
$titulo_pagina = "Añadir Nuevo Cliente";
$nombre_completo = "";
$correo_electronico = "";
$id_cliente = null;
$es_edicion = false;
$action_url = "../Controller/admin_cliente_controlador.php?accion=guardar"; // Acción por defecto: CREAR

// 2. Lógica para EDICIÓN
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_cliente = $_GET['id'];
    $es_edicion = true;
    $titulo_pagina = "Editar Cliente (ID: " . htmlspecialchars($id_cliente) . ")";
    $action_url = "../Controller/admin_cliente_controlador.php?accion=actualizar"; // La ID se enviará en el campo oculto
    
    // Obtener datos del cliente para prellenar el formulario
    try {
        // Obtenemos solo los datos necesarios para prellenar
        $stmt = $conexion->prepare("SELECT nombres_completos, correo_electronico FROM usuarios WHERE id = ? AND tipo_usuario = 'cliente'");
        $stmt->execute([$id_cliente]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            $nombre_completo = $cliente['nombres_completos'];
            $correo_electronico = $cliente['correo_electronico'];
        } else {
            // Si el cliente no existe o no es de tipo 'cliente'
            header("Location: clientes.php?status=error&msg=" . urlencode("Cliente no encontrado o no válido."));
            exit;
        }

    } catch (PDOException $e) {
        die("Error al cargar datos del cliente: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?> - Admin</title>
    <link rel="stylesheet" href="css/dashboard_admin.css"> 
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Ajuste de estilos para usar las clases del dashboard_admin.css */
        .formulario-tarjeta {
            /* Mapeo de la tarjeta de dashboard_admin.css */
            background: var(--color-tarjeta);
            padding: 24px;
            border-radius: 8px;
            border: 1px solid var(--color-borde);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 30px auto;
        }
        .formulario-tarjeta h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--color-borde);
            padding-bottom: 10px;
        }
        /* Mapeo de grupos de formulario */
        .grupo-control {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .grupo-control label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--color-texto);
        }
        /* Reutilización de estilos de input del dashboard_admin.css */
        .grupo-control input[type="text"], 
        .grupo-control input[type="email"],
        .grupo-control input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--color-borde);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .grupo-control input:focus {
            outline: none;
            border-color: var(--color-primario);
            box-shadow: 0 0 0 3px var(--color-primario-claro);
        }
        /* Mapeo de botones de acción */
        .botones-accion {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
            border-top: 1px solid var(--color-borde);
            padding-top: 20px;
        }
        .boton-guardar {
            /* Usar el estilo de boton-nuevo */
            background: var(--color-primario);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }
        .boton-guardar:hover { background: #1a42a0; }

        .boton-cancelar {
            /* Usar un estilo secundario, similar a boton-accion.cancelar */
            background-color: #e5e7eb;
            color: var(--color-texto);
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .boton-cancelar:hover { background-color: #d1d5db; }
    </style>
</head>
<body>
    <div class="contenedor">
        
        <?php include 'partials/admin_sidebar.php'; ?>
        
        <main class="contenido-principal">
            
            <header class="cabecera-principal">
                <h1><?= $titulo_pagina ?></h1>
                <div class="info-usuario">
                    <img src="../img/logo.jpg" alt="Admin" class="avatar">
                    <div>
                        <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
                        <p class="rol-usuario">Administrador</p>
                    </div>
                </div>
            </header>

            <section class="contenido-pagina">
                
                <div class="formulario-tarjeta">
                    <h2><?= $es_edicion ? 'Datos del Cliente' : 'Registro de Cliente' ?></h2>
                    
                    <form action="<?= $action_url ?>" method="POST">

                        <?php if ($es_edicion): ?>
                            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($id_cliente) ?>">
                        <?php endif; ?>

                        <input type="hidden" name="accion" value="<?= $es_edicion ? 'actualizar' : 'guardar' ?>">


                        <div class="grupo-control">
                            <label for="nombres_completos">Nombre Completo:</label>
                            <input type="text" id="nombres_completos" name="nombres_completos" 
                                value="<?= htmlspecialchars($nombre_completo) ?>" 
                                required placeholder="Ej: Juan Pérez">
                        </div>

                        <div class="grupo-control">
                            <label for="correo_electronico">Correo Electrónico (Usuario):</label>
                            <input type="email" id="correo_electronico" name="correo_electronico" 
                                value="<?= htmlspecialchars($correo_electronico) ?>" 
                                required placeholder="Ej: juan@ejemplo.com">
                        </div>

                        <div class="grupo-control">
                            <label for="password">Contraseña 
                                <?php if ($es_edicion): ?>(Dejar vacío para no cambiar)<?php endif; ?>:
                            </label>
                            <input type="password" id="password" name="password" 
                                <?php if (!$es_edicion): ?>required<?php endif; ?> 
                                placeholder="Mínimo 6 caracteres">
                        </div>
                        
                        <div class="botones-accion">
                            <button type="submit" class="boton-guardar">
                                <i data-lucide="save"></i> 
                                <?= $es_edicion ? 'Actualizar Cliente' : 'Guardar Cliente' ?>
                            </button>
                            <a href="clientes.php" class="boton-cancelar">
                                <i data-lucide="x"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>

            </section>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>