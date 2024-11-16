<!DOCTYPE html>
<html>

<head>
    <title>Retrieve Messages</title>
    <script>
    function editMessage(messageId) {
        window.location.href = "edit_message.php?id=" + messageId;
    }
    </script>
</head>

<body>
    <h2>Retrieve Messages</h2>

    <!-- Form to collect user's name and email -->
    <form method="post" action="">
        <input type="text" name="name" placeholder="Enter Name">
        <input type="email" name="email" placeholder="Enter Email">
        <button type="submit" name="submit">Retrieve Messages</button>
    </form>

    <?php
    if(isset($_POST['submit'])) {
        // Your PHP code to retrieve messages here...
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "puiii"; // Replace 'your_database_name' with your actual database name

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get user input
        $name = $_POST['name']; // Change 'Name' to 'name'
        $email = $_POST['email'];

        // Query to retrieve messages based on name and email
        $sql = "SELECT * FROM contact_form_data WHERE Name='$name' AND email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Display retrieved messages with an Edit button
            while ($row = $result->fetch_assoc()) {
                echo "<p><strong>Name:</strong> " . $row['name'] . "</p>";
                echo "<p><strong>Email:</strong> " . $row['email'] . "</p>";
                echo "<p><strong>Subject:</strong> " . $row['subject'] . "</p>";
                echo "<p><strong>Message:</strong> " . $row['message'] . "</p>";
                // Add an Edit button with the message ID as a parameter
                echo '<button onclick="editMessage(' . $row['id'] . ')">Edit</button>';
                echo "<hr>";
            }
        } else {
            echo "No messages found for this user.";
        }

        $conn->close();
    }
    ?>
</body>

</html>