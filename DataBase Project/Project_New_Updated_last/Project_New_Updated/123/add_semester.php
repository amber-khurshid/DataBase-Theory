<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Semester</title>
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
            margin-bottom:6px;
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
        #sem {
            padding: 20px;
            text-align: center;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 id="sem">Add Semester</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="add_semester.php">
            <div class="form-group">
                <label for="Semester_ID">Semester ID</label>
                <select class="form-control" id="Semester_ID" name="Semester_ID">
                    <option value="1st">1st</option>
                    <option value="2nd">2nd</option>
                    <option value="3rd">3rd</option>
                    <option value="4th">4th</option>
                    <option value="5th">5th</option>
                    <option value="6th">6th</option>
                    <option value="7th">7th</option>
                    <option value="8th">8th</option>
                </select>
            </div>
            <div class="form-group">
                <label for="Start_Date">Start Date</label>
                <input type="date" class="form-control" id="Start_Date" name="Start_Date" required>
            </div>
            <div class="form-group">
                <label for="End_Date">End Date</label>
                <input type="date" class="form-control" id="End_Date" name="End_Date" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Semester</button>
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>

