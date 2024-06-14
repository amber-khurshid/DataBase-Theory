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

// Fetch instructors and courses for dropdowns
$instructors = $conn->query("SELECT Instructor_ID, Name FROM Instructor");
$courses = $conn->query("SELECT Course_ID FROM Course");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $instructor_id = $_POST['instructor_id'];
    $course_id = $_POST['course_id'];
    $no_of_classes = $_POST['no_of_classes'];

    // Check if the instructor is already assigned to the course
    $check_sql = "SELECT * FROM instructor_course WHERE instructor_id = ? AND course_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $instructor_id, $course_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "Error: This instructor is already assigned to this course.";
    } else {
        // Prepare the SQL statement
        $sql = "INSERT INTO instructor_course (instructor_id, course_id, no_of_classes) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $instructor_id, $course_id, $no_of_classes);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            $message = "New record created successfully";
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
    <title>Add Instructor to Course</title>
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
        <h1 class="text-center">Add Instructor to Course</h1>
        <?php if ($message != ""): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="instructor_add_course.php" method="post">
            <div class="form-group">
                <label for="instructor_id">Instructor:</label>
                <select class="form-control" id="instructor_id" name="instructor_id" required>
                    <option value="">Select an Instructor</option>
                    <?php if ($instructors->num_rows > 0): ?>
                        <?php while($row = $instructors->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['Instructor_ID']); ?>">
                                <?php echo htmlspecialchars($row['Instructor_ID']) . ' - ' . htmlspecialchars($row['Name']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="course_id">Course ID:</label>
                <select class="form-control" id="course_id" name="course_id" required>
                    <option value="">Select a Course</option>
                    <?php if ($courses->num_rows > 0): ?>
                        <?php while($row = $courses->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['Course_ID']); ?>"><?php echo htmlspecialchars($row['Course_ID']); ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="no_of_classes">Number of Classes:</label>
                <input type="number" class="form-control" id="no_of_classes" name="no_of_classes" required>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Add Entry</button>
        </form>
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

