<?php
include '../config/db.php';

// Kilit açma/kapama işlemi
if (isset($_GET['aksiyon']) && isset($_GET['etkinlik'])) {
    $yeni_durum = ($_GET['aksiyon'] == 'ac') ? 1 : 0;
    $etkinlik = $_GET['etkinlik'];
    
    $sql = "UPDATE sistem_ayarlari SET durum = ? WHERE etkinlik_adi = ?";
    $pdo->prepare($sql)->execute([$yeni_durum, $etkinlik]);
    header("Location: control.php");
}

$ayarlar = $pdo->query("SELECT * FROM sistem_ayarlari")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğretmen Kontrol Masası</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f8f9fa; }
        .panel-card { background: white; padding: 20px; border-left: 5px solid #007bff; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .btn-ac { background: #28a745; color: white; padding: 8px; text-decoration: none; border-radius: 4px; }
        .btn-kapat { background: #dc3545; color: white; padding: 8px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>🚀 Sınıf Yönetimi ve Etkinlik Kontrolü</h2>
    
    <?php foreach($ayarlar as $ayar): ?>
    <div class="panel-card">
        <div>
            <strong><?= strtoupper($ayar['etkinlik_adi']) ?></strong> 
            
        </div>
        <div>
            Durum: <?= ($ayar['durum'] == 1) ? '<span style="color:green">AÇIK</span>' : '<span style="color:red">KAPALI</span>' ?>
            | 
            <a href="?aksiyon=ac&etkinlik=<?= $ayar['etkinlik_adi'] ?>" class="btn-ac">Kilidi Aç</a>
            <a href="?aksiyon=kapat&etkinlik=<?= $ayar['etkinlik_adi'] ?>" class="btn-kapat">Kapat</a>
        </div>
    </div>
    <?php endforeach; ?>

    <p><a href="index.php">⬅ Veri Takip Paneline Dön (Çalınan Şifreler)</a></p>
</body>
</html>