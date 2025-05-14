<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Admin kontrolü
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

$success = '';
$error = '';
$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: /admin/admin_panel.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($new) < 4) {
            $error = 'Yeni şifre en az 4 karakter olmalıdır.';
        } elseif ($new !== $confirm) {
            $error = 'Yeni şifre ve tekrar şifre uyuşmuyor.';
        } else {
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$new_hash, $user_id]);

            $success = 'Şifreniz başarıyla güncellendi.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5" style="max-width: 500px">
    <h4 class="mb-4">🔒 Şifre Değiştir</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Şifre değiştirme formu -->
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Yeni Şifre</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Yeni Şifre (Tekrar)</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Güncelle</button>
    </form>
    <div class="text-center mt-4">
        <a href="/admin/admin_panel.php" class="btn btn-outline-secondary">← Admin Paneli</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
