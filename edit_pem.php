<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_lppd";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Check if ID is set
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Retrieve data for the selected ID
    $sql = "SELECT id, no_ikk, kategori_ikk, indikator, tgl_pengiriman, ikk_output, ikk_outcome FROM pelaporan_pem WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $no_ikk = $_POST['no_ikk'];
    $kategori_ikk = $_POST['kategori_ikk'];
    $indikator = $_POST['indikator'];
    $tgl_pengiriman = !empty($_POST['tgl_pengiriman']) ? $_POST['tgl_pengiriman'] : date('Y-m-d');
    $ikk_output = $_POST['ikk_output'];
    $ikk_outcome = $_POST['ikk_outcome'];
    
    // Update query
    $sql = "UPDATE pelaporan_pem SET no_ikk=?, kategori_ikk=?, indikator=?, tgl_pengiriman=?, ikk_output=?, ikk_outcome=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $no_ikk, $kategori_ikk, $indikator, $tgl_pengiriman, $ikk_output, $ikk_outcome, $id);
    
    if ($stmt->execute()) {
        $message = "Data successfully updated!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    
    // Refresh data after update
    header("Location: edit_pem.php?id=$id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Data IKK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 50px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: auto;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            margin-top: 10px;
            padding: 10px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Data IKK</h2>
        <?php if (!empty($message)) { echo "<div class='message'>$message</div>"; } ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
            <label>No IKK:</label>
            <input type="text" name="no_ikk" value="<?php echo $data['no_ikk']; ?>">
            <label>Kategori:</label>
            <input type="text" name="kategori_ikk" value="<?php echo $data['kategori_ikk']; ?>">
            <label>Indikator:</label>
            <input type="text" name="indikator" value="<?php echo $data['indikator']; ?>">
            <label>Tanggal Pengiriman:</label>
            <input type="date" name="tgl_pengiriman" value="<?php echo $data['tgl_pengiriman']; ?>">
            <label>IKK Output:</label>
            <input type="text" name="ikk_output" value="<?php echo $data['ikk_output']; ?>">
            <label>IKK Outcome:</label>
            <input type="text" name="ikk_outcome" value="<?php echo $data['ikk_outcome']; ?>">
            <button type="submit">Update Data</button>
            <button type="button" onclick="window.location.href='halaman_PEM.php'" style="background-color: #dc3545;">Kembali</button>
        </form>
    </div>
</body>
</html>