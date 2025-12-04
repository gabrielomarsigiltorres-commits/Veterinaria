<?php
// 1. 🏁 Iniciar sesión y conexión
session_start();
require '../Modelo/conexion.php'; // Trae $conexion (PDO)

// 2. 🛡️ Seguridad
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit;
}
$id_usuario_logueado = $_SESSION['usuario_id'];

// 3. 🆔 Validar ID de mascota
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó ID de mascota.");
}
$id_mascota = $_GET['id']; 

$mensaje = "";
$mascota = null; 

// 4. 🧠 Lógica de ACCIONES (Actualizar o Borrar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- ACCIÓN: ACTUALIZAR ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar') {
        
        $nombre = $_POST['nombre'];
        $especie = $_POST['especie'];
        $raza = $_POST['raza'];
        $sexo = $_POST['sexo'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $direccion = $_POST['direccion'];
        $contacto = $_POST['contacto'];
        $correo = $_POST['correo'];
        $alergias = $_POST['alergias'];

        // Calcular nueva edad
        $edad = 0;
        if (!empty($fecha_nacimiento)) {
            $fecha_nac = new DateTime($fecha_nacimiento);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nac)->y;
        }
        
        try {
            $sql_update = "UPDATE mascotas_cliente 
                           SET nombre = :nombre, especie = :especie, raza = :raza, sexo = :sexo,
                               fecha_nacimiento = :fecha_nac, edad = :edad, direccion = :dir, 
                               contacto = :cont, correo = :email, alergias = :alerg
                           WHERE id = :id AND id_usuario = :id_user";
            
            $stmt = $conexion->prepare($sql_update);
            $resultado = $stmt->execute([
                ':nombre' => $nombre,
                ':especie' => $especie,
                ':raza' => $raza,
                ':sexo' => $sexo,
                ':fecha_nac' => $fecha_nacimiento,
                ':edad' => $edad,
                ':dir' => $direccion,
                ':cont' => $contacto,
                ':email' => $correo,
                ':alerg' => $alergias,
                ':id' => $id_mascota,
                ':id_user' => $id_usuario_logueado
            ]);

            if ($resultado) {
                $mensaje = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>¡Datos actualizados con éxito!</div>";
            }
        } catch (PDOException $e) {
            $mensaje = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error al actualizar: " . $e->getMessage() . "</div>";
        }
    }

    // --- ACCIÓN: ELIMINAR ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
        try {
            $sql_delete = "DELETE FROM mascotas_cliente WHERE id = :id AND id_usuario = :id_user";
            $stmt = $conexion->prepare($sql_delete);
            $resultado = $stmt->execute([':id' => $id_mascota, ':id_user' => $id_usuario_logueado]);
            
            if ($resultado) {
                header("Location: dashboard.php"); // Redirigir al dashboard
                exit;
            }
        } catch (PDOException $e) {
             $mensaje = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error al eliminar: " . $e->getMessage() . "</div>";
        }
    }
}


// 5. 🐶 Lógica de VISUALIZACIÓN (Cargar datos)
try {
    $sql_mascota = "SELECT * FROM mascotas_cliente WHERE id = :id AND id_usuario = :id_user";
    $stmt = $conexion->prepare($sql_mascota);
    $stmt->execute([':id' => $id_mascota, ':id_user' => $id_usuario_logueado]);
    $mascota = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mascota) {
        die("<div style='text-align:center; padding:50px;'>Error: Mascota no encontrada o no te pertenece. <a href='dashboard.php'>Volver</a></div>");
    }

    // Lógica imagen
    $ruta_imagen = "img/gato-siames.jpeg"; // Imagen por defecto (asegúrate que esta ruta sea válida desde Views/)
    if (!empty($mascota['imagen'])) {
        $ruta_imagen = "uploads/" . htmlspecialchars($mascota['imagen']);
    }

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Perfil de <?php echo htmlspecialchars($mascota['nombre']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/perfilmascota1.css" />
</head>

<body class="bg-gray-100 font-[Manrope] min-h-screen flex flex-col items-center py-10 px-4">

  <form class="w-full max-w-3xl" method="POST" enctype="multipart/form-data">
    
    <?php if (!empty($mensaje)) echo $mensaje; ?>

    <section class="w-full bg-white rounded-2xl shadow-lg p-6">
      <!-- Encabezado con foto -->
      <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 border-b pb-6 mb-6">
        <div class="relative">
          <!-- Mostramos la imagen -->
          <div class="w-36 h-36 rounded-full bg-gray-200 bg-cover bg-center border-4 border-white shadow-md"
            style='background-image: url("<?php echo $ruta_imagen; ?>");'></div>
        </div>
        <div class="flex flex-col text-center sm:text-left">
          <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($mascota['nombre']); ?></h1>
          <p class="text-gray-500 text-sm">
            <?php echo htmlspecialchars($mascota['especie']); ?> - 
            <?php echo htmlspecialchars($mascota['raza']); ?> - 
            <?php echo htmlspecialchars($mascota['edad']); ?> años
          </p>
        </div>
        <a href="dashboard.php" class="sm:ml-auto mt-4 sm:mt-0 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm transition">
          Volver al Dashboard
        </a>
      </div>

      <!-- Información de la mascota (editable) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Input oculto para identificar la acción -->
        <input type="hidden" name="accion" value="actualizar">
      
        <div class="flex flex-col">
          <label class="label font-semibold text-gray-700">Nombre:</label>
          <input type="text" name="nombre" value="<?php echo htmlspecialchars($mascota['nombre']); ?>" class="input border rounded p-2">
        </div>
        <div class="flex flex-col">
          <label class="label font-semibold text-gray-700">Especie:</label>
          <input type="text" name="especie" value="<?php echo htmlspecialchars($mascota['especie']); ?>" class="input border rounded p-2">
        </div>
        <div class="flex flex-col">
          <label class="label font-semibold text-gray-700">Raza:</label>
          <input type="text" name="raza" value="<?php echo htmlspecialchars($mascota['raza']); ?>" class="input border rounded p-2">
        </div>
        <div class="flex flex-col">
          <label class="label font-semibold text-gray-700">Sexo:</label>
          <select name="sexo" class="input border rounded p-2">
             <option value="Macho" <?= $mascota['sexo'] == 'Macho' ? 'selected' : '' ?>>Macho</option>
             <option value="Hembra" <?= $mascota['sexo'] == 'Hembra' ? 'selected' : '' ?>>Hembra</option>
          </select>
        </div>
        <div class="flex flex-col">
          <label class="label font-semibold text-gray-700">Fecha de Nacimiento:</label>
          <input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($mascota['fecha_nacimiento']); ?>" class="input border rounded p-2">
        </div>
        <div class="flex flex-col">
          <label class="label font-semibold text-gray-700">Dirección:</label>
          <input type="text" name="direccion" value="<?php echo htmlspecialchars($mascota['direccion']); ?>" class="input border rounded p-2">
        </div>
        <div class="flex flex-col">
          <label class="label font-semibold text-gray-700">Contacto:</label>
          <input type="text" name="contacto" value="<?php echo htmlspecialchars($mascota['contacto']); ?>" class="input border rounded p-2">
        </div>
        <div class="flex flex-col">
          <label class="label font-semibold text-gray-700">Correo:</label>
          <input type="email" name="correo" value="<?php echo htmlspecialchars($mascota['correo']); ?>" class="input border rounded p-2">
        </div>
        <div class="flex flex-col md:col-span-2">
          <label class="label font-semibold text-gray-700">Alergias:</label>
          <textarea name="alergias" class="input h-20 resize-none border rounded p-2"><?php echo htmlspecialchars($mascota['alergias']); ?></textarea>
        </div>
        
      </div>

      <!-- Botones de Acción -->
      <div class="flex justify-between items-center mt-8 border-t pt-6">
        <!-- Botón Eliminar (hack: cambiar el value del input hidden con JS o usar otro form, aquí uso un botón con name="accion") -->
        <button type="submit" name="accion" value="eliminar" 
                onclick="return confirm('¿Estás seguro de que quieres eliminar a <?php echo htmlspecialchars($mascota['nombre']); ?>? Esta acción no se puede deshacer.');"
                class="bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-4 py-2 rounded-lg text-sm transition">
          Eliminar Mascota
        </button>
        
        <!-- Botón Actualizar -->
        <button type="submit" name="accion" value="actualizar"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg text-sm transition shadow-md">
          Guardar Cambios
        </button>
      </div>

    </section>
  </form>

</body>
</html>