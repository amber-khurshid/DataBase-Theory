<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vssa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch semesters
$sql = "SELECT Semester_ID, COUNT(*) as student_count FROM Semester GROUP BY Semester_ID";
$result = $conn->query($sql);

$semesters = [];
while($row = $result->fetch_assoc()) {
    $semesters[] = [
        'id' => $row['Semester_ID'],
        'students' => $row['student_count']
    ];
}

// Prepare the data to be returned as JSON
$data = [
    'semesters' => $semesters,
    'total' => [
        'semesters' => count($semesters)
    ]
];

// Set the appropriate header for JSON response
header('Content-Type: application/json');

// Output the JSON data
echo json_encode($data);

$conn->close();
?>

