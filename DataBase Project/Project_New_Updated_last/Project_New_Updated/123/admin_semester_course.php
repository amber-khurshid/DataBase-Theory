<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vssa";  // Updated database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$semester_id = $course_id = $instructor_id = "";
$message = "";

// Fetch available semesters for the semester_id dropdown
$semester_options = [];
$result = $conn->query("SELECT semester_id FROM Semester");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $semester_options[] = $row['semester_id'];
    }
}

// Fetch available courses for the course_id dropdown
$course_options = [];
$result = $conn->query("SELECT course_id, instructor_id FROM instructor_course");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $course_options[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check and sanitize inputs
    $semester_id = isset($_POST['semester_id']) ? htmlspecialchars($_POST['semester_id']) : '';
    $course_id = isset($_POST['course_id']) ? htmlspecialchars($_POST['course_id']) : '';
    $instructor_id = isset($_POST['instructor_id']) ? htmlspecialchars($_POST['instructor_id']) : '';

    if ($semester_id && $course_id && $instructor_id) {
        // Fetch start_date and end_date from Semester table
        $stmt = $conn->prepare("SELECT start_date, end_date FROM Semester WHERE semester_id = ?");
        $stmt->bind_param("s", $semester_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $semester_data = $result->fetch_assoc();
        $start_date = $semester_data['start_date'];
        $end_date = $semester_data['end_date'];
        $stmt->close();

        // Check if the course with the same instructor already exists in the semester
        $check_query = $conn->prepare("SELECT * FROM semester_course WHERE semester_id = ? AND course_id = ? AND instructor_id = ?");
        $check_query->bind_param("sss", $semester_id, $course_id, $instructor_id);
        $check_query->execute();
        $result = $check_query->get_result();

        if ($result->num_rows > 0) {
            $message = "This course with the same instructor is already added to the selected semester.";
        } else {
            // Insert the new semester course into the database
            $stmt = $conn->prepare("INSERT INTO semester_course (semester_id, course_id, instructor_id, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $semester_id, $course_id, $instructor_id, $start_date, $end_date);

            if ($stmt->execute()) {
                $message = "Course added to semester successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
        $check_query->close();
    } else {
        $message = "Please fill in all required fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course to Semester</title>
    <!-- Bootstrap CSS -->
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
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Add Course to Semester</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="admin_semester_course.php" method="post">
            <div class="form-group">
                <label for="semester_id">Semester ID:</label>
                <select class="form-control" id="semester_id" name="semester_id" required>
                    <option value="">Select Semester</option>
                    <?php foreach ($semester_options as $semester_id): ?>
                        <option value="<?php echo $semester_id; ?>"><?php echo $semester_id; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="course_id">Course ID:</label>
                <select class="form-control" id="course_id" name="course_id" required>
                    <option value="">Select Course</option>
                    <?php foreach ($course_options as $option): ?>
                        <option value="<?php echo $option['course_id']; ?>"><?php echo $option['course_id']; ?> - Instructor: <?php echo $option['instructor_id']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="instructor_id">Instructor ID:</label>
                <input type="text" class="form-control" id="instructor_id" name="instructor_id" readonly>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Add Course to Semester</button>
            <a href="admin_dashboard.php" class="btn btn-primary mb-2">Back to Dashboard</a>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    // JavaScript to fill instructor_id based on course_id selection
    document.getElementById('course_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex].text;
        var instructorId = selectedOption.split('Instructor: ')[1];
        document.getElementById('instructor_id').value = instructorId || '';
    });
    </script>
</body>
</html>

