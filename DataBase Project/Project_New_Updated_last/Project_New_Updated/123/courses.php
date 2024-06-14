<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=vssa', 'root', '');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $course_id = htmlspecialchars($_POST['course_id']);
        $course_name = htmlspecialchars($_POST['course_name']);
        $credit_hour = htmlspecialchars($_POST['credit_hour']);
        $pre_req = htmlspecialchars($_POST['pre_req']);
        $course_type = htmlspecialchars($_POST['course_type']);

        // Check if course ID already exists
        $check_query = $db->prepare("SELECT COUNT(*) FROM Course WHERE Course_ID = ?");
        $check_query->execute([$course_id]);
        $course_exists = $check_query->fetchColumn();

        if ($course_exists) {
            $message = "Course ID already exists.";
        } else {
            // Insert the new course if ID does not exist
            $insert_query = $db->prepare("INSERT INTO Course (Course_ID, Course_Name, Credit_Hour, Pre_req, Course_Type) VALUES (?, ?, ?, ?, ?)");
            $result = $insert_query->execute([$course_id, $course_name, $credit_hour, $pre_req, $course_type]);

            if ($result) {
                $message = "Course added successfully!";
            } else {
                $message = "Failed to add course.";
            }
        }
    }

    $query = $db->query("SELECT Course_ID, Course_Name FROM Course");
    $pre_req_options = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
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
        .btn-primary, .btn-secondary, .btn-success {
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
        .btn-success {
            background-color: #28a745;
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
        <h2 class="mt-5">Add New Course</h2>
        <?php if (isset($message) && $message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form action="courses.php" method="post">
            <div class="form-group">
                <label for="course_id">Course ID:</label>
                <input type="text" class="form-control" id="course_id" name="course_id" required>
            </div>
            <div class="form-group">
                <label for="course_name">Course Name:</label>
                <input type="text" class="form-control" id="course_name" name="course_name" required>
            </div>
            <div class="form-group">
                <label for="credit_hour">Credit Hour:</label>
                <input type="number" class="form-control" id="credit_hour" name="credit_hour" required>
            </div>
            <div class="form-group">
                <label for="pre_req">Prerequisite:</label>
                <select class="form-control" id="pre_req" name="pre_req">
                    <option value="">None</option>
                    <?php foreach ($pre_req_options as $option): ?>
                        <option value="<?php echo htmlspecialchars($option['Course_ID']); ?>"><?php echo htmlspecialchars($option['Course_ID']) . ' - ' . htmlspecialchars($option['Course_Name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="course_type">Course Type:</label>
                <input type="text" class="form-control" id="course_type" name="course_type" required>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Add Course</button>
            <a href="admin_semester_course.php" class="btn btn-primary mb-2">Add Courses to Semester</a>
            <a href="admin_dashboard.php" class="btn btn-primary mb-2">Back to Dashboard</a>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
        
