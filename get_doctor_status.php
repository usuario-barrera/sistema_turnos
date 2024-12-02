<?php
include 'db.php';

$doctor_id = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);
$response = ['is_first_time' => false];

$sql = "SELECT password FROM users WHERE user_id = ? AND user_type = 'medico'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && empty($user['password'])) {
    $response['is_first_time'] = true;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
