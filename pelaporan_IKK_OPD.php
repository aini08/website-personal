<?php
session_start();
include '../db_config/db_config.php';

// Cek login dan id_kategori
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_1.php");
    exit();
}

// Ambil data session
$user_id = $_SESSION['user_id'];

// Database connection
try {
    $host = "localhost";
    $dbname = "db_lppd";
    $username = "root";
    $password = "";
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Query untuk mengambil data berdasarkan user_id
$query = "
    SELECT DISTINCT
        p.id,
        p.no_ikk, 
        p.tgl_pengiriman, 
        p.kategori_ikk, 
        p.indikator, 
        p.ikk_output, 
        p.ikk_outcome, 
        o.penjelasan_kendala_masalah,
        o.status_laporan
    FROM pelaporan_pem p
    LEFT JOIN pelaporan_opd o ON p.no_ikk = o.no_ikk AND o.user_id = :user_id
    INNER JOIN user u ON u.user_id = :user_id
    WHERE p.kategori_ikk = (
        SELECT k.nama_kategori_ikk 
        FROM kategori_ikk k 
        INNER JOIN user u ON k.id_kategori = u.id_kategori 
        WHERE u.user_id = :user_id
    )
    AND (
        o.id IS NULL 
        OR o.status_laporan = 'ditolak'
    )";

// Tambahkan kondisi pencarian jika ada
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $query .= " AND (
        p.no_ikk LIKE :keyword 
        OR p.kategori_ikk LIKE :keyword 
        OR p.indikator LIKE :keyword
    )";
}

if (isset($_GET['date']) && !empty($_GET['date'])) {
    $query .= " AND p.tgl_pengiriman = :date";
}

$query .= " ORDER BY p.no_ikk ASC";

try {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    
    if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
        $keyword = "%" . $_GET['keyword'] . "%";
        $stmt->bindParam(':keyword', $keyword);
    }
    
    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $stmt->bindParam(':date', $_GET['date']);
    }
    
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error executing query: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelaporan OPD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e3f2fd; 
        }

        .header {
            background: linear-gradient(to right, #1a237e, #8c9eff); 
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            margin-bottom: 20px;
        }

        .header .nav {
            display: flex;
            align-items: center;
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
            margin-left: auto;
        }

        .header .user-icon {
            width: 30px;
            height: 30px;
            cursor: pointer;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 2px solid rgb(11, 11, 11);
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f4f4f4;
        }

        .search-container {
    margin-top: -10px;     /* Atur margin atas */
    margin-left: 5px;   /* Pertahankan margin kiri */
    margin-right: 15px;  /* Pertahankan margin kanan */
    margin-bottom: 10px; /* Pertahankan margin bawah */
    padding: 12px;
    background-color: #e3f2fd;
    width: 600px;
    float: left;
}

.search-container form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    flex: 1;
    min-width: 180px;
}

.search-box input[type="text"] {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #90caf9;  /* Sesuaikan warna border dengan tema biru */
    border-radius: 4px;
    font-size: 13px;
    transition: border-color 0.3s;
}

.search-box input[type="text"]:focus {
    border-color: #2196f3;  /* Warna biru yang lebih tua saat focus */
    outline: none;
    box-shadow: 0 0 5px rgba(33, 150, 243, 0.2);
}

input[type="date"] {
    padding: 7px 12px;
    border: 1px solid #90caf9;  /* Sesuaikan warna border */
    border-radius: 4px;
    font-size: 13px;
    transition: border-color 0.3s;
}

input[type="date"]:focus {
    border-color: #2196f3;  /* Warna biru yang lebih tua saat focus */
    outline: none;
    box-shadow: 0 0 5px rgba(33, 150, 243, 0.2);
}

button[type="submit"] {
    padding: 8px 16px;
    background-color: #2196f3;  /* Sesuaikan warna button dengan tema biru */
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #1976d2;  /* Warna biru yang lebih gelap saat hover */
}

/* Responsif untuk layar kecil */
@media (max-width: 650px) {
    .search-container {
        width: 95%;
        margin: 10px auto;
        float: none;
    }
    
    .search-container form {
        flex-direction: column;
    }
    
    .search-box {
        width: 100%;
    }
    
    input[type="date"] {
        width: 100%;
    }
    
    button[type="submit"] {
        width: 100%;
    }
}

    </style>
</head>
<body>

<div class="header">
    <div class="nav">
        <a href="../halaman_OPD.php">Beranda</a>
        <a href="../OPD/pelaporan_IKK_OPD.php">Pelaporan IKK</a>
        <a href="../OPD/status_IKK.php">Status IKK</a>
        <a href="../OPD/ubah_password.php">Ubah Password</a>
        <div class="user-menu">
            <img class="user-icon" src="../image/9131529.png" alt="User Icon">
            <div class="dropdown">
                <a href="../login_1.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Search Section -->
<div class="search-container">
    <form method="get" action="">
        <div class="search-box">
            <input type="text" name="keyword" placeholder="Cari..." 
                   value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
        </div>
        <input type="date" name="date" 
               value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>">
        <button type="submit">Cari</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>No IKK</th>
            <th>Tanggal Pengiriman</th>
            <th>Kategori</th>
            <th>Indikator</th>
            <th>IKK Output</th>
            <th>IKK Outcome</th>
            <th>Kendala Masalah</th>
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($data) {
            foreach ($data as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['no_ikk']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tgl_pengiriman']) . "</td>";
                echo "<td>" . htmlspecialchars($row['kategori_ikk']) . "</td>";
                echo "<td>" . htmlspecialchars($row['indikator']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ikk_output']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ikk_outcome']) . "</td>";
                echo "<td>" . htmlspecialchars($row['penjelasan_kendala_masalah'] ?? 'Belum diisi') . "</td>";
                echo "<td>";
                echo "<a href='kelola_data.php?id=" . htmlspecialchars($row['id']) . "'>";
                echo "<button class='btn-edit'>";
                echo $row['status_laporan'] === 'ditolak' ? 'Perbaiki' : 'Kelola Data';
                echo "</button>";
                echo "</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>Tidak ada data yang perlu dilaporkan.</td></tr>";
        }
        ?>
    </tbody>
</table>
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