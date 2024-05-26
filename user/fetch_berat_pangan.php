<?php
include '../db/configdb.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id !== null) {
    $sql = "SELECT berat_pangan FROM pendataan WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        echo $row['berat_pangan'];
    } else {
        echo "No data found for the given id.";
    }

    $stmt->close();
} else {
    echo "No id provided.";
}

$conn->close();
?><?php
include '../db/configdb.php'; // Include your database configuration file

$sql = "SELECT DISTINCT berat_pangan FROM pendataan";
$result = $conn->query($sql);

$beratPanganData = [];
while ($row = $result->fetch_assoc()) {
    $beratPanganData[] = $row['berat_pangan'];
}

echo json_encode($beratPanganData);

$conn->close();
?>