<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "puiii"; // Replace 'puiii' with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['submit'])) {
    if (isset($_POST['messageId']) && isset($_POST['messageContent'])) {
        $messageId = $_POST['messageId'];
        $editedMessage = $_POST['messageContent'];

        // Update message in the database
        $sql = "UPDATE contact_form_data SET message = '$editedMessage' WHERE id = $messageId"; // Replace 'your_table_name' with your actual table name
        if ($conn->query($sql) === TRUE) {
            echo "Message updated successfully";
        } else {
            echo "Error updating message: " . $conn->error;
        }
    } else {
        echo "Missing message ID or edited message content";
    }
}

$conn->close();
?>