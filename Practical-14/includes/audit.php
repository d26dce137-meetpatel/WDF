<?php
function audit_log(PDO $pdo, string $action, string $details = ''): void {
    $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $pdo->prepare(
        'INSERT INTO audit_logs (user_id, action, details, ip_address, created_at)
         VALUES (:user_id, :action, :details, :ip, NOW())'
    );
    $stmt->execute([
        ':user_id' => $userId,
        ':action' => $action,
        ':details' => $details,
        ':ip' => $ip
    ]);
}
