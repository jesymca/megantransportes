<?php
$host = "localhost";
$user = "root";
$password = "01012023";
$database = "megan";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
