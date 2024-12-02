<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: login.php');
    exit();
}

// Obtener los datos enviados desde el formulario
$doctor_id = $_POST['doctor_id'];
$user_id = $_POST['user_id'];
$selected_date = $_POST['selected_date'];
$selected_time = $_POST['selected_time'];

// Validar los datos recibidos
if (!$doctor_id || !$user_id || !$selected_date || !$selected_time) {
    header('Location: error_page.php?error=Datos incompletos');
    exit();
}

// Insertar la cita en la base de datos
$sql = "INSERT INTO appointments (doctor_id, patient_id, date, time) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $doctor_id, $user_id, $selected_date, $selected_time);

if ($stmt->execute()) {
    // Actualizar available_slots para el turno reservado
    $update_sql = "UPDATE available_slots SET disponible=0, usuario_id=? WHERE doctor_id=? AND date=? AND time=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iiss", $user_id, $doctor_id, $selected_date, $selected_time);
    $update_stmt->execute();
    $update_stmt->close();
    // Redirigir a la página de confirmación
    header("Location: confirmation.php?doctor_id=$doctor_id&date=$selected_date&time=$selected_time");
} else {
    header('Location: error_page.php?error=Error al reservar la cita');
}

$stmt->close();
$conn->close();
?>
