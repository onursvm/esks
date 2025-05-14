<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Yalnızca admin erişebilir
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

// Tesis ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['facility_name'])) {
    $facilityName = trim($_POST['facility_name']);
    if (!empty($facilityName)) {
        $stmt = $pdo->prepare("INSERT INTO facilities (name) VALUES (?)");
        $stmt->execute([$facilityName]);
        header("Location: manage_facilities.php");
        exit;
    }
}

// Tesis silme işlemi
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM facilities WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_facilities.php");
    exit;
}

// Mevcut tesisleri çek
$stmt = $pdo->query("SELECT * FROM facilities ORDER BY name");
$facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tesis Yönetimi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f0f8ff; }
        .container { max-width: 700px; margin-top: 60px; }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">Tesis (Salon) Yönetimi</h3>

    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="facility_name" class="form-control" placeholder="Yeni tesis adı" required>
            <button class="btn btn-success" type="submit">➕ Ekle</button>
        </div>
    </form>

    <?php if (count($facilities) > 0): ?>
        <ul class="list-group">
            <?php foreach ($facilities as $facility): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($facility['name']) ?>
                    <a href="?delete=<?= $facility['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?');">❌ Sil</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">Henüz tesis eklenmemiş.</div>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="admin_panel.php" class="btn btn-outline-primary">🔙 Admin Panele Dön</a>
    </div>
</div>
</body>
</html>
