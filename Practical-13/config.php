<?php
declare(strict_types=1);

header('X-Content-Type-Options: nosniff');

const DB_HOST = 'localhost';
const DB_NAME = 'practical13_api';
const DB_USER = 'root';
const DB_PASS = '';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Import database.sql and check config.php.']);
    exit;
}

function json_response(int $status, array $payload): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_json(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '', true);
    if (!is_array($data)) {
        json_response(400, ['success' => false, 'message' => 'Request body must be valid JSON.']);
    }
    return $data;
}

function validate_event(array $data): array {
    $name = trim((string)($data['event_name'] ?? ''));
    $category = trim((string)($data['event_category'] ?? ''));
    $date = trim((string)($data['event_date'] ?? ''));
    $venue = trim((string)($data['venue'] ?? ''));
    $status = trim((string)($data['status'] ?? 'Open'));

    if ($name === '' || $category === '' || $date === '' || $venue === '') {
        json_response(422, ['success' => false, 'message' => 'All fields are required.']);
    }
    if (mb_strlen($name) > 120 || mb_strlen($category) > 60 || mb_strlen($venue) > 150) {
        json_response(422, ['success' => false, 'message' => 'One or more fields exceed the allowed length.']);
    }
    $d = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        json_response(422, ['success' => false, 'message' => 'Enter a valid event date.']);
    }
    if (!in_array($status, ['Open', 'Closed'], true)) {
        json_response(422, ['success' => false, 'message' => 'Status must be Open or Closed.']);
    }

    return [
        'event_name' => $name,
        'event_category' => $category,
        'event_date' => $date,
        'venue' => $venue,
        'status' => $status,
    ];
}
