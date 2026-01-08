<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['ogrenci_id'])) {
    header("Location: index.php");
    exit();
}

// 1. ADIM: Verileri normal şekilde çek
$sorgu = $pdo->query("SELECT * FROM sistem_ayarlari");
$tum_ayarlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

// 2. ADIM: Verileri etkinlik_adi'na göre yeniden diz (Hata almamak için kritik)
$ayarlar = [];
foreach ($tum_ayarlar as $satir) {
    $ayarlar[$satir['etkinlik_adi']] = $satir;
}

// 3. ADIM: Öğretmen notunu çek
$not_sorgu = $pdo->prepare("SELECT ogretmen_notu FROM ogrenciler WHERE id = ?");
$not_sorgu->execute([$_SESSION['ogrenci_id']]);
$ogretmen_notu = $not_sorgu->fetchColumn();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Etkinlik Merkezi</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #e9ecef; text-align: center; padding: 50px; }
        .etkinlik-container { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-top: 30px; }
        .card { background: white; padding: 20px; border-radius: 15px; width: 250px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid #1877f2; }
        .locked { filter: grayscale(1); opacity: 0.6; cursor: not-allowed; border-top: 5px solid #6c757d; }
        .btn { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
        .status-msg { font-size: 13px; color: #dc3545; margin-top: 15px; font-weight: bold; }
        input[type="password"] { width: 90%; padding: 8px; margin-top: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .teacher-note { max-width: 800px; margin: 40px auto; padding: 20px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 10px; color: #856404; }
    </style>
</head>
<body>
    <h1>Hoş geldin, <?= htmlspecialchars($_SESSION['ogrenci_ad']) ?>! 👋</h1>
    <p>Aşağıdaki görevleri sırasıyla tamamla. Her görev için öğretmeninden şifreyi almalısın.</p>

    <?php if($ogretmen_notu): ?>
        <div class="teacher-note">
            <strong>👨‍🏫 Öğretmeninden Mesaj var:</strong><br>
            <?= htmlspecialchars($ogretmen_notu) ?>
        </div>
    <?php endif; ?>

    <div class="etkinlik-container">
        
        <div class="card <?= (isset($ayarlar['etkinlik1']) && $ayarlar['etkinlik1']['durum'] == 1) ? '' : 'locked' ?>">
            <h3>🧩 Etkinlik 1</h3>
            <p>Bilgi Paylaşımı ve Riskler</p>
            <?php if(isset($ayarlar['etkinlik1']) && $ayarlar['etkinlik1']['durum'] == 1): ?>
                <form action="etkinlikler/etkinlik1.php" method="POST">
                    <input type="password" name="e_sifre" placeholder="Şifreyi Gir" required>
                    <button type="submit" class="btn">BAŞLA</button>
                </form>
            <?php else: ?>
                <div class="status-msg">🔒 KİLİTLİ</div>
            <?php endif; ?>
        </div>

        <div class="card <?= (isset($ayarlar['etkinlik2']) && $ayarlar['etkinlik2']['durum'] == 1) ? '' : 'locked' ?>">
            <h3>🕵️ Etkinlik 2</h3>
            <p>Oltalama Dedektifi</p>
            <?php if(isset($ayarlar['etkinlik2']) && $ayarlar['etkinlik2']['durum'] == 1): ?>
                <form action="etkinlikler/etkinlik2.php" method="POST">
                    <input type="password" name="e_sifre" placeholder="Şifreyi Gir" required>
                    <button type="submit" class="btn">BAŞLA</button>
                </form>
            <?php else: ?>
                <div class="status-msg">🔒 KİLİTLİ</div>
            <?php endif; ?>
        </div>

        <div class="card <?= (isset($ayarlar['etkinlik3']) && $ayarlar['etkinlik3']['durum'] == 1) ? '' : 'locked' ?>">
            <h3>🔐 Etkinlik 3</h3>
            <p>Hızlı Kriptocu Yarışı</p>
            <?php if(isset($ayarlar['etkinlik3']) && $ayarlar['etkinlik3']['durum'] == 1): ?>
                <form action="etkinlikler/etkinlik3.php" method="POST">
                    <input type="password" name="e_sifre" placeholder="Şifreyi Gir" required>
                    <button type="submit" class="btn">BAŞLA</button>
                </form>
            <?php else: ?>
                <div class="status-msg">🔒 KİLİTLİ</div>
            <?php endif; ?>
        </div>

    </div>

    <div style="margin-top: 50px;">
        <a href="logout.php" style="color: #666; text-decoration: none;">Güvenli Çıkış</a>
    </div>
</body>
</html>