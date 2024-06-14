<?php
session_start();

// Assuming you have stored the logged-in user's ID in the session
if (!isset($_SESSION['instructor_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vssa";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST["course_id"];
    $credit_hour = $_POST["credit_hour"];

    // Insert data into the instructor_course table
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO instructor_course (instructor_id, course_id, No_of_classes) VALUES ('$instructor_id', '$course_id', '$credit_hour')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Courses</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            padding-top: 20px;
        }
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
        <h2>Courses Offered to Instructor</h2>
        <br>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Course Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT c.course_id, c.course_name 
                        FROM Course c
                        JOIN instructor_course ic ON c.course_id = ic.course_id
                        WHERE ic.instructor_id = '$instructor_id'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".htmlspecialchars($row["course_id"])."</td>";
                        echo "<td>".htmlspecialchars($row["course_name"])."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No courses found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
        <a href="instructor_dashboard.php" class="dashboard-button">Back to Dashboard</a>
    </div>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

