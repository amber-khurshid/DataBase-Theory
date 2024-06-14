<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .left-sidebar {
            background-color: white;
            color: #fff;
            padding: 20px;
            min-height: 100vh;
        }
        .left-sidebar a {
            display: block;
            padding: 10px 0;
            text-align: center;
            font-size: 1.2em;
            text-decoration: none;
        }
        .left-sidebar a:hover {
            color: blue;
        }
        .left-sidebar .bottom a {
            margin-top: 20px;
        }
        .main {
            padding: 30px;
        }
        .menu-icon {
            font-size: 1.5em;
            margin-right: 10px;
        }
        .chart-container {
            width: 400px;
            height: 400px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        canvas {
            max-width: 100% !important;
            max-height: 100% !important;
        }
        h2 {
            padding-top: 20px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 1.5em;
        }
        h1.p-1 {
            padding: 20px 0;
        }
        .clearfix {
            margin-bottom: 20px;
        }
        .stats-box {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-box .icon {
            font-size: 2em;
            margin-bottom: 10px;
            color: #007bff;
        }
        .stats-box .number {
            font-size: 1.5em;
            font-weight: bold;
        }
        .stats-box .label {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 left-sidebar">
                    <img src="https://upload.wikimedia.org/wikipedia/en/thumb/e/e4/National_University_of_Computer_and_Emerging_Sciences_logo.png/250px-National_University_of_Computer_and_Emerging_Sciences_logo.png" alt="" style="max-width: 80px; height: auto;">
                    <br><br><br>
                    <a href="admin_profile.php"><i class="fa fa-user"></i></a>
                    <a href="campus.php"><i class="fa fa-university"></i></a>
                    <a href="admin_adduser.php"><i class="fa fa-user-plus"></i></a>
                    <a href="add_semester.php"><i class="fa fa-calendar-plus"></i></a>
                    <a href="courses.php"><i class="fa fa-book"></i></a>
                    <a href="instructor_add.php"><i class="fa fa-chalkboard-teacher"></i></a>
                    <a href="student_add.php"><i class="fa fa-user-graduate"></i></a>
                    <a href="services.php"><i class="fa fa-cogs"></i></a>
                    
                    <div class="bottom">
                        <a href="login.php"><i class="fas fa-sign-out-alt"></i></a>
                    </div>
                </div>
                <div class="col-md-9 main">
                    <a href="#" style="text-decoration:none"><i class="fa fa-reorder menu-icon"></i></a>
                    <div class="clearfix"></div>
                    <h1 class="p-1">Admin Dashboard</h1>

                    <div class="row">
                        <div class="col-md-4 stats-box">
                            <div class="icon"><i class="fa fa-user"></i></div>
                            <div class="number" id="students-count">0</div>
                            <div class="label">Students</div>
                        </div>
                        <div class="col-md-4 stats-box">
                            <div class="icon"><i class="fa fa-chalkboard-teacher"></i></div>
                            <div class="number" id="instructors-count">0</div>
                            <div class="label">Instructors</div>
                        </div>
                        <div class="col-md-4 stats-box">
                            <div class="icon"><i class="fa fa-book"></i></div>
                            <div class="number" id="courses-count">0</div>
                            <div class="label">Courses</div>
                        </div>
                    </div>

                    <div class="chart-container">
                        <canvas id="myChart" width="400" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
    <!-- Custom JS -->
    <script>
    
        $(document).ready(function() {
            $.ajax({
                url: 'admin_getcounts.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#students-count').text(data.student);
                    $('#instructors-count').text(data.instructor);
                    $('#courses-count').text(data.course);

                    var ctx = document.getElementById('myChart').getContext('2d');
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Students', 'Instructors', 'Courses'],
                            datasets: [{
                                label: 'Total  ',
                                data: [data.student, data.instructor, data.course],
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(255, 206, 86, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 206, 86, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

