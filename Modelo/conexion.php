<?php
// ===============================================
// 1. Variables para la conexión a la base de datos
// ===============================================
$servidor   = "localhost";     // Generalmente "localhost"
$usuario_db = "root";          // Usuario de MySQL
$password_db = "";             // Contraseña de MySQL
$nombre_db  = "veterinaria";   // Nombre de tu base de datos

// ===============================================
// 2. Conexión con PDO
// ===============================================
try {
    // Data Source Name (DSN): especifica el tipo de base de datos y los datos de conexión
    $dsn = "mysql:host=$servidor;dbname=$nombre_db;charset=utf8";

    // Creamos una nueva instancia de PDO
    $conexion = new PDO($dsn, $usuario_db, $password_db);

    // Configuramos el modo de errores para que lance excepciones (recomendado)
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Si llegamos aquí, la conexión fue exitosa 🎉
    // Puedes eliminar este mensaje en producción
    // echo "Conexión exitosa a la base de datos";
} 
catch (PDOException $e) {
    // Si ocurre un error, se captura y se muestra el mensaje
    die("Error en la conexión: " . $e->getMessage());
}
?>
