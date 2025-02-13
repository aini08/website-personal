<?php
session_start(); // Memulai sesi jika menggunakan sesi untuk menyimpan user_id

// config.php - Database Configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "db_lppd";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Pastikan user_id ada (misalnya dari sesi)
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("User tidak terautentikasi.");
}

// Jika ada ID di URL
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Query untuk mengambil data berdasarkan ID dari tabel pelaporan_pem
    $query = "SELECT * FROM pelaporan_pem WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $pembilang = $data['pembilang'] ?? '';
        $penyebut = $data['penyebut'] ?? '';
        $angka_pembilang = $data['angka_pembilang'] ?? 0;
        $angka_penyebut = $data['angka_penyebut'] ?? 0;
    } else {
        die("Data tidak ditemukan.");
    }
} else {
    die("ID tidak diberikan.");
}

// Proses ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function safe_input($input) {
        return is_array($input) ? '' : htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    $id = safe_input($_POST['id'] ?? '');
    $tgl_pengiriman = safe_input($_POST['tgl_pengiriman'] ?? '');
    $kategori_kendala_masalah = safe_input($_POST['kategori_kendala_masalah'] ?? '');
    $penjelasan_kendala_masalah = safe_input($_POST['penjelasan_kendala_masalah'] ?? '');
    $pembilang = safe_input($_POST['pembilang'] ?? '');
    $penyebut = safe_input($_POST['penyebut'] ?? '');
    $angka_pembilang = isset($_POST['angka_pembilang']) ? (int) $_POST['angka_pembilang'] : 0;
    $angka_penyebut = isset($_POST['angka_penyebut']) ? (int) $_POST['angka_penyebut'] : 0;
    $capaian = ($angka_penyebut != 0) ? ($angka_pembilang / $angka_penyebut) * 100 : 0;

    // Direktori penyimpanan file
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!is_writable($upload_dir)) {
        die("Folder 'uploads' tidak dapat ditulis. Periksa izin folder.");
    }

    // Proses upload file
    $unggah_dokumen = null;
    if (!empty($_FILES['unggah_dokumen']['name']) && $_FILES['unggah_dokumen']['error'] == 0) {
        $file_tmp = $_FILES['unggah_dokumen']['tmp_name'];
        $file_name = $_FILES['unggah_dokumen']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_path = $upload_dir . basename($file_name);

        if ($file_ext != 'pdf') {
            die("Hanya file PDF yang diperbolehkan.");
        } else {
            if (move_uploaded_file($file_tmp, $file_path)) {
                $unggah_dokumen = $file_path;
            } else {
                die("Gagal mengunggah dokumen.");
            }
        }
    }

    // Cek apakah ID sudah ada di tabel pelaporan_opd
    $checkQuery = "SELECT id FROM pelaporan_opd WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Jika ID sudah ada, lakukan UPDATE
        $updateQuery = "UPDATE pelaporan_opd 
                        SET no_ikk = ?, tgl_pengiriman = ?, kategori_ikk = ?, indikator = ?, ikk_output = ?, ikk_outcome = ?, 
                            kategori_kendala_masalah = ?, penjelasan_kendala_masalah = ?, penyebut = ?, pembilang = ?, 
                            angka_penyebut = ?, angka_pembilang = ?, capaian = ?, unggah_dokumen = ?, status_laporan = 'menunggu'
                        WHERE id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param(
            "ssssssssssiidsii",
            $data['no_ikk'],
            $tgl_pengiriman,
            $data['kategori_ikk'],
            $data['indikator'],
            $data['ikk_output'],
            $data['ikk_outcome'],
            $kategori_kendala_masalah,
            $penjelasan_kendala_masalah,
            $penyebut,
            $pembilang,
            $angka_penyebut,
            $angka_pembilang,
            $capaian,
            $unggah_dokumen,
            $id,
            $user_id
        );

        if ($updateStmt->execute()) {
            header("Location: ../OPD/pelaporan_IKK_OPD.php?success=update");
            exit;
        } else {
            die("Terjadi kesalahan saat memperbarui data.");
        }

        $updateStmt->close();
    } else {
        // Jika ID tidak ada, lakukan INSERT
        $insertQuery = "INSERT INTO pelaporan_opd 
                        (id, user_id, no_ikk, tgl_pengiriman, kategori_ikk, indikator, ikk_output, ikk_outcome, kategori_kendala_masalah, 
                         penjelasan_kendala_masalah, penyebut, pembilang, angka_penyebut, angka_pembilang, capaian, unggah_dokumen, status_laporan) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'menunggu')";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param(
            "iissssssssssiids",
            $id,
            $user_id,
            $data['no_ikk'],
            $tgl_pengiriman,
            $data['kategori_ikk'],
            $data['indikator'],
            $data['ikk_output'],
            $data['ikk_outcome'],
            $kategori_kendala_masalah,
            $penjelasan_kendala_masalah,
            $penyebut,
            $pembilang,
            $angka_penyebut,
            $angka_pembilang,
            $capaian,
            $unggah_dokumen
        );

        if ($insertStmt->execute()) {
            header("Location: ../OPD/pelaporan_IKK_OPD.php?success=insert");
            exit;
        } else {
            die("Terjadi kesalahan saat menyimpan data.");
        }

        $insertStmt->close();
    }

    $checkStmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data</title>

    <style>
    body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f9f9f9;
}

h1 {
    color: #333;
    text-align: center;
    margin-bottom: 20px;
}

form {
    max-width: 900px;
    margin: auto;
    background: white;
    padding: 30px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

input, textarea, input[type="file"] {
    width: 100%;
    margin-bottom: 15px;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

input[readonly], textarea[readonly] {
    background-color: #e9ecef;
    cursor: not-allowed;
    width: 100%;
}

button {
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    padding: 14px;
    border-radius: 5px;
    font-size: 16px;
    
}

button:hover {
    background-color: #45a049;
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

h4 {
    color: #333;
    margin-top: 20px;
    font-size: 20px;
}

input[type="number"], input[type="date"], textarea {
    background-color: #ffffff;
    color: #333;
}

div {
    margin-bottom: 10px;
}

input[type="checkbox"] {
    margin-right: 5px;
}/* Styling the checkboxes */
input[type="checkbox"] {
    margin-right: 10px;
}

label {
    font-size: 14px;
    color: #333;
    margin-bottom: 10px;
    display: inline-block;
}

div {
    margin-bottom: 10px;
}

.isian{
    border:2.5px solid #000;
    padding:5px;
    border-radius:20px;
    width: 65px;
    text-align: center;
}

.container2 {
    display: flex;
    align-items: center;
    gap: 10px;
}

.fraction1 {
    display: inline-block;
    text-align: center;
    position: relative;
    font-size: 18px;
}

.fraction1 .numerator,
.fraction1 .denominator {
    display: block;
    padding: 2px 5px;
}

.fraction1::after {
    content: "";
    width: 100%;
    height: 2px;
    background-color: black;
    position: absolute;
    top: 50%;
    left: 0;
}

.multiply {
    font-weight: bold;
    font-size: 18px;
    white-space: nowrap;
}

.container2 {
    margin: auto;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
}

.input-group {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    width: 50%;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
    gap: 10px;
}

label {
    font-weight: bold;
    white-space: nowrap; /* Mencegah label turun ke bawah */
}

input {
    flex-grow: 1; /* Input akan menyesuaikan space yang tersisa */
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    min-width: 150px; /* Agar tidak terlalu kecil */
    max-width: 100%; /* Mencegah melebihi container */
}

.result {
    font-size: 16px;
    font-weight: bold;
    text-align: right;
    white-space: nowrap;
}


</style>

</head>
<body>
    <h1>Kelola Data</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($data['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <input type="hidden" name="user_id" value="<?= htmlspecialchars($data['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <label>No IKK:</label>
        <input type="text" name="no_ikk" value="<?= htmlspecialchars($data['no_ikk'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly><br>

        <label>Tanggal Pengiriman:</label>
        <input type="date" name="tgl_pengiriman" value="<?= htmlspecialchars($data['tgl_pengiriman'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly><br>

        <label>Kategori:</label>
        <input type="text" name="kategori_ikk" value="<?= htmlspecialchars($data['kategori_ikk'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly><br>

        <label>Indikator:</label>
        <input type="text" name="indikator" value="<?= htmlspecialchars($data['indikator'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly><br>

        <label>IKK Output:</label>
        <textarea name="ikk_output" readonly><?= htmlspecialchars($data['ikk_output'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea><br>

        <label>IKK Outcome:</label>
        <input type="text" name="ikk_outcome" value="<?= htmlspecialchars($data['ikk_outcome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly><br>


        <table>
            <tr>
                <div class = "container2">
                <td>Rumus : </td>
                <td><div class="fraction1">
                        <span class="denominator"><?= htmlspecialchars($pembilang ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="numerator"><?= htmlspecialchars($penyebut ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <span class="multiply"> X </span> 100%</td>
                </div>
            </tr>
        </table>

        <div class="isian" >Isian +-</div>
        
    <div class="container2">
        <!-- Kolom Pembilang dan pada name=angka_penyebut itu nanti yang akan menyimpan data ke db karena di bagian penyimpanan pada post -->
        <div class="input-group">
            <label for="pembilang"><?= htmlspecialchars($pembilang ?? '', ENT_QUOTES, 'UTF-8') ?></label>
            <input type="number" name ="angka_pembilang" id="pembilang" oninput="hitung()">    
        </div>

        <!-- Kolom Penyebut -->
        <div class="input-group">
            <label for="penyebut"><?= htmlspecialchars($penyebut ?? '', ENT_QUOTES, 'UTF-8') ?></label>
            <input type="number" name="angka_penyebut" id="penyebut" oninput="hitung()">
        </div>

        <!-- Hasil Capaian -->
        <div class="input-group">
            <span>Capaian:</span>
            <div class="result"><span id="hasil">0</span>%</div>
        </div>
    </div>

        <label style="width: 100%;">Kategori Kendala Masalah:</label>
        <table style="margin-bottom:10px; margin-top:10px;">
            <tr>
                <td width="300px"><label for="anggaran">Anggaran</label></td>
                <td><input type="checkbox" name="kategori_kendala_masalah" value="Anggaran" id="anggaran"></td>
            </tr>
            
            <tr>
                <td width="300px"><label for="sdm">Sumber Daya Manusia</label></td>
                <td><input type="checkbox" name="kategori_kendala_masalah" value="Sumber Daya Manusia" id="sdm"></td>
            </tr>
    
            <tr>
                <td width="300px"><label for="kebijakan">Kebijakan</label></td>
                <td><input type="checkbox" name="kategori_kendala_masalah" value="Kebijakan" id="kebijakan"></td>
            </tr>
        </table>

        
        <label style="width: 100%;">Keterangan Kendala Masalah:</label>
        <textarea name="penjelasan_kendala_masalah" required><?= htmlspecialchars($data['penjelasan_kendala_masalah'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea><br>

        <label for="unggah_dokumen" style="width: 100%;">
            <i data-feather="home"></i> Dokumen Pendukung : 
        </label>
        <input type="file" id="unggah_dokumen" name="unggah_dokumen" accept=".pdf" required>

        <button type="button" onclick="window.location.href='../OPD/pelaporan_IKK_OPD.php'"style="background-color: #dc3545;">Kembali</button>
        <button type="submit">Simpan</button>
        

    </form>

    <!-- agar si data nantinya pas disimpan ga bisa diedit tapi masih tetap muncul ketika di klik kelola data --> -->

    <script>

function hitung() {
            // Ambil nilai dari input pembilang dan penyebut
            let pembilang = parseFloat(document.getElementById('pembilang').value) || 0;
            let penyebut = parseFloat(document.getElementById('penyebut').value) || 1; // Hindari pembagian dengan nol

            // Hitung persentase
            let hasil = (pembilang / penyebut) * 100;

            // Tampilkan hasil persentase
            document.getElementById('hasil').innerText = hasil.toFixed(2);
        }
        // function generateFormula() {
        //     let numerator = document.getElementById("pembilang").value;
        //     let denominator = document.getElementById("penyebut").value;
        //     let formulaOutput = document.getElementById("formula-output");

        //     if (numerator && denominator) {
        //         formulaOutput.innerHTML = `
        //             <div class="fraction1">
        //                 <span class="numerator">${numerator}</span>
        //                 <span class="denominator">${denominator}</span>
        //             </div>
        //             <span class="multiply">×</span> 100%
        //         `;
        //     } else {
        //         formulaOutput.innerHTML = "Masukkan pembilang dan penyebut!";
        //     }
        // }
    </script> 

</body>
</html>