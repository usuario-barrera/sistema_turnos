<?php
include 'db.php';

$sql = "SELECT doctors.id, doctors.name, specialties.name AS specialty FROM doctors JOIN specialties ON doctors.specialty_id = specialties.id";
$result = $conn->query($sql);

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

echo json_encode($doctors);

$conn->close();
?>
