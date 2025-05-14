<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = '';
$error = '';

// Form gönderimi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Geçersiz CSRF token!");
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $facility_id = $_POST['facility_id'];
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $user_id = $_SESSION['user']['id'];

    if (empty($title) || empty($description) || empty($start) || empty($end) || empty($facility_id)) {
        $error = "Tüm alanları doldurmalısınız.";
    } else {
        // SUNUCU TARAFI ÇAKIŞMA KONTROLÜ
        $check = $pdo->prepare("SELECT COUNT(*) FROM event_requests 
            WHERE facility_id = ? AND status = 'approved'
            AND (
                (start_datetime < ? AND end_datetime > ?) OR
                (start_datetime >= ? AND start_datetime < ?)
            )");
        $check->execute([$facility_id, $end, $start, $start, $end]);
        $conflict = $check->fetchColumn();

        if ($conflict > 0) {
            $error = "❌ Seçilen tarih ve saat aralığında bu tesis zaten tahsisli.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO event_requests 
                (user_id, facility_id, title, description, start_datetime, end_datetime, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$user_id, $facility_id, $title, $description, $start, $end]);
            $success = "✅ Talebiniz başarıyla gönderildi. Yönlendiriliyorsunuz...";
        }
    }
}

$facilities = $pdo->query("SELECT * FROM facilities ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4" style="max-width: 650px;">
    <h4 class="mb-4 text-center">📌 Tesis / Salon Talep Formu</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success text-center mt-3">
            <?= htmlspecialchars($success) ?>
        </div>
        <script>
            setTimeout(function() {
                window.location.href = "/my_requests.php";
            }, 3000);
        </script>
    <?php endif; ?>

    <form method="POST" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="mb-3">
            <label class="form-label">Etkinlik Başlığı</label>
            <input type="text" name="title" class="form-control" placeholder="Örn: Bilgilendirme Toplantısı" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tesis Seçimi</label>
            <select name="facility_id" class="form-select" required>
                <option value="">Tesis seçiniz</option>
                <?php foreach ($facilities as $f): ?>
                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Etkinlik Açıklaması</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Etkinlik detaylarını giriniz..." required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Başlangıç Tarih/Saat</label>
            <input type="datetime-local" name="start_datetime" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Bitiş Tarih/Saat</label>
            <input type="datetime-local" name="end_datetime" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">📤 Gönder</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
