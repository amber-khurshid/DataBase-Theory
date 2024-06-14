<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "vssa";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch service details
function getServiceDetails($serviceId) {
    global $conn;

    // Prepare and bind the statement
    $stmt = $conn->prepare("SELECT Service_ID, Name, Description, Service_Date FROM Service WHERE Service_ID = ?");
    $stmt->bind_param("s", $serviceId);  

    // Execute the statement
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($id, $name, $description, $date);

    // Fetch the first row
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    // Return the service details as an associative array
    return [
        'Service_ID' => $id,
        'Name' => $name,
        'Description' => $description,
        'Service_Date' => $date
    ];
}

// Function to check if feedback already exists for the student and service
function checkFeedbackExists($serviceId, $studentId) {
    global $conn;

    // Prepare and bind the statement
    $stmt = $conn->prepare("SELECT COUNT(*) FROM service_student WHERE service_id = ? AND student_id = ?");
    $stmt->bind_param("ss", $serviceId, $studentId); 

    // Execute the statement
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($count);

    // Fetch the count
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    // Return whether feedback exists
    return $count > 0;
}

// Check if service_id is provided in the URL
if (!isset($_GET['service_id'])) {
    die('Service ID not provided.');
}

$serviceId = $_GET['service_id'];  // Keep service_id as string (VARCHAR)

// Fetch service details based on service_id
$serviceDetails = getServiceDetails($serviceId);

session_start();

if (!isset($_SESSION['student_id'])) {
    echo "<script type='text/javascript'>alert('Please login first.'); window.location.href='login.php';</script>";
    exit;
}

$student_id = $_SESSION['student_id'];

//$student_id = '22P-9252';  // Ensure this student_id exists in your database
$feedbackExists = checkFeedbackExists($serviceId, $student_id);

$message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$feedbackExists) {
    // Retrieve the complaint and feedback from the form
    $complaint = $_POST['complaint'];
    $feedback = $_POST['feedback'];

    // Sanitize inputs (optional but recommended)
    $complaint = htmlspecialchars($complaint, ENT_QUOTES, 'UTF-8');
    $feedback = htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8');

    // Insert the complaint and feedback into the database
    $stmt = $conn->prepare("INSERT INTO service_student (complaint, feedback, service_id, student_id) VALUES (?, ?, ?, ?)");

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters: s = string, s = string, s = string, s = string
    $stmt->bind_param("ssss", $complaint, $feedback, $serviceId, $student_id);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        $message = "Your feedback has been submitted successfully.";
        $feedbackExists = true;  // Update the feedback exists status
    } else {
        $message = "An error occurred while submitting your feedback: " . htmlspecialchars($stmt->error);
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback for <?php echo htmlspecialchars($serviceDetails['Name']); ?> - VSSA</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Feedback for <?php echo htmlspecialchars($serviceDetails['Name']); ?> - VSSA</h2>
        <!-- Display service details here -->
        <p>Description: <?php echo htmlspecialchars($serviceDetails['Description']); ?></p>
        <p>Service Date: <?php echo htmlspecialchars($serviceDetails['Service_Date']); ?></p>

        <?php if ($message != ""): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!$feedbackExists): ?>
            <!-- Feedback form -->
            <form method="post" action="">
                <div class="form-group">
                    <label for="complaint">Complaint:</label>
                    <input type="text" class="form-control" id="complaint" name="complaint">
                </div>
                <div class="form-group">
                    <label for="feedback">Feedback:</label>
                    <textarea class="form-control" id="feedback" name="feedback"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        
            
        <?php endif; ?>

        <!-- Back Button -->
        <a href="student_dashboard.php" class="btn btn-secondary mt-3">Back</a>
    </div>
</body>
</html>


