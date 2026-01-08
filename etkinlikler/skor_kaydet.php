<?php
include '../config/db.php';
session_start();

// Eğer oturum yoksa veya veri gelmediyse işlemi durdur
if (!isset($_SESSION['ogrenci_id']) || !isset($_POST['etkinlik'])) {
    die("Yetkisiz erişim veya eksik veri.");
}

$ogrenci_id = $_SESSION['ogrenci_id'];
$etkinlik = $_POST['etkinlik'];

// Gelen verileri güvenli hale getir
$puan = isset($_POST['puan']) ? intval($_POST['puan']) : 0;
$sure = isset($_POST['sure']) ? floatval($_POST['sure']) : 0;

if ($etkinlik == "1") {
    // 1. Etkinlik Puan Güncelleme
    $sql = "UPDATE ogrenciler SET puan_etkinlik1 = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$puan, $ogrenci_id]);
} 
elseif ($etkinlik == "2") {
    // 2. Etkinlik Puan Güncelleme
    $sql = "UPDATE ogrenciler SET puan_etkinlik2 = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$puan, $ogrenci_id]);
} 
elseif ($etkinlik == "3") {
    // 3. Etkinlik Hız ve Puan Güncelleme
    // Formül: 10000 / süre (Hızlı olan çok puan alır)
    $toplam_puan = ($sure > 0) ? round(10000 / $sure) : 0;
    
    $sql = "UPDATE ogrenciler SET puan_etkinlik3_sure = ?, puan_etkinlik3_toplam = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$sure, $toplam_puan, $ogrenci_id]);
}

echo "Başarıyla kaydedildi.";
?>