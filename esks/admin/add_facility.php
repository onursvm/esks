<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $capacity = intval($_POST['capacity']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);

    if (empty($name) || empty($capacity)) {
        $error = "Tesis adı ve kapasite alanları zorunludur.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO facilities (name, capacity, location, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $capacity, $location, $description]);
        $success = true;
    }
}
?>

<div class="container mt-4" style="max-width: 600px;">
    <h4 class="mb-4">🏢 Yeni Tesis Ekle</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success text-center">
            ✅ Tesis başarıyla eklendi.
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Tesis Adı</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="capacity" class="form-label">Kapasite</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Konum</label>
            <input type="text" name="location" class="form-control">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Açıklama</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Kaydet</button>
    </form>

    <div class="text-center mt-4">
        <a href="admin_panel.php" class="btn btn-outline-secondary">← Admin Paneline Dön</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
