<?php
// Database connection parameters
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

session_start();

if (!isset($_SESSION['student_id'])) {
    echo "<script type='text/javascript'>alert('Please login first.'); window.location.href='login.php';</script>";
    exit;
}

$studentId = $_SESSION['student_id'];

// Function to calculate GPA
function calculateGPA($grades) {
    $totalPoints = 0;
    $totalCredits = 0;

    foreach ($grades as $grade) {
        $credits = $grade['Credit_Hour'];
        $marks = $grade['Marks'];
        $points = 0;

        if ($marks >= 90) $points = 4.0;
        elseif ($marks >= 86) $points = 4.0;
        elseif ($marks >= 82) $points = 3.7;
        elseif ($marks >= 78) $points = 3.3;
        elseif ($marks >= 74) $points = 3.0;
        elseif ($marks >= 70) $points = 2.7;
        elseif ($marks >= 66) $points = 2.3;
        elseif ($marks >= 62) $points = 2.0;
        elseif ($marks >= 58) $points = 1.7;
        elseif ($marks >= 54) $points = 1.3;
        elseif ($marks >= 50) $points = 1.0;
        else $points = 0.0;

        $totalPoints += $points * $credits;
        $totalCredits += $credits;
    }

    if ($totalCredits == 0) return 0;

    return $totalPoints / $totalCredits;
}

function updateCGPA($conn, $studentId) {
    $sql = "
        SELECT GPA
        FROM student_semester
        WHERE student_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    $totalGPA = 0;
    $totalSemesters = 0;

    while ($row = $result->fetch_assoc()) {
        $totalGPA += $row['GPA'];
        $totalSemesters++;
    }

    $stmt->close();

    if ($totalSemesters > 0) {
        $cgpa = $totalGPA / $totalSemesters;
    } else {
        $cgpa = 0;
    }

    $updateSql = "
        UPDATE Student 
        SET CGPA = ? 
        WHERE Student_ID = ?
    ";

    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ds", $cgpa, $studentId);
    $stmt->execute();
    $stmt->close();
}


// Fetch student data
$sql = "
SELECT 
    s.Student_ID, 
    sc.course_id, 
    c.Credit_Hour, 
    sc.Marks, 
    ss.semester_id 
FROM 
    Student s
JOIN 
    student_course sc ON s.Student_ID = sc.student_id
JOIN 
    Course c ON sc.course_id = c.Course_ID
JOIN 
    student_semester ss ON s.Student_ID = ss.student_id AND sc.semester_id = ss.semester_id
WHERE 
    s.Student_ID = ?
ORDER BY 
    ss.semester_id, c.Course_ID
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $studentId = $row['Student_ID'];
        $semesterId = $row['semester_id'];
        $students[$studentId][$semesterId][] = $row;
    }

    foreach ($students as $studentId => $semesters) {
        foreach ($semesters as $semesterId => $grades) {
            $gpa = calculateGPA($grades);

            $updateSql = "
                UPDATE student_semester 
                SET GPA = ? 
                WHERE student_id = ? AND semester_id = ?
            ";

            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("dss", $gpa, $studentId, $semesterId);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Prepare the SQL query to fetch transcript data
$sql = "
SELECT 
    s.Student_ID, 
    c.Course_ID, 
    c.Course_Name, 
    c.Credit_Hour, 
    sc.Marks, 
    CASE
        WHEN sc.Marks >= 90 THEN 'A+'
        WHEN sc.Marks >= 86 THEN 'A'
        WHEN sc.Marks >= 82 THEN 'A-'
        WHEN sc.Marks >= 78 THEN 'B+'
        WHEN sc.Marks >= 74 THEN 'B'
        WHEN sc.Marks >= 70 THEN 'B-'
        WHEN sc.Marks >= 66 THEN 'C+'
        WHEN sc.Marks >= 62 THEN 'C'
        WHEN sc.Marks >= 58 THEN 'C-'
        WHEN sc.Marks >= 54 THEN 'D+'
        WHEN sc.Marks >= 50 THEN 'D'
        ELSE 'F'
    END AS Grade, 
    ss.semester_id, 
    sem.Start_Date, 
    sem.End_Date, 
    ss.GPA AS Semester_GPA
FROM 
    Student s
JOIN 
    student_course sc ON s.Student_ID = sc.student_id
JOIN 
    Course c ON sc.course_id = c.Course_ID
JOIN 
    student_semester ss ON s.Student_ID = ss.student_id AND sc.semester_id = ss.semester_id
JOIN 
    Semester sem ON ss.semester_id = sem.Semester_ID
WHERE 
    s.Student_ID = ?
ORDER BY 
    ss.semester_id, c.Course_ID
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentId);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

updateCGPA($conn, $studentId);
$stmt->close();
$conn->close();

$json_data = json_encode($data);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Transcript</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    padding: 20px;
}
.card-header {
    background-color: #007bff;
    color: white;
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function displayTranscript(data) {
        let transcriptHtml = '';
        let currentSemester = null;

        data.forEach(function(item, index) {
            if (currentSemester !== item.semester_id) {
                if (currentSemester !== null) {
                    transcriptHtml += '</tbody></table></div></div>';
                }
                currentSemester = item.semester_id;
                transcriptHtml += `
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>Semester ${item.semester_id}</h3>
                                <p>GPA: ${item.Semester_GPA}</p>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Course Name</th>
                                            <th>Credits</th>
                                            <th>Marks</th>
                                            <th>Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;
            }
            transcriptHtml += `
                <tr>
                    <td>${item.Course_Name}</td>
                    <td>${item.Credit_Hour}</td>
                    <td>${item.Marks}</td>
                    <td>${item.Grade}</td>
                </tr>
            `;
            if (index === data.length - 1) {
                transcriptHtml += '</tbody></table></div></div>';
            }
        });

        $('#transcriptContainer').html(transcriptHtml);
    }

    let data = <?php echo $json_data; ?>;
    displayTranscript(data);
});
</script>
</head>
<body>
<div class="container">
    <h1 class="my-4 text-center">Student Transcript</h1>
    <div id="transcriptContainer" class="row"></div>
    <div class="text-center mt-4">
        <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>

