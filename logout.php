<?php
session_start(); // Mevcut oturumu başlat

// Tüm oturum değişkenlerini temizle
$_SESSION = array();

// Eğer oturum çerezlerini kullanıyorsa, onları da temizle
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Oturumu tamamen yok et
session_destroy();

// Giriş sayfasına yönlendir
header("Location: index.php");
exit();
?>