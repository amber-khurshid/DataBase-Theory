<?php
// Database connection settings
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

$message = "";

// Fetch the number of courses from the Course table
$courses_result = $conn->query("SELECT COUNT(*) as course_count FROM Course");
$course_count = 0;
if ($courses_result->num_rows > 0) {
    $row = $courses_result->fetch_assoc();
    $course_count = $row['course_count'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $campus_id = "PWR"; // Fixed Campus_ID
    $name = $_POST['name'];
    $location = $_POST['location'];
    $director = $_POST['director'];
    $courses = $course_count; // Number of courses

    // Check if the record already exists
    $check_sql = "SELECT * FROM Campus WHERE Campus_ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $campus_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "Error: This campus already exists.";
    } else {
        // Prepare the SQL statement
        $sql = "INSERT INTO Campus (Campus_ID, Name, Location, Director, Courses) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssd", $campus_id, $name, $location, $director, $courses);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            $message = "New campus created successfully";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the statement
        $stmt->close();
    }

    // Close the check statement
    $check_stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Campus</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            padding: 10px;
            border-radius: 5px;
        }
        .btn-primary, .btn-secondary {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .message {
            max-width: 600px;
            margin: 20px auto;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            color: #fff;
        }
        .success {
            background-color: #28a745;
        }
        .error {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Add Campus</h1>
        <?php if ($message != ""): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="campus.php" method="post">
            <div class="form-group">
                <label for="name">Campus Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="director">Director:</label>
                <input type="text" class="form-control" id="director" name="director" required>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Add Campus</button>
        </form>
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

