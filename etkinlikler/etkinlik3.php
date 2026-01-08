<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['ogrenci_id'])) { header("Location: ../index.php"); exit(); }

$sorgu = $pdo->prepare("SELECT giris_sifresi FROM sistem_ayarlari WHERE etkinlik_adi = 'etkinlik3'");
$sorgu->execute();
$dogru_sifre = $sorgu->fetchColumn();

if (!isset($_POST['e_sifre']) || $_POST['e_sifre'] !== $dogru_sifre) {
    header("Location: ../anasayfa.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hızlı Kriptocu Yarışı</title>
    <style>
        body { background: #121212; color: #0f0; font-family: 'Courier New', monospace; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .crypto-box { background: #1e1e1e; padding: 40px; border-radius: 15px; border: 2px solid #0f0; box-shadow: 0 0 20px rgba(0,255,0,0.2); width: 80%; max-width: 800px; text-align: center; }
        #timer { font-size: 32px; color: #ff9800; margin-bottom: 20px; font-weight: bold; }
        .key-table { margin: 20px auto; border-collapse: collapse; background: #222; }
        .key-table td { border: 1px solid #0f0; padding: 10px 15px; font-size: 18px; }
        .cipher-text { font-size: 36px; letter-spacing: 5px; margin: 30px 0; color: #fff; background: #333; padding: 20px; border-radius: 8px; }
        input { background: #000; border: 1px solid #0f0; color: #0f0; padding: 15px; font-size: 24px; text-transform: uppercase; width: 80%; text-align: center; outline: none; }
        button { margin-top: 20px; padding: 15px 40px; font-size: 20px; background: #0f0; color: #000; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background: #0c0; }
        .progress-dots { margin-bottom: 10px; font-size: 18px; }
    </style>
</head>
<body>
    <div class="crypto-box">
        <div class="progress-dots">Aşama: <span id="stage">1</span> / 3</div>
        <div id="timer">Süre: 0.00 sn</div>
        
        <h2 id="instruction">MESAJI ÇÖZ(TAMAMI BÜYÜK HARFLE YAZILMALI):</h2>
        
        <table class="key-table">
            <tr>
                <td>A</td><td>B</td><td>C</td><td>D</td><td>E</td><td>G</td><td>I/İ</td><td>K</td>
                <td>L</td><td>N</td><td>R</td><td>S</td><td>T</td><td>Ü</td><td>V</td><td>Z</td>
            </tr>
            <tr>
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td>
                <td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td>
            </tr>
        </table>

        <div class="cipher-text" id="cipher-display">Yükleniyor...</div>
        
        <input type="text" id="cevap" placeholder="MESAJI BURAYA YAZ (CAPS LOCK AÇMAYI UNUTMA)" autofocus autocomplete="off">
        <br>
        <button onclick="kontrolEt()">ONAYLA</button>
    </div>

    <script>
        const sorular = [
            { cipher: "6 - 14 - 15 - 5 - 10 - 9 - 7 - 8", answer: "GÜVENLİK" },
            { cipher: "13 - 5 - 4 - 2 - 7 - 11", answer: "TEDBİR" },
            { cipher: "12 - 7 - 2 - 5 - 11 - 16 - 5 - 8", answer: "SİBERZEK" }
        ];

        let currentStage = 0;
        let startTime = Date.now();

        // Sayacı her 50 milisaniyede bir güncelle
        const timerInterval = setInterval(() => {
            let elapsed = (Date.now() - startTime) / 1000;
            document.getElementById('timer').innerText = "Süre: " + elapsed.toFixed(2) + " sn";
        }, 50);

        function soruYukle() {
            if(currentStage < sorular.length) {
                document.getElementById('stage').innerText = currentStage + 1;
                document.getElementById('cipher-display').innerText = sorular[currentStage].cipher;
                document.getElementById('cevap').value = "";
                document.getElementById('cevap').focus();
            }
        }

        function kontrolEt() {
    // replace ile İ harfini I'ya çevirerek karşılaştırma riskini azaltıyoruz
            let userAnsw = document.getElementById('cevap').value.trim().toUpperCase().replace(/İ/g, "İ");
            
            if(userAnsw === sorular[currentStage].answer) {
                currentStage++;
                if(currentStage < sorular.length) {
                    soruYukle();
                } else {
                    bitir();
                }
            } else {
                alert("Hatalı kod! Lütfen tabloya göre tekrar kontrol et.");
            }
        }

        function bitir() {
            clearInterval(timerInterval);
            let finalTime = (Date.now() - startTime) / 1000;
            
            const data = new URLSearchParams();
            data.append('etkinlik', '3');
            data.append('sure', finalTime);

            fetch('skor_kaydet.php', { method: 'POST', body: data })
            .then(() => {
                alert("TEBRİKLER! 3 şifreyi de toplam " + finalTime.toFixed(2) + " saniyede çözdün.");
                window.location.href = '../anasayfa.php';
            });
        }

        // Enter tuşuna basınca onaylamasını sağla
        document.getElementById('cevap').addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                kontrolEt();
            }
        });

        soruYukle();
    </script>
</body>
</html>