<?php
// 1. 🏁 Iniciar la sesión antes de cualquier salida
session_start();

// 2. Conexión a la base de datos con PDO
require '../Modelo/conexion.php'; // Asegúrate de que este archivo retorne $conexion como instancia PDO

// Variable para errores de inicio de sesión
$error_login = "";

// 3. Verificamos si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Obtenemos los datos del formulario (limpios)
    $correo = trim($_POST['correo_electronico']);
    $contrasena = trim($_POST['contrasena']);

    try {
        // 5. Consulta segura con PDO (parámetro preparado)
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo_electronico = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 6. Verificamos si existe el usuario
        if ($usuario) {
            // ⚙️ Si la contraseña está hasheada (recomendado)
            if ($contrasena === $usuario['contrasena']) {
                  
                // 7. Guardamos datos en sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombres_completos'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
                $_SESSION['logueado'] = true;

                // 8. 🚀 Redirección según tipo de usuario
                if ($usuario['tipo_usuario'] === 'cliente') {
                    header("Location: dashboard.php");
                    exit;
                } elseif ($usuario['tipo_usuario'] === 'administrador') {
                    header("Location: dashboard_admin.php");
                    exit;
                } else {
                    // Rol desconocido
                    $error_login = "Tipo de usuario no reconocido.";
                }
            } else {
                // Contraseña incorrecta
                $error_login = "Contraseña incorrecta.";
            }
        } else {
            // Usuario no encontrado
            $error_login = "No existe una cuenta registrada con ese correo.";
        }

    } catch (PDOException $e) {
        $error_login = "Error en la base de datos: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Clínica Veterinaria del Norte</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="overlay"></div>
    <div class="container-login">
        <div class="login-box">

            <div class="logo-container">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTyXav9eg85Gabb3trsFwkU0_MgCfQKJcnvTA&s" alt="Logo Veterinaria" class="logo">
                <h1>Clínica Veterinaria del Norte</h1>
            </div>

            <div class="social-login">
                <a href="https://www.facebook.com/VETNORTH" target="_blank" class="facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/vetnorthlosolivos/?hl=es-la" target="_blank" class="instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://www.tiktok.com/@vetnorth_los.olivos" target="_blank" class="tiktok"><i class="fab fa-tiktok"></i></a>
            </div>

            <h2>Iniciar Sesión</h2>

            <!-- 🔴 Mostrar error si existe -->
            <?php if (!empty($error_login)): ?>
                <p style="color: red; background: #ffeeee; padding: 10px; border-radius: 5px;">
                    <?= htmlspecialchars($error_login); ?>
                </p>
            <?php endif; ?>

            <!-- FORMULARIO DE LOGIN -->
            <form action="login.php" method="POST" class="form">
                <div class="input-container">
                    <i class="fa fa-user icon"></i>
                    <input type="email" name="correo_electronico" placeholder="Correo electrónico" required>
                </div>

                <div class="input-container">
                    <i class="fa fa-lock icon"></i>
                    <input type="password" name="contrasena" placeholder="Contraseña" required>
                </div>

                <button type="submit" class="btn-login">Acceder</button>
            </form>

            <p class="text-center mt-3">
                ¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>
            </p>
        </div>
    </div>
</body>
</html>
