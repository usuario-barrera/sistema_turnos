document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('specialtyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const specialtyName = document.getElementById('specialty_name').value;
        fetch('admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ 'specialty_name': specialtyName, 'add_specialty': true })
        })
        .then(response => response.text())
        .then(data => {
            alert('Especialidad agregada con éxito.');
            location.reload();
        });
    });

    document.getElementById('doctorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const doctorName = document.getElementById('doctor_name').value;
        const specialtyId = document.getElementById('specialty_id').value;
        fetch('admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ 'doctor_name': doctorName, 'specialty_id': specialtyId, 'add_doctor': true })
        })
        .then(response => response.text())
        .then(data => {
            alert('Médico agregado con éxito.');
            location.reload();
        });
    });

    document.getElementById('scheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const doctorId = document.getElementById('doctor_id').value;
        const date = document.getElementById('date').value;
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const duration = document.getElementById('duration').value;
        fetch('admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ 'doctor_id': doctorId, 'date': date, 'start_time': startTime, 'end_time': endTime, 'duration': duration, 'schedule_doctor': true })
        })
        .then(response => response.text())
        .then(data => {
            alert('Horario programado con éxito.');
            location.reload();
        });
    });
});
