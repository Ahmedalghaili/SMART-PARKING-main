<!DOCTYPE html>
<html>

<head>
    <title>Parking Locations by Faculty</title>
</head>

<body>
    <h2>Parking Locations by Faculty</h2>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "puiii";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $facultyQuery = "SELECT * FROM faculties";
    $facultyResult = $conn->query($facultyQuery);

    if ($facultyResult->num_rows > 0) {
        while ($faculty = $facultyResult->fetch_assoc()) {
            $faculty_id = $faculty["faculty_id"];
            $faculty_name = $faculty["faculty_name"];

            echo "<h3>$faculty_name</h3>";
            echo "<ul>";

            $areaQuery = "SELECT * FROM parkingareas WHERE faculty_id = $faculty_id";
            $areaResult = $conn->query($areaQuery);

            if ($areaResult->num_rows > 0) {
                while ($area = $areaResult->fetch_assoc()) {
                    $parking_area = $area["area_type"];

                    echo "<li>";
                    echo "<strong>$parking_area</strong>: ";
                    echo '<a href="https://www.google.com/maps?q='  . '" target="_blank">View on Google Maps</a>';
                    echo "</li>";
                }
            } else {
                echo "<li>No parking areas found for this faculty.</li>";
            }

            echo "</ul>";
        }
    } else {
        echo "No faculties found.";
    }

    $conn->close();
    ?>
</body>

</html>