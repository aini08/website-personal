<?php
// config.php - Database Configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "db_lppd";

$anu = new mysqli($host, $username, $password, $dbname);

if ($anu->connect_error) {
    die("Koneksi database gagal: " . $anu->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... existing code untuk validasi input ...

    $capaian = ($angka_pembilang != 0) ? ($angka_penyebut / $angka_pembilang) * 100 : 0;
    
    // ... existing code untuk upload dokumen ...

    // Mulai transaksi
    $anu->begin_transaction();

    try {
        // Update tabel pelaporan_opd dengan status_laporan
        $insertQuery = "INSERT INTO pelaporan_opd (
            id,
            user_id, 
            tgl_pengiriman, 
            kategori_kendala_masalah, 
            penjelasan_kendala_masalah, 
            penyebut, 
            pembilang, 
            angka_penyebut, 
            angka_pembilang, 
            capaian, 
            unggah_dokumen,
            status_laporan
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'menunggu')";
        
        $insertStmt = $anu->prepare($insertQuery);
        $insertStmt->bind_param(
            "iisssssddds", 
            $id, 
            $user_id,
            $tgl_pengiriman, 
            $kategori_kendala_masalah, 
            $penjelasan_kendala_masalah,  
            $penyebut, 
            $pembilang, 
            $angka_penyebut, 
            $angka_pembilang, 
            $capaian, 
            $unggah_dokumen
        );

        if ($insertStmt->execute()) {
            $anu->commit();
            header("Location: status_IKK.php"); // Redirect ke halaman status
            exit();
        } else {
            throw new Exception("Gagal menyimpan data");
        }
    } catch (Exception $e) {
        $anu->rollback();
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>