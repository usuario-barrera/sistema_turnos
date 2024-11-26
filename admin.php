<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'administrativo') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_doctor'])) {
        $name = $_POST['doctor_name'];
        $specialty_id = $_POST['specialty_id'];
        $sql = "INSERT INTO doctors (name, specialty_id) VALUES ('$name', '$specialty_id')";
        $conn->query($sql);
    } elseif (isset($_POST['add_specialty'])) {
        $name = $_POST['specialty_name'];
        $sql = "INSERT INTO specialties (name) VALUES ('$name')";
        $conn->query($sql);
    } elseif (isset($_POST['schedule_doctor'])) {
        $doctor_id = $_POST['doctor_id'];
        $date = $_POST['date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $duration = $_POST['duration'];

        // Validar horarios
        if ($end_time <= $start_time) {
            $error = "El horario de fin debe ser mayor al horario de inicio.";
        } else {
            $sql = "INSERT INTO doctor_schedule (doctor_id, date, start_time, end_time, duration) VALUES ('$doctor_id', '$date', '$start_time', '$end_time', '$duration')";
            if ($conn->query($sql) === TRUE) {
                // Generar turnos disponibles
                $time = new DateTime($start_time);
                $end = new DateTime($end_time);
                while ($time < $end) {
                    $slot_time = $time->format('H:i:s');
                    $sql = "INSERT INTO available_slots (doctor_id, date, time) VALUES ('$doctor_id', '$date', '$slot_time')";
                    $conn->query($sql);
                    $time->modify("+$duration minutes");
                }
                $success = "Horario programado y turnos generados correctamente.";
            } else {
                $error = "Error al programar el horario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Administración - Consultorio Médico</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Administración</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Salir</a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <h2 class="text-center mt-5">Gestión Administrativa</h2>

        <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Agregar Especialidad</h5>
                <form method="post" action="admin.php" id="specialtyForm">
                    <div class="form-group">
                        <label for="specialty_name">Nombre de la Especialidad:</label>
                        <input type="text" class="form-control" id="specialty_name" name="specialty_name" required>
                    </div>
                    <button type="submit" name="add_specialty" class="btn btn-primary">Agregar Especialidad</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Agregar Médico</h5>
                <form method="post" action="admin.php" id="doctorForm">
                    <div class="form-group">
                        <label for="doctor_name">Nombre del Médico:</label>
                        <input type="text" class="form-control" id="doctor_name" name="doctor_name" required>
                    </div>
                    <div class="form-group">
                        <label for="specialty_id">Especialidad:</label>
                        <select class="form-control" id="specialty_id" name="specialty_id" required>
                            <?php
                            $sql = "SELECT * FROM specialties";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="add_doctor" class="btn btn-primary">Agregar Médico</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Programar Horario del Médico</h5>
                <form method="post" action="admin.php" id="scheduleForm">
                    <div class="form-group">
                        <label for="doctor_id">Médico:</label>
                        <select class="form-control" id="doctor_id" name="doctor_id" required>
                            <?php
                            $sql = "SELECT * FROM doctors";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Fecha:</label>
                        <input type="date" class="form-control" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="start_time">Hora de Inicio:</label>
                        <input type="time" class="form-control" id="start_time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="end_time">Hora de Fin:</label>
                        <input type="time" class="form-control" id="end_time" name="end_time" required>
                    </div>
                    <div class="form-group">
                        <label for="duration">Duración de la Consulta (minutos):</label>
                        <input type="number" class="form-control" id="duration" name="duration" required>
                    </div>
                    <button type="submit" name="schedule_doctor" class="btn btn-primary">Programar Horario</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
