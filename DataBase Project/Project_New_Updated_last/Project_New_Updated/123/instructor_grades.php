<?php
session_start();

if (!isset($_SESSION['instructor_id'])) {
    header('Location: login.php');
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vssa";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Courses</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .banner {
            width: 100%;
            height: 200px;
            background: url('https://via.placeholder.com/1920x200.png?text=Instructor+Courses') center center/cover no-repeat;
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
        .info-text {
            margin: 20px 0;
            font-size: 18px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="banner"></div>
        <h2>Your Courses</h2>
        <div class="info-text">Click on a course to update marks and attendance for students.</div>
        <br>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Course Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT c.course_id, c.course_name 
                        FROM Course c
                        JOIN instructor_course ic ON c.course_id = ic.course_id
                        WHERE ic.instructor_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $instructor_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".htmlspecialchars($row["course_id"])."</td>";
                        echo "<td>".htmlspecialchars($row["course_name"])."</td>";
                        echo "<td><a href='instructor_course_students.php?course_id=".htmlspecialchars($row["course_id"])."' class='btn btn-primary'>View Students</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No courses found</td></tr>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
        <a href="instructor_dashboard.php" class="dashboard-button">Back to Dashboard</a>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

