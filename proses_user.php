<?php
session_start();
include '../db_config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik = trim($_POST['nik']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fullname = trim($_POST['fullname']);
    $id_kategori = intval($_POST['id_kategori']);

    // Cek apakah NIK sudah ada
    $check_query = "SELECT nik FROM user WHERE nik = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $nik);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('NIK SUDAH TERDAFTAR!'); window.location.href='tambah_user.php';</script>";
        exit();
    }

    // Insert ke database
    $insert_query = "INSERT INTO user (nik, password, fullname, id_kategori, role) VALUES (?, ?, ?, ?, 'opd')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssi", $nik, $password, $fullname, $id_kategori);

    if ($stmt->execute()) {
        echo "<script>alert('User berhasil ditambahkan!'); window.location.href='tambah_user.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan user!'); window.location.href='tambah_user.php';</script>";
    }

    $stmt->close();
    $conn->close();
}

// Proses hapus user
if (isset($_GET['user_id'])) {
    $id = intval($_GET['user_id']);
    $delete_query = "DELETE FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('User berhasil dihapus!'); window.location.href='tambah_user.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user!'); window.location.href='tambah_user.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
