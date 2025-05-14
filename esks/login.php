<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';

// Zaten giriş yapılmışsa yönlendir
if (isset($_SESSION['user'])) {
    $redirect = ($_SESSION['user']['role'] === 'admin') ? '/admin/admin_panel.php' : '/form.php';
    header("Location: $redirect");
    exit;
}

$error = '';
$message = '';

// Form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $limit = 5;
    $lockout_minutes = 10;

    // Son 10 dakikada başarısız deneme sayısı
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempt_time > NOW() - INTERVAL ? MINUTE");
    $stmt->execute([$ip, $lockout_minutes]);
    $attempt_count = $stmt->fetchColumn();

    if ($attempt_count >= $limit) {
        $error = "Çok fazla başarısız giriş denemesi! Lütfen $lockout_minutes dakika sonra tekrar deneyin.";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Kullanıcıyı veritabanından getir
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Şifre doğrulama
            if (password_verify($password, $user['password'])) {
                // Başarılı giriş
                $_SESSION['user'] = [
                    'id'   => $user['id'],
                    'name' => $user['name'],
                    'role' => $user['role']
                ];

                $_SESSION['message'] = "Giriş başarılı, hoş geldiniz!";
                $redirect = ($user['role'] === 'admin') ? '/admin/admin_panel.php' : '/form.php';
                header("Location: $redirect");
                exit;
            } else {
                $error = "Geçersiz kullanıcı adı veya şifre!";
            }
        } else {
            $error = "Geçersiz kullanıcı adı veya şifre!";
        }

        // Hatalı giriş kaydet
        $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, attempt_time) VALUES (?, NOW())");
        $stmt->execute([$ip]);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>E-SKS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('/assets/img/1.jpg') no-repeat center center fixed;
            background-size: cover;
            filter: brightness(0.9) contrast(1.1);
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        footer {
            text-align: center;
            font-weight: bold;
            font-style: italic;
            color: #007bff;
            background-color: rgba(255,255,255,0.6);
            border: 2px solid #007bff;
            padding: 5px 20px;
            border-radius: 10px;
            margin: 20px auto 0 auto;
            max-width: 400px;
        }
        @media (max-width: 576px) {
            .login-container {
                margin: 50px 20px;
                padding: 20px;
            }
            footer {
                font-size: 0.9rem;
                padding: 5px 10px;
            }
        }
    </style>
</head>
<body class="login-background">
<div class="login-container">
    <h4 class="text-center mb-4">SKS DAİRE BAŞKANLIĞI<br>TESİS VE KONGRE SALONU TALEP PLATFORMU</h4>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Kullanıcı Adı (E-posta)</label>
            <input type="text" name="username" class="form-control" placeholder="E-posta adresi" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Parola</label>
            <input type="password" name="password" class="form-control" placeholder="********" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
    </form>

    <div class="text-center mt-3">
        <a href="/calendar.php" class="btn btn-outline-secondary">📅 Etkinlik Takvimini Görüntüle</a>
    </div>
</div>
<footer>
    BAUN / BİLGİ İŞLEM DAİRE BAŞKANLIĞI &copy; 2025
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
