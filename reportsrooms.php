<?php

include('php/connection.php'); // Database connection file

$hname = $_SESSION['hname'];

// Fetch total number of rooms
$totalRoomsQuery = "SELECT COUNT(*) AS total_rooms FROM rooms WHERE hname = '$hname'";
$totalRoomsResult = mysqli_query($conn, $totalRoomsQuery);
$totalRooms = mysqli_fetch_assoc($totalRoomsResult)['total_rooms'];

// Fetch the room with the highest capacity
$highestCapacityQuery = "SELECT room_no, capacity FROM rooms WHERE hname = '$hname' ORDER BY capacity DESC LIMIT 1";
$highestCapacityResult = mysqli_query($conn, $highestCapacityQuery);
$highestCapacityRoom = mysqli_fetch_assoc($highestCapacityResult);
$highestCapacityRoomNo = $highestCapacityRoom['room_no'];
$highestCapacity = $highestCapacityRoom['capacity'];

// Fetch the room with the highest current tenants
$highestTenantQuery = "SELECT room_no, current_tenant FROM rooms WHERE hname = '$hname' ORDER BY current_tenant DESC LIMIT 1";
$highestTenantResult = mysqli_query($conn, $highestTenantQuery);
$highestTenantRoom = mysqli_fetch_assoc($highestTenantResult);
$highestTenantRoomNo = $highestTenantRoom['room_no'];
$highestTenantCount = $highestTenantRoom['current_tenant'];

// Fetch counts of available and full rooms
$roomStatusQuery = "SELECT 
                        SUM(status = 'Available') AS available_rooms, 
                        SUM(status = 'Full') AS full_rooms 
                    FROM rooms 
                    WHERE hname = '$hname'";
$roomStatusResult = mysqli_query($conn, $roomStatusQuery);
$roomStatusData = mysqli_fetch_assoc($roomStatusResult);
$availableRooms = $roomStatusData['available_rooms'];
$fullRooms = $roomStatusData['full_rooms'];

// Fetch detailed room data
$roomsQuery = "SELECT room_no, capacity, current_tenant, amenities, price, status FROM rooms WHERE hname = '$hname' ORDER BY room_no ASC";
$roomsResult = mysqli_query($conn, $roomsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include 'navigationbar.php'; ?>

    <!-- Room Summary Section -->
    <div class="summary">
        <div class="card">
            <h3>Total Rooms</h3>
            <p class="total-amount"><?php echo $totalRooms; ?></p>
        </div>
        <div class="card">
            <h3>Highest Capacity</h3>
            <p class="total-amount"><?php echo "Room $highestCapacityRoomNo - $highestCapacity"; ?></p>
        </div>
        <div class="card">
            <h3>Highest Current Tenants</h3>
            <p class="total-amount"><?php echo "Room $highestTenantRoomNo - $highestTenantCount Tenants"; ?></p>
        </div>
        <div class="card">
            <h3>Available Rooms</h3>
            <p class="total-amount"><?php echo $availableRooms; ?></p>
        </div>
        <div class="card">
            <h3>Full Rooms</h3>
            <p class="total-amount"><?php echo $fullRooms; ?></p>
        </div>
    </div>

    <!-- Room Details Table -->
    <div class="container">
        <h2>Room Details</h2>
        <table id="roomsTable" class="display">
            <thead>
                <tr>
                    <th>Room No</th>
                    <th>Capacity</th>
                    <th>Current Tenants</th>
                    <th>Amenities</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($room = mysqli_fetch_assoc($roomsResult)) { ?>
                    <tr>
                        <td><?php echo $room['room_no']; ?></td>
                        <td><?php echo $room['capacity']; ?></td>
                        <td><?php echo $room['current_tenant']; ?></td>
                        <td><?php echo $room['amenities']; ?></td>
                        <td><?php echo number_format($room['price'], 2); ?> PHP</td>
                        <td><?php echo $room['status']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


</body>
</html>