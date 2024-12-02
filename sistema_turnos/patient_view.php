<?php
include 'db.php';
session_start();

// Verificar si el usuario está autenticado y es un paciente
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: patient_login.php');
    exit();
}

// Obtener el ID del paciente de la sesión
$patient_id = $_SESSION['user_id'];

// Obtener las citas del paciente
$sql = "SELECT appointments.id, appointments.doctor_id, users.username AS doctor_name, appointments.date, appointments.time 
        FROM appointments
        JOIN users ON appointments.doctor_id = users.id
        WHERE appointments.patient_id = ?
        ORDER BY appointments.date, appointments.time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
$stmt->close();
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
        <div class="text-center mb-4">
            <a href="select_doctor.php" class="btn btn-success">Agregar Turno</a>
            <a href="logout.php" class="btn btn-secondary">Salir</a>
        </div>
        <h3 class="mt-5">Citas Programadas</h3>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Doctor</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($appointments) > 0) {
                    foreach ($appointments as $appointment) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['time']); ?></td>
                            <td>
                                <a href="select_date.php?appointment_id=<?php echo $appointment['id']; ?>&doctor_id=<?php echo $appointment['doctor_id']; ?>" class="btn btn-warning">Modificar</a>
                                <a href="cancel_appointment.php?appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-danger">Cancelar</a>
                            </td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr>
                        <td colspan="5" class="text-center">No tienes citas programadas.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
