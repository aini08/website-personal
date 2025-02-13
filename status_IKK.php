<?php
session_start();
include '../db_config/db_config.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_1.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$id_kategori = isset($_GET['id_kategori']) ? $_GET['id_kategori'] : '';

// Buat query dasar
$query = "SELECT * FROM pelaporan_opd WHERE user_id = ?";
$params = [$user_id];

// Tambahkan kondisi pencarian jika ada
if (!empty($search)) {
    $query .= " AND (
        no_ikk LIKE ? OR 
        tgl_pengiriman LIKE ? OR 
        kategori_ikk LIKE ? OR 
        indikator LIKE ? OR 
        ikk_output LIKE ? OR 
        ikk_outcome LIKE ? OR 
        kategori_kendala_masalah LIKE ? OR 
        penjelasan_kendala_masalah LIKE ? OR 
        status_laporan LIKE ?
    )";
    
    // Tambahkan parameter pencarian untuk setiap kolom
    for ($i = 0; $i < 9; $i++) {
        $params[] = "%$search%";
    }
}

// Tambahkan filter kategori jika ada
if (!empty($id_kategori)) {
    $query .= " AND id_kategori = ?";
    $params[] = $id_kategori;
}

// Eksekusi query
$stmt = mysqli_prepare($conn, $query);
$types = str_repeat('s', count($params));
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!-- Tambahkan form pencarian di bawah header -->



<!DOCTYPE html>
<html lang="en">
<style>
    /* Header */
/* Gaya Font Global */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #e3f2fd; 
}

/* Header */
.header {
    background: linear-gradient(to right, #1a237e, #8c9eff);
    padding: 30px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    color: white;
    gap: 20px;
}

.search-container {
    margin-top: 15px; 
    margin bottom: 15px;  /* Hapus 'auto' dan gunakan margin normal */
    max-width: 500px;
    padding: 0 15px;
    text-align: left; 
}

.search-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-input {
    flex: 1;
    padding: 10px;
    border: 2px solid #90caf9;
    border-radius: 4px;
    font-size: 14px;
}

.search-button {
    padding: 10px 20px;
    background-color: #2196f3;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.search-button:hover {
    background-color: #2196f3;
}

.reset-button {
    padding: 10px 20px;
    background-color: #f44336;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.reset-button:hover {
    background-color: #da190b;
}

/* Menu Navigasi */
.nav {
    display: flex;
    gap: 20px;
}

.nav a {
    color: white;
    text-decoration: none;
    font-size: 20px;
}

/* User Menu */
.user-menu {
    position: relative;
}

.user-icon {
    width: 30px;
    height: 30px;
    cursor: pointer;
}

/* Dropdown Menu */
.dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 40px;
    background-color: white;
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

/* Tabel */
.table-container {
    overflow-x: auto;
    padding: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: left;
}

th {
    background-color: #1a237e;
    color: white;
}

/*nengok dokumen codenya */

.view-doc {
    color: #2196F3;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.view-doc:hover {
    background-color: #e3f2fd;
}

.no-doc {
    color: #999;
    font-style: italic;
}

/* Responsiveness */
@media (max-width: 768px) {
    .header {
        flex-direction: column;
        align-items: flex-end;
    }

    .nav {
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    .table-container {
        overflow-x: scroll;
    }

    table {
        min-width: 800px;
    }
}


/* Responsiveness */
@media (max-width: 768px) {
    .header {
        flex-direction: column;
        align-items: flex-end;
    }

    .nav {
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    .table-container {
        overflow-x: scroll;
    }

    table {
        min-width: 800px;
    }
}


/* Container untuk semua tombol */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Container untuk tombol Edit dan Hapus */
.button-group {
    display: flex;
    gap: 5px;
}

/* Style untuk tombol Edit dan Hapus */
.btn-edit, 
.btn-delete {
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: opacity 0.3s;
    min-width: 80px;
    justify-content: center;
}

.btn-delete {
    background-color: #dc3545;
}

.btn-edit {
    background-color: #28a745;
}

.btn-delete:hover,
.btn-edit:hover {
    opacity: 0.85;
    color: white;
}

/* Style untuk link Selengkapnya */
.link-detail {
    color: #007bff;
    text-decoration: none;
    font-size: 12px;
    text-align: center;
    padding: 4px;
    transition: color 0.3s;
}

.link-detail:hover {
    color: #0056b3;
    text-decoration: underline;
}

/* Icon styling */
.fas {
    font-size: 12px;
}

</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status OPD - LPPD Kota Pariaman</title>
</head>
<body>

<div class="header">

        <div class="nav">
            <a href="../halaman_OPD.php">Beranda</a>
            <a href="../OPD/pelaporan_IKK_OPD.php">Pelaporan IKK</a>
            <a href="../OPD/status_IKK.php">Status IKK</a>
            <a href="../OPD/ubah_password.php">Ubah Password</a>
        </div>
        <div class="user-menu">
            <img class="user-icon" src="../image/9131529.png" alt="User Icon">
            <div class="dropdown">
                <a href="../login_1.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="search-container">
    <form method="GET" action="" class="search-form">
        <input type="text" 
               name="search" 
               placeholder="Search Disini..." 
               value="<?= htmlspecialchars($search) ?>"
               class="search-input">
        <button type="submit" class="search-button">Cari</button>
        <?php if (!empty($search)): ?>
            <a href="status_IKK.php" class="reset-button">Reset</a>
        <?php endif; ?>
    </form>
</div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No IKK</th>
                    <th>Tanggal</th>
                    <th>Kategori IKK</th>
                    <th>Indikator</th>
                    <th>Kategori Masalah</th>
                    <th>Penjelasan Masalah</th>
                    <th>Total Capaian</th>
                    <th>Bukti Dok</th>
                    <th>Status Laporan</th>
                    <th>Tanggal Persetujuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['no_ikk']) ?></td>
                        <td><?= htmlspecialchars($row['tgl_pengiriman']) ?></td>
                        <td><?= htmlspecialchars($row['kategori_ikk']) ?></td>
                        <td><?= htmlspecialchars($row['indikator']) ?></td>
                        <td><?= htmlspecialchars($row['kategori_kendala_masalah']) ?></td>
                        <td><?= htmlspecialchars($row['penjelasan_kendala_masalah']) ?></td>
                        <td><?= htmlspecialchars($row['capaian']) ?>%</td>
                        <td>
                            <?php if (!empty($row['unggah_dokumen']) && file_exists("../OPD/" . $row['unggah_dokumen'])): ?>
                                <a href="../OPD/<?= htmlspecialchars($row['unggah_dokumen']) ?>" 
                                target="_blank" 
                                class="view-doc">
                                <i class="fas fa-file-alt"></i> <u>Lihat Dok</u>
                            <?php endif; ?>
                        </td>
                        <td class='status'><?= htmlspecialchars($row['status_laporan'] ?? 'Menunggu') ?></td>
                        <td><?= !empty($row['tanggal_persetujuan']) ? htmlspecialchars($row['tanggal_persetujuan']) : '-' ?></td>
                        
                        <td class="action-column">

                            <a href="hapus_laporan.php?id=<?= htmlspecialchars($row['id']) ?>" 
                                class="btn-delete" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                            </a>
                            <a href="edit_laporan.php?id=<?= htmlspecialchars($row['id']) ?>" 
                                class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="detail_laporan.php?id=<?= htmlspecialchars($row['id']) ?>" 
                            class="btn-detail">
                                Selengkapnya
                            </a>
                        </td>
    </tr>
<?php endwhile; ?>
            </tbody>
        </table>
    </div>


    <script>
    document.addEventListener("DOMContentLoaded", function () {
    const userIcon = document.querySelector(".user-icon");
    const dropdown = document.querySelector(".dropdown");

    // Toggle dropdown saat ikon user diklik
    userIcon.addEventListener("click", function () {
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });

    // Tutup dropdown jika klik di luar area user menu
    document.addEventListener("click", function (event) {
        if (!userIcon.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
    
});
</script>

</body>
</html>


