<!-- <?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "db_lppd";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $no_ikk = $_POST['no_ikk'];
    $tgl_pengiriman = $_POST['tgl_pengiriman'];
    $kategori_ikk = $_POST['kategori_ikk'];
    $indikator = $_POST['indikator'];
    $ikk_output = $_POST['ikk_output'];
    $ikk_outcome = $_POST['ikk_outcome'];
    $kendala = $_POST['kendala'];

    $query = "UPDATE pelaporan_pem SET no_ikk = ?, tgl_pengiriman = ?, kategori_ikk = ?, indikator = ?, ikk_output = ?, ikk_outcome = ?, kendala = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssi", $no_ikk, $tgl_pengiriman, $kategori_ikk, $indikator, $ikk_output, $ikk_outcome, $kendala, $id);

    if ($stmt->execute()) {
        echo "Data berhasil diperbarui.";
    } else {
        echo "Gagal memperbarui data: " . $conn->error;
    }
}
?>
