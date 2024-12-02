<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
    $date = filter_input(INPUT_POST, 'selected_date', FILTER_SANITIZE_STRING);
    $time = filter_input(INPUT_POST, 'selected_time', FILTER_SANITIZE_STRING);
    $patient_id = $_SESSION['user_id'];

    var_dump($doctor_id, $date, $time);  // Mensaje de depuración
    echo "Consultando disponibilidad...";  // Mensaje de depuración

    // Verificar que el horario esté disponible
    $sql = "SELECT * FROM available_slots WHERE doctor_id = ? AND date = ? AND time = ? AND disponible = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $doctor_id, $date, $time);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "Número de filas encontradas: " . $result->num_rows;  // Mensaje de depuración

    if ($result->num_rows > 0) {
        echo "Reservando cita...";  // Mensaje de depuración
        
        // Reservar la cita
        $sql = "INSERT INTO appointments (doctor_id, patient_id, date, time) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $doctor_id, $patient_id, $date, $time);
        if ($stmt->execute()) {
            echo "Actualizando disponibilidad...";  // Mensaje de depuración
            
            // Marcar el horario como reservado
            $sql = "UPDATE available_slots SET disponible = 0 WHERE doctor_id = ? AND date = ? AND time = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $doctor_id, $date, $time);
            $stmt->execute();

            // Redirigir a confirmation.php con datos del turno reservado
            header("Location: confirmation.php?doctor_id=$doctor_id&date=$date&time=$time");
            exit();
        } else {
            echo "Error al reservar la cita.";
        }
        $stmt->close();
    } else {
        echo "El horario seleccionado no está disponible.";
    }

    $conn->close();
}
?>
