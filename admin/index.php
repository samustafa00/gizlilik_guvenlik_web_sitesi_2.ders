<?php
include '../config/db.php';

// Tüm öğrencileri ve puanlarını getir
$sorgu = $pdo->query("SELECT * FROM ogrenciler ORDER BY id DESC");
$ogrenciler = $sorgu->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5"> 
    <title>Siber Güvenlik | Admin Paneli</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
        h1 { border-bottom: 2px solid #00ff00; padding-bottom: 10px; }
        
        /* Navigasyon Paneli */
        .admin-nav { 
            background: #333; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px; 
            display: flex; 
            gap: 15px; 
        }
        .nav-btn { 
            background: #00ff00; 
            color: #000; 
            text-decoration: none; 
            padding: 10px 20px; 
            font-weight: bold; 
            border-radius: 3px; 
            font-family: sans-serif;
        }
        .nav-btn:hover { background: #00cc00; }
        .nav-btn.secondary { background: #f1c40f; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 12px; text-align: left; }
        th { background: #333; color: #fff; }
        .puan-badge { background: #004400; padding: 2px 8px; border-radius: 4px; }
        .alert { color: #ff0000; font-weight: bold; animation: blink 1s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0; } 100% { opacity: 1; } }
    </style>
</head>
<body>

    <div class="admin-nav">
        <a href="control.php" class="nav-btn">🚀 ETKİNLİK KONTROLÜNE GİT</a>
        <a href="liderlik.php" class="nav-btn secondary">🏆 LİDERLİK TABLOSU</a>
        <a href="verileri_sil.php" class="nav-btn" 
        style="background: #dc3545; color: white; margin-left: auto;" 
        onclick="return confirm('DİKKAT! Tüm öğrenci verileri ve puanları silinecek. Yeni sınıfa geçmeye hazır mısın?')">
        🗑️ TÜM VERİLERİ SIFIRLA
        </a>
    </div>

    <?php if(isset($_GET['mesaj']) && $_GET['mesaj'] == 'temizlendi'): ?>
        <div style="background: #28a745; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-family: sans-serif;">
            ✅ Tüm veriler temizlendi ve etkinlik kilitleri kapatıldı. Yeni sınıf için hazır!
        </div>
    <?php endif; ?>

    <h1>CANLI VERİ AKIŞI (SİBER GÜVENLİK ANALİZİ)</h1>
    <p>Sisteme bağlı öğrenci sayısı: <strong><?= count($ogrenciler) ?></strong></p>

    <table>
        <thead>
            <tr>
                <th>Öğrenci</th>
                <th>E-Posta</th>
                <th>Şifre (Maskelenmiş)</th>
                <th>E1 Puan</th>
                <th>E2 Puan</th>
                <th>E3 Puan</th>
                <th>Tarih</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ogrenciler as $ogrenci): ?>
            <tr>
                <td><?= htmlspecialchars($ogrenci['ad_soyad']) ?></td>
                <td><?= htmlspecialchars($ogrenci['email']) ?></td>
                <td>
                    <code>
                    <?php 
                        $s = $ogrenci['sifre'];
                        if(strlen($s) > 2) {
                            echo htmlspecialchars(substr($s, 0, 1)) . str_repeat("*", strlen($s)-1); 
                        } else {
                            echo "**";
                        }
                    ?>
                    </code>
                </td>
                <td><span class="puan-badge"><?= $ogrenci['puan_etkinlik1'] ?></span></td>
                <td><span class="puan-badge"><?= $ogrenci['puan_etkinlik2'] ?></span></td>
                <td><span class="puan-badge"><?= $ogrenci['puan_etkinlik3_toplam'] ?></span></td>
                <td style="font-size: 11px;"><?= $ogrenci['kayit_tarihi'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; background: #000; padding: 15px; border: 1px dashed red;">
        <span class="alert">KRİTİK UYARI:</span> Bir web sitesine şifrenizi girdiğiniz an, veriler yukarıdaki gibi yönetici paneline düşer. Hiçbir zaman güvenmediğiniz sitelere şifre girmeyin!
    </div>

</body>
</html>