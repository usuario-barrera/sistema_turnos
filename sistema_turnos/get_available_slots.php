<?php
include 'db.php';

$doctor_id = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);
$date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);

$sql = "SELECT time, disponible FROM available_slots WHERE doctor_id = ? AND date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $doctor_id, $date);
$stmt->execute();
$result = $stmt->get_result();
$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = $row;
}

header('Content-Type: application/json');
echo json_encode($slots);

$stmt->close();
$conn->close();
?>
