<?php
$servername = "localhost"; // Cambia esto si tu servidor de base de datos es diferente
$username = "root"; // Cambia esto a tu usuario de base de datos
$password = ""; // Cambia esto a tu contraseña de base de datos
$dbname = "sistema_turnos";

// Crear conexión
$conn = new mysqli($servername, $username, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Crear la base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    //echo "Base de datos creada o ya existe.";
} else {
    die("Error al crear la base de datos: " . $conn->error);
}

// Conectar a la base de datos
$conn->select_db($dbname);

if ($conn->connect_error) {
    die("Conexión fallida a la base de datos: " . $conn->connect_error);
}
?>
