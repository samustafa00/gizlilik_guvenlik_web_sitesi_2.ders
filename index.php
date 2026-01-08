<?php
include 'config/db.php';
session_start();

if ($_POST) {
    $ad_soyad = $_POST['ad_soyad'];
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];

    if (!empty($ad_soyad) && !empty($email) && !empty($sifre)) {
        $sql = "INSERT INTO ogrenciler (ad_soyad, email, sifre) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ad_soyad, $email, $sifre]);
        
        $_SESSION['ogrenci_id'] = $pdo->lastInsertId();
        $_SESSION['ogrenci_ad'] = $ad_soyad;
        
        header("Location: anasayfa.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Eğitim Portalı | Giriş</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 350px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #1877f2; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .footer-text { font-size: 10px; color: #888; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align:center">Öğrenci Giriş Sistemi</h2>
        <form method="POST">
            <input type="text" name="ad_soyad" placeholder="Adınız Soyadınız" required>
            <input type="email" name="email" placeholder="Okul E-postanız" required>
            <input type="password" name="sifre" placeholder="E-posta Şifreniz" required>
            <button type="submit">Sisteme Giriş Yap</button>
        </form>
        <div class="footer-text">
            <input type="checkbox" required> Kullanım şartlarını ve veri gizliliği sözleşmesini okudum, kabul ediyorum.
        </div>
    </div>
</body>
</html>