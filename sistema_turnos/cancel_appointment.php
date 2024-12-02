<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$appointment_id = filter_input(INPUT_GET, 'appointment_id', FILTER_VALIDATE_INT);

if (!$appointment_id) {
    die("ID de cita no encontrado.");
}

// Obtener los detalles de la cita para actualizar available_slots
$sql = "SELECT doctor_id, date, time FROM appointments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();

if ($appointment) {
    $doctor_id = $appointment['doctor_id'];
    $date = $appointment['date'];
    $time = $appointment['time'];

    // Marcar el horario como disponible en available_slots
    $sql = "UPDATE available_slots 
            SET disponible = 1, usuario_id = NULL 
            WHERE doctor_id = ? AND date = ? AND time = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $doctor_id, $date, $time);
    $stmt->execute();
    $stmt->close();

    // Eliminar la cita de appointments
    $sql = "DELETE FROM appointments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->close();

    header('Location: patient_view.php?message=Cita cancelada correctamente');
    exit();
} else {
    die("Cita no encontrada.");
}
?>
