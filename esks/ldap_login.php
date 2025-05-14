<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // LDAP sunucu ayarları
    $ldap_server = "ldap.balikesir.edu.tr";
    $ldap_port = 389;
    $ldap_dn = "ou=People,dc=balikesir,dc=edu,dc=tr";
    $ldap_user_attr = "uid";

    try {
        // LDAP bağlantısı
        $ldap_conn = ldap_connect($ldap_server, $ldap_port);

        if ($ldap_conn) {
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

            // LDAP bind (bağlantı)
            $ldap_bind = @ldap_bind($ldap_conn, "$ldap_user_attr=$username,$ldap_dn", $password);

            if ($ldap_bind) {
                // Kullanıcı bilgilerini çek
                $filter = "($ldap_user_attr=$username)";
                $result = ldap_search($ldap_conn, $ldap_dn, $filter);
                $entries = ldap_get_entries($ldap_conn, $result);

                if ($entries['count'] > 0) {
                    $user_info = $entries[0];

                    // Veritabanında kullanıcı var mı kontrol et
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                    $stmt->execute([$username . "@balikesir.edu.tr"]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$user) {
                        // Kullanıcı yoksa oluştur
                        $name = $user_info['cn'][0] ?? $username;
                        $email = $username . "@balikesir.edu.tr";
                        $role = 'user'; // Varsayılan rol

                        $insert = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                        $insert->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);

                        $user_id = $pdo->lastInsertId();
                        $user = ['id' => $user_id, 'name' => $name, 'email' => $email, 'role' => $role];
                    }

                    // Oturum başlat
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'role' => $user['role']
                    ];

                    header("Location: /");
                    exit;
                }
            }
        }

        // Hatalı giriş
        $_SESSION['error'] = "Geçersiz kullanıcı adı veya şifre!";
        header("Location: /login.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = "LDAP bağlantı hatası: " . $e->getMessage();
        header("Location: /login.php");
        exit;
    } finally {
        if ($ldap_conn) {
            ldap_close($ldap_conn);
        }
    }
}

// Eğer GET isteği gelirse login sayfasına yönlendir
header("Location: /login.php");
exit;