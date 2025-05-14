<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? null;
    $action = $_POST['action'] ?? '';

    if ($request_id && in_array($action, ['approve', 'reject'])) {
        $status = ($action === 'approve') ? 'approved' : 'rejected';

        $stmt = $pdo->prepare("UPDATE event_requests SET status = ? WHERE id = ?");
        $stmt->execute([$status, $request_id]);

        header("Location: filter_requests.php");
        exit;
    }
}

http_response_code(400);
echo "Hatalı istek!";
