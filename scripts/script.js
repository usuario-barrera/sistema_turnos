document.addEventListener('DOMContentLoaded', function() {
    const doctorList = document.getElementById('doctorsList');

    fetch('get_doctors.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(doctor => {
                const doctorCard = document.createElement('div');
                doctorCard.className = 'col-md-4 mb-4';
                doctorCard.innerHTML = `
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">${doctor.name}</h5>
                            <p class="card-text">${doctor.specialty}</p>
                            <a href="book_appointment.php?doctor_id=${doctor.id}" class="btn btn-primary">Ver Disponibilidad</a>
                        </div>
                    </div>`;
                doctorList.appendChild(doctorCard);
            });
        });

    // Reservar cita
    if (document.getElementById('appointmentForm')) {
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const doctorId = document.getElementById('doctor_id').value;
            const date = document.getElementById('selected_date').value;
            const time = document.getElementById('selected_time').value;

            fetch('book_appointment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ doctor_id: doctorId, date, time })
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
        });
    }

    // Cargar disponibilidad
    if (document.getElementById('calendar')) {
        const doctorId = document.getElementById('doctor_id').value;

        fetch('get_appointments.php?doctor_id=' + doctorId)
            .then(response => response.json())
            .then(data => {
                const calendar = document.getElementById('calendar');

                data.schedule.forEach(schedule => {
                    const dateDiv = document.createElement('div');
                    dateDiv.className = 'date';

                    const dateLabel = document.createElement('h5');
                    dateLabel.innerText = schedule.date;
                    dateDiv.appendChild(dateLabel);

                    const startTime = new Date(schedule.date + ' ' + schedule.start_time);
                    const endTime = new Date(schedule.date + ' ' + schedule.end_time);
                    const duration = schedule.duration;

                    for (let time = startTime; time < endTime; time.setMinutes(time.getMinutes() + duration)) {
                        const timeButton = document.createElement('button');
                        timeButton.className = 'btn btn-time';
                        timeButton.innerText = time.toTimeString().substr(0, 5);
                        timeButton.dataset.time = time.toTimeString().substr(0, 5);
                        timeButton.dataset.date = schedule.date;

                        // Verificar si la franja horaria está ocupada
                        const isBooked = data.appointments.some(appointment => 
                            appointment.date === schedule.date && appointment.time === timeButton.dataset.time);
                        
                        if (isBooked) {
                            timeButton.classList.add('btn-danger');
                            timeButton.disabled = true;
                        } else {
                            timeButton.classList.add('btn-success');
                            timeButton.addEventListener('click', function() {
                                document.querySelectorAll('.btn-time').forEach(btn => btn.classList.remove('btn-primary'));
                                timeButton.classList.add('btn-primary');
                                document.getElementById('selected_date').value = timeButton.dataset.date;
                                document.getElementById('selected_time').value = timeButton.dataset.time;
                            });
                        }

                        dateDiv.appendChild(timeButton);
                    }

                    calendar.appendChild(dateDiv);
                });
            });
    }
});
