<?php
$conn = new mysqli('localhost', 'root', '', 'db_lppd');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $no_ikk = $_POST['no_ikk'];
    $tgl_pengiriman = $_POST['tgl_pengiriman'];
    $kategori_ikk = $_POST['kategori_ikk'];
    $indikator = $_POST['indikator'];
    $ikk_output = $_POST['ikk_output'];
    $ikk_outcome = $_POST['ikk_outcome'];

    $sql = "UPDATE pelaporan_IKK_PEM SET no_ikk=?, tgl_pengiriman=?, kategori_ikk=?, indikator=?, ikk_output=?, ikk_outcome=?, WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $no_ikk, $tgl_pengiriman, $kategori_ikk, $indikator, $ikk_output, $ikk_outcome, $id);
    
    if ($stmt->execute()) {
        echo "Data berhasil diperbarui!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Ambil data untuk ditampilkan
$result = $conn->query("SELECT * FROM pelaporan_pem");
?>

<?php
$conn = new mysqli('localhost', 'root', '', 'db_lppd');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Hapus Data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM pelaporan_pem WHERE id=$id");
    header("Location: halaman_PEM.php");
}

// Ambil data untuk ditampilkan
$result = $conn->query("SELECT * FROM pelaporan_pem");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPPD Kota Pariaman</title>
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
        .container { 
            max-width: 1000px; 
            margin: 20px auto; 
            padding: 20px; 
            background: white; 
            border-radius: 8px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse;
        }
        th, td { 
            padding: 12px; 
            border: 1px solid #ddd; 
            text-align: left; 
        }
        th { 
            background-color: #0d47a1; 
            color: white; 
        }
        
        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-right: 10px;
            display: inline-block;
            text-decoration: none; /* Remove underline */
        }

.edit {
    background:rgb(0, 255, 34);
    color: white;
}

.delete {
    background: #d32f2f;
    color: white;
}

/* Optionally, for better alignment and responsiveness */
.button-container {
    display: flex;
    justify-content: start;
    gap: 10px;
}


        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: linear-gradient(to right, #0000FF, #FFFFFF);
            color: white;
            text-align: center;
            padding: 2px;
        }


         /* Responsiveness */
         @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .header .nav {
                flex-direction: column;
                gap: 10px;
            }
            .container {
                width: 95%;
                padding: 15px;
            }
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav">
            <a href="halaman_PEM.php">Beranda</a>
            <a href="PEM/pelaporan_IKK_PEM.php">Pelaporan IKK</a>
            <a href="PEM/kategori_ikk.php">Kategori IKK</a>
            <a href="PEM/indikator.php">Indikator</a>
            <a href="PEM/Verifikasi_IKK.php">Verifikasi IKK</a>
            <a href="PEM/tambah_user.php">Tambah User</a>
        </div>
        <div class="user-menu" id="user-menu">
            <img class="user-icon" src="image/9131529.png" alt="User Icon">
            <div class="dropdown" id="dropdown-menu">
                <a href="login_1.php">Logout</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <h2 style="text-align: center;">Data Pelaporan IKK</h2>
        <label for="filterKategori">Filter Kategori IKK:</label>
        <select id="filterKategori">
            <option value="">Semua</option>
            <?php 
            $kategoriResult = $conn->query("SELECT DISTINCT kategori_ikk FROM pelaporan_pem");
            while ($kategori = $kategoriResult->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($kategori['kategori_ikk']) . "'>" . htmlspecialchars($kategori['kategori_ikk']) . "</option>";
            }
            ?>
        </select>
        <table>
            <thead>
                <tr>
                    <th>Nomor IKK</th>
                    <th>Tanggal Pengiriman</th>
                    <th>Kategori IKK</th>
                    <th>Indikator</th>
                    <th>IKK Output</th>
                    <th>IKK Outcome</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="dataTable">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['no_ikk']); ?></td>
                        <td><?php echo htmlspecialchars($row['tgl_pengiriman']); ?></td>
                        <td><?php echo htmlspecialchars($row['kategori_ikk']); ?></td>
                        <td><?php echo htmlspecialchars($row['indikator']); ?></td>
                        <td><?php echo htmlspecialchars($row['ikk_output']); ?></td>
                        <td><?php echo htmlspecialchars($row['ikk_outcome']); ?></td>
                        <td>
                        <div class="button-container">
                          
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            <a href="edit_pem.php?id=<?php echo $row['id']; ?>" class="btn edit">Edit</a>

                        </div>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2025 LPPD Kota Pariaman</p>
    </footer>
    
    <script>
        document.getElementById("filterKategori").addEventListener("change", function() {
            let filterValue = this.value.toLowerCase();
            let rows = document.querySelectorAll("#dataTable tr");
            rows.forEach(row => {
                let kategori = row.children[2].textContent.toLowerCase();
                row.style.display = filterValue === "" || kategori.includes(filterValue) ? "" : "none";
            });
        });

        document.getElementById("user-menu").addEventListener("click", function(event) {
            event.stopPropagation();
            let dropdownMenu = document.getElementById("dropdown-menu");
            dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
        });

        document.addEventListener("click", function() {
            let dropdownMenu = document.getElementById("dropdown-menu");
            if (dropdownMenu.style.display === "block") {
                dropdownMenu.style.display = "none";
            }
        });
    </script>
</body>
</html>