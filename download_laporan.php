<?php
session_start();
include '../db_config/db_config.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

if(empty($id)) {
    die("ID tidak ditemukan");
}

$query = "SELECT po.*, pp.pembilang, pp.penyebut
          FROM pelaporan_opd po 
          LEFT JOIN pelaporan_pem pp ON po.id = pp.id 
          WHERE po.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result->num_rows == 0) {
    die("Data tidak ditemukan");
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan IKK - <?= htmlspecialchars($data['no_ikk'] ?? '') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1a237e;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 15px;
        }
        .header h2, .header h3 {
            margin: 5px 0;
            color: #1a237e;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
        }
        .content-table td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
        }
        .label {
            font-weight: bold;
            width: 200px;
            background-color: #f8f9fa;
            color: #2c3e50;
        }
        .section {
            margin: 25px 0;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .section-title {
            color: #1a237e;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0e0e0;
        }
        .btn-print {
            background-color: #1a237e;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 20px 0;
            transition: background-color 0.3s;
        }
        .btn-print:hover {
            background-color: #0d47a1;
        }
        .btn-print i {
            font-size: 16px;
        }
        @media print {
            .btn-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 20px;
                background: white;
            }
            .container {
                box-shadow: none;
                padding: 0;
            }
            .section {
                box-shadow: none;
                padding: 15px 0;
            }
            .content-table {
                border: 1px solid #000;
            }
            .content-table td {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak PDF
        </button>
        
        <div class="header">
            <img src="../image/logo kota pariaman sumbar.png" alt="Logo Pemda" class="logo">
            <h2>PEMERINTAH KOTA PARIAMAN</h2>
            <h3>LAPORAN INDIKATOR KINERJA KUNCI (IKK)</h3>
        </div>

        <div class="section">
            <div class="section-title">
                <i class="fas fa-info-circle"></i> Detail IKK
            </div>
            <table class="content-table">
                <tr>
                    <td class="label">No IKK</td>
                    <td><?= htmlspecialchars($data['no_ikk'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Tanggal Pengiriman</td>
                    <td><?= htmlspecialchars($data['tgl_pengiriman'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Kategori</td>
                    <td><?= htmlspecialchars($data['kategori_ikk'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Indikator</td>
                    <td><?= htmlspecialchars($data['indikator'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">
                <i class="fas fa-calculator"></i> Rumus dan Perhitungan
            </div>
            <table class="content-table">
                <tr>
                    <td class="label">Rumus</td>
                    <td><?= htmlspecialchars($data['pembilang'] ?? '-') ?> / <?= htmlspecialchars($data['penyebut'] ?? '-') ?> × 100%</td>
                </tr>
                <tr>
                    <td class="label">Angka Pembilang</td>
                    <td><?= htmlspecialchars($data['angka_pembilang'] ?? '0') ?></td>
                </tr>
                <tr>
                    <td class="label">Angka Penyebut</td>
                    <td><?= htmlspecialchars($data['angka_penyebut'] ?? '0') ?></td>
                </tr>
                <tr>
                    <td class="label">Capaian</td>
                    <td><?= isset($data['capaian']) ? number_format($data['capaian'], 2) : '0' ?>%</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">
                <i class="fas fa-exclamation-triangle"></i> Kendala dan Masalah
            </div>
            <table class="content-table">
                <tr>
                    <td class="label">Kategori Kendala</td>
                    <td><?= htmlspecialchars($data['kategori_kendala_masalah'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Penjelasan Kendala</td>
                    <td><?= nl2br(htmlspecialchars($data['penjelasan_kendala_masalah'] ?? '-')) ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">
                <i class="fas fa-clipboard-check"></i> Status dan Verifikasi
            </div>
            <table class="content-table">
                <tr>
                    <td class="label">Status Laporan</td>
                    <td><?= htmlspecialchars($data['status_laporan'] ?? '-') ?></td>
                </tr>
                <?php if (!empty($data['catatan_perbaikan'])): ?>
                <tr>
                    <td class="label">Catatan Perbaikan</td>
                    <td><?= nl2br(htmlspecialchars($data['catatan_perbaikan'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>