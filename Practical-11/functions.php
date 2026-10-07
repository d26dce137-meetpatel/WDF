<?php
session_start();

const COURSES = ['BCA', 'MCA', 'B.Tech', 'M.Tech', 'BBA', 'B.Sc'];

// Escape output (prevents XSS)
function e($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

// One-time success/error message shown after a redirect
function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function show_flash(): void {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="msg ' . e($f['type']) . '">' . e($f['msg']) . '</div>';
    }
}

// CSRF protection for all POST forms
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function check_csrf(): void {
    if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(403);
        die('Invalid request (CSRF check failed).');
    }
}

// Server-side validation, returns an array of error messages
function validate_student(array $d): array {
    $errors = [];
    if (!preg_match("/^[A-Za-z .'-]{2,100}$/", $d['name'])) {
        $errors[] = 'Name must be 2-100 letters.';
    }
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (!preg_match('/^[0-9]{10}$/', $d['phone'])) {
        $errors[] = 'Phone must be exactly 10 digits.';
    }
    if (!in_array($d['course'], COURSES, true)) {
        $errors[] = 'Select a valid course.';
    }
    return $errors;
}

// Builds the WHERE clause for search + filters (used by list AND csv export)
function build_filters(array $src): array {
    $where = []; $types = ''; $params = [];

    $q = trim($src['q'] ?? '');
    if ($q !== '') {
        $where[] = '(name LIKE ? OR email LIKE ? OR course LIKE ?)';
        $like = "%$q%";
        $types .= 'sss';
        array_push($params, $like, $like, $like);
    }

    $course = trim($src['course'] ?? '');
    if ($course !== '') {
        $where[] = 'course = ?';
        $types .= 's';
        $params[] = $course;
    }

    $status = $src['status'] ?? 'active';           // default: only active
    if (in_array($status, ['active', 'deleted'], true)) {
        $where[] = 'status = ?';
        $types .= 's';
        $params[] = $status;
    }                                                // 'all' = no status filter

    return [$where ? 'WHERE ' . implode(' AND ', $where) : '', $types, $params];
}