<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$appointment_id = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
$selected_date = filter_input(INPUT_POST, 'selected_date', FILTER_SANITIZE_STRING);
$selected_time = filter_input(INPUT_POST, 'selected_time', FILTER_SANITIZE_STRING);
$doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

if (!$doctor_id || !$selected_date || !$selected_time) {
    die("Faltan datos necesarios.");
}

if ($appointment_id) {
    // Modificar cita: Poner disponible el horario anterior y marcar como no disponible el horario seleccionado
    $sql = "UPDATE available_slots 
            SET disponible = 1, usuario_id = NULL 
            WHERE doctor_id = ? AND date = (SELECT date FROM appointments WHERE id = ?) AND time = (SELECT time FROM appointments WHERE id = ?) AND disponible = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $doctor_id, $appointment_id, $appointment_id);
    $stmt->execute();
    $stmt->close();

    // Actualizar la cita en appointments
    $sql = "UPDATE appointments 
            SET date = ?, time = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $selected_date, $selected_time, $appointment_id);
    $stmt->execute();
    $stmt->close();
} else {
    // Agregar nueva cita en appointments
    $sql = "INSERT INTO appointments (patient_id, doctor_id, date, time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $user_id, $doctor_id, $selected_date, $selected_time);
    $stmt->execute();
    $appointment_id = $stmt->insert_id; // Obtener el ID de la nueva cita
    $stmt->close();
}

// Asignar turno o marcar el nuevo horario como no disponible
$sql = "UPDATE available_slots 
        SET disponible = 0, usuario_id = ? 
        WHERE doctor_id = ? AND date = ? AND time = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $doctor_id, $selected_date, $selected_time);
$stmt->execute();
$stmt->close();

$message = "Horario confirmado correctamente";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Confirmación</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Confirmación</h2>
        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <p class="text-center">Turno asignado para el día <strong><?php echo htmlspecialchars($selected_date); ?></strong> a las <strong><?php echo htmlspecialchars($selected_time); ?></strong>.</p>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                No se ha recibido ningún mensaje de confirmación.
            </div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="patient_view.php" class="btn btn-primary">Volver a mis citas</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
