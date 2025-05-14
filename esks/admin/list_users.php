<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Admin kontrolü
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

// Kullanıcıları veritabanından al
$stmt = $pdo->query("SELECT id, name, email, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <h4 class="mb-4 text-center">Kullanıcı Listele</h4>

    <!-- Yeni kullanıcı ekleme bağlantısı -->
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
