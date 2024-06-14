<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vssa";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST["course_id"];
    $credit_hour = $_POST["credit_hour"];
    $instructor_id = 1; // Assuming instructor ID 1 for demonstration

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
} else {
    echo "Form submission method not allowed.";
}
?>

