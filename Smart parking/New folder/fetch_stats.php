<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "puiii";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$faculty_id = $_POST['faculty_id'];
$area_type = $_POST['area_type'];

$sql = "SELECT available_spaces, unavailable_spaces FROM parkingareas WHERE faculty_id = $faculty_id AND area_type = '$area_type'";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data = array(
            'unavailable_spaces' => $row["unavailable_spaces"],
            'available_spaces' => $row["available_spaces"]
        );
    }
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    echo json_encode(array('error' => 'No data available for the selected area.'));
}

$conn->close();
?>