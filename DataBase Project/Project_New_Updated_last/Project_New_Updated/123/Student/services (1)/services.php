<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "vssa";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve services from the database
$sql = "SELECT * FROM Service";
$result = $conn->query($sql);

// Array to store service data
$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Services</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>All Services</h2>
        <ul class="list-group">
            <?php foreach ($services as $service): ?>
                <li class="list-group-item">
                    <?php echo $service['Name']; ?>
                    <a href="feedback.php?service_id=<?php echo $service['Service_ID']; ?>" class="btn btn-primary btn-sm float-right">Feedback</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>

