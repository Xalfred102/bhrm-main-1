<?php

include('php/connection.php'); // Database connection file

$hname = $_SESSION['hname'];

// Fetch total sum of payments from the reports table (based on payment)
$totalPaymentsQuery = "SELECT SUM(payment) AS total_payments 
                       FROM reports 
                       WHERE hname = '$hname' AND pay_date IS NOT NULL";
$totalPaymentsResult = mysqli_query($conn, $totalPaymentsQuery);
$totalPayments = mysqli_fetch_assoc($totalPaymentsResult)['total_payments'];

// Fetch total number of tenants (counting reports entries)
$paymentStatusQuery = "SELECT 
                          SUM(pay_stat = 'Fully Paid') AS fully_paid_count,
                          SUM(pay_stat = 'Not Paid') AS not_paid_count,
                          SUM(pay_stat = 'Partially Paid') AS partially_paid_count
                       FROM reports 
                       WHERE hname = '$hname'";
$paymentStatusResult = mysqli_query($conn, $paymentStatusQuery);
$paymentStatusData = mysqli_fetch_assoc($paymentStatusResult);

$fullyPaidCount = $paymentStatusData['fully_paid_count'];
$notPaidCount = $paymentStatusData['not_paid_count'];
$partiallyPaidCount = $paymentStatusData['partially_paid_count'];

// Fetch detailed report data for the landlord's boarding house
$reportQuery = "SELECT * FROM reports WHERE hname = '$hname' ORDER BY id DESC";
$reportResult = mysqli_query($conn, $reportQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - <?php echo $hname; ?></title>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- jQuery (necessary for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            margin-left: 220px; /* Offset for the navbar */
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        header h1 {
            font-size: 2.5em;
            color: #333;
        }

        /* Style for summary cards */
        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 30%;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            font-size: 1.8em;
            color: #333;
            font-weight: bold;
        }

        .card .total-amount {
            font-size: 2em;
            font-weight: bold;
            color: #fff;
            background-color: #ffc107;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* DataTable styling */
        .dataTables_wrapper {
            padding: 20px;
        }
        .dataTables_length,
        .dataTables_filter {
            margin: 20px 0;
        }

        .dataTables_filter input {
            margin-left: 10px;
            padding: 5px;
        }

        table.dataTable {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table.dataTable th,
        table.dataTable td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table.dataTable th {
            background-color: #ffc107;
            color: #fff;
        }

        table.dataTable tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table.dataTable tr:hover {
            background-color: #f1f1f1;
        }

        table.dataTable td {
            font-size: 1em;
        }
    </style>
</head>
<body>
    <?php include 'navigationbar.php'; ?>

    <div class="container">
        <h1>Reports for <?php echo $hname; ?></h1>

        <!-- Display Total Payments and Tenants -->
        <div class="summary">
            <p><strong>Total Payments:</strong> <?php echo number_format($totalPayments, 2); ?> PHP</p>
            <p><strong>Fully Paid:</strong> <?php echo $fullyPaidCount; ?></p>
            <p><strong>Not Paid:</strong> <?php echo $notPaidCount; ?></p>
            <p><strong>Partially Paid:</strong> <?php echo $partiallyPaidCount; ?></p>
        </div>

        <!-- Detailed Reports Table -->
        <table id="reportTable" class="display">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Room No</th>
                    <th>Room Rent</th>
                    <th>Payment</th>
                    <th>Balance</th>
                    <th>Payment Date</th>
                    <th>Payment Status</th>
                    <th>Date In</th>
                    <th>Date Out</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($report = mysqli_fetch_assoc($reportResult)) {
                        $price = $report['price'] ?: 0; // Default to 0 if price is null
                        $payment = $report['payment'] ?: 0; // Default to 0 if payment is null
                        $balance = $price - $payment; // Calculate balance
                ?>
                    
                    <tr>
                        <td><?php echo $report['fname'] . ' ' . $report['lname']; ?></td>
                        <td><?php echo $report['gender']; ?></td>
                        <td><?php echo $report['email']; ?></td>
                        <td><?php echo $report['room_no']; ?></td>
                        <td><?php echo $report['price']; ?></td>
                        <td><?php echo number_format($report['payment'], 2); ?> PHP</td>
                        <td><?php echo number_format($price, 2); ?> PHP</td>
                        <td><?php echo number_format($payment, 2); ?> PHP</td>
                        <td><?php echo number_format($balance, 2); ?> PHP</td>
                        <td><?php echo $report['pay_date'] ?: 'N/A'; ?></td>
                        <td><?php echo $report['pay_stat'] ?: 'N/A'; ?></td>
                        <td><?php echo $report['date_in']; ?></td>
                        <td><?php echo $report['date_out'] ?: 'N/A'; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#reportTable').DataTable();
        });
    </script>
</body>
</html>
