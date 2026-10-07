CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

CREATE TABLE IF NOT EXISTS students (
    enrollment_number VARCHAR(50) PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mobile VARCHAR(10) NOT NULL,
    course VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    registration_date DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(150) NOT NULL
);

CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_number VARCHAR(50) NOT NULL,
    event_name VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(150) NOT NULL
);

