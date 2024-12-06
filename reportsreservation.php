<?php
include('php/connection.php'); // Database connection file

$hname = $_SESSION['hname'];

// Fetch total reservations
$totalReservationsQuery = "SELECT COUNT(*) AS total_reservations FROM reservation WHERE hname = '$hname'";
$totalReservationsResult = mysqli_query($conn, $totalReservationsQuery);
$totalReservations = mysqli_fetch_assoc($totalReservationsResult)['total_reservations'];

// Fetch most reserved room
$mostReservedRoomQuery = "SELECT room_no, COUNT(room_no) AS count 
                          FROM reservation 
                          WHERE hname = '$hname' 
                          GROUP BY room_no 
                          ORDER BY count DESC LIMIT 1";
$mostReservedRoomResult = mysqli_query($conn, $mostReservedRoomQuery);
$mostReservedRoom = mysqli_fetch_assoc($mostReservedRoomResult);

// Fetch most common gender reserved
$mostGenderReservedQuery = "SELECT gender, COUNT(gender) AS count 
                            FROM reservation 
                            WHERE hname = '$hname' 
                            GROUP BY gender 
                            ORDER BY count DESC LIMIT 1";
$mostGenderReservedResult = mysqli_query($conn, $mostGenderReservedQuery);
$mostGenderReserved = mysqli_fetch_assoc($mostGenderReservedResult);

// Fetch most common student status reserved
$mostStudentStatusReservedQuery = "SELECT status, COUNT(status) AS count 
                                   FROM reservation 
                                   WHERE hname = '$hname' 
                                   GROUP BY status 
                                   ORDER BY count DESC LIMIT 1";
$mostStudentStatusReservedResult = mysqli_query($conn, $mostStudentStatusReservedQuery);
$mostStudentStatusReserved = mysqli_fetch_assoc($mostStudentStatusReservedResult);

// Fetch email with the highest reservations
$emailHighestReservationsQuery = "SELECT email, COUNT(email) AS count 
                                  FROM reservation 
                                  WHERE hname = '$hname' 
                                  GROUP BY email 
                                  ORDER BY count DESC LIMIT 1";
$emailHighestReservationsResult = mysqli_query($conn, $emailHighestReservationsQuery);
$emailHighestReservations = mysqli_fetch_assoc($emailHighestReservationsResult);

// Fetch counts for reservation statuses
$reservationStatusQuery = "SELECT 
                              SUM(res_stat = 'Approved') AS approved_count,
                              SUM(res_stat = 'Rejected') AS rejected_count,
                              SUM(res_stat = 'Ended') AS ended_count,
                              SUM(res_stat = 'Cancelled') AS cancelled_count
                           FROM reservation 
                           WHERE hname = '$hname'";
$reservationStatusResult = mysqli_query($conn, $reservationStatusQuery);
$reservationStatusData = mysqli_fetch_assoc($reservationStatusResult);

$approvedCount = $reservationStatusData['approved_count'];
$rejectedCount = $reservationStatusData['rejected_count'];
$endedCount = $reservationStatusData['ended_count'];
$cancelledCount = $reservationStatusData['cancelled_count'];

// Fetch reservation details for table
$reservationQuery = "SELECT * FROM reservation WHERE hname = '$hname' ORDER BY id DESC";
$reservationResult = mysqli_query($conn, $reservationQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- jQuery (necessary for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    
</head>
<body>
    <?php include 'navigationbar.php'; ?>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin-left: 220px; /* Offset for the navbar */
            padding: 20px;
        }

        .summary {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            width: calc(33.333% - 20px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            text-align: center;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h3 {
            font-size: 1.5em;
            color: #333;
        }

        table.dataTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        <h1>Reservation Reports for <?php echo $hname; ?></h1>

        <!-- Summary Cards -->
        <div class="summary">
            <div class="card">
                <h3>Total Reservations</h3>
                <p><?php echo $totalReservations; ?></p>
            </div>
            <div class="card">
                <h3>Most Reserved Room</h3>
                <p><?php echo $mostReservedRoom['room_no'] . ' (' . $mostReservedRoom['count'] . ' reservations)'; ?></p>
            </div>
            <div class="card">
                <h3>Gender with most Reservation</h3>
                <p><?php echo $mostGenderReserved['gender'] . ' (' . $mostGenderReserved['count'] . ')'; ?></p>
            </div>
            <div class="card">
                <h3>Most Reserved Status</h3>
                <p><?php echo $mostStudentStatusReserved['status'] . ' (' . $mostStudentStatusReserved['count'] . ')'; ?></p>
            </div>
            <div class="card">
                <h3>Email with Highest Reservations</h3>
                <p><?php echo $emailHighestReservations['email'] . ' (' . $emailHighestReservations['count'] . ')'; ?></p>
            </div>
            <div class="card">
                <h3>Approved Reservations</h3>
                <p><?php echo $approvedCount; ?></p>
            </div>
            <div class="card">
                <h3>Rejected Reservations</h3>
                <p><?php echo $rejectedCount; ?></p>
            </div>
            <div class="card">
                <h3>Ended Reservations</h3>
                <p><?php echo $endedCount; ?></p>
            </div>
            <div class="card">
                <h3>Cancelled Reservations</h3>
                <p><?php echo $cancelledCount; ?></p>
            </div>
        </div>

        <h2>Reservation Details</h2>
            <table id="reservationTable" class="display">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Room No</th>
                        <th>Status</th>
                        <th>Requests</th>
                        <th>Date In</th>
                        <th>Date Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reservation = mysqli_fetch_assoc($reservationResult)) { ?>
                        <tr>
                            <td><?php echo $reservation['fname']; ?></td>
                            <td><?php echo $reservation['lname']; ?></td>
                            <td><?php echo $reservation['email']; ?></td>
                            <td><?php echo $reservation['room_no']; ?></td>
                            <td><?php echo $reservation['status']; ?></td>
                            <td><?php echo $reservation['addons']; ?></td>
                            <td><?php echo $reservation['date_in']; ?></td>
                            <td><?php echo $reservation['date_out'] ?: 'N/A'; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        <script>
            $(document).ready(function() {
                $('#reservationTable').DataTable();
            });
        </script>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>