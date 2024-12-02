<?php
include 'db.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'paciente') {
    header('Location: patient_login.php');
    exit();
}

// Obtener la lista de doctores
$sql = "SELECT id, username FROM users WHERE type = 'medico'";
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
    <title>Seleccionar Doctor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Seleccionar Doctor</h2>
        <form id="doctorForm" method="get" action="select_date.php">
            <div class="form-group">
                <label for="doctor_id">Doctor:</label>
                <select class="form-control" id="doctor_id" name="doctor_id" required>
                    <option value="">Seleccione un doctor</option>
                    <?php foreach ($doctors as $doctor) {
                        echo "<option value=\"{$doctor['id']}\">{$doctor['username']}</option>";
                    } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Seleccionar Doctor</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
