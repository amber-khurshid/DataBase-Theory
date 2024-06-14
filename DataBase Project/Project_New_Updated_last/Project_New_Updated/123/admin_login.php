<?php include('server.php') ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FAST NUCES</title>
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
  <link rel="stylesheet" href="AdminLogin.css">
  <style>
    body {
  margin: 80px auto 50px;
    }

    .cont {
      margin-top: 50px; 
    }
  </style>
</head>
<body>
<div class="cont">
  <div class="form sign-in">
    <form method="post" id="admin_login" action="admin_login.php">
      <h2>Admin Login </h2>
      <label>
        <span>Email</span>
        <input type="email" name="email" id="admin_signin_email" placeholder="abc@nu.edu.pk" required pattern="(.+@nu.edu.pk)"
               title="Please enter an email with Medipol extension"><br>
      </label>
      <label>
        <span>Password</span>
        <input type="password" name="password" id="admin_signin_password" pattern=".{5,15}" required title="5 to 15 characters"
               placeholder="**********"><br>
      </label>
      <button class="submit" name="login_admin">Sign In</button>
    </form>
    <a href="login.php"><button class="fb-btn"><span>Continue as student</span></button></a>
    <a href="login.php" class="back-btn"><button class="fb-btn"><span>Back to Dashboard</span></button></a> <!-- Added button -->
  </div>
</div>

<a class="icon-link">
  <img src="https://upload.wikimedia.org/wikipedia/en/thumb/e/e4/National_University_of_Computer_and_Emerging_Sciences_logo.png/250px-National_University_of_Computer_and_Emerging_Sciences_logo.png">
</a>

</body>
</html>

