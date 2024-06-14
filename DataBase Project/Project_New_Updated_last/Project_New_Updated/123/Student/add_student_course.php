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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $marks = $_POST['marks'];
    $attendance = $_POST['attendance'];
    $semester_id = $_POST['semester_id'];

    // Prepare the SQL statement
    $sql = "INSERT INTO student_course (student_id, course_id, marks, attendance, semester_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdds", $student_id, $course_id, $marks, $attendance, $semester_id);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $message = "New record created successfully";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student Course Entry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label, input, button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        button {
            width: auto;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            max-width: 500px;
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
    <h1>Add Student Course Entry</h1>
    <?php if ($message != ""): ?>
        <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="add_student_course.php" method="post">
        <label for="student_id">Student ID:</label>
        <input type="text" id="student_id" name="student_id" required>
        
        <label for="course_id">Course ID:</label>
        <input type="text" id="course_id" name="course_id" required>
        
        <label for="marks">Marks:</label>
        <input type="number" id="marks" name="marks" step="0.01" required>
        
        <label for="attendance">Attendance (%):</label>
        <input type="number" id="attendance" name="attendance" step="0.01" required>
        
        <label for="semester_id">Semester ID:</label>
        <input type="text" id="semester_id" name="semester_id" required>
        
        <button type="submit">Add Entry</button>
    </form>
</body>
</html>

