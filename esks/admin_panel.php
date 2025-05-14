<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

// Sadece admin erişebilir
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

// İstatistikleri al
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_facilities = $pdo->query("SELECT COUNT(*) FROM facilities")->fetchColumn();
$pending_requests = $pdo->query("SELECT COUNT(*) FROM event_requests WHERE status = 'pending'")->fetchColumn();
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Admin Kontrol Paneli</h3>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Kullanıcılar</h5>
                    <p class="card-text display-4"><?= $total_users ?></p>
                    <a href="list_users.php" class="btn btn-light">Yönet</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Tesisler</h5>
                    <p class="card-text display-4"><?= $total_facilities ?></p>
                    <a href="manage_facilities.php" class="btn btn-light">Yönet</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Bekleyen Talepler</h5>
                    <p class="card-text display-4"><?= $pending_requests ?></p>
                    <a href="../filter_requests.php" class="btn btn-dark">İncele</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    Hızlı Erişim
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="add_user.php" class="btn btn-outline-primary">Yeni Kullanıcı Ekle</a>
                        <a href="add_facility.php" class="btn btn-outline-success">Yeni Tesis Ekle</a>
                        <a href="../calendar.php" class="btn btn-outline-secondary">Etkinlik Takvimi</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    Son Etkinlikler
                </div>
                <div class="card-body">
                    <?php
                    $recent_events = $pdo->query("SELECT er.title, f.name as facility, er.start_datetime 
                                                 FROM event_requests er 
                                                 JOIN facilities f ON er.facility_id = f.id 
                                                 WHERE er.status = 'approved' 
                                                 ORDER BY er.start_datetime DESC LIMIT 5")->fetchAll();

                    if (count($recent_events) > 0) {
                        echo '<ul class="list-group">';
                        foreach ($recent_events as $event) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                            echo htmlspecialchars($event['title']);
                            echo '<span class="badge bg-primary rounded-pill">' .
                                htmlspecialchars($event['facility']) . ' - ' .
                                date('d.m.Y H:i', strtotime($event['start_datetime'])) . '</span>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<div class="alert alert-info">Henüz onaylanmış etkinlik bulunmamaktadır.</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>