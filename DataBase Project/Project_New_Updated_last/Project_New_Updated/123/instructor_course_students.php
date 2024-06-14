<?php
session_start();

if (!isset($_SESSION['instructor_id'])) {
    header('Location: login.php');
    exit();
}

$instructor_id = $_SESSION['instructor_id'];
$course_id = $_GET['course_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vssa";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for updating marks and attendance
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST["student_id"];
    $marks = $_POST["marks"];
    $attendance = $_POST["attendance"];

    $sql = "UPDATE student_course SET Marks = ?, Attendance = ? WHERE student_id = ? AND course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddss", $marks, $attendance, $student_id, $course_id);

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Students</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .banner {
            width: 100%;
            height: 200px;
            background: url('https://via.placeholder.com/1920x200.png?text=Course+Students') center center/cover no-repeat;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .dashboard-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .dashboard-button:hover {
            color: white;
            background-color: #0056b3;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr {
            transition: background-color 0.2s;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="banner"></div>
        <h2>Students in Course <?php echo htmlspecialchars($course_id); ?></h2>
        <br>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Marks</th>
                    <th>Attendance</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT s.student_id, sc.Marks, sc.Attendance 
                        FROM Student s
                        JOIN student_course sc ON s.student_id = sc.student_id
                        WHERE sc.course_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $course_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<form method='POST' action='instructor_course_students.php?course_id=".htmlspecialchars($course_id)."'>";
                        echo "<td><input type='hidden' name='student_id' value='".htmlspecialchars($row["student_id"])."'>".htmlspecialchars($row["student_id"])."</td>";
                        echo "<td><input type='number' name='marks' value='".htmlspecialchars($row["Marks"])."' min='0' max='100' step='0.01'></td>";
                       echo "<td><input type='number' name='attendance' value='".htmlspecialchars($row["Attendance"])."' min='0' max='100' step='10'></td>";

                        echo "<td><button type='submit' class='btn btn-primary'>Update</button></td>";
                        echo "</form>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No students found</td></tr>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
        <a href="instructor_courses.php" class="dashboard-button">Back to Courses</a>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

