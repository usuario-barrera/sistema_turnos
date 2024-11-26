<?php
include 'db.php';

$sql = "
CREATE DATABASE IF NOT EXISTS sistema_turnos;
USE sistema_turnos;

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
CREATE TABLE available_slots (
  id int(11) NOT NULL,
  doctor_id int(11) DEFAULT NULL,
  date date DEFAULT NULL,
  time time DEFAULT NULL,
  usuario_id int(11) DEFAULT NULL,
  disponible tinyint(1) DEFAULT 1
);
";

if ($conn->multi_query($sql) === TRUE) {
    echo "Base de datos inicializada correctamente";
} else {
    echo "Error inicializando base de datos: " . $conn->error;
}

$conn->close();
?>
