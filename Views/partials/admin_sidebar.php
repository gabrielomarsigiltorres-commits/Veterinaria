<?php
// Obtiene el nombre del archivo actual para saber cuál enlace marcar como 'activo'
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
?>

<aside class="barra-lateral">
    <div class="logo-contenedor">
        <!-- Ruta sube un nivel (de /partials) y baja a /img -->
        <img src="../img/veterinarialogo.png" alt="Logo Veterinaria" class="logo-imagen">
        <span class="logo-texto">Admin<br>Veterinaria del Norte</span>
    </div>
    
    <nav class="navegacion-principal">
        <!-- 
          Ruta del Dashboard: Sube un nivel (de /partials a /Views)
          y busca dashboard_admin.php 
        -->
        <a href="dashboard_admin.php" 
           class="<?php echo ($paginaActual == 'dashboard_admin.php') ? 'activo' : ''; ?>">
            <i data-lucide="layout-dashboard"></i> <span>Dashboard</span>
        </a>
        
        <!-- 
          Ruta de Páginas Admin: Sube un nivel (de /partials a /Views)
          y busca el archivo (ej. clientes.php)
        -->
        <a href="clientes.php" 
           class="<?php echo ($paginaActual == 'clientes.php' || $paginaActual == 'cliente_detalle.php') ? 'activo' : ''; ?>">
            <i data-lucide="users"></i> <span>Clientes</span>
        </a>
        <a href="mascotas_admin.php" 
           class="<?php echo ($paginaActual == 'mascotas_admin.php') ? 'activo' : ''; ?>">
            <i data-lucide="paw-print"></i> <span>Mascotas</span>
        </a>
        <a href="citas_admin.php" 
           class="<?php echo ($paginaActual == 'citas_admin.php') ? 'activo' : ''; ?>">
            <i data-lucide="calendar"></i> <span>Citas</span>
        </a>
         <a href="admin_monitoreo.php" 
           class="<?php echo ($paginaActual == 'admin_monitoreo.php') ? 'activo' : ''; ?>">
            <i data-lucide="eye"></i> <span>Monitoreo</span>
        </a>
        <a href="servicios_admin.php" 
           class="<?php echo ($paginaActual == 'servicios_admin.php') ? 'activo' : ''; ?>">
            <i data-lucide="stethoscope"></i> <span>Servicios</span>
        </a>
        <a href="admin_productos.php" 
           class="<?php echo ($paginaActual == 'admin_productos.php' || $paginaActual == 'admin_producto_form.php') ? 'activo' : ''; ?>">
            <i data-lucide="shopping-basket"></i> <span>Productos</span>
        </a>
        <a href="anuncios_admin.php" 
           class="<?php echo ($paginaActual == 'anuncios_admin.php') ? 'activo' : ''; ?>">
            <i data-lucide="megaphone"></i> <span>Anuncios</span>
        </a>
        <a href="reportes.php" 
           class="<?php echo ($paginaActual == 'reportes.php') ? 'activo' : ''; ?>">
            <i data-lucide="bar-chart-3"></i> <span>Reportes</span>
        </a>
    </nav>
    
    <div class="navegacion-secundaria">
        <a href="configuracion.php" 
           class="<?php echo ($paginaActual == 'configuracion.php') ? 'activo' : ''; ?>">
           <i data-lucide="settings"></i> <span>Configuración</span>
        </a>
        <!-- 
          Ruta de Salida: Sube un nivel (de /Views/partials a /Views) y luego sube otro (a /Veterinaria) 
          y entra a /Controller 
        -->
        <a href="../Controller/cerrar_sesion.php" class="enlace-logout">
            <i data-lucide="log-out"></i> <span>Cerrar Sesión</span>
        </a>
    </div>
</aside>