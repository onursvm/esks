<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <!-- Mobil uyumluluk için gerekli meta -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anasayfa</title>
  <!-- Bootstrap CDN (mobil ve responsive tasarım desteği için) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Özel stil dosyanız -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php if ($message): ?>
    <div class="alert alert-success text-center"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <!-- Ana içerik alanı -->
  <div class="container my-5">
    <h1 class="mb-4">Hoşgeldiniz</h1>
    <p>Buraya ana sayfa içeriğinizi ekleyebilirsiniz.</p>
  </div>

  <!-- Bootstrap için gerekli JS kütüphaneleri -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Yönlendirme veya diğer interaktif özellikler için özel JavaScript dosyanız -->
  <script src="js/script.js"></script>
</body>
</html>
