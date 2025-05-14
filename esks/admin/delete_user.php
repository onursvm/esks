<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Admin kontrolü
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    // Kullanıcıyı silme işlemi
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    // Silme işleminden sonra başarılı mesajı
    $_SESSION['message'] = "Kullanıcı başarıyla silindi.";
} else {
    // Eğer user_id parametresi yoksa hata mesajı
    $_SESSION['error'] = "Geçersiz istek.";
}

header("Location: /admin/admin_panel.php");
exit;
?>
