<?php
include 'db.php';

$doctor_id = $_GET['doctor_id'];

// Obtener el horario del médico
$sql = "SELECT * FROM doctor_schedule WHERE doctor_id=$doctor_id AND date >= CURDATE()";
$schedule_result = $conn->query($sql);

$schedule = [];
while ($row = $schedule_result->fetch_assoc()) {
    $schedule[] = $row;
}

// Obtener las citas ya reservadas para el médico
$sql = "SELECT * FROM appointments WHERE doctor_id=$doctor_id AND date >= CURDATE()";
$appointments_result = $conn->query($sql);

$appointments = [];
while ($row = $appointments_result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode(['schedule' => $schedule, 'appointments' => $appointments]);

$conn->close();
?>
    