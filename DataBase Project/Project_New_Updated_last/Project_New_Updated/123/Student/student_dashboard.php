<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <link rel="stylesheet" href="student_dashboard.css">
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
            width: 600px;
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
                    <img src="https://upload.wikimedia.org/wikipedia/en/thumb/e/e4/National_University_of_Computer_and_Emerging_Sciences_logo.png/250px-National_University_of_Computer_and_Emerging_Sciences_logo.png" alt="University Logo" style="max-width: 80px; height: auto;">
                    <br><br><br>
                    <a href="student_student.php"><i class="fa fa-user"></i></a>
                    <a href="student_index.php"><i class="fa fa-book"></i></a>
                    <a href="student_grades.php"><i class="fa fa-chart-bar"></i></a>
                    <a href="student_services.php"><i class="fa fa-cogs"></i></a>
                    <a href="../chatbot.php"><i class="fa fa-robot"></i></a>
                    <div class="bottom">
                        <a href="../login.php"><i class="fas fa-sign-out-alt"></i></a>
                    </div>
                </div>
                <div class="col-md-9 main">
                    <a href="#" style="text-decoration:none"><i class="fa fa-reorder menu-icon"></i></a>
                  
                    <div class="clearfix"></div>
                    <h1 class="p-1">Student Dashboard</h1>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-box">
                                <div class="icon"><i class="fas fa-user-graduate"></i></div>
                                <div class="number" id="cgpa">0</div>
                                <div class="label">CGPA</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-box">
                                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                                <div class="number" id="totalSemesters">0</div>
                                <div class="label">Total Semesters</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-box">
                                <div class="icon"><i class="fas fa-cogs"></i></div>
                                <div class="number" id="totalServices">0</div>
                                <div class="label">Total Services</div>
                            </div>
                        </div>
                    </div>

                    <h2>Course Enrollment Statistics</h2>
                    <div class="chart-container">
                        <canvas id="myChart"></canvas>
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
                url: 'fetch_student_dashboard_data.php',
                method: 'GET',
                success: function(response) {
                    try {
                        const parsedResponse = JSON.parse(response);

                        if (parsedResponse.error) {
                            alert(parsedResponse.error);
                            return;
                        }

                        const { data, cgpa, totalSemesters, totalServices } = parsedResponse;
                        
                        $('#cgpa').text(parseFloat(cgpa).toFixed(2));
                        $('#totalSemesters').text(totalSemesters);
                        $('#totalServices').text(totalServices);

                        const semesters = data.map(item => item.Semester);
                        const gpas = data.map(item => parseFloat(item.GPA));

                        const ctx = document.getElementById('myChart').getContext('2d');
                        const myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: semesters,
                                datasets: [{
                                    label: 'GPA',
                                    data: gpas,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ],
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    },
                                    x: {
                                        barPercentage: 0.5
                                    }
                                }
                            }
                        });
                    } catch (error) {
                        console.error("Parsing error:", error);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX request failed:", textStatus, errorThrown);
                }
            });
        });
    </script>
</body>
</html>

