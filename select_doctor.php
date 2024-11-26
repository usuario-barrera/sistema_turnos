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
    <title>Seleccionar Doctor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Seleccionar Doctor</h2>

        <form id="doctorForm" action="select_date.php" method="get" class="text-center">
            <div class="form-group">
                <label for="doctorSelect">Doctor:</label>
                <select id="doctorSelect" name="doctor_id" class="form-control" required>
                    <option value="">Seleccione un doctor</option>
                    <?php foreach ($doctors as $doctor) { ?>
                        <option value="<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['name']) . ' - ' . htmlspecialchars($doctor['specialty']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Continuar</button>
        </form>

        <div class="text-center mt-4">
            <a href="login.php" class="btn btn-danger">Cerrar Sesión</a>
            <a href="patient_view.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</body>
</html>
