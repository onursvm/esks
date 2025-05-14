<?php
try {
    $host = 'localhost'; // Veritabanı sunucusu
    $dbname = 'esks_db'; // 🔁 Doğru veritabanı adı
    $username = 'esks_db';  // Kullanıcı adınız
    $password = '8_5ACXm6I!lS6d0_'; // Şifreniz

    // PDO bağlantısını oluştur
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // PDO hata ayıklama ayarları
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>
