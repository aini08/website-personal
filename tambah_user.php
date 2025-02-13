<?php
session_start();
include '../db_config/db_config.php';


// Ambil kategori dari database
$kategori_query = "SELECT id_kategori, nama_kategori_ikk FROM kategori_ikk";
$result_kategori = $conn->query($kategori_query);

// Ambil daftar user dengan filter kategori (jika dipilih)
$filter_kategori = $_GET['filter_kategori'] ?? '';
$user_query = "SELECT user.user_id, user.nik, user.fullname, kategori_ikk.nama_kategori_ikk FROM user 
               JOIN kategori_ikk ON user.id_kategori = kategori_ikk.id_kategori 
               WHERE role = 'opd'";

if ($filter_kategori !== '') {
    $user_query .= " AND user.id_kategori = " . intval($filter_kategori);
}

$result_user = $conn->query($user_query);


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User OPD</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
    background: linear-gradient(to right, #1a237e, #8c9eff);
    padding: 30px;
    display: flex;
    align-items: center;
    justify-content: flex-end; /* Memindahkan header ke kanan */
    color: white;
}

.header .nav {
    display: flex;
    gap: 20px;
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
    margin-left: 10px;
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

        /* Kontainer */
        .content {
            max-width: 900px;
            margin: 30px auto; /*untuk atur jarak dengan header */
            padding: 50px; /*ukuran tabel besar kotak yang isi banyak nik,pw dll */
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Form */
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .small-btn {
            padding: 8px 12px;
            font-size: 14px;
            background-color: #1a237e;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            border-radius: 50px;
        }

        .small-btn:hover {
            background-color: #3f51b5;
        }

        /* Tabel */
        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #1a237e;
            color: white;
        }

        tr:hover {
            background-color: #f0f0f0;
        }

        .delete-btn {
            padding: 6px 10px;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
        }

        .edit-btn {
            background-color: #fbc02d;
        }

        .delete-btn {
            background-color: #d32f2f;
        }

        .edit-btn:hover {
            background-color: #ffeb3b;
        }

        .delete-btn:hover {
            background-color: #ff5252;
        }
    </style>
</head>
<body>

<div class="header">
    <div>
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

<div class="content">
    <h2>Tambah User OPD</h2>
    <form action="proses_user.php" method="POST">
        <input type="text" name="nik" placeholder="Masukkan NIK" required>
        <input type="password" name="password" placeholder="Masukkan Password" required>
        <input type="text" name="fullname" placeholder="Nama Lengkap" required>
        <select name="id_kategori" required>
            <option value="">-- Pilih Kategori IKK --</option>
            <?php while ($row = $result_kategori->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['id_kategori']) . '">' . htmlspecialchars($row['nama_kategori_ikk']) . '</option>';
            } ?>
        </select>
        <button type="submit" class="small-btn">Tambah User</button>
    </form>

    <div class="table-container">
        <h2>Daftar User OPD</h2>

        <!-- Filter Pencarian -->
        <form method="GET">
            <select name="filter_kategori" onchange="this.form.submit()">
                <option value="">-- Filter Berdasarkan Kategori --</option>
                <?php
                $result_kategori->data_seek(0); // Reset hasil query kategori
                while ($row = $result_kategori->fetch_assoc()) {
                    $selected = ($row['id_kategori'] == $filter_kategori) ? "selected" : "";
                    echo '<option value="' . htmlspecialchars($row['id_kategori']) . '" ' . $selected . '>' . htmlspecialchars($row['nama_kategori_ikk']) . '</option>';
                }
                ?>
            </select>
        </form>

        <table>
            <tr>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>Kategori</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $result_user->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['nik']) ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kategori_ikk']) ?></td>
                    <td>
                       
                        <a href="proses_user.php?user_id=<?= $row['user_id'] ?>" class="delete-btn" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    let userMenu = document.getElementById("user-menu");
    let dropdownMenu = document.getElementById("dropdown-menu");

    userMenu.addEventListener("click", function (event) {
        event.stopPropagation(); // Mencegah event klik keluar langsung menutup dropdown
        dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
    });

    document.addEventListener("click", function (event) {
        if (!userMenu.contains(event.target)) {
            dropdownMenu.style.display = "none";
        }
    });
});


</script>

</body>
</html>
