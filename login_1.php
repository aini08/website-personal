<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPPD Kota Pariaman</title>
    <script defer>
        document.addEventListener("DOMContentLoaded", function () {
            const userIcon = document.querySelector(".user-icon");
            const dropdown = document.querySelector(".dropdown");

            // Tampilkan atau sembunyikan dropdown saat ikon diklik
            userIcon.addEventListener("click", function (event) {
                event.preventDefault();
                dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
            });

            // Tutup dropdown saat klik di luar elemen user-menu
            document.addEventListener("click", function (event) {
                if (!event.target.closest(".user-menu")) {
                    dropdown.style.display = "none";
                }
            });
        });
    </script>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #e3f2fd; /* Background yang lebih terang untuk keseluruhan halaman */
    }

    .header {
        background: linear-gradient(to right, #1a237e, #8c9eff); /* Gradasi biru dongker ke biru muda */
        padding: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        margin-bottom: 20px;
    }

    .header .nav {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-left: auto;
    }

    .header a {
        color: white;
        text-decoration: none;
        font-size: 20px;
    }

    .user-menu {
        position: relative;
        margin-left: auto;
    }

    .header .user-icon {
        width: 30px;
        height: 30px;
        cursor: pointer;
    }

    .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 40px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 4px;
        z-index: 1000;
        text-align: left;
        padding: 5px;
        white-space: nowrap;
    }

    .dropdown a {
        display: block;
        padding: 10px 20px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
        transition: background-color 0.3s;
    }

    .dropdown a:hover {
        background-color: #f0f0f0;
    }

    .title-container {
        display: flex;
        align-items: left;
        justify-content: left;
        gap: 50px;
        margin-top: 20px;
        margin-left: 50px;
    }

    .title-container img {
        width: 100px;
        height: 100px;
    }

    .title {
        font-size: 40px;
        color: black; 
        text-align: left;
        font-weight: bold;
    }

    .title p {
        margin: 5px 0;
    }

    .user-icon {
        width: 30px;
        height: 30px; 
        border-radius: 50%; 
        object-fit: cover; 
    }

    .content {
        text-align: center;
        position: relative;
    }

    .content img {
        width: 100%; 
        height: 600px;
        margin-top: 20px;
        border-top-right-radius: 250px; 
        object-position: center; 
    }

    button {
        margin-top: 30px;
        font-size: 18px;
        padding: 10px 20px;
        background-color: #0d47a1;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #1a237e;
    }

    .information-section {
        margin: 20px;
    }

    .info-box {
        display: flex;
        justify-content: center;
        gap: 30px;
    }

    .info-box .box {
        background-color: white;
        padding: 10px;
        width: 25%;
        height: 200px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        position: relative;
    }

    .more-link {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        text-decoration: none;
        color: #0d47a1;
        font-size: 14px;
    }

    .more-link:hover {
        color: #1a237e;
        text-decoration: underline;
    }

    .footer {
        background-color: #1a237e;
        color: white;
        padding: 20px;
        text-align: left;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .footer .links, .footer .details, .footer .footer-text {
        margin-bottom: 20px;
    }

    .footer .image {
    display: flex;
    justify-content: flex-end; /* Logo media sosial di kanan atas */
    gap: 15px; /* Jarak antar-logo */
    margin-bottom: 20px; /* Memberikan sedikit ruang ke bawah */
}

.footer .image img {
    width: 30px; /* Lebar logo */
    height: 30px; /* Tinggi logo */
    border-radius: 50%; /* Opsional: Membuat logo berbentuk bulat */
    object-fit: cover; /* Menjaga proporsi logo */
    cursor: pointer; /* Memberikan efek kursor pada logo */
    transition: transform 0.2s; /* Efek animasi saat logo diklik */
}

.footer .image img:hover {
    transform: scale(1.2); /* Efek memperbesar saat logo disorot */
}
    .footer .links ul, .footer .details ul, .footer .footer-text ul {
        list-style: none;
        padding: 0;
    }

    .footer .links ul li, .footer .details ul li {
        margin-bottom: 5px;
        
    }

    .footer .separator {
        border-top: 1px solid white;
        width: 100%;
        margin: 10px 0;
    }

    .footer .copyright {
        text-align: center;
        width: 100%;
        margin-top: 5px;
    }
    
    .links ul li a {
        text-decoration: none;
        color: white;
    }
</style>


</head>
<body>
    <div class="header">
        <div class="nav">
            <a href="#">Beranda</a>
            <div class="user-menu">
                <img class="user-icon" src="image/9131529.png" alt="User Icon">
                <div class="dropdown">
                    <a href="OPD/login_OPD.php">Login OPD</a>
                    <a href="PEM/login_PEM.php">Login PEM</a>
                </div>
            </div>
        </div>
    </div>

    <div class="title-container">
        <img src="image/logo kota pariaman sumbar.png" alt="Logo Kota Pariaman">
        <div class="title">
            <p>LPPD</p>
            <p>Kota Pariaman</p>
        </div>
    </div>

    <div class="content">
        <img src="image/Balaikota_Pariaman.png" alt="Gedung Kota Pariaman">
    </div>


    <div class="footer">
    <p>Link Sosmed:</p>
    <div class="image">
        <a href="https://www.instagram.com" target="_blank">
            <img src="image/Instagram_logo_2022.png" alt="Logo Instagram">
        </a>
        <a href="https://www.facebook.com" target="_blank">
            <img src="image/facebook logo.png" alt="Logo Facebook">
        </a>
    </div>
    <div class="separator"></div>
        <div class="links">
            <ul>
                <li><a href="login_1.php">Beranda</a></li>
            </ul>
        </div>

        <div class="details">
            <p>Jam Operasional:</p>
            <ul>
                <li>Senin - Kamis  : 7:30 - 16:00</li>
                <li>Jumat          : 7:30 - 16:30</li>
                <li>Sabtu - Minggu : Tutup</li>
            </ul>
        </div>

        <div class="footer-text">
            <p><h1>LPPD Kota Pariaman</h1></p>
            <p>Laporan Penyelenggaraan Pemerintahan Daerah</p>
        </div>

        <div class="separator"></div>
        <p class="copyright">&copy; 2025 Pemerintah Kota Pariaman</p>
    </div>
</body>
</html>