<?php
//session_start();
<<<<<<< HEAD

=======
>>>>>>> bd0abd8d0c13c18fa75983dd0b92d63579dbc92a
// Lista de páginas que NO requieren autenticación (ej: login, recuperar contraseña)
$paginas_publicas = [
    '/megantransportes/login/index.php',
    '/megantransportes/login/recuperar.php',
    '/megantransportes/login/cambiar_password.php'
];

// Obtener la página actual
$pagina_actual = $_SERVER['PHP_SELF'];

// Si la página no es pública y el usuario no está logueado, redirigir al login
if (!in_array($pagina_actual, $paginas_publicas) && !isset($_SESSION['usuario_id'])) {
    header("Location: /megantransportes/login/index.php");
    exit(); // Importante para evitar ejecución adicional
}
?>