<?php 
session_start();

try {
    $db = new PDO('mysql:host=localhost;dbname=vssa', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: ". $e->getMessage();
    exit;
}

// Login process for instructors
if (isset($_POST['login_lecturer'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM User WHERE email=:email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "<script type='text/javascript'>alert('User not found');</script>";
    } else {
        $stored_password = $row['password'];
        if (password_verify($password, $stored_password)) {
            $username = $row['name'];
            $account_type = $row['type'];
            $user_id = $row['user_id']; // Get the user ID

            // Fetch the instructor ID using the user ID
            $instructor_query = "SELECT Instructor_ID FROM Instructor WHERE user_id=:user_id";
            $instructor_stmt = $db->prepare($instructor_query);
            $instructor_stmt->bindParam(':user_id', $user_id);
            $instructor_stmt->execute();
            $instructor_row = $instructor_stmt->fetch(PDO::FETCH_ASSOC);

            if ($instructor_row) {
                $instructor_id = $instructor_row['Instructor_ID'];
                $_SESSION['instructor_id'] = $instructor_id; // Set the instructor ID in session
                $_SESSION['name'] = $username;
                $_SESSION['email'] = $email;

                echo "<script type='text/javascript'>alert('Login Success, Welcome ' + '$username'); window.location.href='instructor_dashboard.php'; </script>";
            } else {
                echo "<script type='text/javascript'>alert('Instructor record not found');</script>";
            }
        } else {
            echo "<script type='text/javascript'>alert('Password is wrong, Please try again');</script>";
        }
    }
}


// Login process for students
// Login process for students
if (isset($_POST['login_student'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM User WHERE email=:email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$row) {
        echo "<script type='text/javascript'>alert('User not found');</script>";
    } else {
        $stored_password = $row['password'];
        if (password_verify($password, $stored_password)) {
            $username = $row['name'];
            $account_type = $row['type'];
            $user_id = $row['user_id']; // Get the user ID

            // Fetch the student ID using the user ID
            $student_query = "SELECT Student_ID FROM Student WHERE user_id=:user_id";
            $student_stmt = $db->prepare($student_query);
            $student_stmt->bindParam(':user_id', $user_id);
            $student_stmt->execute();
            $student_row = $student_stmt->fetch(PDO::FETCH_ASSOC);

            if ($student_row) {
                $student_id = $student_row['Student_ID'];
                $_SESSION['student_id'] = $student_id; // Set the student ID in session
                $_SESSION['name'] = $username;
                $_SESSION['email'] = $email;

                echo "<script type='text/javascript'>alert('Login Success, Welcome ' + '$username'); window.location.href='Student/student_dashboard.php'; </script>";
            } else {
                echo "<script type='text/javascript'>alert('Student record not found');</script>";
            }
        } else {
            echo "<script type='text/javascript'>alert('Password is wrong, Please try again');</script>";
        }
    }
}



// Admin login process
if (isset($_POST['login_admin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $password = md5($password);
    $query = "SELECT * FROM User WHERE email=:email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(count($results) == 0) {
        echo "<script type='text/javascript'>alert('User not found');</script>";
    } else {
        $row = $results[0];
        $username = $row['name'];
        $account_type = $row['type'];

        if($password!= $row['password']) {
            echo "<script type='text/javascript'>alert('Password is wrong, Please try again');</script>";
        } else if($account_type == 'Admin' && $password == $row['password']) {
            $_SESSION['name'] = $username;
            $_SESSION['email'] = $email;
            echo "<script type='text/javascript'>alert('Login Success, Welcome ' + '$username'); window.location.href='admin_dashboard.php'; </script>";
        } else {
            echo "<script type='text/javascript'>alert('The account type is not admin');</script>";
        }
    }
}


// Log out process
if (isset($_POST['log_out'])) {
    session_destroy();
    echo "<script type='text/javascript'>alert('Logged out successfully'); window.location.href='login.php'; </script>";
}

