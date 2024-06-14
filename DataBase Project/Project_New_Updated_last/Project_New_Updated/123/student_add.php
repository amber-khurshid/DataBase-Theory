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
$student_id = $name = $email = $contact_no = $address = $dob = $gender = $cgpa = "";
$message = "";

// Fetch users with role "Student"
$students = [];
$result = $conn->query("SELECT user_id, Name, Email, Phone FROM User WHERE Type = 'Student'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $user_id = htmlspecialchars($_POST['user_id']);
    $student_id = htmlspecialchars($_POST['student_id']);
    $address = htmlspecialchars($_POST['address']);
    $dob = htmlspecialchars($_POST['dob']);
    $gender = htmlspecialchars($_POST['gender']);
    $cgpa = htmlspecialchars($_POST['cgpa']);

    // Validate student_id format
    if (!preg_match("/^\d{2}P-\d{4}$/", $student_id)) {
        $message = "Error: Roll number must be in the format 22P-9278.";
    } else {
        // Fetch name, email, and contact number for the selected user
        $user_result = $conn->query("SELECT Name, Email, Phone FROM User WHERE user_id = '$user_id'");
        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $name = $user_row['Name'];
            $email = $user_row['Email'];
            $contact_no = $user_row['Phone'];
        }

        // Check if the user_id already exists in the Student table
        $check_result = $conn->query("SELECT * FROM Student WHERE user_id = '$user_id'");
        if ($check_result->num_rows > 0) {
            $message = "Error: Student with the specified user ID already exists.";
        } else {
            // Insert the new student into the database
            $stmt = $conn->prepare("INSERT INTO Student (user_id, Name, Email, Contact_No, Address, DOB, Gender, CGPA, Student_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $user_id, $name, $email, $contact_no, $address, $dob, $gender, $cgpa, $student_id);

            if ($stmt->execute()) {
                $message = "Student added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
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
    <title>Add Student</title>
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
        .btn-primary, .btn-secondary, .btn-info {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 4px;
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
        .btn-info {
            background-color: #17a2b8;
        }
        .btn-info:hover {
            background-color: #138496;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mt-5">Add New Student</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form action="student_add.php" method="post">
        <div class="form-group">
            <label for="user_id">Student:</label>
            <select class="form-control" id="user_id" name="user_id" required>
                <option value="">Select a student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo htmlspecialchars($student['user_id']); ?>">
                        <?php echo htmlspecialchars($student['user_id']) . " (" . htmlspecialchars($student['Name']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="student_id">Roll No:</label>
            <input type="text" class="form-control" id="student_id" name="student_id" pattern="\d{2}P-\d{4}" title="Roll number must be in the format 22P-9278" required>
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" readonly required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" readonly required>
        </div>
        <div class="form-group">
            <label for="contact_no">Contact Number:</label>
            <input type="text" class="form-control" id="contact_no" name="contact_no" readonly required>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" class="form-control" id="address" name="address" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="cgpa">CGPA:</label>
            <input type="text" class="form-control" id="cgpa" name="cgpa" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Student</button>
    </form>
    <br>
    <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    <a href="add_student_course.php" class="btn btn-primary">Add Student to Course</a>
    <!-- Add this in the relevant section within the container div in student_add.php -->
<a href="admin_student_semester.php" class="btn btn-primary">Add Student to Semester</a>

</div>

<script>
$(document).ready(function() {
    $('#user_id').change(function() {
        var userId = $(this).val();
        if (userId) {
            $.ajax({
                type: 'POST',
                url: 'get_user_details.php',
                data: {user_id: userId},
                dataType: 'json',
                success: function(response) {
                    $('#name').val(response.Name);
                    $('#email').val(response.Email);
                    $('#contact_no').val(response.Phone);
                }
            });
        } else {
            $('#name').val('');
            $('#email').val('');
            $('#contact_no').val('');
        }
    });
});
</script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

