<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "puiii";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $transaction_id = $_POST["transaction_id"];

    $checkTransaction = "SELECT * FROM parkingtransactions WHERE transaction_id = '$transaction_id'";
    $result = $conn->query($checkTransaction);

    if ($result->num_rows > 0) {
        $updateExitTime = "UPDATE parkingtransactions 
                           SET exit_time = NOW() 
                           WHERE transaction_id = '$transaction_id'";

        if ($conn->query($updateExitTime) === TRUE) {
            $area_id_query = "SELECT area_id FROM parkingtransactions WHERE transaction_id = '$transaction_id'";
            $area_result = $conn->query($area_id_query);
            $row = $area_result->fetch_assoc();
            $area_id = $row['area_id'];

            $updateSpaces = "UPDATE parkingareas 
                             SET available_spaces = LEAST(available_spaces + 1, total_spaces),
                                 unavailable_spaces = GREATEST(unavailable_spaces - 1, 0)
                             WHERE area_id = $area_id";

            if ($conn->query($updateSpaces) === TRUE) {
                // Delete the transaction entry after successful exit recording
                $deleteTransaction = "DELETE FROM parkingtransactions WHERE transaction_id = '$transaction_id'";
                if ($conn->query($deleteTransaction) === TRUE) {
                    echo "Exit recorded successfully and transaction deleted.";
                } else {
                    echo "Error deleting transaction: " . $conn->error;
                }
            } else {
                echo "Error updating spaces: " . $conn->error;
            }
        } else {
            echo "Error updating exit time: " . $conn->error;
        }
    } else {
        echo "Transaction ID not found. Please enter a valid ID.";
    }
}

$conn->close();
?>