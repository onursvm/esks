<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

// Tesis listesini getir
$stmt = $pdo->query("SELECT * FROM facilities ORDER BY name");
$facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h3 class="mb-4">Mevcut Tesisler</h3>

    <?php if (count($facilities) > 0): ?>
        <div class="row">
            <?php foreach ($facilities as $facility): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0"><?= htmlspecialchars($facility['name']) ?></h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                <strong>Kapasite:</strong> <?= $facility['capacity'] ?><br>
                                <strong>Konum:</strong> <?= htmlspecialchars($facility['location']) ?><br>
                                <strong>Açıklama:</strong> <?= nl2br(htmlspecialchars($facility['description'])) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="/calendar.php?facility_id=<?= $facility['id'] ?>"
                                class="btn btn-sm btn-outline-primary">Takvimi Gör</a>
                            <?php if (isAdmin()): ?>
                                <a href="/admin/manage_facilities.php?delete=<?= $facility['id'] ?>"
                                    class="btn btn-sm btn-outline-danger float-end"
                                    onclick="return confirm('Bu tesisi silmek istediğinize emin misiniz?')">Sil</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz tesis eklenmemiş.</div>
    <?php endif; ?>

    <?php if (isAdmin()): ?>
        <div class="text-center mt-4">
            <a href="/admin/add_facility.php" class="btn btn-primary">Yeni Tesis Ekle</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>