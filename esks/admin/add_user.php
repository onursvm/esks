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

// Form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'user'; // Default rol 'user' olarak belirleniyor

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Tüm alanları doldurmalısınız.';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } else {
        // Şifreyi hash'leyerek veritabanına ekle
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Kullanıcıyı veritabanına ekle
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password_hash, $role]);

        $success = 'Yeni kullanıcı başarıyla eklendi.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5" style="max-width: 500px">
    <h4 class="mb-4">Yeni Kullanıcı Ekle</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Kullanıcı ekleme formu -->
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Ad</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">E-posta</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Şifre</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role" class="form-select">
                <option value="user">Kullanıcı</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Kullanıcı Ekle</button>
    </form>
    <div class="text-center mt-4">
        <a href="/admin/admin_panel.php" class="btn btn-outline-secondary">← Admin Paneli</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
