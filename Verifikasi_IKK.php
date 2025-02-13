<?php
session_start();
include '../db_config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['action'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $action = $_POST['action'];
        
        if ($action === 'setuju') {
            $status = 'Setuju';
            $tanggal_persetujuan = date('Y-m-d H:i:s');
            $query = "UPDATE pelaporan_opd SET status_laporan = '$status', tanggal_persetujuan = '$tanggal_persetujuan' WHERE id = '$id'";
        } else if ($action === 'perbaiki') {
            $status = 'Perbaiki';
            $query = "UPDATE pelaporan_opd SET status_laporan = '$status' WHERE id = '$id'";
        }
        
        if (mysqli_query($conn, $query)) {
            // Redirect ke halaman yang sama untuk refresh data
            header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['opd']) ? "?opd=" . urlencode($_GET['opd']) : ""));
            exit();
        }
    }
}

// Mengubah query untuk mengambil kategori dari tabel kategori_ikk
$query_opd = "SELECT DISTINCT nama_kategori_ikk FROM kategori_ikk ORDER BY nama_kategori_ikk ASC";
$result_opd = mysqli_query($conn, $query_opd);

// Modifikasi query filter
$selected_opd = isset($_GET['opd']) ? $_GET['opd'] : '';

$query_menunggu = "SELECT p.*, k.nama_kategori_ikk 
                   FROM pelaporan_opd p 
                   LEFT JOIN kategori_ikk k ON p.kategori_ikk = k.nama_kategori_ikk 
                   WHERE p.status_laporan = 'Menunggu'";

$query_setuju = "SELECT p.*, k.nama_kategori_ikk 
                 FROM pelaporan_opd p 
                 LEFT JOIN kategori_ikk k ON p.kategori_ikk = k.nama_kategori_ikk 
                 WHERE p.status_laporan = 'Setuju'";

$query_perbaiki = "SELECT p.*, k.nama_kategori_ikk 
                   FROM pelaporan_opd p 
                   LEFT JOIN kategori_ikk k ON p.kategori_ikk = k.nama_kategori_ikk 
                   WHERE p.status_laporan = 'Perbaiki'";

if ($selected_opd) {
    $query_menunggu .= " AND p.kategori_ikk = '" . mysqli_real_escape_string($conn, $selected_opd) . "'";
    $query_setuju .= " AND p.kategori_ikk = '" . mysqli_real_escape_string($conn, $selected_opd) . "'";
    $query_perbaiki .= " AND p.kategori_ikk = '" . mysqli_real_escape_string($conn, $selected_opd) . "'";
}

$query_setuju .= " ORDER BY p.tanggal_persetujuan DESC";

$result_menunggu = mysqli_query($conn, $query_menunggu);
$result_setuju = mysqli_query($conn, $query_setuju);
$result_perbaiki = mysqli_query($conn, $query_perbaiki);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi IKK</title>
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
            margin-left: auto;
        }

        .header a {
            color: white;
            text-decoration: none;
            font-size: 20px;
        }

        .section {
            margin: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .section-title {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid;
            font-size: 18px;
        }

        .menunggu-title { border-color: #ffc107; }
        .setuju-title { border-color: #28a745; }
        .perbaiki-title { border-color: #dc3545; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
            font-size: 14px;
        }

        .btn-setuju {
            background-color: #28a745;
            color: white;
        }

        .btn-perbaiki {
            background-color: #dc3545;
            color: white;
        }

        .btn-preview {
            display: inline-block;
            padding: 6px 12px;
            background-color: #17a2b8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 5px;
            font-size: 14px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }

        .tanggal {
            color: #666;
            font-size: 14px;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .detail-item {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .detail-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .dropdown-container {
        position: relative;
        display: inline-block;
    }

    .dropdown-trigger {
        color: white;
        text-decoration: none;
        font-size: 20px;
        padding: 10px;
        cursor: pointer;
    }

    .nav-dropdown {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 200px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        z-index: 1000;
        border-radius: 4px;
        top: 100%;
        left: 0;
    }

.nav-dropdown a {
        color: #333 !important;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font-size: 14px !important;
        transition: background-color 0.2s;
    }

    .nav-dropdown a:hover {
        background-color: #f1f1f1;
        padding-left: 20px;
    }

    .dropdown-container:hover .nav-dropdown {
        display: block;
    }

    .dropdown-trigger::after {
        content: '▼';
        font-size: 12px;
        margin-left: 5px;
    }

    .nav-dropdown .active {
        background-color: #f4f4f4;
        font-weight: bold;
    }


    .nav-dropdown {
        transform-origin: top;
        animation: dropdownAnimation 0.2s ease-out;
    }

    @keyframes dropdownAnimation {
        from {
            opacity: 0;
            transform: scaleY(0);
        }
        to {
            opacity: 1;
            transform: scaleY(1);
        }
    }

        .dropdown-container:hover .nav-dropdown {
            display: block;
        }

        .dropdown-trigger::after {
            content: '▼';
            font-size: 12px;
            margin-left: 5px;
        }

        .user-menu {
        position: relative;
        margin-left: 20px;
    }

    .user-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
    }

    .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 45px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        border-radius: 4px;
        z-index: 1000;
        min-width: 120px;
    }

    .dropdown a {
        color: #333 !important;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font-size: 14px;
        transition: background-color 0.2s;
    }

    .dropdown a:hover {
        background-color: #f1f1f1;
    }

    /* Tambahkan arrow indicator untuk dropdown */
    .dropdown::before {
        content: '';
        position: absolute;
        top: -8px;
        right: 12px;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 8px solid #fff;
    }

        .section {
            margin: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .section-title {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid;
            font-size: 18px;
        }

        .menunggu-title { border-color: #ffc107; }
        .setuju-title { border-color: #28a745; }
        .perbaiki-title { border-color: #dc3545; }

        /* Modal Input Catatan Perbaikan */
#modalCatatanPerbaikan {
    display: none; /* Tersembunyi secara default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Latar belakang semi-transparan */
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
    width: 400px; /* Lebar modal */
    max-width: 90%; /* Responsif */
    margin: auto; /* Center modal */
    animation: fadeIn 0.3s; /* Animasi saat muncul */
}

/* Animasi Fade In */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

h3 {
    margin: 0 0 15px; /* Margin bawah untuk judul */
    font-size: 20px; /* Ukuran font judul */
    color: #333; /* Warna teks judul */
}

textarea {
    width: 100%; /* Lebar penuh */
    height: 100px; /* Tinggi textarea */
    padding: 10px; /* Padding di dalam textarea */
    border: 1px solid #ccc; /* Border */
    border-radius: 5px; /* Sudut melengkung */
    resize: none; /* Nonaktifkan resize */
    font-size: 14px; /* Ukuran font */
    color: #555; /* Warna teks */
}

button {
    background-color: #007bff; /* Warna tombol */
    color: white; /* Warna teks tombol */
    border: none; /* Tanpa border */
    border-radius: 5px; /* Sudut melengkung */
    padding: 10px 15px; /* Padding tombol */
    cursor: pointer; /* Pointer saat hover */
    margin-right: 10px; /* Margin kanan untuk tombol */
    transition: background-color 0.3s; /* Transisi saat hover */
}

button:hover {
    background-color: #0056b3; /* Warna saat hover */
}

button:last-child {
    background-color: #dc3545; /* Warna tombol Batal */
}

button:last-child:hover {
    background-color: #c82333; /* Warna saat hover untuk tombol Batal */
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
          <div class="dropdown-container">
            <a href="#" class="dropdown-trigger">Verifikasi IKK</a>
            <div class="nav-dropdown">
                <a href="Verifikasi_IKK.php">Semua Verifikasi</a>
                <?php 
                mysqli_data_seek($result_opd, 0);
                while($opd = mysqli_fetch_assoc($result_opd)): 
                ?>
                    <a href="Verifikasi_IKK.php?opd=<?= urlencode($opd['nama_kategori_ikk']) ?>" 
                       <?= $selected_opd === $opd['nama_kategori_ikk'] ? 'class="active"' : '' ?>>
                        <?= htmlspecialchars($opd['nama_kategori_ikk']) ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
        <a href="../PEM/tambah_user.php">Tambah User</a>
    </div>
    <div class="user-menu">
        <img src="../image/9131529.png" alt="User Icon" title="User Menu" class="user-icon" id="user-icon">
        <div class="dropdown" id="dropdown-menu">
            <a href="../login_1.php">Logout</a>
        </div>
    </div>
</div>
     <!-- Menunggu Verifikasi -->
    <div class="section">
        <h3 class="section-title menunggu-title">Menunggu Verifikasi</h3>
        <table>
            <thead>
                <tr>
                    <th>No IKK</th>
                    <th>Tanggal Pengiriman</th>
                    <th>Kategori IKK</th>
                    <th>Indikator</th>
                    <th>Aksi</th>
                </tr>

<!-- Modal Input Catatan Perbaikan -->
<div id="modalCatatanPerbaikan" style="display: none;">
    <div class="modal-content">
        <h3>Masukkan Catatan Perbaikan</h3>
        <textarea id="catatan_perbaikan" rows="4" cols="50"></textarea>
        <br>
        <button onclick="simpanCatatan()">Simpan</button>
        <button onclick="tutupModal()">Batal</button>
        <input type="hidden" id="idPelaporan">
    </div>
</div>

<script>
function simpanCatatan() {
    let id = document.getElementById('idPelaporan').value; // Ambil ID pelaporan
    let catatan = document.getElementById('catatan_perbaikan').value; // Ambil catatan perbaikan

    // Kirim data ke server via AJAX
    fetch('simpan_catatan.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: id=${id}&catatan_perbaikan=${encodeURIComponent(catatan)}
    }).then(response => response.text()).then(data => {
        alert(data); // Notifikasi
        tutupModal(); // Tutup modal setelah simpan
        // Redirect ke halaman yang sama untuk refresh data
        window.location.reload(); // Reload halaman untuk melihat perubahan
    }).catch(error => console.error('Error:', error));
}
</script>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result_menunggu) > 0) {
                    while ($row = mysqli_fetch_assoc($result_menunggu)): 
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['no_ikk']) ?></td>
                        <td><?= htmlspecialchars($row['tgl_pengiriman']) ?></td>
                        <td><?= htmlspecialchars($row['kategori_ikk']) ?></td>
                        <td><?= htmlspecialchars($row['indikator']) ?></td>
                        <td>
                        <button class="btn-action btn-setuju" 
                                    onclick="verifikasiAction(<?= $row['id'] ?>, 'setuju')">
                                Setuju
                            </button>
                            <button class="btn-action btn-perbaiki" onclick="verifikasiAction(<?= $row['id'] ?>, 'perbaiki')">
    Perbaiki
</button>
                            <a href="../PEM/detail_laporan_pem.php?id=<?= $row['id'] ?>" class="btn-preview" target="_blank">
                                Lihat Detail
                            </a>
                            <a href="download_laporan.php?id=<?= $row['id'] ?>" class="btn-download">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                <?php 
                    endwhile;
                } else {
                    echo "<tr><td colspan='5' style='text-align: center;'>Tidak ada data yang menunggu verifikasi</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Yang Sudah Disetujui -->
    <div class="section">
        <h3 class="section-title setuju-title">Laporan yang Disetujui</h3>
        <table>
            <thead>
                <tr>
                    <th>No IKK</th>
                    <th>Tanggal Pengiriman</th>
                    <th>Kategori IKK</th>
                    <th>Indikator</th>
                    <th>Tanggal Persetujuan</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result_setuju) > 0) {
                    while ($row = mysqli_fetch_assoc($result_setuju)): 
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['no_ikk']) ?></td>
                        <td><?= htmlspecialchars($row['tgl_pengiriman']) ?></td>
                        <td><?= htmlspecialchars($row['kategori_ikk']) ?></td>
                        <td><?= htmlspecialchars($row['indikator']) ?></td>
                        <td class="tanggal">
                            <?= date('d/m/Y H:i', strtotime($row['tanggal_persetujuan'])) ?>
                        </td>
                        <td>
                        <a href="../PEM/detail_laporan_pem.php?id=<?= $row['id'] ?>" class="btn-preview" target="_blank">
                            Lihat Detail
                        </a>
                        <a href="download_laporan.php?id=<?= $row['id'] ?>" class="btn-download">
                            <i class="fas fa-download"></i> Download
                        </a>
                        </td>
                    </tr>
                <?php 
                    endwhile;
                } else {
                    echo "<tr><td colspan='6' style='text-align: center;'>Belum ada laporan yang disetujui</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Yang Perlu Perbaikan -->
    <div class="section">
        <h3 class="section-title perbaiki-title">Laporan yang Perlu Perbaikan</h3>
        <table>
            <thead>
                <tr>
                    <th>No IKK</th>
                    <th>Tanggal Pengiriman</th>
                    <th>Kategori IKK</th>
                    <th>Indikator</th>
                    <th>Status</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result_perbaiki) > 0) {
                    while ($row = mysqli_fetch_assoc($result_perbaiki)): 
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['no_ikk']) ?></td>
                        <td><?= htmlspecialchars($row['tgl_pengiriman']) ?></td>
                        <td><?= htmlspecialchars($row['kategori_ikk']) ?></td>
                        <td><?= htmlspecialchars($row['indikator']) ?></td>
                        <td>
                            <span class="status-badge" style="background-color: #dc3545; color: white;">
    Perlu Perbaikan
</span>
<small style="color: #dc3545; margin-left: 8px;">
        <a href="lihat_catatan.php?id=<?= $row['id'] ?>" style="color: #dc3545; text-decoration: underline;">
            (lihat catatan perbaikan)
        </a>
    </small>

                        </td>
                        <td>
                        <a href="../PEM/detail_laporan_pem.php?id=<?= $row['id'] ?>" class="btn-preview" target="_blank">
                            Lihat Detail
                        </a>
                        </td>
                    </tr>
                <?php 
                    endwhile;
                } else {
                    echo "<tr><td colspan='6' style='text-align: center;'>Tidak ada laporan yang perlu perbaikan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    <script>
function showCatatanPerbaikan(id) {
    document.getElementById('modalCatatanPerbaikan').style.display = 'block';
    document.getElementById('idPelaporan').value = id; // Set ID pelaporan ke input tersembunyi
}

function tutupModal() {
    document.getElementById('modalCatatanPerbaikan').style.display = 'none'; // Menyembunyikan modal
}

function simpanCatatan() {
    let id = document.getElementById('idPelaporan').value; // Ambil ID pelaporan
    let catatan = document.getElementById('catatan_perbaikan').value; // Ambil catatan perbaikan

    // Kirim data ke server via AJAX
    fetch('simpan_catatan.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${id}&catatan_perbaikan=${encodeURIComponent(catatan)}` // Perbaiki di sini
    }).then(response => response.text()).then(data => {
        alert(data); // Notifikasi
        tutupModal(); // Tutup modal setelah simpan
        // Redirect ke halaman yang sama untuk refresh data
        window.location.reload(); // Reload halaman untuk melihat perubahan
    }).catch(error => console.error('Error:', error));
}

function verifikasiAction(id, action) {
    if (action === 'setuju') {
        if (confirm('Apakah Anda yakin ingin menyetujui laporan ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.href; // Tambahkan ini untuk mempertahankan parameter URL
            
            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id';
            inputId.value = id;
            
            const inputAction = document.createElement('input');
            inputAction.type = 'hidden';
            inputAction.name = 'action';
            inputAction.value = action;
            
            form.appendChild(inputId);
            form.appendChild(inputAction);
            document.body.appendChild(form);
            form.submit();
        }
    } else if (action === 'perbaiki') {
        // Jika aksi adalah 'perbaiki', langsung tampilkan modal
        showCatatanPerbaikan(id);
    }
}

// Dropdown menu
document.getElementById('user-icon').onclick = function() {
    document.getElementById('dropdown-menu').style.display = 
        document.getElementById('dropdown-menu').style.display === 'block' ? 'none' : 'block';
}

window.onclick = function(event) {
    if (!event.target.matches('.user-icon')) {
        var dropdown = document.getElementById('dropdown-menu');
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        }
    }
}
</script>
</body>
</html>