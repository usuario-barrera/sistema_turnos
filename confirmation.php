<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: login.php');
    exit();
}

$doctor_id = $_GET['doctor_id'];
$selected_date = $_GET['date'];
$selected_time = $_GET['time'];

// Obtener la información del doctor
$sql = "SELECT name FROM doctors WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();

if (!$doctor) {
    header('Location: error_page.php?error=Doctor no encontrado');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Confirmación de Turno</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Confirmación de Turno</h2>
        <p class="text-center">Su turno ha sido reservado con éxito.</p>
        <p class="text-center">Doctor: <?php echo htmlspecialchars($doctor['name']); ?></p>
        <p class="text-center">Fecha: <?php echo htmlspecialchars($selected_date); ?></p>
        <p class="text-center">Hora: <?php echo htmlspecialchars($selected_time); ?></p>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-success">Salir</a>
        </div>
    </div>
</body>
</html>
