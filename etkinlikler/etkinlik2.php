<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['ogrenci_id'])) { header("Location: ../index.php"); exit(); }

$sorgu = $pdo->prepare("SELECT giris_sifresi FROM sistem_ayarlari WHERE etkinlik_adi = 'etkinlik2'");
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
    <title>Oltalama Dedektifi</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; padding: 20px; }
        .quiz-container { max-width: 800px; margin: 40px auto; text-align: center; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .phishing-img { width: 100%; max-height: 350px; object-fit: contain; border: 2px solid #ddd; margin-bottom: 20px; border-radius: 8px; background: #fafafa; }
        .choice-btn { padding: 18px 35px; font-size: 20px; cursor: pointer; border: none; border-radius: 8px; margin: 10px; font-weight: bold; transition: all 0.2s; }
        .choice-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }
        .safe { background: #28a745; color: white; }
        .danger { background: #dc3545; color: white; }
        .progress { margin-bottom: 20px; color: #555; font-size: 1.1em; }
        .progress-bar { height: 10px; background: #eee; border-radius: 5px; margin-top: 5px; overflow: hidden; }
        #fill { height: 100%; background: #1877f2; width: 0%; transition: width 0.3s; }
    </style>
</head>
<body>
    <div class="quiz-container">
        <div class="progress">
            Soru: <span id="current-question-num">1</span> / <span id="total-questions-num">10</span>
            <div class="progress-bar"><div id="fill"></div></div>
        </div>
        
        <div id="quiz-content">
            <h2 id="question-text">Analiz Ediliyor...</h2>
            <img src="" id="question-img" class="phishing-img" alt="Soru Görseli">
            <p id="question-desc" style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-style: italic;"></p>
            
            <div style="margin-top: 25px;">
                <button class="choice-btn safe" onclick="cevapVer(false)">GÜVENLİ ✅</button>
                <button class="choice-btn danger" onclick="cevapVer(true)">TUZAK / ŞÜPHELİ ❌</button>
            </div>
        </div>
    </div>

    <script>
        const sorular = [
            { text: "instagram: 'Giriş denemesi engellendi' adlı bir mesaj gönderdi alttaki mailde sence güvelim mi? tuzak mı?" , img: "../assets/img/o1.png", desc: "Gönderen: security@insta-support.net", isPhishing: true, feedback: "Hatalı! Resmi Instagram adresi @mail.instagram.com ile biter. .net uzantısı şüphelidir." },
            { text: "EBA Giriş Ekranı", img: "../assets/img/o2.jpg", desc: "URL: https://giris.eba.gov.tr/", isPhishing: false, feedback: "link doğruydu" },
            { text: "Hediye Çeki Kazandınız!", img: "../assets/img/o3.jpg", desc: "WhatsApp Mesajı: 'Büyük marketten 1000 TL çek! Tıkla: hediye-al.me'", isPhishing: true, feedback: "Hatalı! Marketler WhatsApp üzerinden bağlantı ile çek dağıtmaz." },
            { text: "e-Devlet Şifre Hatırlatma", img: "../assets/img/o4.jpg", desc: "URL: https://giris.turkiye.gov.tr/", isPhishing: false, feedback: "link doğruydu" },
            { text: "Oyun İçi Bedava Elmas", img: "../assets/img/o5.jpg", desc: "Mesaj: 'Kullanıcı adını ve şifreni yaz, hesabına 5000 elmas yüklensin!'", isPhishing: true, feedback: "Hatalı! Hiçbir oyun yetkilisi sizden şifrenizi istemez." },
            { text: "Banka: 'Kredi Kartı Puanı'", img: "../assets/img/o6.webp", desc: "Gönderen: puan-merkezi@banka-onay.com", isPhishing: true, feedback: "Hatalı! Bankalar puan iadesi için web sitesi linki üzerinden kart bilgisi istemez." },
            { text: "Okul Duyurusu", img: "../assets/img/o7.jpg", desc: "Gönderen: okul_idaresi@meb.k12.tr", isPhishing: false, feedback: "" },
            { text: "Kargo Takip Hatası", img: "../assets/img/o8.jpg", desc: "Mesaj: 'Kargonuz teslim edilemedi. 7,5 TL ödeyip bilgileri güncelleyin.'", isPhishing: true, feedback: "Hatalı! Kargo şirketleri küçük ödemeler için SMS ile link göndermez." },
            { text: "Sistem Hatası: 'Virüs Bulundu'", img: "../assets/img/o9.jpg", desc: "Tarayıcı Penceresi: 'Windows'unuzda 5 virüs var! Hemen Tara!'", isPhishing: true, feedback: "Hatalı! Tarayıcılar bilgisayarın içinde virüs taraması yapamaz." },
            { text: "Netflix: 'Ödeme Sorunu'", img: "../assets/img/o10.jpg", desc: "Gönderen: info@neflix-odeme.com (T harfi eksik)", isPhishing: true, feedback: "Hatalı! 'Neflix' yazım hatasına ve sahte alan adına dikkat etmeliydin." }
        ];

        let mevcutSoruIndex = 0;
        let dogruSayisi = 0;

        function soruyuGoster() {
            const soru = sorular[mevcutSoruIndex];
            document.getElementById('current-question-num').innerText = mevcutSoruIndex + 1;
            document.getElementById('total-questions-num').innerText = sorular.length;
            document.getElementById('question-text').innerText = soru.text;
            document.getElementById('question-img').src = soru.img;
            document.getElementById('question-desc').innerText = soru.desc;
            document.getElementById('fill').style.width = ((mevcutSoruIndex + 1) / sorular.length * 100) + "%";
        }

        function cevapVer(secim) {
            const soru = sorular[mevcutSoruIndex];
            
            if(secim === soru.isPhishing) {
                // Doğru cevapta sessizce devam et
                dogruSayisi++;
            } else {
                // Yanlış cevapta bilgilendirme uyarısı ver
                alert("DİKKAT! ⚠️\n" + soru.feedback);
            }

            mevcutSoruIndex++;

            if(mevcutSoruIndex < sorular.length) {
                soruyuGoster();
            } else {
                quizBitir();
            }
        }

        function quizBitir() {
            const finalPuan = Math.round((dogruSayisi / sorular.length) * 100);
            document.getElementById('quiz-content').innerHTML = `
                <h2 style="color: #1877f2;">Analiz Tamamlandı! 🕵️‍♂️</h2>
                <div style="font-size: 50px; margin: 20px 0;">🎯</div>
                <p style="font-size: 20px;">10 durumdan <b>${dogruSayisi}</b> tanesini doğru teşhis ettin.</p>
                <div style="font-size: 32px; background: #1877f2; color: white; display: inline-block; padding: 10px 30px; border-radius: 10px; margin: 15px 0;">Puan: ${finalPuan}</div>
                <p>Veriler işleniyor, anasayfaya dönülüyor...</p>
            `;
            
            puanKaydet(finalPuan);
        }

        function puanKaydet(puan) {
            const formData = new URLSearchParams();
            formData.append('etkinlik', '2');
            formData.append('puan', puan);

            fetch('skor_kaydet.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: formData.toString()
            }).then(() => {
                setTimeout(() => { window.location.href = '../anasayfa.php'; }, 4000);
            });
        }

        soruyuGoster();
    </script>
</body>
</html>