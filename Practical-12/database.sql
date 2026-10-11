CREATE DATABASE IF NOT EXISTS event_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_management;

CREATE TABLE IF NOT EXISTS admins (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
  event_id INT AUTO_INCREMENT PRIMARY KEY,
  event_name VARCHAR(150) NOT NULL,
  event_category VARCHAR(80) NOT NULL DEFAULT 'General',
  event_date DATE NOT NULL,
  venue VARCHAR(180) NOT NULL,
  description TEXT NULL,
  poster VARCHAR(255) NULL,
  status ENUM('Open','Closed') NOT NULL DEFAULT 'Open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_logs (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NULL,
  event_id INT NULL,
  action VARCHAR(30) NOT NULL,
  details VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_admin FOREIGN KEY (admin_id) REFERENCES admins(admin_id) ON DELETE SET NULL
);

-- Demo account: username admin, password Admin@123
-- This hash is for the classroom demo password; change it for real use.
INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2y$10$Q6r5wYgB4p2y8Xx8dQ6x6e4m1z7z6q8u5Q3b9v3W7h0c4g8m3u2aK')
ON DUPLICATE KEY UPDATE username = VALUES(username);

INSERT INTO events (event_name, event_category, event_date, venue, description, status)
VALUES
('Tech Innovation Day', 'Technology', '2027-01-15', 'Main Auditorium', 'Student technology talks, demonstrations and project displays.', 'Open'),
('Cultural Festival', 'Cultural', '2027-02-10', 'College Ground', 'A celebration of music, dance and cultural activities.', 'Open'),
('Career Guidance Seminar', 'Seminar', '2027-03-05', 'Seminar Hall', 'Career planning and industry guidance for students.', 'Closed');
