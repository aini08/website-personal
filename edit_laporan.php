<?php
session_start();
include '../db_config/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_1.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Ambil data dari pelaporan_opd (data yang sudah diisi user)
$query = "SELECT po.*, pp.pembilang, pp.penyebut 
          FROM pelaporan_opd po 
          LEFT JOIN pelaporan_pem pp ON po.id = pp.id 
          WHERE po.id = ? AND po.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "is", $id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result->num_rows > 0) {
    $data = mysqli_fetch_assoc($result);
} else {
    die("Data tidak ditemukan.");
}

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_kendala_masalah = $_POST['kategori_kendala_masalah'] ?? '';
    $penjelasan_kendala_masalah = $_POST['penjelasan_kendala_masalah'] ?? '';
    $angka_pembilang = $_POST['angka_pembilang'] ?? 0;
    $angka_penyebut = $_POST['angka_penyebut'] ?? 0;
    $capaian = ($angka_penyebut != 0) ? ($angka_pembilang / $angka_penyebut) * 100 : 0;

    // Handle file upload
    $unggah_dokumen = $data['unggah_dokumen']; // Keep existing document by default
    if (!empty($_FILES['unggah_dokumen']['name'])) {
        $upload_dir = '../OPD/';
        $file_name = uniqid() . '_' . $_FILES['unggah_dokumen']['name'];
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['unggah_dokumen']['tmp_name'], $file_path)) {
            // Delete old file if exists
            if (!empty($data['unggah_dokumen']) && file_exists($upload_dir . $data['unggah_dokumen'])) {
                unlink($upload_dir . $data['unggah_dokumen']);
            }
            $unggah_dokumen = $file_name;
        }
    }

    // Update query
    $updateQuery = "UPDATE pelaporan_opd SET 
                   kategori_kendala_masalah = ?,
                   penjelasan_kendala_masalah = ?,
                   angka_pembilang = ?,
                   angka_penyebut = ?,
                   capaian = ?,
                   unggah_dokumen = ?,
                   status_laporan = 'Menunggu'
                   WHERE id = ? AND user_id = ?";

    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "ssdddssi", 
        $kategori_kendala_masalah,
        $penjelasan_kendala_masalah,
        $angka_pembilang,
        $angka_penyebut,
        $capaian,
        $unggah_dokumen,
        $id,
        $user_id
    );

    if (mysqli_stmt_execute($updateStmt)) {
        header("Location: status_IKK.php?success=update");
        exit();
    } else {
        die("Gagal mengupdate data.");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
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

        input, textarea {
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
        }

        .btn-group {
    display: flex;
    gap: 20px; /* Memperbesar jarak antar tombol */
    margin-top: 20px;
    justify-content: center; /* Menengahkan tombol */
}

.btn-submit, 
.btn-cancel {
    padding: 12px 30px; /* Memperbesar padding horizontal */
    border: none;
    border-radius: 25px; /* Membuat tombol lebih bulat */
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    min-width: 120px; /* Menetapkan lebar minimum yang sama */
    text-align: center;
    transition: opacity 0.3s; /* Efek hover yang halus */
}

.btn-submit {
    background-color: #4CAF50;
    color: white;
}

.btn-cancel {
    background-color: #dc3545;
    color: white;
    text-decoration: none;
    display: inline-flex; /* Untuk align dengan button */
    align-items: center;
    justify-content: center;
}

/* Hover effect */
.btn-submit:hover,
.btn-cancel:hover {
    opacity: 0.85;
}

        .isian {
            border: 2.5px solid #000;
            padding: 5px;
            border-radius: 20px;
            width: 65px;
            text-align: center;
            margin-bottom: 15px;
        }

        .container2 {
            margin: 20px 0;
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .fraction1 {
            display: inline-block;
            text-align: center;
            position: relative;
            margin: 0 10px;
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

        .current-doc {
            margin: 10px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .doc-link {
            color: #2196F3;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .file-info {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }

        .checkbox-group {
            margin: 15px 0;
        }

        .checkbox-group label {
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <h1>Edit Laporan</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">

        <label>No IKK:</label>
        <input type="text" value="<?= htmlspecialchars($data['no_ikk']) ?>" readonly>

        <label>Tanggal Pengiriman:</label>
        <input type="date" value="<?= htmlspecialchars($data['tgl_pengiriman']) ?>" readonly>

        <label>Kategori:</label>
        <input type="text" value="<?= htmlspecialchars($data['kategori_ikk']) ?>" readonly>

        <label>Indikator:</label>
        <input type="text" value="<?= htmlspecialchars($data['indikator']) ?>" readonly>

        <label>IKK Output:</label>
        <textarea readonly><?= htmlspecialchars($data['ikk_output']) ?></textarea>

        <label>IKK Outcome:</label>
        <input type="text" value="<?= htmlspecialchars($data['ikk_outcome']) ?>" readonly>

        <div class="container2">
            <table>
                <tr>
                    <td>Rumus : </td>
                    <td>
                        <div class="fraction1">
                            <span class="numerator"><?= htmlspecialchars($data['pembilang']) ?></span>
                            <span class="denominator"><?= htmlspecialchars($data['penyebut']) ?></span>
                        </div>
                        <span class="multiply">X 100%</span>
                    </td>
                </tr>
            </table>

            <div class="isian">Isian +-</div>

            <div class="input-group">
                <label for="pembilang"><?= htmlspecialchars($data['pembilang']) ?>:</label>
                <input type="number" name="angka_pembilang" id="pembilang" 
                       value="<?= htmlspecialchars($data['angka_pembilang']) ?>" 
                       oninput="hitung()">
            </div>

            <div class="input-group">
                <label for="penyebut"><?= htmlspecialchars($data['penyebut']) ?>:</label>
                <input type="number" name="angka_penyebut" id="penyebut" 
                       value="<?= htmlspecialchars($data['angka_penyebut']) ?>" 
                       oninput="hitung()">
            </div>

            <div class="input-group">
                <span>Capaian:</span>
                <div class="result"><span id="hasil"><?= number_format($data['capaian'], 2) ?></span>%</div>
            </div>
        </div>

        <label>Kategori Kendala Masalah:</label>
        <div class="checkbox-group">
            <label>
                <input type="checkbox" name="kategori_kendala_masalah" value="Anggaran"
                       <?= ($data['kategori_kendala_masalah'] == 'Anggaran') ? 'checked' : '' ?>>
                Anggaran
            </label>
            <label>
                <input type="checkbox" name="kategori_kendala_masalah" value="Sumber Daya Manusia"
                       <?= ($data['kategori_kendala_masalah'] == 'Sumber Daya Manusia') ? 'checked' : '' ?>>
                Sumber Daya Manusia
            </label>
            <label>
                <input type="checkbox" name="kategori_kendala_masalah" value="Kebijakan"
                       <?= ($data['kategori_kendala_masalah'] == 'Kebijakan') ? 'checked' : '' ?>>
                Kebijakan
            </label>
        </div>

        <label>Keterangan Kendala Masalah:</label>
        <textarea name="penjelasan_kendala_masalah" rows="4"><?= htmlspecialchars($data['penjelasan_kendala_masalah']) ?></textarea>

        <label>Dokumen Saat Ini:</label>
        <div class="current-doc">
            <?php if (!empty($data['unggah_dokumen'])): ?>
                <a href="../OPD/<?= htmlspecialchars($data['unggah_dokumen']) ?>" 
                   target="_blank" 
                   class="doc-link">
                    <i class="fas fa-file-pdf"></i> Lihat Dokumen
                </a>
            <?php else: ?>
                <span>Tidak ada dokumen</span>
            <?php endif; ?>
        </div>

        <label>Upload Dokumen Baru (Opsional):</label>
        <input type="file" name="unggah_dokumen" accept=".pdf">
        <div class="file-info">Format yang diizinkan: PDF (Maksimal 5MB)</div>

        <div class="btn-group">
            <a href="status_IKK.php" class="btn-cancel">Kembali</a>
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </div>
    </form>

    <script>
        function hitung() {
            let pembilang = parseFloat(document.getElementById('pembilang').value) || 0;
            let penyebut = parseFloat(document.getElementById('penyebut').value) || 1;
            let hasil = (pembilang / penyebut) * 100;
            document.getElementById('hasil').innerText = hasil.toFixed(2);
        }
    </script>
</body>
</html>