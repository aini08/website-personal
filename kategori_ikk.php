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

// Pastikan tabel kategori_ikk ada
$sql_create_table = "CREATE TABLE IF NOT EXISTS kategori_ikk (
    id_kategori INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nama_kategori_ikk VARCHAR(255) NOT NULL
)";
$conn->query($sql_create_table);

// Fungsi untuk menambah kategori
// Fungsi untuk menambah kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    if (!empty($_POST['nama_kategori_ikk'])) {
        $nama_kategori_ikk = trim($_POST['nama_kategori_ikk']);

        // Cek apakah nama kategori sudah ada di database
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM kategori_ikk WHERE nama_kategori_ikk = ?");
        $stmt_check->bind_param("s", $nama_kategori_ikk);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            // Jika sudah ada, tampilkan alert
            echo "<script>alert('Nama Dinas Sudah Ada'); window.location.href='kategori_ikk.php';</script>";
        } else {
            // Jika belum ada, tambahkan ke database
            $stmt = $conn->prepare("INSERT INTO kategori_ikk (nama_kategori_ikk) VALUES (?)");
            $stmt->bind_param("s", $nama_kategori_ikk);

            if ($stmt->execute()) {
                echo "<script>alert('Kategori IKK Berhasil Ditambahkan.'); window.location.href='kategori_ikk.php';</script>";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}


// Fungsi untuk menghapus kategori
if (isset($_GET['hapus'])) {
    $id_kategori = intval($_GET['hapus']);

    if ($id_kategori > 0) {
        $stmt = $conn->prepare("DELETE FROM kategori_ikk WHERE id_kategori = ?");
        $stmt->bind_param("i", $id_kategori);

        if ($stmt->execute()) {
            echo "<script>alert('Kategori IKK berhasil dihapus.'); window.location.href='kategori_ikk.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Menampilkan daftar kategori
$result = $conn->query("SELECT * FROM kategori_ikk ORDER BY id_kategori ASC");
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
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

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
            margin-left: auto; 
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
            margin-left: 15px;
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

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        input[type="text"] {
            width: 80%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #28a745;
            color: white;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .delete-button {
            background-color: #FF3030;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .delete-button a {
            color: white;
            text-decoration: none;
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
        <div class="user-menu" id="user-menu">
            <img class="user-icon" src="../image/9131529.png" alt="User Icon">
            <div class="dropdown" id="dropdown-menu">
                <a href="../login_1.php">Logout</a>
            </div>
    </div>
    </div>

<div class="container">
    <h2>Tambah Kategori IKK</h2>
    <form method="POST" action="">
        <input type="text" name="nama_kategori_ikk" placeholder="Nama Kategori IKK" required>
        <button type="submit" name="tambah">Tambah</button>
    </form>

    <h2>Daftar Kategori IKK</h2>
    <table>
        <tr>
            <th>No</th>
            <th>Nama Kategori IKK</th>
            <th>Aksi</th>
        </tr>
        <?php 
        $no = 1;
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama_kategori_ikk']); ?></td>
                <td>
                    <button class="delete-button">
                        <a href="kategori_ikk.php?hapus=<?= $row['id_kategori']; ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </button>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<script>
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

</body>
</html>

<?php
$conn->close();
?>
