<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "puiii";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$area_id = $_POST["area_id"]; // Assuming this comes from form submission
$area_type = $_POST["area_type"]; // Assuming this comes from form submission

$sql = "SELECT unavailable_spaces, available_spaces FROM parkingareas WHERE area_id = $area_id AND area_type = '$area_type'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data = array(
        'unavailable_spaces' => $row["unavailable_spaces"],
        'available_spaces' => $row["available_spaces"]
    );
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    echo "No data available for the selected area.";
}

$conn->close();
?>