<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "puiii";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_id = $_POST["vehicle_id"];

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to find the parking details of the vehicle
    $sql = "SELECT pa.area_id, pa.area_name, pa.available_spaces, pa.unavailable_spaces
            FROM parkingareas pa
            INNER JOIN parkingtransactions pt ON pa.area_id = pt.area_id
            WHERE pt.vehicle_id = '$vehicle_id' AND pt.exit_time IS NULL";

    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // Vehicle found in parking
            $row = $result->fetch_assoc();
            $area_id = $row["area_id"];
            $area_name = $row["area_name"];
            $available_spaces = $row["available_spaces"];
            $unavailable_spaces = $row["unavailable_spaces"];

            echo "Vehicle with ID " . $vehicle_id . " is parked in Area ID: " . $area_id . ", Area Name: " . $area_name . "<br>";
            echo "Available Spaces: " . $available_spaces . ", Unavailable Spaces: " . $unavailable_spaces;
        } else {
            // Vehicle not found in parking
            echo "Vehicle with ID " . $vehicle_id . " is not currently parked.";
        }
    } else {
        // Error in SQL query
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>