$(document).ready(function() {
    $.ajax({
        url: 'fetch_dashboard_data.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log(data); // Debug: Check the fetched data

            // Clear any existing content in the container
            $('.row').empty();

            // Prepare data for the pie chart
            var semesterLabels = [];
            var studentCounts = [];

            data.semesters.forEach(function(semester) {
                semesterLabels.push('Semester ' + semester.id);
                studentCounts.push(semester.students);
            });

            // Create the pie chart
            var ctx = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: semesterLabels,
                    datasets: [{
                        label: 'Students per Semester',
                        data: studentCounts,
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
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Add overall statistics
            var overallHtml = `
                <div class="col-md-4">
                    <div class="info-box">
                        <h3>Overall</h3>
                        <p>Total Semesters: <span>${data.total.semesters}</span></p>
                    </div>
                </div>
            `;
            $('.row').append(overallHtml);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching dashboard data:', error);
        }
    });
});

