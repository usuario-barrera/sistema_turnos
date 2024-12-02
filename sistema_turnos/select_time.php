<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
$selected_date = filter_input(INPUT_POST, 'selected_date', FILTER_SANITIZE_STRING);
$appointment_id = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);

if (!$doctor_id || !$selected_date) {
    die("Doctor o fecha no encontrados.");
}

// Obtener los horarios disponibles desde available_slots
$sql = "SELECT time FROM available_slots WHERE doctor_id = ? AND date = ? AND disponible = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $doctor_id, $selected_date);
$stmt->execute();
$result = $stmt->get_result();
$available_slots = [];
while ($row = $result->fetch_assoc()) {
    $available_slots[] = $row['time'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Seleccionar Horario</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Seleccionar Horario</h2>
        <form id="timeForm" method="post" action="confirmation.php">
            <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
            <input type="hidden" name="selected_date" value="<?php echo $selected_date; ?>">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
            <div class="form-group slot-list">
                <label for="selected_time">Horarios Disponibles:</label>
                <select class="form-control" id="selected_time" name="selected_time" required>
                    <option value="">Seleccione un horario</option>
                    <?php foreach ($available_slots as $slot) {
                        echo "<option value=\"$slot\">$slot</option>";
                    } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Confirmar Horario</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
