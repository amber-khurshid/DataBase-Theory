<?php
// Database connection settings
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

$message = "";

// Fetch users for dropdown
$users = $conn->query("SELECT user_id, email, name, phone FROM User WHERE type = 'Instructor' OR type = 'instructor'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $user_id = $_POST['user_id'];
    $instructor_id = $_POST['instructor_id'];
    $instructor_pay = $_POST['instructor_pay'];

    // Fetch user details
    $user_sql = "SELECT email, name, phone FROM User WHERE user_id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("s", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_row = $user_result->fetch_assoc();
    $email = $user_row['email'];
    $name = $user_row['name'];
    $phone = $user_row['phone'];
    $user_stmt->close();

    // Check if the user_id or instructor_id already exists in the Instructor table
    $check_sql = "SELECT * FROM Instructor WHERE user_id = ? OR Instructor_ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $user_id, $instructor_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "Error: This user is already an instructor or the Instructor ID already exists.";
    } else {
        // Prepare the SQL statement
        $sql = "INSERT INTO Instructor (Instructor_ID, Name, Email, Contact_No, Instructor_Pay, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssds", $instructor_id, $name, $email, $phone, $instructor_pay, $user_id);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            $message = "New instructor added successfully";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the statement
        $stmt->close();
    }

    // Close the check statement
    $check_stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Instructor</title>
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Add Instructor</h1>
        <?php if ($message != ""): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="instructor_add.php" method="post">
            <div class="form-group">
                <label for="user_id">User ID:</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    <option value="">Select a User</option>
                    <?php if ($users->num_rows > 0): ?>
                        <?php while($row = $users->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['user_id']); ?>"><?php echo htmlspecialchars($row['user_id']); ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="instructor_id">Instructor ID:</label>
                <input type="text" class="form-control" id="instructor_id" name="instructor_id" required>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" readonly>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" class="form-control" id="phone" name="phone" readonly>
            </div>
            <div class="form-group">
                <label for="instructor_pay">Instructor Pay:</label>
                <input type="number" class="form-control" id="instructor_pay" name="instructor_pay" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Add Instructor</button>
        </form>

        <a href="instructor_add_course.php" class="btn btn-primary mb-2">Add Instructor to Course</a>
                <a href="admin_dashboard.php" class="btn btn-primary mb-2">Back to Dashboard</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#user_id').change(function() {
                var userId = $(this).val();
                if (userId) {
                    $.ajax({
                        url: 'instructor_fetch_user_details.php',
                        type: 'POST',
                        data: { user_id: userId },
                        success: function(data) {
                            var user = JSON.parse(data);
                            if (user.error) {
                                alert(user.error);
                                $('#user_id').val('');
                                $('#name').val('');
                                $('#email').val('');
                                $('#phone').val('');
                            } else {
                                $('#name').val(user.name);
                                $('#email').val(user.email);
                                $('#phone').val(user.phone);
                            }
                        }
                    });
                } else {
                    $('#name').val('');
                    $('#email').val('');
                    $('#phone').val('');
                }
            });
        });
    </script>
</body>
</html>

