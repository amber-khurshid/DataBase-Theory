<?php
// Check if the semester is selected
if (!isset($_POST['semester'])) {
    die('Semester not selected.');
}

$semester = $_POST['semester'];
$studentId = '22P-9252'; // You can get the student ID from the session or any other method

// Fetch courses and attendance for the selected semester
function getCoursesAndAttendance($studentId, $semester) {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'VSSA_PROJECT');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT sc.course_id, c.Course_Name, sc.Marks, sc.Attendance
            FROM student_course sc
            JOIN Course c ON sc.course_id = c.Course_ID
            WHERE sc.student_id = ? AND sc.semester_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $studentId, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    $courses = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }

    $stmt->close();
    $conn->close();
    return $courses;
}

$courses = getCoursesAndAttendance($studentId, $semester);
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
        <h2>Courses for Semester: <?php echo htmlspecialchars($semester); ?></h2>
        <?php if (count($courses) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Marks</th>
                        <th>Attendance (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['course_id']); ?></td>
                            <td><?php echo htmlspecialchars($course['Course_Name']); ?></td>
                            <td><?php echo htmlspecialchars($course['Marks']); ?></td>
                            <td><?php echo htmlspecialchars($course['Attendance']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No courses found for the selected semester.</p>
        <?php endif; ?>
    </div>
</body>
</html>

