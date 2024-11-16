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

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $messageId = $_GET['id'];

    // Fetch the message from the database based on the ID
    $sql = "SELECT * FROM contact_form_data WHERE id = $messageId"; // Replace 'contact_form_data' with your actual table name
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Display the message details for editing
        $message = $result->fetch_assoc();
        ?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Message</title>
</head>

<body>
    <h2>Edit Message</h2>
    <form method="post" action="update_message.php">
        <input type="hidden" name="messageId" value="<?php echo $message['id']; ?>">
        <input type="text" name="subject" value="<?php echo $message['subject']; ?>">
        <textarea name="messageContent"><?php echo $message['message']; ?></textarea>
        <button type="submit" name="submit">Update Message</button>
    </form>
</body>

</html>
<?php
    } else {
        echo "No message found with this ID.";
    }
} else {
    echo "Invalid message ID.";
}

$conn->close();
?>