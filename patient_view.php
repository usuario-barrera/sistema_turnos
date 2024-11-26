<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];  // Obtener el ID del usuario de la sesión
$current_date = date('Y-m-d');  // Obtener la fecha actual

// Obtener las citas del paciente que sean iguales o mayores a la fecha actual
$sql = "SELECT appointments.id, appointments.date, appointments.time, doctors.name AS doctor_name, specialties.name AS specialty 
        FROM appointments 
        JOIN doctors ON appointments.doctor_id = doctors.id 
        JOIN specialties ON doctors.specialty_id = specialties.id 
        WHERE appointments.patient_id = ? AND appointments.date >= ?
        ORDER BY appointments.date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $current_date);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mis Citas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Mis Citas</h2>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Médico</th>
                    <th>Especialidad</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['time']); ?></td>
                        <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['specialty']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="text-center mt-4">
            <a href="select_doctor.php" class="btn btn-primary">Agendar Nuevo Turno</a>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
