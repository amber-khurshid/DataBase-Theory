<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "VSSA_PROJECT";

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
    $stmt->bind_param("i", $serviceId);  

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

// Check if service_id is provided in the URL
if (!isset($_GET['service_id'])) {
    die('Service ID not provided.');
}

$serviceId = (int) $_GET['service_id'];  // Ensure the service ID is an integer

// Fetch service details based on service_id
$serviceDetails = getServiceDetails($serviceId);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the complaint and feedback from the form
    $complaint = $_POST['complaint'];
    $feedback = $_POST['feedback'];

    // Sanitize inputs (optional but recommended)
    $complaint = htmlspecialchars($complaint, ENT_QUOTES, 'UTF-8');
    $feedback = htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8');

    // Insert the complaint and feedback into the database
    $student_id = '22P-9252';  // Ensure this student_id exists in your database
    $stmt = $conn->prepare("INSERT INTO service_student (complaint, feedback, service_id, student_id) VALUES (?, ?, ?, ?)");

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters: i = integer, s = string
    // Bind parameters: s = string, s = string, i = integer, s = string
$stmt->bind_param("ssss", $complaint, $feedback, $serviceId, $student_id);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Success message
        echo "Your feedback has been submitted successfully.";
    } else {
        // Error message with detailed error information
        echo "An error occurred while submitting your feedback: " . htmlspecialchars($stmt->error);
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
        <p>Description: <?php echo htmlspecialchars($serviceDetails['Description']); ?> - VSSA</p>
        <p>Service Date: <?php echo htmlspecialchars($serviceDetails['Service_Date']); ?> - VSSA</p>
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
        <!-- Back Button -->
        <a href="/Front-endd/Front-end/Student/dashboard.php" class="btn btn-secondary mt-3">Back</a>
    </div>
</body>
</html>

