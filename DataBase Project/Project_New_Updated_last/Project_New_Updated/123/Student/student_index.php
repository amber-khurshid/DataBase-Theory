<?php
// Fetch available semesters from the database
function getSemesters() {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'vssa');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT DISTINCT semester_id FROM student_semester";
    $result = $conn->query($sql);

    $semesters = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $semesters[] = $row['semester_id'];
        }
    }

    $conn->close();
    return $semesters;
}

$semesters = getSemesters();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Courses</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <img src="https://khi.nu.edu.pk/wp-content/uploads/2023/01/FAST-NU-logo.png" alt="University Banner" class="header-image mb-4">
        <div class="alert alert-info" role="alert">
            Select your semester to view your courses and attendance.
        </div>

        <!-- Back to Dashboard Button -->
        <a href="student_dashboard.php" class="btn btn-secondary mb-4">Back to Dashboard</a>

        <h2>Select Semester</h2>
        <form method="post" action="courses.php">
            <div class="form-group">
                <label for="semester">Select Semester:</label>
                <select class="form-control" id="semester" name="semester">
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?php echo $semester; ?>"><?php echo $semester; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Courses</button>
        </form>
    </div>

    <footer class="footer mt-5 py-3 bg-light">
        <div class="container">
            <span class="text-muted">© 2024 FAST NUCES. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Contact Us</a></span>
        </div>
    </footer>
</body>
</html>

