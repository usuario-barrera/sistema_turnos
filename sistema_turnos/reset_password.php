<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50));
        
        $sql = "UPDATE users SET reset_token='$token' WHERE email='$email'";
        if ($conn->query($sql) === TRUE) {
            // Enviar correo con el enlace de restablecimiento (esto es solo un ejemplo, necesitas configurar un servidor de correo real)
            mail($email, "Restablecer Contraseña", "Usa este enlace para restablecer tu contraseña: http://localhost/new_password.php?token=$token");
            $message = "Se ha enviado un correo con instrucciones para restablecer tu contraseña.";
        } else {
            $error = "Error al generar el enlace de restablecimiento.";
        }
    } else {
        $error = "Correo no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Olvidé mi Contraseña</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Recuperar Contraseña</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php if (isset($message)) { echo "<div class='alert alert-success'>$message</div>"; } ?>
        <form method="post" action="reset_password.php">
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
</body>
</html>
