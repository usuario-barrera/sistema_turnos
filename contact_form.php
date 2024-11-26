<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $to = 'tu_correo@ejemplo.com'; // Reemplaza con tu dirección de correo
    $subject = 'Nuevo mensaje de contacto';
    $body = "Nombre: $name\nCorreo: $email\nMensaje:\n$message";
    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        echo '<div class="alert alert-success">Mensaje enviado con éxito.</div>';
    } else {
        echo '<div class="alert alert-danger">Hubo un problema al enviar el mensaje. Intenta de nuevo más tarde.</div>';
    }
} else {
    header('Location: contact.php');
}
?>
