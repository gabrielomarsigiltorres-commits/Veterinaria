<?php
// 1. Incluimos la conexión PDO
require '../Modelo/conexion.php'; // Trae $conexion como instancia PDO

// 2. Verificamos si el formulario fue enviado (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Obtenemos los datos del formulario
    $nombres = $_POST['nombres_completos'] ?? '';
    $correo = $_POST['correo_electronico'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

    // 4. Validamos que las contraseñas coincidan
    if ($contrasena != $confirmar_contrasena) {
        echo "Error: Las contraseñas no coinciden.";
    } else {
        try {
            // 5. Insertamos el nuevo usuario con consulta preparada PDO
            $stmt = $conexion->prepare("INSERT INTO usuarios 
                (nombres_completos, correo_electronico, contrasena) 
                VALUES (:nombres, :correo, :contrasena)");

            $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':contrasena', $contrasena, PDO::PARAM_STR); // ⚠️ (por ahora sin hash, como pediste)

            if ($stmt->execute()) {
                // 6. Éxito → redirigimos al login
                header("Location: login.php?status=success");
                exit;
            }

        } catch (PDOException $e) {
            // 7. Manejo de errores (correo duplicado, etc.)
            if ($e->getCode() == 23000) { // Código 23000 = duplicado en PDO
                echo "Error: El correo electrónico ya está registrado.";
            } else {
                echo "Error al registrar el usuario: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrarse - Clínica Veterinaria del Norte</title>
    <link rel="stylesheet" href="css/register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="overlay"></div>
    <div class="container-register">
        <div class="register-box">
            
            <div class="logo-container">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTyXav9eg85Gabb3trsFwkU0_MgCfQKJcnvTA&s" alt="Logo Veterinaria" class="logo">
                <h1>Clínica Veterinaria del Norte</h1>
            </div>

            <div class="social-login">
                <a href="https://www.facebook.com/VETNORTH" target="_blank" class="facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/vetnorthlosolivos/?hl=es-la" target="_blank" class="instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://www.tiktok.com/@vetnorth_los.olivos" target="_blank" class="tiktok"><i class="fab fa-tiktok"></i></a>
            </div>

            <h2>Crear una Cuenta</h2>

            <form action="register.php" method="POST" class="form">
                <div class="input-container">
                    <i class="fa fa-user icon"></i>
                    <input type="text" name="nombres_completos" placeholder="Nombres completos" required>
                </div>

                <div class="input-container">
                    <i class="fa fa-envelope icon"></i>
                    <input type="email" name="correo_electronico" placeholder="Correo electrónico" required>
                </div>

                <div class="input-container">
                    <i class="fa fa-lock icon"></i>
                    <input type="password" name="contrasena" placeholder="Contraseña" required>
                </div>

                <div class="input-container">
                    <i class="fa fa-lock icon"></i>
                    <input type="password" name="confirmar_contrasena" placeholder="Confirmar contraseña" required>
                </div>

                <button type="submit" class="btn-register">Registrarse</button>
            </form>

            <p class="text-center mt-3">
                ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
            </p>
        </div>
    </div>
</body>
</html>
