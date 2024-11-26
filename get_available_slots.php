<?php
include 'db.php';

$doctor_id = $_GET['doctor_id'];
$date = $_GET['date'] ?? '';

// Obtener los horarios disponibles
if ($date) {
    $sql = "SELECT date, time, disponible FROM available_slots WHERE doctor_id=$doctor_id AND date='$date'";
} else {
    $sql = "SELECT DISTINCT date FROM available_slots WHERE doctor_id=$doctor_id AND disponible=1";
}
$result = $conn->query($sql);

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = $row;
}

echo json_encode($slots);
$conn->close();
?>
