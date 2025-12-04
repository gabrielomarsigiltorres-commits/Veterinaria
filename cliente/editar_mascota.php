<?php
include_once("../includes/conexion.php"); // Usa tu conexión PDO

if (!isset($_GET['id'])) {
    die("Error: Mascota no especificada.");
}

$id = $_GET['id'];

// Obtener los datos de la mascota
$sql = "SELECT * FROM mascotas WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$mascota = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mascota) {
    die("Error: Mascota no encontrada.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $especie = $_POST["especie"];
    $raza = $_POST["raza"];
    $sexo = $_POST["sexo"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $direccion = $_POST["direccion"];
    $contacto = $_POST["contacto"];
    $correo = $_POST["correo"];
    $alergias = $_POST["alergias"];

    // Calcular edad automáticamente
    $fecha_nac = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $fecha_nac->diff($hoy)->y;

    $sql_update = "UPDATE mascotas SET 
        nombre = ?, especie = ?, raza = ?, sexo = ?, fecha_nacimiento = ?, edad = ?, 
        direccion = ?, contacto = ?, correo = ?, alergias = ?
        WHERE id = ?";
    $stmt_update = $pdo->prepare($sql_update);

    $ok = $stmt_update->execute([
        $nombre, $especie, $raza, $sexo, $fecha_nacimiento, $edad,
        $direccion, $contacto, $correo, $alergias, $id
    ]);

    if ($ok) {
        echo "<script>alert('✅ Mascota actualizada correctamente'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('❌ Error al actualizar la mascota');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mascota | Veterinaria</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

<div class="contenedor-perfil-mascota">
    <header class="cabecera-perfil">
        <div class="info-perfil">
            <h1>Editar Mascota</h1>
            <p>Actualiza la información de tu mascota</p>
        </div>
    </header>

    <section class="seccion-datos">
        <form action="" method="POST" class="formulario-datos-mascota">
            <div class="grupo-campo-datos-principales">
                <div class="grupo-campo">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($mascota['nombre']) ?>" required>
                </div>
                <div class="grupo-campo">
                    <label>Especie:</label>
                    <input type="text" name="especie" value="<?= htmlspecialchars($mascota['especie']) ?>" required>
                </div>
                <div class="grupo-campo">
                    <label>Raza:</label>
                    <input type="text" name="raza" value="<?= htmlspecialchars($mascota['raza']) ?>">
                </div>
                <div class="grupo-campo">
                    <label>Sexo:</label>
                    <select name="sexo" required>
                        <option value="Macho" <?= $mascota['sexo'] == 'Macho' ? 'selected' : '' ?>>Macho</option>
                        <option value="Hembra" <?= $mascota['sexo'] == 'Hembra' ? 'selected' : '' ?>>Hembra</option>
                    </select>
                </div>
                <div class="grupo-campo">
                    <label>Fecha de Nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" value="<?= $mascota['fecha_nacimiento'] ?>" required>
                </div>
                <div class="grupo-campo">
                    <label>Dirección:</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($mascota['direccion']) ?>" required>
                </div>
                <div class="grupo-campo">
                    <label>Contacto:</label>
                    <input type="text" name="contacto" value="<?= htmlspecialchars($mascota['contacto']) ?>" required>
                </div>
                <div class="grupo-campo">
                    <label>Correo:</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($mascota['correo']) ?>" required>
                </div>
                <div class="grupo-campo">
                    <label>Alergias:</label>
                    <textarea name="alergias"><?= htmlspecialchars($mascota['alergias']) ?></textarea>
                </div>
            </div>

            <div class="contenedor-formulario-crud">
                <button type="submit" class="boton-guardar">Guardar Cambios</button>
                <a href="index.php" class="boton-cancelar">Cancelar</a>
            </div>
        </form>
    </section>
</div>

</body>
</html>
