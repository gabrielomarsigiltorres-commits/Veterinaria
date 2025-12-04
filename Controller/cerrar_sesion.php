<?php
// 1. Siempre iniciar la sesión
session_start();

// 2. Eliminar todas las variables de sesión
$_SESSION = array();

// 3. Destruir la sesión (borra la cookie de sesión)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finalmente, destruir la sesión
session_destroy();

// 5. Redirigir al login
header("Location: ../Views/login.php");
exit;
?>
