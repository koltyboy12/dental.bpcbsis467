<?php
// Include the database connection script
include ('./function/alert.php');
// Function to fetch schedule data with week
function getWeeklySchedule($week)
{
    global $conn;
    $sql = "SELECT date, WEEK(date) as week FROM schedule WHERE WEEK(date) = $week"; // Modified SQL query to fetch data for a specific week
    $result = $conn->query($sql);
    $dateLabels = array();
    $dateOccurrences = array();

    // Initialize the occurrences array for all days of the week
    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    foreach ($daysOfWeek as $day) {
        $dateLabels[] = $day;
        $dateOccurrences[$day] = 0;
    }

    if ($result->num_rows > 0) {
        while ($week_row = $result->fetch_assoc()) {
            $date = $week_row['date'];
            // Increment the occurrence count for the corresponding day
            $dayOfWeek = date('l', strtotime($date));
            $dateOccurrences[$dayOfWeek]++;
        }
    }
    return array(
        'labels' => $dateLabels,
        'occurrences' => array_values($dateOccurrences)
    );
}

// Handle navigation
$week = isset($_GET['week']) ? $_GET['week'] : date('W'); // Default to current week
$weeklySchedule = getWeeklySchedule($week);
$dateLabels = $weeklySchedule['labels'];
$dateOccurrences = $weeklySchedule['occurrences'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Appointment History</title><!--chart_bar-->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../../css/styles.css" rel="stylesheet" />
    <!--Online Icon Design;-->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!--BoxIcons-->
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
</head>

<body class="sb-nav-fixed">
    <!--Top Navbar-->
    <?php include ('./function/navbar.php'); ?>
    <div id="layoutSidenav">
        <!--Nav Sidebar-->
        <?php include ('./function/sidebar.php'); ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Appointment History</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item">My Pages</li>
                        <li class="breadcrumb-item">My Reports</li>
                        <li class="breadcrumb-item active">Appointment History</li>
                    </ol>
                    <!--Message-->
                    <?php if (isset($_SESSION['success'])) { ?>
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                onclick="window.location.href ='apk_history.php'"><span aria-hidden="true"><i
                                        class='bx bx-x-circle'></i></span></button>
                            <?php echo $_SESSION['success']; ?>
                        </div>
                        <?php
                        unset($_SESSION['success']);
                    } ?>
                    <!--space-->
                    <?php if (isset($_SESSION['failed'])) { ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                onclick="window.location.href ='apk_history.php'"><span aria-hidden="true"><i
                                        class='bx bx-x-circle'></i></span></button>
                            <?php echo $_SESSION['failed']; ?>
                        </div>
                        <?php
                        unset($_SESSION['failed']);
                    } ?>
                    <!--Chart-->
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-area me-1"></i>
                                    Apointment History
                                </div>
                                <div class="card-body"><br><br><canvas id="myChart" width="100%" height="40"></canvas>
                                </div>
                                <?php
                                // Your PHP data
                                //history
                                $history_query = "SELECT * from schedule where `location_id` = '{$row['location_id']}' AND timestamp";
                                $history_query_run = mysqli_query($conn, $history_query);
                                $history_total = mysqli_num_rows($history_query_run);
                                //Done
                                $done_query = "SELECT * from schedule where status= 'Done' AND `location_id` = '{$row['location_id']}'";
                                $done_query_run = mysqli_query($conn, $done_query);
                                $done_total = mysqli_num_rows($done_query_run);
                                //Pending
                                $pending_query = "SELECT * from schedule where status= 'Waiting' AND `location_id` = '{$row['location_id']}'";
                                $pending_query_run = mysqli_query($conn, $pending_query);
                                $pending_total = mysqli_num_rows($pending_query_run);
                                //Cancelled
                                $cancelled_query = "SELECT * from schedule where status= 'Cancelled' AND `location_id` = '{$row['location_id']}'";
                                $cancelled_query_run = mysqli_query($conn, $cancelled_query);
                                $cancelled_total = mysqli_num_rows($cancelled_query_run);
                                //Value
                                $xValues = ["Done Apointment", "Pending Apointment", "Cancelled Apointment",];
                                $yValues = [$done_total, $pending_total, $cancelled_total];
                                $barColors = ["#28a745", "#ffc107", "#dc3545"];
                                ?>
                                <div class="card-footer small text-muted">
                                    <?php
                                    $time_query = "SELECT COUNT(*) AS total_rows, MAX(timestamp) AS last_timestamp FROM schedule where `location_id` = '{$row['location_id']}'";
                                    $time_query_run = mysqli_query($conn, $time_query);
                                    if ($history_query_run && mysqli_num_rows($time_query_run) > 0) {
                                        $time_row = mysqli_fetch_assoc($time_query_run);
                                        $time_total = $time_row['total_rows'];
                                        $last_timestamp = $time_row['last_timestamp'];
                                        ?>
                                        <label class="mb-0">
                                            Total Rows:
                                            <?php echo $time_total; ?><br>
                                            Last Timestamp:
                                            <?php echo $last_timestamp; ?>
                                        </label>
                                    <?php } else { ?>
                                        <h4 class="mb-0">0</h4>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Weekly Schedule Bar Chart
                                </div>
                                <div class="card-body">
                                    <div>
                                        <a class="btn btn-secondary" href="?week=<?php echo $week - 1; ?>">Previous</a>
                                        <!-- Navigate to previous week -->
                                        <a class="btn btn-secondary" href="?week=<?php echo $week + 1; ?>">Next</a>
                                        <!-- Navigate to next week -->
                                    </div>
                                    <canvas id="scheduleChart" width="800" height="400"></canvas>
                                </div>
                                <div class="card-footer small text-muted">
                                    <p>
                                        <span id="weekLabel">Week
                                            <?php echo $week; ?>
                                        </span> <!-- Display current week -->
                                        <span id="weekDates"></span> <!-- Display dates for the week -->
                                    </p>
                                    <?php
                                    $time_query = "SELECT COUNT(*) AS total_rows, MAX(timestamp) AS last_timestamp FROM schedule WHERE `location_id` = '{$row['location_id']}'";
                                    $time_query_run = mysqli_query($conn, $time_query);
                                    if ($time_query_run && mysqli_num_rows($time_query_run) > 0) {
                                        $week_row = mysqli_fetch_assoc($time_query_run);
                                        $last_timestamp = $week_row['last_timestamp'];
                                        ?>
                                        <label class="mb-0">
                                            Last Timestamp:
                                            <?php echo $last_timestamp; ?>
                                        </label>
                                        <?php
                                    } else {
                                        ?>
                                        <h4 class="mb-0">0</h4>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Card-->
                    <?php include ('./function/card.php'); ?>
                    <!--Secondary Nav-->
                    <div class="card mb-4">
                        <!--Secondary Nav-->
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <!--Help-->
                            <a class="btn btn-info text-white" data-bs-toggle="collapse"
                                data-bs-target="#pagesCollapsehow" aria-expanded="false"
                                aria-controls="pagesCollapsehow"><i class='bx bx-info-circle'></i>
                                How
                                to use</a>
                            <a class="btn btn-primary text-white" href="./function/print/history.php"><i
                                    class='bx bxs-printer'></i> Print All</a>
                            <a class="btn btn-dark text-white" href="./function/download/history.php"><i
                                    class='bx bxs-download'></i> Download All</a>
                        </div>
                    </div>
                    <!--Table-->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            DataTable for List of Appointment History
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover dt-responsive" id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Account Status</th>
                                        <th>Dentist Duty</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Member</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Account Status</th>
                                        <th>Dentist Duty</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php $schdule_query = mysqli_query($conn, "select * from schedule WHERE `location_id` = '{$row['location_id']}'") or die(mysqli_error($conn));
                                    while ($schdule_row = mysqli_fetch_array($schdule_query)) {
                                        $id = $schdule_row['id'];
                                        $timeslot = $schdule_row['timeslot'];
                                        $member_id = $schdule_row['member_id'];
                                        $account_id = $schdule_row['user_id'];
                                        $service_id = $schdule_row['service_id'];

                                        $timeslot_query = mysqli_query($conn, "select * from timeslot where timeslot = '$timeslot'") or die(mysqli_error($conn));
                                        $timeslot_row = mysqli_fetch_array($timeslot_query);
                                        /* member query  */
                                        $member_query = mysqli_query($conn, "select * from members where member_id = ' $member_id'") or die(mysqli_error($conn));
                                        $member_row = mysqli_fetch_array($member_query);
                                        /* service query  */
                                        $account_query = mysqli_query($conn, "select * from users where user_id = ' $account_id' ") or die(mysqli_error($conn));
                                        $account_row = mysqli_fetch_array($account_query);
                                        /* service query  */
                                        $service_query = mysqli_query($conn, "select * from service where service_id = '$service_id' ") or die(mysqli_error($conn));
                                        $service_row = mysqli_fetch_array($service_query);
                                        ?>
                                        <tr>
                                            <td>
                                                <!--NAME-->
                                                <?php echo $member_row['firstname'] . " " . $member_row['lastname']; ?>
                                            </td>
                                            <td>
                                                <?php echo $schdule_row['date']; ?>
                                            </td>
                                            <td>
                                                <?php
                                                // Extracting time start and time end from the database
                                                $time_start = $timeslot_row['time_start'];
                                                $time_end = $timeslot_row['time_end'];

                                                // Converting time to AM/PM format
                                                $time_start_ampm = date("h:i A", strtotime($time_start));
                                                $time_end_ampm = date("h:i A", strtotime($time_end));

                                                echo $time_start_ampm . " to " . $time_end_ampm;
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $service_row['service_offer']; ?>
                                            </td>
                                            <td>
                                                <?php if ($schdule_row['status'] == "Done") { ?>
                                                    <button class="btn btn-success" disabled>Appointment Done</button>
                                                <?php } else if ($schdule_row['status'] == "Waiting") { ?>
                                                        <button class="btn btn-warning" disabled>Pending</button>
                                                <?php } else if ($schdule_row['status'] == "Process") { ?>
                                                            <button class="btn btn-primary" disabled>Process</button>
                                                <?php } else if ($schdule_row['status'] == "Cancelled") { ?>
                                                                <button class="btn btn-danger" disabled>Cancelled</button>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($member_row['status'] == "active") { ?>
                                                    <form action="./function/update.php" method="GET">
                                                        <input type="hidden" name="user_id"
                                                            value="<?php echo $_SESSION['admin_id']; ?>">
                                                        <input type="hidden" name="member_id"
                                                            value="<?php echo $member_row['member_id']; ?>">
                                                        <input type="hidden" name="status" value="deactivate">
                                                        <button type="submit" class="btn bg-success text-white"
                                                            name="update_deactivate"
                                                            onclick="return confirm('Are you sure you want to deactivate the account?')">Activate</button>
                                                    </form>
                                                <?php } else if ($member_row['status'] == "deactivate") { ?>
                                                        <form action="./function/update.php" method="GET">
                                                            <input type="hidden" name="user_id"
                                                                value="<?php echo $_SESSION['admin_id']; ?>">
                                                            <input type="hidden" name="member_id"
                                                                value="<?php echo $member_row['member_id']; ?>">
                                                            <input type="hidden" name="status" value="active">
                                                            <button type="submit" class="btn bg-danger text-white"
                                                                name="update_active"
                                                                onclick="return confirm('Are you sure you want to activate the account?')">Deactivate</button>
                                                        </form>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php
                                                if (!empty($account_row['username'])) {
                                                    echo $account_row['username'];
                                                } else {
                                                    // If $account_row['username'] is empty, you can echo some default content or leave it blank
                                                    echo "Username Not Available";
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a class="btn"
                                                    href="./schedule_view.php?id=<?php echo $schdule_row['id']; ?>"
                                                    name="schedule_view"><i class='bx bxs-info-circle'></i>
                                                    View</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../../js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../../assets/demo/chart-area-demo.js"></script>
    <script src="../../assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="../../js/datatables-simple-demo.js"></script>

    <!--JS Addition-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"> </script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="./js/script.js"></script>


    <script>
        /* Pie Chart for Appointment */
        var xValues = <?php echo json_encode($xValues); ?>;
        var yValues = <?php echo json_encode($yValues); ?>;
        var barColors = <?php echo json_encode($barColors); ?>;

        new Chart("myChart", {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                }]
            },
            options: {
                legend: { display: false },
                title: {
                    display: true,
                    text: "The Total :<?php echo json_encode($history_total); ?>",
                },
            }
        });
        /* Bar Chart */
        /* Bar Chart */
        document.addEventListener('DOMContentLoaded', function () {
            var dateLabels = <?php echo json_encode($dateLabels); ?>;
            var dateOccurrences = <?php echo json_encode($dateOccurrences); ?>;
            var weekNumber = <?php echo $week; ?>;

            var ctx = document.getElementById('scheduleChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dateLabels,
                    datasets: [{
                        label: 'Events in week',
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        data: dateOccurrences
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });

            // Function to get the start and end dates of the week
            function getWeekDates(week, year) {
                var date = new Date(year, 0, 1 + (week - 1) * 7);
                var startDate = new Date(date);
                var endDate = new Date(date.setDate(date.getDate() + 6));
                return [startDate.toDateString(), endDate.toDateString()];
            }

            var currentYear = new Date().getFullYear();
            var [startDate, endDate] = getWeekDates(weekNumber, currentYear);
            document.getElementById('weekDates').textContent = startDate + ' to ' + endDate;
        });

    </script>
</body>

</html>