<?php
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

// Fetch the number of students
$students_count = $conn->query("SELECT COUNT(*) AS count FROM Student")->fetch_assoc()['count'];

// Fetch the number of instructors
$instructors_count = $conn->query("SELECT COUNT(*) AS count FROM Instructor")->fetch_assoc()['count'];

// Fetch the number of courses
$courses_count = $conn->query("SELECT COUNT(*) AS count FROM Course")->fetch_assoc()['count'];

$response = array(
    "student" => $students_count,
    "instructor" => $instructors_count,
    "course" => $courses_count
);

echo json_encode($response);

$conn->close();
?>

