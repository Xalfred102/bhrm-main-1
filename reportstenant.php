<?php
include('php/connection.php'); // Database connection file

$hname = $_SESSION['hname'];

// Fetch total tenants
$totalTenantsQuery = "SELECT COUNT(*) AS total_tenants FROM users WHERE hname = '$hname' and role = 'user'";
$totalTenantsResult = mysqli_query($conn, $totalTenantsQuery);
$totalTenants = mysqli_fetch_assoc($totalTenantsResult)['total_tenants'];

// Fetch total male and female tenants
$genderCountQuery = "SELECT 
                        SUM(gender = 'Male') AS male_count, 
                        SUM(gender = 'Female') AS female_count 
                     FROM users 
                     WHERE hname = '$hname'";
$genderCountResult = mysqli_query($conn, $genderCountQuery);
$genderCount = mysqli_fetch_assoc($genderCountResult);

$maleCount = $genderCount['male_count'];
$femaleCount = $genderCount['female_count'];

// Fetch tenant details for table
$tenantDetailsQuery = "SELECT * FROM users WHERE hname = '$hname' ORDER BY id DESC";
$tenantDetailsResult = mysqli_query($conn, $tenantDetailsQuery);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants Report - <?php echo $hname; ?></title>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- jQuery (necessary for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin-left: 220px; /* Offset for the navbar */
            padding: 20px;
        }

        .summary {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            width: calc(33.333% - 20px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            font-size: 1.5em;
            color: #333;
        }

        table.dataTable {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table.dataTable th,
        table.dataTable td {
            padding: 15px;
            text-align: left;
        }
    </style>
</head>
<body>
    <?php include 'navigationbar.php'; ?>

    <div class="container">
        <h1>Tenant Reports for <?php echo $hname; ?></h1>

        <!-- Summary Cards -->
        <div class="summary">
            <div class="card">
                <h3>Total Tenants</h3>
                <p><?php echo $totalTenants; ?></p>
            </div>
            <div class="card">
                <h3>Total Male Tenants</h3>
                <p><?php echo $maleCount; ?></p>
            </div>
            <div class="card">
                <h3>Total Female Tenants</h3>
                <p><?php echo $femaleCount; ?></p>
            </div>
        </div>

        <!-- Tenant Details Table -->
        <h2>Tenant Details</h2>
        <table id="tenantTable" class="display">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tenant = mysqli_fetch_assoc($tenantDetailsResult)) { ?>
                    <tr>
                        <td><?php echo $tenant['fname']; ?></td>
                        <td><?php echo $tenant['lname']; ?></td>
                        <td><?php echo $tenant['gender']; ?></td>
                        <td><?php echo $tenant['uname']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#tenantTable').DataTable();
        });
    </script>
</body>
</html>

</body>
</html>