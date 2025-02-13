<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_lppd";

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan tabel memiliki kolom id_kategori di indikator
$sql_create_table = "CREATE TABLE IF NOT EXISTS indikator (
    id_indikator INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    nama_indikator VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_kategori) REFERENCES kategori_ikk(id_kategori) ON DELETE CASCADE
)";
$conn->query($sql_create_table);

// Ambil daftar kategori sekali dan simpan dalam array
$kategori_list = [];
$result_kategori = $conn->query("SELECT * FROM kategori_ikk");
while ($row = $result_kategori->fetch_assoc()) {
    $kategori_list[] = $row;
}

// Ambil daftar kategori
$result_kategori = $conn->query("SELECT * FROM kategori_ikk");

// Tambah indikator
if (isset($_POST['tambah'])) {
    $nama_indikator = trim($_POST['nama_indikator']);
    $id_kategori = intval($_POST['id_kategori']);

    if (!empty($nama_indikator) && $id_kategori > 0) {
        $stmt = $conn->prepare("INSERT INTO indikator (id_kategori, nama_indikator) VALUES (?, ?)");
        $stmt->bind_param("is", $id_kategori, $nama_indikator);
        $stmt->execute();
        echo "<script>alert('Indikator Berhasil Ditambahkan.'); window.location.href='indikator.php';</script>";
    }
}

// Hapus indikator
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $conn->query("DELETE FROM indikator WHERE id_indikator = $id_hapus");
    echo "<script>alert('Indikator Berhasil Dihapus.'); window.location.href='indikator.php';</script>";
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $result_edit = $conn->query("SELECT * FROM indikator WHERE id_indikator = $id_edit");
    $edit_data = $result_edit->fetch_assoc();
}

// Update indikator
if (isset($_POST['update'])) {
    $id_update = intval($_POST['id_indikator']);
    $nama_indikator = trim($_POST['nama_indikator']);
    $id_kategori = intval($_POST['id_kategori']);

    $stmt = $conn->prepare("UPDATE indikator SET id_kategori = ?, nama_indikator = ? WHERE id_indikator = ?");
    $stmt->bind_param("isi", $id_kategori, $nama_indikator, $id_update);
    $stmt->execute();
    echo "<script>alert('Indikator Berhasil Diperbarui.'); window.location.href='indikator.php';</script>";
}

// Ambil daftar indikator
$query_indikator = "SELECT indikator.*, kategori_ikk.nama_kategori_ikk 
                    FROM indikator 
                    JOIN kategori_ikk ON indikator.id_kategori = kategori_ikk.id_kategori";
$result = $conn->query($query_indikator);


$filter_kategori = isset($_POST['filter_kategori']) ? intval($_POST['filter_kategori']) : 0;
$query_indikator = "SELECT indikator.*, kategori_ikk.nama_kategori_ikk 
                    FROM indikator 
                    JOIN kategori_ikk ON indikator.id_kategori = kategori_ikk.id_kategori";
if ($filter_kategori > 0) {
    $query_indikator .= " WHERE indikator.id_kategori = $filter_kategori";
}
$result = $conn->query($query_indikator);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Indikator</title>
    <style>

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* HEADER */
.header {
            background: linear-gradient(to right, #1a237e, #8c9eff);
            padding: 30px;
            display: flex;
            align-items: center;
            color: white;
        }

        .header .nav {
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

        .header .user-icon {
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

/* FORM */
h2 {
    color: #333;
    text-align: center;
    margin-top: 20px;
}

form {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    max-width: 500px;
}

select, input, button {
    padding: 10px;
    margin: 10px 0;
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

button {
    background: #28a745;
    color: white;
    font-weight: bold;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #218838;
}

/* GAYA UNTUK FILTER DI DALAM TABEL */
.filter-row {
    text-align: left;
    padding: 8px;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Pastikan ke kiri */
    width: 1500%; /* Tambahin ini biar nggak center otomatis */
}

.filter-row select {
    padding: 8px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    cursor: pointer;
    background-color: white;
    width: 200px; /* Ukuran dropdown */
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    margin-top: 10px;
}

th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}

th {
    background: #007bff;
    color: white;
}

/* BUTTON ACTION */
.action-btn {
    text-decoration: none;
    padding: 8px 12px;
    margin: 3px;
    border-radius: 5px;
    color: white;
    display: inline-block;
    transition: 0.3s;
}

.edit-btn {
    background: #ffc107;
}

.delete-btn {
    background: #dc3545;
}

.edit-btn:hover {
    background: #e0a800;
}

.delete-btn:hover {
    background: #c82333;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .header {
        flex-direction: column;
        text-align: center;
    }
    
    .nav {
        flex-direction: column;
        gap: 10px;
    }
    
    form {
        width: 90%;
    }
    
    table {
        font-size: 14px;
    }

/* RESPONSIVE */
@media (max-width: 768px) {
    .table-filters {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .table-filters form {
        width: 100%;
    }
}
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

    <h2><?= $edit_data ? "Edit" : "Tambah"; ?> Indikator</h2>
    <form method="POST">
        <input type="hidden" name="id_indikator" value="<?= $edit_data['id_indikator'] ?? ''; ?>">
        
        <label for="id_kategori"></label>
        <select name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php while ($row = $result_kategori->fetch_assoc()) { ?>
                <option value="<?= $row['id_kategori']; ?>" 
                    <?= isset($edit_data) && $edit_data['id_kategori'] == $row['id_kategori'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($row['nama_kategori_ikk']); ?>
                </option>
            <?php } ?>
        </select>
        
        <input type="text" name="nama_indikator" placeholder="Masukkan Indikator" 
               value="<?= htmlspecialchars($edit_data['nama_indikator'] ?? '') ?>" required>

        <button type="submit" name="<?= $edit_data ? 'update' : 'tambah' ?>">
            <?= $edit_data ? 'Update' : 'Tambah' ?>
        </button>
    </form>

    <h2>Daftar Indikator</h2>
    <table>
    <thead>
        <tr>
            <th colspan="4">
                <div class="table-row">
                    <form method="POST">
                        <select name="filter_kategori" id="filter_kategori" onchange="this.form.submit()">
                            <option value="0">-- Semua Kategori --</option>
                            <?php foreach ($kategori_list as $row) { ?>
                                <option value="<?= $row['id_kategori']; ?>" <?= $filter_kategori == $row['id_kategori'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($row['nama_kategori_ikk']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </form>
        <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Indikator</th>
            <th>Aksi</th>
        </tr>
        <?php 
        $no = 1;
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama_kategori_ikk']); ?></td>
                <td><?= htmlspecialchars($row['nama_indikator']); ?></td>
                <td>
                    <a href="indikator.php?edit=<?= $row['id_indikator']; ?>" class="action-btn edit-btn">Edit</a>
                    <a href="indikator.php?hapus=<?= $row['id_indikator']; ?>" class="action-btn delete-btn" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </table>

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
})
    </script>

</body>
</html>

<?php $conn->close(); ?>
