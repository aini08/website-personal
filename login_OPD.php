<?php
session_start(); // Pastikan session dimulai sebelum digunakan

include '../db_config/db_config.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = trim($_POST['nik']);
    $password = trim($_POST['password']);

    if ($nik === '' || $password === '') {
        $error_message = "NIK atau Password tidak boleh kosong!";
    } else {
        // Query untuk mengambil data user berdasarkan NIK
        $query = "SELECT user.*, kategori_ikk.id_kategori
                  FROM user 
                  LEFT JOIN kategori_ikk ON user.id_kategori = kategori_ikk.id_kategori 
                  WHERE user.nik = ?";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $nik);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Gunakan password_verify untuk mencocokkan password
                if (password_verify($password, $user['password']) && $user['role'] === 'opd') {
                    // Set session setelah login berhasil
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['nik'] = $user['nik'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['id_kategori'] = $user['id_kategori']; // Simpan ID kategori

                    // Redirect ke halaman OPD setelah login
                    header('Location: ../halaman_OPD.php');
                    exit;
                } else {
                    $error_message = "NIK atau Password salah!";
                }
            } else {
                $error_message = "NIK atau Password salah!";
            }
            $stmt->close();
        } else {
            $error_message = "Terjadi kesalahan sistem: " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login OPD</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f0f0f0;
    }

    .container {
      width: 100%;
      height: 100%;
      position: relative;
      overflow: hidden;
    }

    .background img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      z-index: 1;
    }

    .login-box {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      padding: 50px;
      width: 300px;
      z-index: 2;
      text-align: center;
    }

    .blue-bar {
    width: 100%; 
    height: 35px;
    background-color: #1a237e;
    position: absolute;
    bottom: -0px; 
    left: 50%;
    transform: translateX(-50%);
    border-radius: 5px;
   
}

    .blue-bar.top,
.blue-bar.bottom {
    width: 100%; 
    height: 45px;
    background-color: #1a237e;
    position: absolute;
    left: 50%;
    transform: translateX(-50%); 
    border-radius: 6px;
    z-index: 2;
}

.blue-bar.top {
    top: 0;
}

.blue-bar.bottom {
    bottom: 0;
}

    .header .logo {
      width: 80px;
      height: auto;
    }

    h2 {
      font-size: 20px;
      color: #333;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    form input {
      margin: 10px 0;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }

    .button-container {
      display: flex;
      justify-content: center;
    }

    form button {
      padding: 10px;
      background-color: rgb(237, 237, 244);
      color: black;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      width: 200px;
      border: 1px solid #0056b3;
    }

    form button:hover {
      background-color: #0056b3;
      color: white;
    }

    .login-pem {
      margin-top: 10px;
      display: block;
      color: #007bff;
      text-decoration: none;
      font-size: 14px;
    }

    .login-pem:hover {
      text-decoration: underline;
    }

    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 15px;
    }
  </style>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const form = document.querySelector("form");
      form.addEventListener("submit", function (event) {
        const nik = form.querySelector("input[name='nik']").value.trim();
        const password = form.querySelector("input[name='password']").value.trim();

        if (!nik || !password) {
          event.preventDefault();
          alert("NIK dan Password harus diisi!");
        }
      });
    });
  </script>
</head>
<body>
  <div class="container">
    <div class="background">
      <img src="../image/gedung bappeda.jpg" alt="Background Gedung">
    </div>
    <div class="login-box">
    <div class="blue-bar top"></div>
      <div class="header">
        <img src="../image/logo kota pariaman sumbar.png" alt="Logo Kota" class="logo">
        <h2>Login OPD</h2>
      </div>
      <?php if ($error_message): ?>
      <div class="error"><?php echo $error_message; ?></div>
      <?php endif; ?>
      <form action="" method="POST">
        <input type="text" name="nik" placeholder="NIK" required>
        <input type="password" name="password" placeholder="Password" required>
        <div class="button-container">
          <button type="submit">Masuk</button>
        </div>
        <a href="../PEM/login_PEM.php" class="login-pem">Login PEM</a>
      </form>
      <div class="blue-bar"></div> 
    </div>
  </div>
</body>
</html>
