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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];

    // Check if the user is already an instructor
    $check_sql = "SELECT * FROM Instructor WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(["error" => "This user is already an instructor."]);
    } else {
        $sql = "SELECT name, email, phone FROM User WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        echo json_encode($user);
        $stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?>

