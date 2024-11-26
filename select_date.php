<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: login.php');
    exit();
}

$doctor_id = $_GET['doctor_id'];

if (!$doctor_id) {
    header('Location: select_doctor.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Seleccionar Fecha</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.2/css/pikaday.min.css">
    <style>
        .highlight {
            background-color: #17a2b8 !important;
            color: #fff !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Seleccionar Fecha</h2>

        <div id="calendar" class="my-5"></div>

        <form id="dateForm" action="select_time.php" method="get" class="text-center">
            <input type="hidden" id="doctor_id" name="doctor_id" value="<?php echo $doctor_id; ?>">
            <input type="hidden" id="selected_date" name="selected_date">
            <button type="submit" class="btn btn-primary">Continuar</button>
        </form>

        <div class="text-center mt-4">
            <a href="select_doctor.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.2/pikaday.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const doctorId = document.getElementById('doctor_id').value;

            fetch('get_available_slots.php?doctor_id=' + doctorId)
                .then(response => response.json())
                .then(data => initializeCalendar(data));

            function initializeCalendar(data) {
                const availableDates = data.map(slot => slot.date);

                const picker = new Pikaday({
                    field: document.getElementById('calendar'),
                    format: 'YYYY-MM-DD',
                    onSelect: function(date) {
                        const selectedDate = picker.toString();
                        if (availableDates.includes(selectedDate)) {
                            document.querySelector('.highlight')?.classList.remove('highlight');
                            document.getElementById('calendar').classList.add('highlight');
                            document.getElementById('selected_date').value = selectedDate;
                        } else {
                            alert('La fecha seleccionada no está disponible. Por favor, elija otra fecha.');
                        }
                    },
                    disableDayFn: function(date) {
                        const formattedDate = moment(date).format('YYYY-MM-DD');
                        return !availableDates.includes(formattedDate);
                    }
                });

                picker.show();
            }

            document.getElementById('dateForm').addEventListener('submit', function(event) {
                const selectedDate = document.getElementById('selected_date').value;
                if (!selectedDate) {
                    event.preventDefault();
                    alert('Por favor, seleccione un día disponible antes de continuar.');
                }
            });
        });
    </script>
</body>
</html>
