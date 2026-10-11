CREATE DATABASE IF NOT EXISTS practical13_api
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE practical13_api;

CREATE TABLE IF NOT EXISTS events (
    event_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(120) NOT NULL,
    event_category VARCHAR(60) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(150) NOT NULL,
    status ENUM('Open','Closed') NOT NULL DEFAULT 'Open',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO events (event_name, event_category, event_date, venue, status) VALUES
('Web Development Workshop', 'Technical', '2026-11-05', 'Computer Lab 1', 'Open'),
('Annual Sports Meet', 'Sports', '2026-11-12', 'College Ground', 'Open'),
('Cultural Night', 'Cultural', '2026-11-20', 'Main Auditorium', 'Closed');
