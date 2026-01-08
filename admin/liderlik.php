<?php
include '../config/db.php';

// Toplam puanı hesapla ve sırala
// Formül: Etkinlik 1 + Etkinlik 2 + Etkinlik 3 Puanı
$sql = "SELECT *, (puan_etkinlik1 + puan_etkinlik2 + puan_etkinlik3_toplam) as toplam 
        FROM ogrenciler 
        ORDER BY toplam DESC";
$ogrenciler = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sınıf Liderlik Tablosu</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #2c3e50; color: white; text-align: center; }
        .leaderboard { width: 80%; margin: 50px auto; background: #34495e; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; border-bottom: 1px solid #2c3e50; }
        th { background: #1abc9c; color: white; text-transform: uppercase; }
        tr:nth-child(1) { background: #f1c40f; color: #000; font-weight: bold; font-size: 1.2em; } /* Altın rengi - 1. olan */
        tr:nth-child(2) { background: #bdc3c7; color: #000; } /* Gümüş - 2. olan */
        tr:nth-child(3) { background: #e67e22; color: #000; } /* Bronz - 3. olan */
        .badge { font-size: 24px; }
    </style>
</head>
<body>
    <h1>🏆 Sınıf Siber Güvenlik Liderleri 🏆</h1>
    <div class="leaderboard">
        <table>
            <thead>
                <tr>
                    <th>Sıra</th>
                    <th>Öğrenci Adı</th>
                    <th>Etk-1</th>
                    <th>Etk-2</th>
                    <th>Etk-3 (Hız)</th>
                    <th>Toplam Puan</th>
                </tr>
            </thead>
            <tbody>
                <?php $sira = 1; foreach($ogrenciler as $o): ?>
                <tr>
                    <td>
                        <?php 
                        if($sira == 1) echo '<span class="badge">🥇</span>';
                        elseif($sira == 2) echo '<span class="badge">🥈</span>';
                        elseif($sira == 3) echo '<span class="badge">🥉</span>';
                        else echo $sira;
                        ?>
                    </td>
                    <td><?= htmlspecialchars($o['ad_soyad']) ?></td>
                    <td><?= $o['puan_etkinlik1'] ?></td>
                    <td><?= $o['puan_etkinlik2'] ?></td>
                    <td><?= $o['puan_etkinlik3_toplam'] ?> (<?= $o['puan_etkinlik3_sure'] ?>s)</td>
                    <td><strong><?= $o['toplam'] ?></strong></td>
                </tr>
                <?php $sira++; endforeach; ?>
            </tbody>
        </table>
    </div>
    <p><a href="index.php" style="color: #bdc3c7;">Geri Dön</a></p>
</body>
</html>