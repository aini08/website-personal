<?php
session_start();
include '../db_config/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_1.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Ambil data dari pelaporan_opd
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- CSS sama seperti edit_laporan.php -->
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

.form {
    max-width: 900px;
    margin: auto;
    background: white;
    padding: 30px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

/* Style untuk area readonly */
.readonly-view {
    margin-bottom: 20px;
}

.readonly-label {
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
    display: block;
}

.readonly-value {
    width: 100%;
    padding: 12px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    color: #495057;
    min-height: 20px;
    box-sizing: border-box;
}

/* Style untuk textarea readonly */
.readonly-value.textarea {
    min-height: 100px;
    white-space: pre-wrap;
}

/* Style untuk rumus */
.container2 {
    margin: 20px 0;
}

.isian {
    border: 2.5px solid #000;
    padding: 5px;
    border-radius: 20px;
    width: 65px;
    text-align: center;
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

.multiply {
    margin: 0 5px;
}

/* Style untuk dokumen */
.doc-link {
    color: #2196F3;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.doc-link:hover {
    text-decoration: underline;
}

.doc-link i {
    font-size: 16px;
}

/* Style untuk tombol */
.btn-group {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    justify-content: flex-end;
}

.btn-cancel {
    background-color: rgb(241, 37, 37);
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.2s;
}

.btn-cancel:hover {
    background-color:rgb(241, 37, 37);
    color: white;
}

/* Style untuk capaian */
.capaian-value {
    font-weight: bold;
    color: #28a745;
}

/* Style untuk kendala masalah */
.kendala-badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.kendala-badge {
    background-color: #e9ecef;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    color: #495057;
}

/* Style untuk status */
.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.status-menunggu {
    background-color: #ffc107;
    color: #000;
}

.status-diterima {
    background-color: #28a745;
    color: #fff;
}

.status-ditolak {
    background-color: #dc3545;
    color: #fff;
}

/* Responsif untuk layar kecil */
@media (max-width: 768px) {
    .form {
        padding: 20px;
        margin: 10px;
    }

    .btn-group {
        flex-direction: column;
    }

    .btn-cancel {
        width: 100%;
        text-align: center;
    }
}
    </style>
</head>
<body>
    <h1>Detail Laporan</h1>
    <div class="form">
        <div class="readonly-view">
            <div class="readonly-label">No IKK:</div>
            <div class="readonly-value"><?= htmlspecialchars($data['no_ikk']) ?></div>
        </div>

        <div class="readonly-view">
            <div class="readonly-label">Tanggal Pengiriman:</div>
            <div class="readonly-value"><?= htmlspecialchars($data['tgl_pengiriman']) ?></div>
        </div>

        <div class="readonly-view">
            <div class="readonly-label">Kategori:</div>
            <div class="readonly-value"><?= htmlspecialchars($data['kategori_ikk']) ?></div>
        </div>

        <div class="readonly-view">
            <div class="readonly-label">Indikator:</div>
            <div class="readonly-value"><?= htmlspecialchars($data['indikator']) ?></div>
        </div>

        <div class="readonly-view">
            <div class="readonly-label">IKK Output:</div>
            <div class="readonly-value"><?= nl2br(htmlspecialchars($data['ikk_output'])) ?></div>
        </div>

        <div class="readonly-view">
            <div class="readonly-label">IKK Outcome:</div>
            <div class="readonly-value"><?= htmlspecialchars($data['ikk_outcome']) ?></div>
        </div>

        <div class="container2">
            <div class="readonly-view">
                <div class="readonly-label">Rumus:</div>
                <div class="readonly-value">
                    <div class="fraction1">
                        <span class="numerator"><?= htmlspecialchars($data['pembilang']) ?></span>
                        <span class="denominator"><?= htmlspecialchars($data['penyebut']) ?></span>
                    </div>
                    <span class="multiply">X 100%</span>
                </div>
            </div>

            <div class="readonly-view">
                <div class="readonly-label">Angka Pembilang:</div>
                <div class="readonly-value"><?= htmlspecialchars($data['angka_pembilang']) ?></div>
            </div>

            <div class="readonly-view">
                <div class="readonly-label">Angka Penyebut:</div>
                <div class="readonly-value"><?= htmlspecialchars($data['angka_penyebut']) ?></div>
            </div>

            <div class="readonly-view">
                <div class="readonly-label">Capaian:</div>
                <div class="readonly-value"><?= number_format($data['capaian'], 2) ?>%</div>
            </div>
        </div>

        <div class="readonly-view">
            <div class="readonly-label">Kategori Kendala Masalah:</div>
            <div class="readonly-value"><?= htmlspecialchars($data['kategori_kendala_masalah']) ?></div>
        </div>

        <div class="readonly-view">
            <div class="readonly-label">Keterangan Kendala Masalah:</div>
            <div class="readonly-value"><?= nl2br(htmlspecialchars($data['penjelasan_kendala_masalah'])) ?></div>
        </div>

        <div class="readonly-view">
            <div class="readonly-label">Dokumen:</div>
            <div class="readonly-value">
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
        </div>

        <div class="btn-group">
            <a href="status_IKK.php" class="btn-cancel">Kembali</a>
        </div>
    </div>
</body>
</html>