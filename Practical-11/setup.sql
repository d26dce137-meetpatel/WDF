CREATE DATABASE IF NOT EXISTS student_db;
USE student_db;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    phone CHAR(10) NOT NULL,
    course VARCHAR(30) NOT NULL,
    status ENUM('active','deleted') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students (name, email, phone, course) VALUES
('Aarav Patel',     'aarav.patel@example.com',     '9876500001', 'BCA'),
('Diya Shah',       'diya.shah@example.com',       '9876500002', 'MCA'),
('Rohan Mehta',     'rohan.mehta@example.com',     '9876500003', 'B.Tech'),
('Isha Desai',      'isha.desai@example.com',      '9876500004', 'BBA'),
('Kabir Joshi',     'kabir.joshi@example.com',     '9876500005', 'B.Sc'),
('Meera Trivedi',   'meera.trivedi@example.com',   '9876500006', 'BCA'),
('Yash Parmar',     'yash.parmar@example.com',     '9876500007', 'M.Tech'),
('Anaya Gandhi',    'anaya.gandhi@example.com',    '9876500008', 'MCA'),
('Vivaan Rana',     'vivaan.rana@example.com',     '9876500009', 'B.Tech'),
('Saanvi Modi',     'saanvi.modi@example.com',     '9876500010', 'BBA'),
('Arjun Thakkar',   'arjun.thakkar@example.com',   '9876500011', 'BCA'),
('Kavya Bhatt',     'kavya.bhatt@example.com',     '9876500012', 'B.Sc'),
('Dev Solanki',     'dev.solanki@example.com',     '9876500013', 'MCA'),
('Riya Chauhan',    'riya.chauhan@example.com',    '9876500014', 'B.Tech'),
('Harsh Vora',      'harsh.vora@example.com',      '9876500015', 'BCA'),
('Nidhi Pandya',    'nidhi.pandya@example.com',    '9876500016', 'BBA'),
('Manav Kapadia',   'manav.kapadia@example.com',   '9876500017', 'M.Tech'),
('Tara Dave',       'tara.dave@example.com',       '9876500018', 'B.Sc'),
('Om Raval',        'om.raval@example.com',        '9876500019', 'MCA'),
('Pooja Barot',     'pooja.barot@example.com',     '9876500020', 'BCA'),
('Neel Amin',       'neel.amin@example.com',       '9876500021', 'B.Tech'),
('Sneha Prajapati', 'sneha.prajapati@example.com', '9876500022', 'BBA');