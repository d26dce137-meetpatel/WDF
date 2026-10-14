CREATE DATABASE IF NOT EXISTS studenthub_practical7_15 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub_practical7_15;
CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(190) NOT NULL UNIQUE,username VARCHAR(60) NOT NULL UNIQUE,password_hash VARCHAR(255) NOT NULL,role ENUM('student','admin') NOT NULL DEFAULT 'student',status ENUM('active','inactive') NOT NULL DEFAULT 'active',last_login DATETIME NULL,remember_token_hash CHAR(64) NULL,remember_expires DATETIME NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS students (id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL UNIQUE,enrollment_number VARCHAR(40) NOT NULL UNIQUE,name VARCHAR(100) NOT NULL,email VARCHAR(190) NOT NULL UNIQUE,mobile VARCHAR(20) NOT NULL DEFAULT '',course VARCHAR(100) NOT NULL DEFAULT '',status ENUM('active','inactive','deleted') NOT NULL DEFAULT 'active',registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL);
CREATE TABLE IF NOT EXISTS events (id INT AUTO_INCREMENT PRIMARY KEY,event_name VARCHAR(160) NOT NULL,event_date DATE NOT NULL,venue VARCHAR(180) NOT NULL,category VARCHAR(80) NOT NULL DEFAULT 'General',description TEXT,poster VARCHAR(255) NULL,status ENUM('open','closed') NOT NULL DEFAULT 'open',deleted_at DATETIME NULL DEFAULT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS registrations (id INT AUTO_INCREMENT PRIMARY KEY,student_id INT NOT NULL,event_id INT NOT NULL,registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,UNIQUE KEY uq_student_event(student_id,event_id),FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,FOREIGN KEY(event_id) REFERENCES events(id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS contact_messages (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NOT NULL,
 subject VARCHAR(180) NOT NULL DEFAULT 'Student Portal Contact',
 message TEXT NOT NULL,
 status ENUM('unread','read') NOT NULL DEFAULT 'unread',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_contact_status (status),
 INDEX idx_contact_created (created_at),
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS audit_logs (id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,action VARCHAR(100) NOT NULL,details TEXT,ip_address VARCHAR(45),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL);
-- Demo admin password is Admin@123 (change after first login). Hash generated with PHP password_hash.
INSERT IGNORE INTO users(name,email,username,password_hash,role) VALUES('Administrator','admin@studenthub.local','admin','$2y$12$AjwVnzu5yUZmGNY9E1vDy.sAil9FaLWFHUiHPb0kICgnAvLqJxY9e','admin');
INSERT IGNORE INTO events(event_name,event_date,venue,category,description,status) VALUES
('Web Development Workshop','2026-11-05','Computer Lab 1','Workshop','Hands-on PHP and MySQL workshop','open'),('Annual Tech Fest','2026-11-20','Main Auditorium','Festival','Student technology festival','open'),('Career Guidance Seminar','2026-12-02','Seminar Hall','Seminar','Career planning and interview skills','open');
