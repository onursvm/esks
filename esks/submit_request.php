<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $facility_id = $_POST['facility_id'];
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $user_id = $_SESSION['user']['id'];

    if (empty($title) || empty($description) || empty($start) || empty($end) || empty($facility_id)) {
        $error = "Tüm alanları doldurmalısınız.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO event_requests (user_id, facility_id, title, description, start_datetime, end_datetime, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$user_id, $facility_id, $title, $description, $start, $end]);
        $success = true;
    }
}

$facilities = $pdo->query("SELECT * FROM facilities ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4" style="max-width: 600px;">
    <h4 class="mb-4">🎯 Etkinlik Talep Formu</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success text-center mt-3">
            ✅ Talebiniz başarıyla gönderildi.
        </div>
    <?php endif; ?>

    <form method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Etkinlik Başlığı</label>
            <input type="text" name="title" class="form-control" required>
            <small class="form-text text-muted">Etkinliğin kısa ve öz adını girin.</small>
        </div>
        <div class="mb-3">
            <label for="facility_id" class="form-label">Tesis Seçimi</label>
            <select name="facility_id" class="form-select" required>
                <option value="">Tesis seçiniz</option>
                <?php foreach ($facilities as $f): ?>
                    <option value="<?= $f['id'] ?>"><?= $f['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Etkinlik Açıklaması</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="start" class="form-label">Başlangıç Tarihi</label>
            <input type="datetime-local" name="start_datetime" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end" class="form-label">Bitiş Tarihi</label>
            <input type="datetime-local" name="end_datetime" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Gönder</button>
    </form>

    <div class="text-center mt-4">
        <a href="/my_requests.php" class="btn btn-outline-secondary">📄 Taleplerim</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
