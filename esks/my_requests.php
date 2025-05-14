<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Sadece kullanıcı rolü görebilsin
if ($_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT er.*, f.name AS facility_name 
    FROM event_requests er 
    JOIN facilities f ON er.facility_id = f.id 
    WHERE er.user_id = ? 
    ORDER BY er.created_at DESC
");
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container mt-5">
    <h3 class="mb-4">📋 Etkinlik Talep Geçmişi</h3>

    <?php if (count($requests) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Tesis</th>
                        <th>Başlık</th>
                        <th>Açıklama</th>
                        <th>Başlangıç</th>
                        <th>Bitiş</th>
                        <th>Durum</th>
                        <th>Oluşturulma</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= htmlspecialchars($req['facility_name']) ?></td>
                            <td><?= htmlspecialchars($req['title']) ?></td>
                            <td><?= nl2br(htmlspecialchars($req['description'])) ?></td>
                            <td><?= $req['start_datetime'] ?></td>
                            <td><?= $req['end_datetime'] ?></td>
                            <td>
                                <?php
                                    $badge = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger'
                                    ];
                                    $label = [
                                        'pending' => 'Beklemede',
                                        'approved' => 'Onaylandı',
                                        'rejected' => 'Reddedildi'
                                    ];
                                    echo "<span class='badge bg-{$badge[$req['status']]}'>" . $label[$req['status']] . "</span>";
                                ?>
                            </td>
                            <td><?= $req['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz bir etkinlik talebiniz bulunmamaktadır.</div>
    <?php endif; ?>

    <a href="form.php" class="btn btn-outline-primary mt-4">➕ Yeni Talep Oluştur</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
