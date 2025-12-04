<?php
include_once("../includes/conexion.php"); // Usa tu conexión con PDO

if (!isset($_GET['id'])) {
    die("Error: Mascota no especificada.");
}

$id = $_GET['id'];

$sql = "DELETE FROM mascotas WHERE id = ?";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([$id])) {
    echo "<script>alert('✅ Mascota eliminada correctamente'); window.location.href='index.php';</script>";
} else {
    echo "<script>alert('❌ Error al eliminar la mascota'); window.location.href='index.php';</script>";
}
?>
