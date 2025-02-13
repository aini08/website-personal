<?php
session_start();
include '../db_config/db_config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_1.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? $_GET['id'] : '';

if (!empty($id)) {
    // Hapus data
    $query = "DELETE FROM pelaporan_opd WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Redirect dengan pesan sukses
        header("Location: status_IKK.php?success=delete");
    } else {
        // Redirect dengan pesan error
        header("Location: status_IKK.php?error=delete");
    }
} else {
    // Redirect jika tidak ada ID
    header("Location: status_IKK.php?error=invalid");
}
exit();
?>