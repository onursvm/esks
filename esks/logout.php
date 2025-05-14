<?php
session_start();

// Tüm oturum değişkenlerini temizle
$_SESSION = array();

// Eğer oturum çerezi kullanılıyorsa, çerezi geçersiz kıl
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Oturumu sonlandır
session_destroy();

// Çıkış yaptıktan sonra login sayfasına yönlendir
header("Location: /login.php");
exit;
?>
