<?php
// Database configuration (should be in a separate file for security)
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'db_lppd';

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate input data
        $no_ikk = isset($_POST['no_ikk']) ? htmlspecialchars($_POST['no_ikk']) : '';
        $kategori_ikk = isset($_POST['kategori']) ? htmlspecialchars($_POST['kategori']) : '';
        $indikator = isset($_POST['indikator']) ? htmlspecialchars($_POST['indikator']) : '';
        $ikk_output = isset($_POST['ikk_output']) ? htmlspecialchars($_POST['ikk_output']) : '';
        $ikk_outcome = isset($_POST['ikk_outcome']) ? htmlspecialchars($_POST['ikk_outcome']) : '';
        $pembilang = isset($_POST['pembilang']) ? htmlspecialchars($_POST['pembilang']) : '';
        $penyebut = isset($_POST['penyebut']) ? htmlspecialchars($_POST['penyebut']) : '';
        $tgl_pengiriman = isset($_POST['tgl_pengiriman']) ? htmlspecialchars($_POST['tgl_pengiriman']) : date('Y-m-d');
        // $kendala_masalah = isset($_POST['penjelasan_kendala_masalah']) ? htmlspecialchars($_POST['kendala_masalah']) : '';


        // Validate required fields (you can expand this with other validations as needed)
        if (empty($no_ikk) || empty($kategori_ikk) || empty($indikator)) {
            throw new Exception('No IKK, Kategori, and Indikator are required fields.');
        }

        // Prepare and execute the SQL statement to insert data
        $stmt = $pdo->prepare("INSERT INTO pelaporan_pem (no_ikk, kategori_ikk, indikator, ikk_output, ikk_outcome, penyebut, pembilang, tgl_pengiriman) 
                               VALUES (:no_ikk, :kategori_ikk, :indikator, :ikk_output, :ikk_outcome, :penyebut, :pembilang, :tgl_pengiriman)");
        $stmt->bindParam(':no_ikk', $no_ikk);
        $stmt->bindParam(':kategori_ikk', $kategori_ikk);
        $stmt->bindParam(':indikator', $indikator);
        $stmt->bindParam(':ikk_output', $ikk_output);
        $stmt->bindParam(':ikk_outcome', $ikk_outcome);
        $stmt->bindParam(':pembilang', $pembilang);
        $stmt->bindParam(':penyebut', $penyebut);
        $stmt->bindParam(':tgl_pengiriman', $tgl_pengiriman);
        // $stmt->bindParam(':penjelasan_kendala_masalah', $penjelasan_kendala_masalah);

        // Execute the query
        $stmt->execute();

        // After successful insert, redirect to the pelaporan_ikk_pem.php page with success=true
        header("Location: pelaporan_ikk_pem.php?success=true");
        exit();
    }

} catch (PDOException $e) {
    // Display error message
    echo "Database Error: " . $e->getMessage();
} catch (Exception $e) {
    // Display validation or custom error messages
    echo "Error: " . $e->getMessage();
}
?>
