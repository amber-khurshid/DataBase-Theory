<?php
$host = 'localhost';
$db   = 'vssa';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);

session_start();

if (!isset($_SESSION['student_id'])) {
    echo "<script type='text/javascript'>alert('Please login first.'); window.location.href='login.php';</script>";
    exit;
}

$studentId = $_SESSION['student_id'];

//$studentId = '22P-9252'; // Replace with the actual student ID
$stmt = $pdo->prepare('SELECT * FROM Student WHERE Student_ID = ?');
$stmt->execute([$studentId]);
$student = $stmt->fetch();

// Check if the gender is Female and change the logo link accordingly
if ($student['Gender'] == 'Female') {
    $logoSrc = 'https://static.vecteezy.com/system/resources/previews/021/553/043/original/cute-hijabi-girl-cartoon-style-illustration-vector.jpg';
} else {
    $logoSrc = 'https://img.freepik.com/premium-photo/anime-boy-with-brown-hair-red-shirt-smiling-generative-ai_900833-23001.jpg'; // Default logo link
}

// Update CGPA function
function updateCGPA($pdo, $studentId) {
    $sql = "
        SELECT GPA
        FROM student_semester
        WHERE student_id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$studentId]);
    $result = $stmt->fetchAll();

    $totalGPA = 0;
    $totalSemesters = count($result);

    foreach ($result as $row) {
        $totalGPA += $row['GPA'];
    }

    if ($totalSemesters > 0) {
        $cgpa = $totalGPA / $totalSemesters;
    } else {
        $cgpa = 0;
    }

    $updateSql = "
        UPDATE Student 
        SET CGPA = ? 
        WHERE Student_ID = ?
    ";

    $stmt = $pdo->prepare($updateSql);
    $stmt->execute([$cgpa, $studentId]);
}

updateCGPA($pdo, $studentId);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Information</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="student_studentstyles.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <a href="#"><img src="<?php echo $logoSrc; ?>" alt="Logo"></a>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><strong><?php echo htmlspecialchars($student['Name']); ?></strong></h5>
                <h6 class="card-subtitle mb-2 text-muted"><strong><?php echo htmlspecialchars($student['Student_ID']); ?></strong></h6>
                <p class="card-text">
                    <strong>Email:</strong> <?php echo htmlspecialchars($student['Email']); ?><br>
                    <strong>Contact No:</strong> <?php echo htmlspecialchars($student['Contact_No']); ?><br>
                    <strong>Address:</strong> <?php echo htmlspecialchars($student['Address']); ?><br>
                    <strong>DOB:</strong> <?php echo htmlspecialchars($student['DOB']); ?><br>
                    <strong>Gender:</strong> <?php echo htmlspecialchars($student['Gender']); ?><br>
                    <strong>CGPA:</strong> <?php echo htmlspecialchars($student['CGPA']); ?>
                </p>
                <a href="student_dashboard.php" class="btn btn-back">Back</a>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>

