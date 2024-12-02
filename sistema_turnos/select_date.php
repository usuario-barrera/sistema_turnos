<?php
include 'db.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: patient_login.php');
    exit();
}

$appointment_id = filter_input(INPUT_GET, 'appointment_id', FILTER_VALIDATE_INT);
$doctor_id = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);

// Obtener las fechas habilitadas desde la tabla available_slots
$sql = "SELECT DISTINCT date FROM available_slots WHERE doctor_id = ? AND disponible = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$available_dates = [];
while ($row = $result->fetch_assoc()) {
    $available_dates[] = $row['date'];
}
$stmt->close();

// Mostrar las fechas habilitadas
echo "<p>Días habilitados para seleccionar: " . implode(", ", $available_dates) . "</p>";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Seleccionar Fecha</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .enabled-day {
            background-color: #c8e6c9; /* Verde claro */
            color: #000;
        }
        .disabled-day {
            background-color: #eee;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Seleccionar Fecha</h2>
        <form id="dateForm" method="post" action="select_time.php">
            <?php if ($appointment_id): ?>
                <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
            <?php endif; ?>
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
            <div class="form-group">
                <label for="selected_date">Fecha:</label>
                <input type="date" class="form-control" id="selected_date" name="selected_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Seleccionar Fecha</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const availableDates = <?php echo json_encode($available_dates); ?>;
            const dateInput = document.getElementById('selected_date');

            // Función para verificar si una fecha está disponible
            const isDateAvailable = (dateString) => {
                return availableDates.includes(dateString);
            };

            // Aplicar estilos a las fechas habilitadas y deshabilitadas
            const applyDateStyles = () => {
                const today = new Date();
                const minDate = today.toISOString().split('T')[0];

                dateInput.setAttribute('min', minDate);

                dateInput.addEventListener('input', function() {
                    const selectedDate = this.value;
                    if (!isDateAvailable(selectedDate)) {
                        this.setCustomValidity('Fecha no habilitada para citas.');
                    } else {
                        this.setCustomValidity('');
                    }
                });

                dateInput.addEventListener('click', function() {
                    setTimeout(() => {
                        const days = document.querySelectorAll('td[data-day]');
                        days.forEach(day => {
                            const date = day.getAttribute('data-day');
                            if (isDateAvailable(date)) {
                                day.classList.add('enabled-day');
                            } else {
                                day.classList.add('disabled-day');
                                day.setAttribute('disabled', true);
                            }
                        });
                    }, 10);
                });
            };

            applyDateStyles();
        });
    </script>
</body>
</html>
