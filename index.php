<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('images/fondo.jpg'); /* Aquí puedes poner la URL de tu imagen de fondo */
            background-size: cover;
            background-repezat: no-repeat;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .welcome-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }
        .welcome-buttons a {
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>Bienvenido a la Aplicación de Turnos</h1>
        <div class="welcome-buttons">
            <a href="login.php" class="btn btn-primary btn-lg">Acceso Administrativos</a>
            <a href="patient_login.php" class="btn btn-secondary btn-lg">Acceso Pacientes</a>
            <a href="doctor_login.php" class="btn btn-info btn-lg">Acceso Médicos</a>
        </div>
    </div>
</body>
</html>
<?php 
include 'init_db.php'; // Asegura que las tablas se creen cuando se accede a index.php
?>