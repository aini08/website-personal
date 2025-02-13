<?php
session_start();
include '../db_config/db_config.php';

if(isset($_GET['file'])) {
    $file = $_GET['file'];
    // Sesuaikan path dengan struktur folder yang benar
    $file_path = "../OPD/" . $file;
    
    if(file_exists($file_path)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        die("File tidak ditemukan di: " . $file_path);
    }
}

// Debugging
echo "File request: " . ($_GET['file'] ?? 'none') . "<br>";
echo "Looking in path: ../OPD/" . ($_GET['file'] ?? '');
?>