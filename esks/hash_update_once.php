<?php
require_once __DIR__ . '/config/database.php';

$new_password = '12345678';
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$new_hash, 'bid@balikesir.edu.tr']);

echo "Yeni hash kaydedildi: $new_hash";
