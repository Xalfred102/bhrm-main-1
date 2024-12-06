<?php 
require 'php/connection.php';

if(!empty($_SESSION["uname"]) && $_SESSION["role"] == 'landlord'){
    $uname = $_SESSION['uname'];
    $query = "select * from boardinghouses inner join documents on boardinghouses.hname = documents.hname where boardinghouses.owner = '$uname'";
    $result = mysqli_query($conn, $query);
    $fetch = mysqli_fetch_assoc($result);   
    echo "
    <script src='jquery.min.js'></script>
    <link rel='stylesheet' href='toastr.min.css'/>
    <script src='toastr.min.js'></script>
    <script>
        $(document).ready(function() {
            // Check if the login message should be displayed
            " . (isset($_SESSION['login_message_displayed']) ? "toastr.success('Logged in Successfully');" : "") . "
        });
    </script>
    ";

    // Unset the session variable to avoid repeated notifications
    if (isset($_SESSION['login_message_displayed'])) {
        unset($_SESSION['login_message_displayed']);
    }
}else{
    header('location: index.php');
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" <?php echo time() ?> rel="stylesheet">
</head>
<!-- Bootstrap CSS -->
    <style>
        /* Custom CSS */
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }
        
        a{
            text-decoration: none;
            color: black;
            
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            margin-left: 220px; /* Offset for the navbar */
        }

        
        .content-background{
            background-color: white;
            margin: 60px 200px 90px 200px;
            border-radius: 10px;
        }

        .back{
            height: 100px;
            display: flex;
            justify-content: right;
            align-items: center;
            margin-right: 50px;
        }.back a{
           height: auto;
        }

        .section2{
            height: 100px;
            display: flex;
            justify-content: left;
            align-items: center;
            margin: 0px 100px;
        }

        @media (max-width: 1000px){
            .section2{
                width: 100%;
                margin: 0px auto 0 auto;
            }
        }
     
        .btn{
            color: rgb(255, 255, 255);
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
        }

        .section3{
            height: auto;
            display: flex;
            border-radius: 10px;
            flex-wrap: wrap;
            padding-top: 5px;
            padding-left: 30px;
            padding-right: 30px;
            padding-bottom: 20px;
        }

        @media (max-width: 1000px){
            .section3{
                justify-content: center;
            }
        }

        .section3::-webkit-scrollbar {
            display: none; /* For Chrome, Safari, and Opera */
        }
    </style>
<body>
    <?php include 'navigationbar.php'; ?>

    <div class="section2">
        <div type="button" class="btn btn-warning">
            <a href='php/roomfunction.php' class='btn'>Add Rooms</a>
        </div>
    </div>
        
    <div class="section3">            
        <?php 
            $hname = $_SESSION['hname'];

            $query = "SELECT * FROM rooms WHERE hname = '$hname' order by room_no";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {  // Check if there are any results
                while ($fetch = mysqli_fetch_assoc($result)) {
                    $id = $fetch['id'];
                    $hname = $fetch['hname'];
                    $tenantcount = $fetch['current_tenant'];
                    $roomno = $fetch['room_no'];
                    $capacity = $fetch['capacity'];
            ?>
            <div class="card">
                <img src="<?php echo $fetch['image']?>" width="20%" class="card-img-top" alt="Room Image">
                <div class="card-content">
                <h5><strong>Room No:</strong> <?php echo $fetch['room_no']; ?></h5>
                <p><strong>Capacity:</strong> <?php echo $fetch['capacity']; ?></p>
                <p><strong>Rent / Month:</strong> <?php echo $fetch['price']; ?></p>
                <p><strong>The rent is based on each person.</strong></p>
                <p><strong>Amenities:</strong> <?php echo $fetch['amenities']; ?></p>
                <p><strong>Tenant Type:</strong> <?php echo $fetch['tenant_type']; ?> Only </p>
                <p><strong>Current Tenant:</strong> <?php echo $fetch['current_tenant']; ?> / <?php echo $fetch['capacity']; ?></p>
                <p><strong>Room Floor:</strong> <?php echo $fetch['room_floor']; ?></p>
                <p><strong>Status:</strong> <?php echo $fetch['status']; ?></p>
                    <style>
                        .card{
                            width: 360px;
                            border-radius: 8px;
                            overflow: hidden;
                            box-shadow: 0px 10px 20px #aaaaaa;
                            margin: 20px;
                            display: flex;
                            flex-direction: column; /* Ensure the flex direction is column */
                            justify-content: space-between; /* Align items to the bottom */
                            padding-bottom: 10px;
                            height: auto;
                        }
                        .card img{
                            width: 100%;
                            height: 50%;
                        }
                        
                        .card-content{
                            padding: 16px;
                        }

                        .card-content h5{
                            font-size: 28px;
                            margin-bottom: 8px;
                        }

                        .card-content p{
                            color: black;
                            font-size: 15px;
                            margin-bottom: 8px;
                        }

                        .room-btn{
                            margin-top: 20px;
                        }

                    </style>
                    <div class="room-btn"> 
                    <button data-action="update" data-id="<?php echo $id; ?>" class="btn btn-success action-button">Update</button>
                    <button data-action="delete" data-id="<?php echo $id; ?>" class="btn btn-danger action-button">Delete</button>  
                        <?php 
                        if ($tenantcount == $capacity){ 
                            $query = "UPDATE rooms SET status = 'Full' WHERE room_no = $roomno";
                            mysqli_query($conn, $query);

                            $query = "UPDATE reservation SET status = 'Full' WHERE room_no = $roomno";
                            mysqli_query($conn, $query);
                        ?>
                            
                        <?php }
                        else if ($tenantcount <= $capacity){
                            $query = "UPDATE rooms SET status = 'available' WHERE room_no = $roomno";
                            mysqli_query($conn, $query);

                            $query = "UPDATE reservation SET status = 'available' WHERE room_no = $roomno";
                            mysqli_query($conn, $query);
                        ?>  
                         <?php }?>
                    </div>
                </div> 
            </div>     
        <?php } } ?>

    </div>
                      <!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalMessage">
                Are you sure you want to perform this action?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <a href="#" id="confirmActionBtn" class="btn btn-danger">Yes</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const actionButtons = document.querySelectorAll('.action-button');
        const confirmActionBtn = document.getElementById('confirmActionBtn');
        const modalMessage = document.getElementById('modalMessage');
        const modalLabel = document.getElementById('confirmModalLabel');
        
        actionButtons.forEach(button => {
            button.addEventListener('click', function () {
                const actionType = this.getAttribute('data-action');  // 'delete' or 'update'
                const roomId = this.getAttribute('data-id');  // Room ID
                
                // Set the appropriate message and URL for the action
                if (actionType === 'delete') {
                    modalMessage.textContent = 'Are you sure you want to delete this room?';
                    confirmActionBtn.setAttribute('href', 'php/roomfunction.php?rdelete=' + roomId);
                    modalLabel.textContent = 'Confirm Deletion';
                } else if (actionType === 'update') {
                    modalMessage.textContent = 'Are you sure you want to update this room?';
                    confirmActionBtn.setAttribute('href', 'php/roomfunction.php?rupdate=' + roomId);
                    modalLabel.textContent = 'Confirm Update';
                }

                // Show the modal
                $('#confirmModal').modal('show');
            });
        });
    });
</script>
    
</body>
</html>
