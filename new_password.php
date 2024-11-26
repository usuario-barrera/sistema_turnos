<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "UPDATE users SET password='$new_password', reset_token=NULL WHERE reset_token='$token'";
    if ($conn->query($sql) === TRUE) {
        header('Location: login.php');
        exit();
    } else {
        $error = "Error al restablecer la contraseña.";
    }
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    die('Token no proporcionado.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Restablecer Contraseña</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="post" action="new_password.php">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <div class="form-group">
                <label for="password">Nueva Contraseña:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
        </form>
    </div>
</body>
</html>
