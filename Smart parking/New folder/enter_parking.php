<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "puiii";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $area_id = $_POST["area_id"];

    // Start a transaction
    $conn->begin_transaction();

    $checkAvailability = "SELECT available_spaces, total_spaces, unavailable_spaces FROM parkingareas WHERE area_id = ?";
    $stmt = $conn->prepare($checkAvailability);
    $stmt->bind_param("s", $area_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $available_spaces = $row["available_spaces"];
        $total_spaces = $row["total_spaces"];
        $unavailable_spaces = $row["unavailable_spaces"];

        if ($available_spaces > 0 && $available_spaces <= $total_spaces && $unavailable_spaces >= 0 && $unavailable_spaces < $total_spaces) {
            // Update available_spaces and unavailable_spaces using prepared statements
            $updateSpaces = "UPDATE parkingareas 
                             SET available_spaces = ?,
                                 unavailable_spaces = ?
                             WHERE area_id = ?";
            $stmt = $conn->prepare($updateSpaces);
            $newAvailableSpaces = $available_spaces - 1;
            $newUnavailableSpaces = $unavailable_spaces + 1;
            $stmt->bind_param("iii", $newAvailableSpaces, $newUnavailableSpaces, $area_id);

            if ($stmt->execute()) {
                $transaction_id = 0; // Initialize the transaction ID

                // Insert data into parkingtransactions table
                $sql = "INSERT INTO parkingtransactions (area_id, entry_time) VALUES (?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $area_id);

                if ($stmt->execute()) {
                    $transaction_id = $stmt->insert_id;
                    $message = "Transaction recorded successfully for entrance. Transaction ID: " . $transaction_id;
                    echo '<div id="transaction_id_display">' . $transaction_id . '</div>';
                    // Commit the transaction
                    $conn->commit();
                } else {
                    $message = "Error adding transaction: " . $stmt->error;
                    // Rollback the transaction in case of an error
                    $conn->rollback();
                }
            } else {
                $message = "Error updating parking area: " . $stmt->error;
                // Rollback the transaction in case of an error
                $conn->rollback();
            }
        } else {
            $message = "Parking area is full or invalid. Cannot enter.";
        }
    } else {
        $message = "Invalid area ID.";
    }
}

$conn->close();
?>

<?php echo $message; ?>