<?php
include 'db.php';

$doctor_id = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);

$sql = "SELECT * FROM appointments WHERE doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

header('Content-Type: application/json');
echo json_encode($appointments);

$stmt->close();
$conn->close();
?>
