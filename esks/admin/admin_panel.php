<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

// Ekstra kontrole gerek yok, çünkü auth.php zaten oturum kontrolü yapıyor
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

$user = $_GET['user'] ?? '';
$facility = $_GET['facility'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$sql = "SELECT er.*, u.name AS user_name, f.name AS facility_name, f.capacity, f.description
        FROM event_requests er
        JOIN users u ON er.user_id = u.id
        JOIN facilities f ON er.facility_id = f.id
        WHERE 1=1";
$params = [];

if ($user) {
    $sql .= " AND u.name LIKE ?";
    $params[] = "%$user%";
}
if ($facility) {
    $sql .= " AND f.name LIKE ?";
    $params[] = "%$facility%";
}
if ($from) {
    $sql .= " AND er.start_datetime >= ?";
    $params[] = $from;
}
if ($to) {
    $sql .= " AND er.end_datetime <= ?";
    $params[] = $to;
}

$sql .= " ORDER BY er.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Etkinlik Talepleri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f2f9ff; }
        .container { max-width: 1100px; margin-top:0px; }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">Etkinlik Talepleri Filtreleme</h3>
    <form class="row g-3 mb-4" method="GET">
        <div class="col-md-3">
            <input type="text" class="form-control" name="user" placeholder="Kullanıcı adı" value="<?= htmlspecialchars($user) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="facility" placeholder="Tesis adı" value="<?= htmlspecialchars($facility) ?>">
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" name="from" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" name="to" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrele</button>
        </div>
    </form>

    <table class="table table-bordered table-hover">
        <thead class="table-secondary">
            <tr>
                <th>Kullanıcı</th>
                <th>Tesis</th>
                <th>Başlık</th>
                <th>Başlangıç</th>
                <th>Bitiş</th>
                <th>Durum</th>
                <th>Detay</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['user_name']) ?></td>
                    <td><?= htmlspecialchars($r['facility_name']) ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= $r['start_datetime'] ?></td>
                    <td><?= $r['end_datetime'] ?></td>
                    <td><?= ucfirst($r['status']) ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modal<?= $r['id'] ?>">
                            Görüntüle
                        </button>
                        <!-- Modal -->
                        <div class="modal fade" id="modal<?= $r['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Etkinlik Detayı</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Kullanıcı:</strong> <?= htmlspecialchars($r['user_name']) ?></p>
                                        <p><strong>Tesis:</strong> <?= htmlspecialchars($r['facility_name']) ?></p>
                                        <p><strong>Başlık:</strong> <?= htmlspecialchars($r['title']) ?></p>
                                        <p><strong>Açıklama:</strong> <?= nl2br(htmlspecialchars($r['description'])) ?></p>
                                        <p><strong>Başlangıç:</strong> <?= $r['start_datetime'] ?></p>
                                        <p><strong>Bitiş:</strong> <?= $r['end_datetime'] ?></p>
                                        <p><strong>Durum:</strong> <?= ucfirst($r['status']) ?></p>
                                        <p><strong>Tesis Kapasitesi:</strong> <?= $r['capacity'] ?></p>
                                        <p><strong>Tesis Açıklaması:</strong> <?= nl2br(htmlspecialchars($r['description'])) ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center mt-4">
        <a href="admin_panel.php" class="btn btn-outline-secondary">← Admin Paneli</a>
    </div>
</div>

<?php
// Kullanıcıları veritabanından al
$stmt = $pdo->query("SELECT id, name, email, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h4 class="mb-4 text-center">Kullanıcı Yönetimi</h4>

    <a href="add_user.php" class="btn btn-primary mb-4">Yeni Kullanıcı Ekle</a>

    <table class="table table-bordered table-hover">
        <thead class="table-secondary">
            <tr>
                <th>Ad</th>
                <th>Email</th>
                <th>Rol</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <a href="reset_password.php?user_id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Şifre Değiştir</a>
                        <a href="delete_user.php?user_id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="/admin/admin_panel.php" class="btn btn-outline-secondary">← Admin Paneli</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>