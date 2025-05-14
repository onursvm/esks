<?php
session_start();
require_once "includes/auth.php";
require_once "config/database.php";

if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Tüm talepler
$total = $pdo->query("SELECT COUNT(*) FROM event_requests")->fetchColumn();
$approved = $pdo->query("SELECT COUNT(*) FROM event_requests WHERE status = 'approved'")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM event_requests WHERE status = 'pending'")->fetchColumn();
$rejected = $pdo->query("SELECT COUNT(*) FROM event_requests WHERE status = 'rejected'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İstatistikler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:600px;">
    <h4 class="text-center mb-4">📊 Sistem İstatistikleri</h4>
    <ul class="list-group shadow">
        <li class="list-group-item d-flex justify-content-between">
            <span>Toplam Talep</span>
            <strong><?= $total ?></strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Onaylı Talepler</span>
            <strong class="text-success"><?= $approved ?></strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Bekleyen Talepler</span>
            <strong class="text-warning"><?= $pending ?></strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Reddedilen Talepler</span>
            <strong class="text-danger"><?= $rejected ?></strong>
        </li>
    </ul>
    <div class="text-center mt-4">
        <a href="admin/admin_panel.php" class="btn btn-outline-secondary">← Admin Paneli</a>
    </div>
</div>
</body>
</html>
