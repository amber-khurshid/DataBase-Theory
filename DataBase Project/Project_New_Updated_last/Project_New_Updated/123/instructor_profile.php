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
session_start();

if (!isset($_SESSION['instructor_id'])) {
    echo "<script type='text/javascript'>alert('Please login first.'); window.location.href='login.php';</script>";
    exit;
}

$instructorId  = $_SESSION['instructor_id'];

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT Name, Email, Contact_No, Instructor_Pay FROM Instructor WHERE Instructor_ID = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $instructorId);

// Execute the statement
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

// Bind the result to variables
$stmt->bind_result($name, $email, $contact_no, $instructor_pay);

// Fetch the data
if ($stmt->fetch()) {
    // Sanitize output
    $name = htmlspecialchars($name);
    $email = htmlspecialchars($email);
    $contact_no = htmlspecialchars($contact_no);
    $instructor_pay = htmlspecialchars($instructor_pay);
} else {
    echo "No instructor found";
    $stmt->close();
    $conn->close();
    exit;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            padding-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid #007bff;
        }
        .card {
            margin-bottom: 20px;
            border: none;
            background: none;
        }
        .card-body {
            padding: 0;
        }
        .card-title {
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
            color: #007bff;
        }
        .card-subtitle {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 15px;
            text-align: center;
        }
        .card-text {
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .card-text strong {
            font-weight: bold;
        }
        .info-item {
            background-color: #f1f1f1;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .btn-back {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            display: block;
            width: 100%;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <a href="#"><img src="https://img.icons8.com/color/144/000000/person-male.png" alt="Profile Picture"></a>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $name; ?></h5>
                <h6 class="card-subtitle"><?php echo $instructorId; ?></h6>
                <div class="info-item">
                    <strong>Email:</strong> <?php echo $email; ?>
                </div>
                <div class="info-item">
                    <strong>Contact No:</strong> <?php echo $contact_no; ?>
                </div>
                <div class="info-item">
                    <strong>Pay:</strong> <?php echo $instructor_pay; ?>
                </div>
                <a href="instructor_dashboard.php" class="btn btn-back">Back to Dashboard</a>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>

