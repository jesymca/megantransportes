<?php
session_start(); // Necesario si db.php depende de la sesión o para futuras verificaciones
include '../includes/db.php'; // Solo lo necesario para la consulta

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $pregunta_seguridad_respuesta = ""; // Respuesta por defecto

    // Preparar la consulta para evitar inyección SQL
    $sql = "SELECT pregunta_seguridad FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $usuario = $result->fetch_assoc();
            // Es buena práctica escapar la salida, aunque venga de tu BD
            $pregunta_seguridad_respuesta = htmlspecialchars($usuario['pregunta_seguridad']);
        }
        $stmt->close();
    }
    // $conn->close(); // Considera si cerrar la conexión aquí o dejar que se cierre al final del script

    // Siempre debemos hacer echo de algo para la respuesta AJAX
    echo $pregunta_seguridad_respuesta;
    exit(); // Importante para no enviar más contenido
}

// Si no se proporciona email, devuelve una cadena vacía
echo "";
exit();
?>