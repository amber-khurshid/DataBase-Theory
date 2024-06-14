<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    echo json_encode(["error" => "Please login first."]);
    exit;
}

$studentId = $_SESSION['student_id'];

try {
    $db = new PDO('mysql:host=localhost;dbname=vssa', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch student information along with CGPA and semester GPA
    $sql = "SELECT s.Name AS StudentName, ss.semester_id AS Semester, ss.GPA, s.CGPA
            FROM Student s
            JOIN student_semester ss ON s.Student_ID = ss.student_id
            WHERE s.Student_ID = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$studentId]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch total services count from the Service table
    $sqlServices = "SELECT COUNT(*) AS totalServices FROM Service";
    $stmtServices = $db->prepare($sqlServices);
    $stmtServices->execute();
    $resultServices = $stmtServices->fetch(PDO::FETCH_ASSOC);
    $totalServices = $resultServices['totalServices'];

    // Return JSON response with all data, including CGPA
    echo json_encode([
        "data" => $data,
        "cgpa" => isset($data[0]) ? $data[0]['CGPA'] : 0,
        "totalSemesters" => count($data),
        "totalServices" => $totalServices
    ]);

} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
?>

