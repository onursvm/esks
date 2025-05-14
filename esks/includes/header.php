<?php
// Oturum henüz başlamadıysa başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>E-SKS Platformu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Özel style.css dosyan (istersen) -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <style>
        body {
            background-color: #eef7ff;
            /* Sabit navbar yüksekliği kadar üst boşluk */
            margin: 0;
            padding-top: 70px;
        }

        /* Mobil uyumlu navbar geliştirmesi */
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1rem;
            }
            .btn {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-primary fs-4" href="/admin/admin_panel.php">E-SKS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- gap-1 -> küçük boşluklar, flex-wrap -> Butonlar ufak ekranlarda alt satıra iner -->
            <ul class="navbar-nav ms-auto d-flex flex-wrap gap-1">
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="/admin/admin_panel.php">Admin Paneli</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="/calendar.php">Etkinlik Takvimi</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="/filter_requests.php">Talepler</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="/admin/add_facility.php">Tesis Ekle</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="/admin/list_users.php">Kullanıcı İşlemleri</a>
                    </li>
                <?php elseif (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user'): ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="/my_requests.php">📄 Taleplerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="/calendar.php">📅 Takvimi Gör</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="/form.php">📝 Tesis/Salon Talep Formu</a>
                    </li>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item d-flex align-items-center">
                        <span class="badge bg-info text-dark">
                            <?= $_SESSION['user']['role'] === 'admin' ? 'Yönetici' : 'Kullanıcı' ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger btn-sm" href="/logout.php">Çıkış Yap</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Sayfa içeriği için container açılışı -->
<div class="container mt-3">
