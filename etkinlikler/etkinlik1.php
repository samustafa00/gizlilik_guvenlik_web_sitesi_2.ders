<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['ogrenci_id'])) { header("Location: ../index.php"); exit(); }

$sorgu = $pdo->prepare("SELECT giris_sifresi FROM sistem_ayarlari WHERE etkinlik_adi = 'etkinlik1'");
$sorgu->execute();
$dogru_sifre = $sorgu->fetchColumn();

// Form gönderilmediyse veya şifre yanlışsa anasayfaya at
if (!isset($_POST['e_sifre']) || $_POST['e_sifre'] !== $dogru_sifre) {
    header("Location: ../anasayfa.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Etkinlik 1 - Gizlilik Matrisi</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; text-align: center; }
        .drag-container { display: flex; justify-content: space-around; margin-top: 30px; gap: 10px; }
        .drop-zone { background: #fff; border: 2px dashed #ccc; padding: 15px; width: 30%; min-height: 300px; border-radius: 10px; transition: background 0.3s; }
        .item { background: #1877f2; color: white; padding: 10px; margin: 8px; cursor: move; border-radius: 5px; display: inline-block; font-size: 14px; user-select: none; }
        .drop-zone h3 { border-bottom: 2px solid #eee; padding-bottom: 10px; font-size: 16px; }
        #submit-btn { margin-top: 30px; padding: 15px 40px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 18px; font-weight: bold; }
        #submit-btn:hover { background: #218838; }
        #items-pool { background: #ddd; padding: 20px; margin-bottom: 20px; border-radius: 10px; min-height: 100px; }
    </style>
</head>
<body>
    <h2>Etkinlik 1: Bilgilerimi Kiminle Paylaşmalıyım?</h2>
    <p>Kartları uygun kutulara sürükle. Tüm kartlar bittiğinde "Etkinliği Bitir" butonuna tıkla.</p>

    <div id="items-pool" ondrop="drop(event)" ondragover="allowDrop(event)">
        <div class="item" draggable="true" id="i1" data-correct="gizli">T.C. Kimlik Numaram</div>
        <div class="item" draggable="true" id="i2" data-correct="tanidik">Ev Adresim</div>
        <div class="item" draggable="true" id="i3" data-correct="herkes">En Sevdiğim Renk</div>
        <div class="item" draggable="true" id="i4" data-correct="gizli">E-posta Şifrem</div>
        <div class="item" draggable="true" id="i5" data-correct="tanidik">Telefon Numaram</div>
        <div class="item" draggable="true" id="i6" data-correct="gizli">Banka/Kart Şifreleri</div>
        <div class="item" draggable="true" id="i7" data-correct="tanidik">Okul numaram</div>
        <div class="item" draggable="true" id="i8" data-correct="herkes">En sevdiğim müzik türü</div>
        <div class="item" draggable="true" id="i9" data-correct="gizli">Özel Günlük Yazılarım</div>
        <div class="item" draggable="true" id="i10" data-correct="tanidik">Tatil planlarımız</div>
        <div class="item" draggable="true" id="i11" data-correct="herkes">Tuttuğum Takım</div>
        <div class="item" draggable="true" id="i12" data-correct="tanidik">Ebeveyn İş Adresi</div>
        <div class="item" draggable="true" id="i13" data-correct="gizli">Konum Bilgim (Anlık)</div>
        <div class="item" draggable="true" id="i14" data-correct="tanidik">Gittiğim Kurslar</div>
        <div class="item" draggable="true" id="i15" data-correct="herkes">Hobilerim</div>
    </div>

    <div class="drag-container">
        <div class="drop-zone" id="gizli" ondrop="drop(event)" ondragover="allowDrop(event)">
            <h3>🔴 Tamamen Gizli</h3>
        </div>
        <div class="drop-zone" id="tanidik" ondrop="drop(event)" ondragover="allowDrop(event)">
            <h3>🟡 Sadece Tanıdıklar</h3>
        </div>
        <div class="drop-zone" id="herkes" ondrop="drop(event)" ondragover="allowDrop(event)">
            <h3>🟢 Herkese Açık</h3>
        </div>
    </div>

    <button id="submit-btn" onclick="puanHesapla()">Etkinliği Bitir</button>

    <script>
        function allowDrop(ev) { ev.preventDefault(); }
        
        function drag(ev) { ev.dataTransfer.setData("text", ev.target.id); }

        // Kartlara sürükleme özelliğini bağla
        document.querySelectorAll('.item').forEach(item => {
            item.addEventListener('dragstart', drag);
        });

        function drop(ev) {
            ev.preventDefault();
            var data = ev.dataTransfer.getData("text");
            var target = ev.target;
            
            // Eğer bırakılan yer drop-zone değilse en yakın drop-zone'u bul
            while(target && !target.classList.contains('drop-zone') && target.id !== 'items-pool') {
                target = target.parentElement;
            }
            
            if (target) {
                target.appendChild(document.getElementById(data));
            }
        }

        function puanHesapla() {
            let dogruSayisi = 0;
            const items = document.querySelectorAll('.item');
            const toplamItem = items.length;

            items.forEach(item => {
                // Kartın şu an içinde bulunduğu kutunun id'si ile doğru cevabı karşılaştır
                if(item.parentElement.id === item.getAttribute('data-correct')) {
                    dogruSayisi++;
                }
            });
            
            // 100 üzerinden puan hesapla
            let finalPuan = Math.round((dogruSayisi / toplamItem) * 200);
            
            // Veritabanına gönder
            const formData = new URLSearchParams();
            formData.append('etkinlik', '1');
            formData.append('puan', finalPuan);

            fetch('skor_kaydet.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: formData.toString()
            })
            .then(response => {
                alert('Etkinlik Tamamlandı!\nDoğru: ' + dogruSayisi + '/' + toplamItem + '\nPuan: ' + finalPuan);
                window.location.href = '../anasayfa.php';
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('Puan kaydedilirken bir hata oluştu.');
            });
        }
    </script>
</body>
</html>