<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vssa"; // Updated database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$student_id = $semester_id = $gpa = "";
$message = "";

// Fetch students
$students = [];
$result = $conn->query("SELECT Student_ID FROM Student");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Fetch semesters
$semesters = [];
$result = $conn->query("SELECT Semester_ID FROM Semester");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $semesters[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $student_id = htmlspecialchars($_POST['student_id']);
    $semester_id = htmlspecialchars($_POST['semester_id']);
    $gpa = htmlspecialchars($_POST['gpa']);

    // Validate GPA
    if (!preg_match("/^[1-4]\.\d{2}$/", $gpa)) {
        $message = "Error: GPA must be a number between 1.00 and 4.00.";
    } else {
        // Check if the student_id and semester_id combination already exists
        $check_query = $conn->prepare("SELECT * FROM student_semester WHERE Student_ID = ? AND Semester_ID = ?");
        $check_query->bind_param("ss", $student_id, $semester_id);
        $check_query->execute();
        $result = $check_query->get_result();

        if ($result->num_rows > 0) {
            $message = "This student with this semester is already submitted.";
        } else {
            // Insert the new student semester record into the database
            $stmt = $conn->prepare("INSERT INTO student_semester (Student_ID, Semester_ID, GPA) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $student_id, $semester_id, $gpa);

            if ($stmt->execute()) {
                $message = "Student semester record added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $check_query->close();
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
    <title>Add Student to Semester</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
        .btn-primary {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 4px;
            background-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mt-5">Add Student to Semester</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form action="admin_student_semester.php" method="post">
        <div class="form-group">
            <label for="student_id">Student:</label>
            <select class="form-control" id="student_id" name="student_id" required>
                <option value="">Select a student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo htmlspecialchars($student['Student_ID']); ?>">
                        <?php echo htmlspecialchars($student['Student_ID']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="semester_id">Semester:</label>
            <select class="form-control" id="semester_id" name="semester_id" required>
                <option value="">Select a semester</option>
                <?php foreach ($semesters as $semester): ?>
                    <option value="<?php echo htmlspecialchars($semester['Semester_ID']); ?>">
                        <?php echo htmlspecialchars($semester['Semester_ID']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="gpa">GPA:</label>
            <input type="text" class="form-control" id="gpa" name="gpa" pattern="^[1-4]\.\d{2}$" title="GPA must be a number between 1.00 and 4.00" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Record</button>
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

