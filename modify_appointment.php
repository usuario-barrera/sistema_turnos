<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: patient_login.php');
    exit();
}

$appointment_id = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
$selected_date = filter_input(INPUT_POST, 'selected_date', FILTER_SANITIZE_STRING);
$selected_time = filter_input(INPUT_POST, 'selected_time', FILTER_SANITIZE_STRING);
$user_id = $_SESSION['user_id'];

if (!$appointment_id || !$selected_date || !$selected_time) {
    die("Datos de la cita no encontrados.");
}

// Obtener la información de la cita actual
$sql = "SELECT doctor_id, date, time FROM appointments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$stmt->bind_result($doctor_id, $current_date, $current_time);
$stmt->fetch();
$stmt->close();

// Marcar la franja horaria anterior como disponible y setear usuario_id a NULL
$sql = "UPDATE available_slots SET disponible = 1, usuario_id = NULL WHERE doctor_id = ? AND date = ? AND time = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $doctor_id, $current_date, $current_time);
$stmt->execute();
$stmt->close();

// Marcar la nueva franja horaria como no disponible y asignar el usuario_id
$sql = "UPDATE available_slots SET disponible = 0, usuario_id = ? WHERE doctor_id = ? AND date = ? AND time = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $doctor_id, $selected_date, $selected_time);
$stmt->execute();
$stmt->close();

// Actualizar la cita con la nueva fecha y hora
$sql = "UPDATE appointments SET date = ?, time = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $selected_date, $selected_time, $appointment_id);
$stmt->execute();
$stmt->close();

header('Location: patient_view.php');
exit();
?>
