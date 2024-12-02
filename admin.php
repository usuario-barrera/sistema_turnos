<?php
include 'db.php';

session_start();

// Verificar si el usuario está autenticado y es un administrativo
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 'administrativo') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y sanitizar las entradas
    if (isset($_POST['add_doctor'])) {
        $name = filter_input(INPUT_POST, 'doctor_name', FILTER_SANITIZE_STRING);
        $specialty_id = filter_input(INPUT_POST, 'specialty_id', FILTER_VALIDATE_INT);
        $sql = "INSERT INTO doctors (name, specialty_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $name, $specialty_id);
        if ($stmt->execute()) {
            $doctor_id = $stmt->insert_id;
            echo "<script>
                    var doctorId = '$doctor_id';
                    var doctorName = '".htmlspecialchars($name)."';
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('doctorId').value = doctorId;
                        document.getElementById('doctorName').textContent = doctorName;
                        $('#passwordModal').modal('show');
                    });
                </script>";
        } else {
            $error = "Error al crear el doctor.";
        }
        $stmt->close();
    }elseif (isset($_POST['add_user_password'])) {
        $doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // Generar usuario en la tabla `users` con el tipo `medico`
        $sql = "INSERT INTO users (id, username, email, type, password) VALUES (?, ?, ?, 'medico', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $doctor_id, $username, $email, $hashed_password);
        if ($stmt->execute()) {
            $success = "Usuario creado con éxito.";
        } else {
            $error = "Error al crear el usuario.";
        }
        $stmt->close();
    }
    
     elseif (isset($_POST['add_specialty'])) {
        $name = filter_input(INPUT_POST, 'specialty_name', FILTER_SANITIZE_STRING);
        $sql = "INSERT INTO specialties (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $success = "Especialidad agregada con éxito.";
        } else {
            $error = "Error al agregar la especialidad.";
        }
        $stmt->close();
    } elseif (isset($_POST['schedule_doctor'])) {
        $doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
        $start_time = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_STRING);
        $end_time = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_STRING);
        $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);

        // Validar horarios
        if ($end_time <= $start_time) {
            $error = "El horario de fin debe ser mayor al horario de inicio.";
        } else {
            $sql = "INSERT INTO doctor_schedule (doctor_id, date, start_time, end_time, duration) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssi", $doctor_id, $date, $start_time, $end_time, $duration);
            if ($stmt->execute()) {
                // Generar turnos disponibles
                $time = new DateTime($start_time);
                $end = new DateTime($end_time);
                while ($time < $end) {
                    $slot_time = $time->format('H:i:s');
                    $sql = "INSERT INTO available_slots (doctor_id, date, time) VALUES (?, ?, ?)";
                    $slot_stmt = $conn->prepare($sql);
                    $slot_stmt->bind_param("iss", $doctor_id, $date, $slot_time);
                    $slot_stmt->execute();
                    $slot_stmt->close();
                    $time->modify("+$duration minutes");
                }
                $success = "Horario programado y turnos generados correctamente.";
            } else {
                $error = "Error al programar el horario.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['update_specialty'])) {
        $doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
        $specialty_id = filter_input(INPUT_POST, 'specialty_id', FILTER_VALIDATE_INT);
        $sql = "UPDATE doctors SET specialty_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $specialty_id, $doctor_id);
        if ($stmt->execute()) {
            $success = "Especialidad actualizada correctamente.";
        } else {
            $error = "Error al actualizar la especialidad.";
        }
        $stmt->close();
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
                                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
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
                                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
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

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Modificar Especialidad Asignada</h5>
                <form method="post" action="admin.php" id="updateSpecialtyForm">
                    <div class="form-group">
                        <label for="doctor_id_update">Médico:</label>
                        <select class="form-control" id="doctor_id_update" name="doctor_id" required>
                            <?php
                            $sql = "SELECT * FROM doctors";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="specialty_id_update">Nueva Especialidad:</label>
                        <select class="form-control" id="specialty_id_update" name="specialty_id" required>
                            <?php
                            $sql = "SELECT * FROM specialties";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="update_specialty" class="btn btn-primary">Actualizar Especialidad</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal para ingresar la contraseña del nuevo usuario -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Configurar Credenciales para <span id="doctorName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="admin.php">
                    <input type="hidden" id="doctorId" name="doctor_id">
                    <div class="form-group">
                        <label for="username">Nombre de Usuario:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="add_user_password" class="btn btn-primary">Guardar Credenciales</button>
                </form>
            </div>
        </div>
    </div>
</div>

    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
