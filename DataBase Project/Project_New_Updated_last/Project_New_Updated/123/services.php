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

// Initialize variables
$name = $description = $service_date = $campus_id = "";
$message = "";

// Fetch available campus IDs for the campus_id dropdown
$campus_options = [];
$result = $conn->query("SELECT Campus_ID FROM Campus");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $campus_options[] = $row['Campus_ID'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $service_id = htmlspecialchars($_POST['service_id']);
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $service_date = htmlspecialchars($_POST['service_date']);
    $campus_id = htmlspecialchars($_POST['campus_id']);

    // Check if the service_id already exists
    $check_query = $conn->prepare("SELECT * FROM Service WHERE Service_ID = ?");
    $check_query->bind_param("s", $service_id);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        $message = "This service ID already exists.";
    } else {
        // Insert the new service into the database
        $stmt = $conn->prepare("INSERT INTO Service (Service_ID, Name, Description, Service_Date, Campus_ID) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $service_id, $name, $description, $service_date, $campus_id);

        if ($stmt->execute()) {
            $message = "Service added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check_query->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        .btn-primary, .btn-secondary, .btn-success {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-success {
            background-color: #28a745;
            color: #fff;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mt-5">Add New Service</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form action="services.php" method="post">
        <div class="form-group">
            <label for="service_id">Service ID:</label>
            <input type="text" class="form-control" id="service_id" name="service_id" required>
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="service_date">Service Date:</label>
            <input type="date" class="form-control" id="service_date" name="service_date" required>
        </div>
        <div class="form-group">
            <label for="campus_id">Campus ID:</label>
            <select class="form-control" id="campus_id" name="campus_id" required>
                <option value="">Select Campus</option>
                <?php foreach ($campus_options as $campus_id): ?>
                    <option value="<?php echo $campus_id; ?>"><?php echo $campus_id; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Service</button>
    </form>
    <br>
    <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
</div>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

