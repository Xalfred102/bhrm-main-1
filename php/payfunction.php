<?php
// Include database connection
include 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the payment record
    $query = "SELECT * FROM payments WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $payment = mysqli_fetch_assoc($result);
    $email = $payment['email'];
    $hname = $payment['hname'];

    if (!$payment) {
        echo "Payment not found.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_amount = floatval($_POST['payment']);
    $pay_stat = $_POST['pay_stat'];
    $pay_date = $_POST['pay_date'];
    $price = floatval($payment['price']);

    // Calculate the payment status if it's not manually selected
    if ($pay_stat === 'Fully Paid' || $pay_stat === 'Partially Paid') {
        // Validate user override
        if ($pay_stat === 'Fully Paid' && $payment_amount < $price) {
            $pay_stat = 'Partially Paid';
        }
    } else {
        // Automatically calculate pay_stat based on amount
        $pay_stat = $payment_amount >= $bed_price ? 'Fully Paid' : 'Partially Paid';
    }

    
    // Update the payment record
    $updateQuery = "UPDATE payments SET 
                    payment = $payment_amount, 
                    pay_stat = '$pay_stat', 
                    pay_date = '$pay_date' 
                    WHERE id = $id and email = '$email' and hname = '$hname'";

    if (mysqli_query($conn, $updateQuery)) {
        header('Location: ../payment.php');
        exit;
    } else {
        echo "Error updating payment: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payment</title>
</head>
<body>
    <h1>Edit Payment</h1>
    <form method="POST">
        <label for="payment">Payment Amount:</label>
        <input type="number" id="payment" name="payment" step="0.01" value="<?php echo $payment['payment']; ?>" required>
        <br><br>
        <label for="pay_stat">Payment Status:</label>
        <select id="pay_stat" name="pay_stat">
            <option value="Fully Paid" <?php echo $payment['pay_stat'] == 'Fully Paid' ? 'selected' : ''; ?>>Fully Paid</option>
            <option value="Partially Paid" <?php echo $payment['pay_stat'] == 'Partially Paid' ? 'selected' : ''; ?>>Partially Paid</option>
            <option value="Not Paid" <?php echo $payment['pay_stat'] == 'Not Paid' ? 'selected' : ''; ?>>Not Paid</option>
        </select>
        <br><br>
        <label for="pay_date">Payment Date:</label>
        <input type="datetime-local" id="pay_date" name="pay_date" value="<?php echo date('Y-m-d\TH:i', strtotime($payment['pay_date'])); ?>">
        <br><br>
        <button type="submit">Save Changes</button>
        <a href="../payment.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>
