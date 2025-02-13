<?php
session_start();
include '../db_config/db_config.php';

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? $_GET['id'] : '';

if(empty($id)) {
    die("ID tidak ditemukan");
}

// Query untuk mengambil data
$query = "SELECT * FROM pelaporan_opd WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Error query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result->num_rows == 0) {
    die("Data tidak ditemukan");
}

$data = mysqli_fetch_assoc($result);

// Debug untuk melihat data dokumen
// echo "<pre>";
// print_r($data);
// echo "</pre>";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan IKK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1a237e;
        }
        .section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .section-title {
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1a237e;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .value {
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 4px;
            min-height: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-back {
            background-color: #dc3545;
        }
        .btn-back:hover {
            background-color: #c82333;
        }
        .doc-link {
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
            transition: background-color 0.2s;
        }
        .doc-link:hover {
            background-color: #0056b3;
        }
        .doc-link i {
            margin-right: 5px;
        }
        .capaian {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>DETAIL LAPORAN IKK</h2>
        </div>

        <!-- Informasi Dasar -->
        <div class="section">
            <div class="section-title">Informasi Dasar</div>
            <div class="field">
                <div class="label">No IKK</div>
                <div class="value"><?= htmlspecialchars($data['no_ikk'] ?? '-') ?></div>
            </div>
            <div class="field">
                <div class="label">Tanggal Pengiriman</div>
                <div class="value"><?= htmlspecialchars($data['tgl_pengiriman'] ?? '-') ?></div>
            </div>
            <div class="field">
                <div class="label">Kategori IKK</div>
                <div class="value"><?= htmlspecialchars($data['kategori_ikk'] ?? '-') ?></div>
            </div>
        </div>

        <!-- Detail IKK -->
        <div class="section">
            <div class="section-title">Detail IKK</div>
            <div class="field">
                <div class="label">Indikator</div>
                <div class="value"><?= nl2br(htmlspecialchars($data['indikator'] ?? '-')) ?></div>
            </div>
            <div class="field">
                <div class="label">IKK Output</div>
                <div class="value"><?= nl2br(htmlspecialchars($data['ikk_output'] ?? '-')) ?></div>
            </div>
            <div class="field">
                <div class="label">IKK Outcome</div>
                <div class="value"><?= nl2br(htmlspecialchars($data['ikk_outcome'] ?? '-')) ?></div>
            </div>
        </div>

        <!-- Perhitungan -->
        <div class="section">
            <div class="section-title">Perhitungan</div>
            <div class="field">
                <div class="label">Angka Pembilang</div>
                <div class="value"><?= htmlspecialchars($data['angka_pembilang'] ?? '0') ?></div>
            </div>
            <div class="field">
                <div class="label">Angka Penyebut</div>
                <div class="value"><?= htmlspecialchars($data['angka_penyebut'] ?? '0') ?></div>
            </div>
            <div class="field">
                <div class="label">Capaian</div>
                <div class="value capaian">
                    <?= isset($data['capaian']) ? number_format($data['capaian'], 2) . '%' : '0%' ?>
                </div>
            </div>
        </div>

        <!-- Kendala dan Masalah -->
        <div class="section">
            <div class="section-title">Kendala dan Masalah</div>
            <div class="field">
                <div class="label">Kategori Kendala</div>
                <div class="value"><?= htmlspecialchars($data['kategori_kendala_masalah'] ?? '-') ?></div>
            </div>
            <div class="field">
                <div class="label">Penjelasan Kendala</div>
                <div class="value"><?= nl2br(htmlspecialchars($data['penjelasan_kendala_masalah'] ?? '-')) ?></div>
            </div>
        </div>

        <!-- Dokumen -->
        <div class="section">
            <div class="section-title">Dokumen Pendukung</div>
            <div class="field">
                <div class="label">File Dokumen</div>
                <div class="value">
                    <?php if (!empty($data['unggah_dokumen'])): ?>
                        <?php
                        $file_path = "../OPD/" . $data['unggah_dokumen'];
                        // Debug informasi file
                        // echo "Path: " . $file_path . "<br>";
                        // echo "Exists: " . (file_exists($file_path) ? 'Yes' : 'No') . "<br>";
                        ?>
                        <a href="<?= $file_path ?>" 
                           class="doc-link" 
                           target="_blank">
                            <i class="fas fa-eye"></i> Lihat Dokumen
                        </a>
                        <a href="download.php?file=<?= urlencode($data['unggah_dokumen']) ?>" 
                           class="doc-link">
                            <i class="fas fa-download"></i> Download Dokumen
                        </a>
                    <?php else: ?>
                        <span>Tidak ada dokumen</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div style="text-align: right; margin-top: 20px;">
            <a href="Verifikasi_IKK.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</body>
</html>