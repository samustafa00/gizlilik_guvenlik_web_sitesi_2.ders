<?php
include '../config/db.php';
session_start();

// Güvenlik: Sadece adminin bu işlemi yapması için buraya bir kontrol eklenebilir.
// Şimdilik doğrudan silme işlemini yapıyoruz.

try {
    // 1. Tüm öğrenci verilerini ve puanlarını sil
    $pdo->exec("TRUNCATE TABLE ogrenciler");

    // 2. Etkinlik kilitlerini başlangıç haline getir (Hepsini kapat)
    $pdo->exec("UPDATE sistem_ayarlari SET durum = 0");

    header("Location: index.php?mesaj=temizlendi");
} catch (PDOException $e) {
    die("Hata oluştu: " . $e->getMessage());
}
?>