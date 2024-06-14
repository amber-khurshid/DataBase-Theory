<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
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

// Assuming instructor_id is provided by session or a query parameter
session_start();
if (!isset($_SESSION['instructor_id'])) {
    echo json_encode(["error" => "Please login first."]);
    exit;
}

$instructorId = $_SESSION['instructor_id'];

// Fetch courses, number of students, and credit hours
$sql = "SELECT c.Course_Name, COUNT(DISTINCT sc.student_id) AS Student_Count, SUM(c.Credit_Hour) AS Total_Credit_Hours
        FROM instructor_course ic
        JOIN Course c ON ic.course_id = c.Course_ID
        JOIN student_course sc ON c.Course_ID = sc.course_id
        WHERE ic.instructor_id = ?
        GROUP BY c.Course_Name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $instructorId);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode(["error" => $stmt->error]);
}

// Close connection
$stmt->close();
$conn->close();
?>

