<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vssa";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch existing user IDs
$existing_user_ids = [];
$result = $conn->query("SELECT user_id FROM User");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $existing_user_ids[] = $row['user_id'];
    }
}

// Generate available user IDs
$available_user_ids = [];
for ($i = 1; $i <= 100; $i++) {
    if (!in_array($i, $existing_user_ids)) {
        $available_user_ids[] = $i;
    }
}

echo json_encode($available_user_ids);

$conn->close();
?>

