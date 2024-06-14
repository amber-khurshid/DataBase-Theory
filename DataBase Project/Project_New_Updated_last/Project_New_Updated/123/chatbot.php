<?php
session_start();

// For testing, set a default student_id in the session
// Remove this line in production or replace it with actual session management logic

session_start();

if (!isset($_SESSION['student_id'])) {
    echo "<script type='text/javascript'>alert('Please login first.'); window.location.href='login.php';</script>";
    exit;
}
try {
    $db = new PDO('mysql:host=localhost;dbname=mebis', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['message'])) {
    $userMessage = $_GET['message'];
    $botResponse = generateResponse($userMessage, $db);
    echo $botResponse;
    exit;
}

function generateResponse($message, $db) {
    $message = strtolower($message);
    if (strpos($message, 'courses') !== false) {
        return getCourses($db);
    } elseif (strpos($message, 'grades') !== false) {
        return getGrades($db);
    } elseif (strpos($message, 'gpa') !== false) {
        return getGPA($db);
    } else {
        return getDefaultResponse();
    }
}

function getCourses($db) {
    $query = "SELECT course_name FROM courses WHERE class_no = (SELECT class_no FROM students WHERE student_id = :student_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':student_id', $_SESSION['student_id']);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($courses) {
        $response = "Your courses are: ";
        foreach ($courses as $course) {
            $response .= $course['course_name'] . ", ";
        }
        return rtrim($response, ", ");
    } else {
        return "You are not enrolled in any courses.";
    }
}

function getGrades($db) {
    $query = "SELECT grade FROM students WHERE student_id = :student_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':student_id', $_SESSION['student_id']);
    $stmt->execute();
    $grade = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($grade) {
        return "Your grade is: " . $grade['grade'];
    } else {
        return "No grades found.";
    }
}

function getGPA($db) {
    $query = "SELECT gpa FROM students WHERE student_id = :student_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':student_id', $_SESSION['student_id']);
    $stmt->execute();
    $gpa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gpa) {
        return "Your GPA is: " . $gpa['gpa'];
    } else {
        return "GPA not found.";
    }
}

function getDefaultResponse() {
    $responses = [
        "Hello! How can I help you?",
        "I'm just a simple PHP chatbot.",
        "I'm sorry, I don't understand.",
        "Thanks for chatting with me!"
    ];

    $randomIndex = array_rand($responses);
    return $responses[$randomIndex];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple PHP Chatbot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        #chat-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 100%;
            padding: 20px;
            box-sizing: border-box;
            margin-bottom: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        #chat {
            height: 300px;
            overflow-y: scroll;
            margin-bottom: 20px;
            padding-right: 20px;
        }

        #chat p {
            margin: 5px 0;
        }

        input[type="text"] {
            width: calc(100% - 80px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {

            padding: 10px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .dashboard-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .dashboard-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div id="chat-container">
        <h1>Simple PHP Chatbot</h1>
        <div id="chat"></div>
        <div>
            <input type="text" id="user-input" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
    <a href="Student/student_dashboard.php" class="dashboard-button">Back to Dashboard</a>

    <script>
        function sendMessage() {
            var userInput = document.getElementById('user-input').value;
            if (userInput.trim() === '') return;

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = this.responseText;
                    document.getElementById("chat").innerHTML += "<p><strong>You:</strong> " + userInput + "</p>";
                    document.getElementById("chat").innerHTML += "<p><strong>Bot:</strong> " + response + "</p>";
                    document.getElementById('user-input').value = '';
                    scrollToBottom();
                }
            };
            xmlhttp.open("GET", "?message=" + userInput, true);
            xmlhttp.send();
        }

        function scrollToBottom() {
            var chatDiv = document.getElementById('chat');
            chatDiv.scrollTop = chatDiv.scrollHeight;
        }
    </script>
</body>
</html>

