<?php
include '../db_config/db_config.php';

// Check if success query parameter is set (if redirected after form submission)
$successMessage = isset($_GET['success']) && $_GET['success'] === 'true';

// Query 1: Ambil data kategori
$sql_kategori = "SELECT id_kategori, nama_kategori_ikk FROM kategori_ikk";
$result_kategori = $conn->query($sql_kategori);

// Pastikan query berhasil
if (!$result_kategori) {
    die("Error Query Kategori: " . $conn->error);
}

// Query 2: Ambil data indikator
$sql_indikator = "SELECT nama_indikator FROM indikator";
$result_indikator = $conn->query($sql_indikator);

// Pastikan query berhasil
if (!$result_indikator) {
    die("Error Query Indikator: " . $conn->error);
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPPD Kota Pariaman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            background: linear-gradient(to right, #1a237e, #8c9eff);
            padding: 30px;
            display: flex;
            align-items: center;
            color: white;
        }

        .nav {
            display: flex;
            gap: 20px;
            margin-left: auto; /* Geser menu ke kanan */
        }

        .header a {
            color: white;
            text-decoration: none;
            font-size: 20px;
        }

        .user-menu {
            position: relative;
        }

        .user-icon {
            width: 30px;
            height: 30px; 
            border-radius: 50%; 
            cursor: pointer;
            margin-left: 15px; /* Jarak antara teks dan ikon */
        }

        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 45px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            z-index: 1000;
            text-align: left;
            padding: 5px 0;
        }

        .dropdown a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }

        .container {
            margin: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        select, input, textarea {
            width: 98%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor:.rumus-container {
            display: inline-block;
            text-align: center;
            font-size: 20px;
        }}
        .fraction {
            display: inline-block;
            text-align: center;
        }
        .fraction input {
            width: 80px;
            text-align: center;
            font-size: 18px;
        }
        .line {
            border-bottom: 2px solid black;
            margin: 5px 0;
        }
        .hasil {
            margin-top: 20px;
            font-size: 24px;
            font-weight: bold; pointer;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        footer {
            background: linear-gradient(to right, #0000ff, #ffffff);
            color: white;
            text-align: center;
            padding: 15px;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            font-size: 14px;

        }

        footer p {
            margin: 5px 0;
        }
        
        .fraction1 {
            display: inline-block;
            text-align: center;
        }
        .fraction1 span {
            display: block;
        }
        .fraction1 .denominator {
            border-top: 2px solid black;
            padding-top: 2px;
        }
        .multiply {
            font-size: 20px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav">
            <a href="../halaman_PEM.php">Beranda</a>
            <a href="../PEM/pelaporan_IKK_PEM.php">Pelaporan IKK</a>
            <a href="../PEM/kategori_ikk.php">Kategori IKK</a>
            <a href="../PEM/indikator.php">Indikator</a>
            <a href="../PEM/Verifikasi_IKK.php">Verifikasi IKK</a>
            <a href="../PEM/tambah_user.php">Tambah User</a>
        </div>
        <div class="user-menu">
            <img src="../image/9131529.png" alt="User Icon" title="User Menu" class="user-icon" id="user-icon">
            <div class="dropdown" id="dropdown-menu">
                <a href="../login_1.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <form id="ikk-form" method="POST" action="simpan.php">
            <div class="form-group">
                <label for="No IKK">No IKK</label>
                <input type="text" id="No-IKK" name="no_ikk" placeholder="Masukkan No IKK">
            </div>

           <!-- untuk menampilkan dropdown bagi si kategori -->
    <div class="form-group">
        <label>Pilih Kategori:</label>
        <select name="kategori">
            <option value="">-- Pilih Kategori --</option>
            <?php while ($row = $result_kategori->fetch_assoc()) { ?>
                <option value="<?= $row['nama_kategori_ikk']; ?>"><?= $row['nama_kategori_ikk']; ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- untuk menampilkan dropdown bagi si indikator -->

    <div class="form-group">
        <label>Pilih Indikator:</label>
        <select name="indikator">
            <option value="">-- Pilih Indikator --</option>
            <?php while ($row = $result_indikator->fetch_assoc()) { ?>
                <option value="<?= $row['nama_indikator']; ?>"><?= $row['nama_indikator']; ?></option>
            <?php } ?>
        </select>
    </div>


            <div class="form-group">
                <label for="ikk-output">IKK Output</label>
                <textarea id="ikk-output" name="ikk_output" placeholder="Masukkan IKK Output" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="ikk-outcome">IKK Outcome</label>
                <input type="text" id="ikk-outcome" name="ikk_outcome" placeholder="Masukkan IKK Outcome">
            </div>

            <div class="form-group">
                <label for="pembilang">Pembilang</label>
                <input type="text" id="pembilang" name="pembilang" placeholder="Masukkan pembilang">
            </div>

            <div class="form-group">
                <label for="penyebut">Penyebut</label>
                <input type="text" id="penyebut" name="penyebut" placeholder="Masukkan penyebut">
            </div>

            <div class="form-group" style="text-align: right;">
                <button type="reset" id="hapus" class="btn-danger">Hapus</button>
                <!-- <button onclick="generateFormula()" class="btn-success">Simpan</button> -->
                <button type="submit" id="simpan" class="btn-success">Simpan</button>
            </div>
        </form>
        <div id="formula-output">
        <!-- Hasil rumus akan muncul di sini -->
        </div>
    </div>

    <footer>
        <p>&copy; 2025 LPPD Kota Pariaman</p>
    </footer>



<script>

document.addEventListener('DOMContentLoaded', function () {
    const userIcon = document.getElementById('user-icon');
    const dropdownMenu = document.getElementById('dropdown-menu');

    // Toggle dropdown saat ikon diklik
    userIcon.addEventListener('click', function (event) {
        event.stopPropagation(); 
        dropdownMenu.style.display = (dropdownMenu.style.display === 'block') ? 'none' : 'block';
    });

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function () {
        if (dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        }
    });

    dropdownMenu.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    //Function untuk hanya angka saja yang dapat menginput di bagian penyebut dan pembilang
    // function hanyaAngka(evt) {
    //     // Izinkan tombol kontrol (backspace, delete, tab, panah kiri/kanan)
    //     if (evt.key === "Backspace" || evt.key === "Delete" || evt.key === "Tab" || evt.key === "ArrowLeft" || evt.key === "ArrowRight") {
    //         return true;
    //     }
    //     // Izinkan hanya angka (0-9)
    //     if (evt.key < "0" || evt.key > "9") {
    //         return false;
    //     }
    //     return true;
    // }

    // Function to create custom popup style
    function createPopup(message) {
        const popupMessage = document.createElement('div');
        popupMessage.style.position = 'fixed';
        popupMessage.style.top = '50%';
        popupMessage.style.left = '50%';
        popupMessage.style.transform = 'translate(-50%, -50%)';
        popupMessage.style.background = 'rgba(255, 0, 0, 0.9)';
        popupMessage.style.color = 'white';
        popupMessage.style.padding = '20px';
        popupMessage.style.borderRadius = '5px';
        popupMessage.style.border = '2px solid white';
        popupMessage.style.fontSize = '18px';
        popupMessage.style.textAlign = 'center';
        popupMessage.style.zIndex = '1000';
        popupMessage.innerText = message;

        document.body.appendChild(popupMessage);

        setTimeout(function () {
            popupMessage.remove();
        }, 3000);
    }

    // Form submission check for incomplete data
    document.getElementById("ikk-form").addEventListener("submit", function (event) {
        const noIKK = document.getElementById("No-IKK").value.trim();
        const kategori = document.querySelector("select[name='kategori']").value.trim();
        const indikator = document.querySelector("select[name='indikator']").value.trim();
        const ikkOutput = document.getElementById("ikk-output").value.trim();
        const ikkOutcome = document.getElementById("ikk-outcome").value.trim();
        const pembilang = document.getElementById("pembilang").value.trim();
        const penyebut = document.getElementById("penyebut").value.trim();
        

        if (!noIKK || !kategori || !indikator || !ikkOutput || !ikkOutcome || !penyebut || !pembilang) {
            event.preventDefault();
            createPopup("Data Belum Lengkap! Harap lengkapi semua field.");
        }
    });
});

</script>

    <?php if (!empty($successMessage)): ?>
        <script>alert('Data berhasil disimpan!');</script>
    <?php endif; ?>
</body>
</html>