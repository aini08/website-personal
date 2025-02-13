<?php
session_start();
include '../db_config/db_config.php'; // Pastikan file ini berisi koneksi ke database

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Ambil ID pengguna dari sesi
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        die("Semua field harus diisi!");
    }

    if ($new_password !== $confirm_password) {
        die("Konfirmasi password tidak cocok!");
    }

    // Ambil password lama dari database
    $stmt = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verifikasi password lama
        if (password_verify($old_password, $hashed_password)) {
            // Hash password baru
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password di database
            $update_stmt = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
            $update_stmt->bind_param("si", $new_hashed_password, $user_id);
            if ($update_stmt->execute()) {
                echo "Password berhasil diubah!";
            } else {
                echo "Terjadi kesalahan saat mengubah password.";
            }
        } else {
            echo "Password lama salah!";
        }
    } else {
        echo "User tidak ditemukan.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <style>
/* Header */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 110px 0 0 0; /* Tambah padding atas lebih besar biar konten gak ketutup header */
    background-color: #f4f4f4;
}

.header {
    background: linear-gradient(to right, #1a237e, #8c9eff);
    padding: 30px 0; /* Tetap 30px seperti permintaan */
    display: flex;
    align-items: center; /* Memastikan semua item di tengah vertikal */
    justify-content: flex-end; /* Menu tetap ke kanan */
    color: white;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
}

.nav {
    display: flex;
    align-items: center; /* Pusatkan teks di tengah vertikal */
    gap: 15px;
}

.header a {
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
    border-radius: 50%;
    cursor: pointer;
    margin-right: 20px;
}

/* Dropdown */
.dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 40px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 4px;
    z-index: 1000;
    padding: 5px 0;
}

.dropdown a {
    display: block;
    padding: 10px 20px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
}

.form-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 350px;
    text-align: center;
    margin: auto;
    margin-top: 20px; /* Jarak dari header */
    display: flex;
    flex-direction: column;
    align-items: center; /* Pusatkan semua elemen */
    justify-content: center;
}

/* Form Group */
.form-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    margin-bottom: 10px; /* Biar ada jarak antar input */
}

/* Label */
label {
    text-align: center; /* Buat teks label di tengah */
    font-weight: bold;
    margin-bottom: 5px;
    font-size: 13px;
    color: #333;
}

/* Input */
input[type="password"] {
    padding: 10px;
    border: 1px solid #bdbdbd;
    border-radius: 5px;
    width: 80%;
    background: #fafafa;
    text-align: center;
}

/* Button */
button {
    margin-top: 10px;
    padding: 10px;
    background-color: #ff7043;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background-color: #e64a19;
}


/* Responsiveness */
@media (max-width: 600px) {
    .header {
        padding: 30px; /* Tetap 30px di mobile */
    }

    .header a {
        font-size: 14px;
    }

    .nav {
        gap: 10px;
    }

    .container {
        width: 95%;
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

    <?php if ($error_message): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <form method="POST" class="form-container">

<div class="form-group">
    <label>Password Lama:</label>
    <input type="password" name="old_password" required>
</div>

<div class="form-group">
    <label>Password Baru:</label>
    <input type="password" name="new_password" required>
</div>

<div class="form-group">
    <label>Konfirmasi Password:</label>
    <input type="password" name="confirm_password" required>
</div>

<button type="submit">Ubah Password</button>

</form>

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
