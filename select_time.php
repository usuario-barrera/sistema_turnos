<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: login.php');
    exit();
}

$doctor_id = $_GET['doctor_id'];
$selected_date = $_GET['selected_date'];

if (!$doctor_id || !$selected_date) {
    header('Location: select_doctor.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Seleccionar Horario</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Seleccionar Horario</h2>

        <div class="form-group">
            <label for="timeSlots">Horarios Disponibles para el <?php echo htmlspecialchars($selected_date); ?>:</label>
            <select id="timeSlots" class="form-control">
                <option value="">Seleccione un horario</option>
            </select>
        </div>

        <form id="appointmentForm" action="book_appointment_backend.php" method="post" class="text-center">
            <input type="hidden" id="doctor_id" name="doctor_id" value="<?php echo $doctor_id; ?>">
            <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" id="selected_date" name="selected_date" value="<?php echo $selected_date; ?>">
            <input type="hidden" id="selected_time" name="selected_time">
            <button type="submit" class="btn btn-success">Reservar Turno</button>
        </form>

        <div class="text-center mt-4">
            <a href="select_date.php?doctor_id=<?php echo $doctor_id; ?>" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const doctorId = '<?php echo $doctor_id; ?>';
            const selectedDate = '<?php echo $selected_date; ?>';

            fetch(`get_available_slots.php?doctor_id=${doctorId}&date=${selectedDate}`)
                .then(response => response.json())
                .then(slots => {
                    const timeSlots = document.getElementById('timeSlots');
                    timeSlots.innerHTML = '<option value="">Seleccione un horario</option>';

                    slots.forEach(slot => {
                        if (slot.disponible == 1) {
                            const timeString = slot.time;
                            const option = document.createElement('option');
                            option.value = timeString;
                            option.innerText = timeString;
                            timeSlots.appendChild(option);
                        }
                    });

                    timeSlots.addEventListener('change', function() {
                        const selectedTime = timeSlots.value;
                        if (selectedTime) {
                            document.getElementById('selected_time').value = selectedTime;
                        } else {
                            document.getElementById('selected_time').value = '';
                        }
                    });
                });
        });
    </script>
</body>
</html>
