<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id !== null && $id !== false && $id < 1) {
    json_response(400, ['success' => false, 'message' => 'Invalid event ID.']);
}

try {
    if ($method === 'GET') {
        if ($id) {
            $stmt = $pdo->prepare('SELECT * FROM events WHERE event_id = ?');
            $stmt->execute([$id]);
            $event = $stmt->fetch();
            if (!$event) {
                json_response(404, ['success' => false, 'message' => 'Event not found.']);
            }
            json_response(200, ['success' => true, 'data' => $event]);
        }

        $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC, event_id DESC');
        json_response(200, ['success' => true, 'data' => $stmt->fetchAll()]);
    }

    if ($method === 'POST') {
        $event = validate_event(request_json());
        $stmt = $pdo->prepare(
            'INSERT INTO events (event_name, event_category, event_date, venue, status) VALUES (:event_name, :event_category, :event_date, :venue, :status)'
        );
        $stmt->execute($event);
        $eventId = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare('SELECT * FROM events WHERE event_id = ?');
        $stmt->execute([$eventId]);
        json_response(201, ['success' => true, 'message' => 'Event added successfully.', 'data' => $stmt->fetch()]);
    }

    if ($method === 'PUT') {
        if (!$id) {
            json_response(400, ['success' => false, 'message' => 'Event ID is required for update.']);
        }
        $event = validate_event(request_json());
        $stmt = $pdo->prepare(
            'UPDATE events SET event_name=:event_name, event_category=:event_category, event_date=:event_date, venue=:venue, status=:status WHERE event_id=:id'
        );
        $stmt->execute($event + ['id' => $id]);
        if ($stmt->rowCount() === 0) {
            $check = $pdo->prepare('SELECT event_id FROM events WHERE event_id = ?');
            $check->execute([$id]);
            if (!$check->fetch()) {
                json_response(404, ['success' => false, 'message' => 'Event not found.']);
            }
        }
        $stmt = $pdo->prepare('SELECT * FROM events WHERE event_id = ?');
        $stmt->execute([$id]);
        json_response(200, ['success' => true, 'message' => 'Event updated successfully.', 'data' => $stmt->fetch()]);
    }

    if ($method === 'DELETE') {
        if (!$id) {
            json_response(400, ['success' => false, 'message' => 'Event ID is required for delete.']);
        }
        $stmt = $pdo->prepare('DELETE FROM events WHERE event_id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            json_response(404, ['success' => false, 'message' => 'Event not found.']);
        }
        json_response(200, ['success' => true, 'message' => 'Event deleted successfully.']);
    }

    header('Allow: GET, POST, PUT, DELETE');
    json_response(405, ['success' => false, 'message' => 'Method not allowed.']);
} catch (PDOException $e) {
    json_response(500, ['success' => false, 'message' => 'Database operation failed.']);
}
