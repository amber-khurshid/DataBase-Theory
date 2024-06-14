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
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $user_id = htmlspecialchars($_POST['user_id']);
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash(htmlspecialchars($_POST['password']), PASSWORD_DEFAULT); // Hashing the password
    $phone = htmlspecialchars($_POST['phone']);
    $type = htmlspecialchars($_POST['type']);

    // Check if the user_id already exists
    $check_query = $conn->prepare("SELECT * FROM User WHERE user_id = ?");
    $check_query->bind_param("s", $user_id);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        $message = "This user ID already exists.";
    } else {
        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO User (user_id, name, email, password, phone, type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $user_id, $name, $email, $password, $phone, $type);

        if ($stmt->execute()) {
            $message = "User added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check_query->close();

    echo json_encode(['message' => $message, 'success' => strpos($message, 'successfully') !== false]);
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
        .form-group select {
            padding: 10px;
            border-radius: 5px;
        }
        .btn-primary, .btn-secondary {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .message {
            max-width: 600px;
            margin: 20px auto;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            color: #fff;
        }
        .success {
            background-color: #28a745;
        }
        .error {
            background-color: #dc3545;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Add New User</h2>
        <div id="message-container"></div>
        <form id="userForm" action="admin_adduser.php" method="post">
            <div class="form-group">
                <label for="user_id">User ID:</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    <option value="">Select User ID</option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" pattern="\d*" title="Please enter only numbers" required>
            </div>
            <div class="form-group">
                <label for="type">Type:</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">Select Type</option>
                    <option value="Admin">Admin</option>
                    <option value="Student">Student</option>
                    <option value="Instructor">Instructor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Add User</button>
        </form>
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <script>
        // Function to fetch available user IDs
        function fetchAvailableUserIds() {
            $.ajax({
                url: 'get_available_user_ids.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    const userIdSelect = $('#user_id');
                    userIdSelect.empty();
                    userIdSelect.append('<option value="">Select User ID</option>');
                    data.forEach(function(userId) {
                        userIdSelect.append(`<option value="${userId}">${userId}</option>`);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching available user IDs:', error);
                }
            });
        }

        $(document).ready(function() {
            // Fetch available user IDs on page load
            fetchAvailableUserIds();

            // Handle form submission
            $('#userForm').on('submit', function(event) {
                event.preventDefault();

                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        // Show the message
                        const messageContainer = $('#message-container');
                        messageContainer.empty();
                        const messageClass = response.success ? 'success' : 'error';
                        messageContainer.append(`<div class="message ${messageClass}">${response.message}</div>`);

                        // Clear the form fields if successful
                        if (response.success) {
                            form.trigger('reset');
                            fetchAvailableUserIds(); // Refresh the available user IDs
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error submitting form:', error);
                    }
                });
            });
        });
    </script>
</body>
</html>

