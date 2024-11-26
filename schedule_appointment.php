<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];  // Obtener el ID del usuario de la sesión

// Obtener todos los doctores
$sql = "SELECT doctors.id, doctors.name, specialties.name AS specialty 
        FROM doctors 
        JOIN specialties ON doctors.specialty_id = specialties.id";
$result = $conn->query($sql);
$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Agendar Nuevo Turno</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .available-day {
            background-color: #28a745 !important;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Agendar Nuevo Turno</h2>

        <div class="form-group">
            <label for="doctorSelect">Seleccionar Doctor:</label>
            <select id="doctorSelect" class="form-control">
                <option value="">Seleccione un doctor</option>
                <?php foreach ($doctors as $doctor) { ?>
                    <option value="<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['name']) . ' - ' . htmlspecialchars($doctor['specialty']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div id="calendarContainer" class="d-none">
            <div id="calendar" class="my-5"></div>

            <div class="form-group">
                <label for="timeSlots">Seleccionar Horario:</label>
                <select id="timeSlots" class="form-control">
                    <option value="">Seleccione un horario</option>
                </select>
            </div>

            <form id="appointmentForm" class="text-center">
                <input type="hidden" id="doctor_id" name="doctor_id">
                <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" id="selected_date" name="selected_date">
                <input type="hidden" id="selected_time" name="selected_time">
                <button type="submit" class="btn btn-success">Reservar Turno</button>
            </form>
        </div>

        <div class="text-center mt-4">
            <a href="login.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const doctorSelect = document.getElementById('doctorSelect');
            const calendarContainer = document.getElementById('calendarContainer');
            const doctorIdInput = document.getElementById('doctor_id');

            doctorSelect.addEventListener('change', function() {
                const doctorId = doctorSelect.value;
                if (doctorId) {
                    doctorIdInput.value = doctorId;
                    fetch('get_available_slots.php?doctor_id=' + doctorId)
                        .then(response => response.json())
                        .then(data => {
                            calendarContainer.classList.remove('d-none');
                            generateCalendar(data);
                        });
                } else {
                    calendarContainer.classList.add('d-none');
                }
            });

            function generateCalendar(data) {
                const calendar = document.getElementById('calendar');
                const today = new Date();
                const currentMonth = today.getMonth();
                const currentYear = today.getFullYear();

                const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

                const firstDay = new Date(currentYear, currentMonth).getDay();

                calendar.innerHTML = '';

                const monthAndYear = document.createElement('div');
                monthAndYear.className = 'text-center mb-3';
                monthAndYear.innerHTML = `<h4>${monthNames[currentMonth]} ${currentYear}</h4>`;
                calendar.appendChild(monthAndYear);

                const daysRow = document.createElement('div');
                daysRow.className = 'row';
                const dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                dayNames.forEach(day => {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'col text-center';
                    dayDiv.innerText = day;
                    daysRow.appendChild(dayDiv);
                });
                calendar.appendChild(daysRow);

                let date = 1;
                for (let i = 0; i < 6; i++) {
                    const weekRow = document.createElement('div');
                    weekRow.className = 'row';

                    for (let j = 0; j < 7; j++) {
                        const dayCell = document.createElement('div');
                        dayCell.className = 'col text-center';

                        if (i === 0 && j < firstDay) {
                            dayCell.innerHTML = '';
                        } else if (date > daysInMonth) {
                            break;
                        } else {
                            const dayDate = new Date(currentYear, currentMonth, date);
                            const formattedDate = dayDate.toISOString().split('T')[0];
                            const hasAvailableSlots = data.some(slot => slot.date === formattedDate && slot.disponible == 1);

                            if (dayDate >= today) {
                                if (hasAvailableSlots) {
                                    dayCell.innerHTML = `<button type="button" class="btn btn-success available-day" data-date="${formattedDate}">${date}</button>`;
                                    dayCell.querySelector('button').addEventListener('click', function() {
                                        document.querySelectorAll('.btn-success').forEach(btn => btn.classList.remove('btn-primary'));
                                        dayCell.querySelector('button').classList.add('btn-primary');
                                        document.getElementById('selected_date').value = formattedDate;

                                        // Obtener y mostrar horarios disponibles
                                        fetch(`get_available_slots.php?doctor_id=${doctorIdInput.value}&date=${formattedDate}`)
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
                                } else {
                                    dayCell.innerHTML = `<button class="btn btn-light" disabled>${date}</button>`;
                                }
                            } else {
                                dayCell.innerHTML = `${date}`;
                            }

                            date++;
                        }
                        weekRow.appendChild(dayCell);
                    }

                    calendar.appendChild(weekRow);
                }
            }
        });

        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const doctorId = document.getElementById('doctor_id').value;
            const userId = document.getElementById('user_id').value;
            const date = document.getElementById('selected_date').value;
            const time = document.getElementById('selected_time').value;

            if (date && time) {
                fetch('book_appointment_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ doctor_id: doctorId, user_id: userId, date, time })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cita reservada con éxito.');
                        window.location.href = 'patient_view.php';
                    } else {
                        alert('Error al reservar la cita.');
                    }
                });
            } else {
                alert('Por favor seleccione una fecha y un horario.');
            }
        });
    </script>
</body>
</html>
