<?php
include 'db.php';

// Crear las tablas si no existen
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    type ENUM('paciente', 'administrativo', 'medico') NOT NULL
);

CREATE TABLE IF NOT EXISTS specialties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialty_id INT,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id)
);

CREATE TABLE IF NOT EXISTS doctor_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration INT NOT NULL,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    doctor_id INT,
    date DATE NOT NULL,
    time TIME NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);

CREATE TABLE IF NOT EXISTS available_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT,
    date DATE,
    time TIME,
    usuario_id INT,
    disponible TINYINT(1) DEFAULT 1,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);
";

// Ejecutar las consultas de creación de tablas
if ($conn->multi_query($sql) === TRUE) {
    // Asegurarse de que todas las tablas se hayan creado
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());

    // Verificar si el usuario admin ya existe
    $sql_check_admin = "SELECT * FROM users WHERE username = 'admin'";
    $result = $conn->query($sql_check_admin);

    if ($result->num_rows == 0) {
        // Crear el usuario admin si no existe
        $admin_password = password_hash('1234', PASSWORD_DEFAULT); // Encriptar la contraseña para mayor seguridad
        $sql_insert_admin = "
        INSERT INTO users (username, password, email, type) VALUES 
        ('admin', '$admin_password', '1234@1234.com', 'administrativo')
        ";
        if ($conn->query($sql_insert_admin) === TRUE) {
            echo "Usuario admin creado correctamente";
        } else {
            echo "Error al crear el usuario admin: " . $conn->error;
        }
    } else {
        echo "El usuario admin ya existe";
    }
} else {
    echo "Error al crear las tablas: " . $conn->error;
}

$conn->close();
?>
