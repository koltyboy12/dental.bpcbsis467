<?php include ('./function/alert.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Service - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../../css/styles.css" rel="stylesheet" />
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
                    <h1 class="mt-4">List of Service</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item">My Dental</li>
                        <li class="breadcrumb-item active">List of Service</li>
                    </ol>
                    <!--Message-->
                    <?php if (isset ($_SESSION['success'])) { ?>
                        <div class="alert alert-success">
                            <button onclick="window.location.href ='service.php'" type="button" class="close"
                                data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i
                                        class='bx bx-x-circle'></i></span></button>
                            <?php echo $_SESSION['success'];
                            // Unset multiple specific session variables
                            unset($_SESSION['success']); ?>
                        </div>
                    <?php } ?>
                    <!--space-->
                    <?php if (isset ($_SESSION['failed'])) { ?>
                        <div class="alert alert-danger">
                            <button onclick="window.location.href ='service.php'" type="button" class="close"
                                data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i
                                        class='bx bx-x-circle'></i></span></button>
                            <?php echo $_SESSION['failed'];
                            // Unset multiple specific session variables
                            unset($_SESSION['failed']); ?>
                        </div>
                    <?php } ?>
                    <!--Help information-->
                    <div id="pagesCollapsehow" class="collapse alert alert-info">
                        <h3><i class='bx bx-info-circle'></i> How To Use</h3>
                        1. You can Edit the Service Information using the <i class="fas fa-table me-1"></i> DataTable
                        for Contact Staff then select and <i class='bx bxs-edit-alt'></i> Edit Button
                        <br>
                        2. You can Edit the Service if it is <a class="link"
                            href="./service=available_location.php"><strong>Available in
                                Location</strong></a> using the button inside the <i class="fas fa-table me-1"></i> DataTable
                        for Contact Staff.
                        <br>
                        3. You can View the Service if it is <a class="link"
                            href="./service=available_location.php"><strong>Available in
                                Location</strong></a> using the button.
                        <br>
                    </div>
                    <!--Secondary Nav-->
                    <div class="card mb-4">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <!--Help-->
                            <a class="btn btn-info text-white" data-bs-toggle="collapse"
                                data-bs-target="#pagesCollapsehow" aria-expanded="false"
                                aria-controls="pagesCollapsehow"><i class='bx bx-info-circle'></i>
                                How
                                to use</a>
                            <a class="btn btn-secondary active text-white" href="./service.php">All Service</a>
                            <a class="btn btn-success text-white" href="./service=available_location.php">Available in
                                Location</a>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            DataTable for List Services
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover dt-responsive" id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Service Offer</th>
                                        <th>Price</th>
                                        <th>Available in Location</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Service Offer</th>
                                        <th>Price</th>
                                        <th>Available in Location</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $service_query = "SELECT * FROM `service`";
                                    $service_result = mysqli_query($conn, $service_query);
                                    //while statement
                                    while ($service_row = mysqli_fetch_array($service_result)) {
                                        if ($service_row['status'] == "Available") {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $service_row['service_offer']; ?>
                                            </td>
                                            <td> ₱
                                                <?php echo $service_row['price']; ?>
                                            </td>
                                            <td>
                                                <?php
                                                // Check if $service_row['location_id'] exists within the comma-separated list of $row['location_id']
                                                if (strpos($service_row['location_id'], strval($row['location_id'])) !== false) {
                                                    ?>
                                                    <form action="./function/add.php" method="POST">
                                                        <input type="hidden" id="user_id" value="<?php echo $row['user_id']; ?>"
                                                            name="user_id" required>
                                                        <input type="hidden" name="service_id"
                                                            value="<?php echo $service_row['service_id']; ?>">
                                                        <input type="hidden" id="location_id" name="location_id"
                                                            value="<?php echo $row['location_id']; ?>">
                                                        <button type="submit" name="remove_service_location"
                                                            class="btn bg-success text-white" onclick="return confirm('are you sure you want to disable?')">
                                                            Available</button>
                                                    </form>
                                                <?php } else { ?>
                                                    <form action="./function/add.php" method="POST">
                                                        <input type="hidden" id="user_id" value="<?php echo $row['user_id']; ?>"
                                                            name="user_id" required>
                                                        <input type="hidden" name="service_id"
                                                            value="<?php echo $service_row['service_id']; ?>">
                                                        <input type="hidden" id="location_id" name="location_id"
                                                            value="<?php echo $row['location_id']; ?>">
                                                        <button type="submit" name="update_service_location_yes"
                                                            class="btn bg-danger text-white" onclick="return confirm('are you sure you want to active?')">Not
                                                            Available</button>
                                                    </form>
                                                <?php } ?>
                                            </td>
                                            <td width="auto">
                                                <!--Update-->
                                                <a
                                                    href="./function/edit_service.php?service_id=<?php echo $service_row['service_id']; ?>"><button
                                                        class="btn  btn-primary text-white" type="submit"
                                                        name="service_edit"><i class='bx bxs-edit-alt'></i>
                                                        Edit</button></a>
                                            </td>
                                        </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <?php include ('./function/footer.php'); ?>
        </div>
    </div>
    <!--JS Addition from me-->
    <script src="./js/script.js"></script>
    <script rel="stylesheet" src="../../js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
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
</body>

</html>