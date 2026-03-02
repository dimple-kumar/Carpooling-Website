<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$receipt = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ride_id = intval($_POST['ride_id']);
    $user_sql = "SELECT id FROM users WHERE username='" . $conn->real_escape_string($_SESSION['username']) . "'";
    $user_result = $conn->query($user_sql);
    if ($user_result && $user_result->num_rows == 1) {
        $user = $user_result->fetch_assoc();
        $user_id = $user['id'];

        // Check if ride exists and seats are available
        $ride_sql = "SELECT rides.*, users.username FROM rides JOIN users ON rides.user_id = users.id WHERE rides.id = $ride_id";
        $ride_result = $conn->query($ride_sql);
        if ($ride_result && $ride_result->num_rows == 1) {
            $ride = $ride_result->fetch_assoc();
            if ($ride['seats_available'] > 0) {
                // Reduce seats available by 1
                $new_seats = $ride['seats_available'] - 1;
                $update_sql = "UPDATE rides SET seats_available = $new_seats WHERE id = $ride_id";
                if ($conn->query($update_sql) === TRUE) {
                    // Generate receipt data
                    $receipt = [
                        'drivers' => $ride['username'],
                        'origin' => $ride['origin'],
                        'destination' => $ride['destination'],
                        'date' => $ride['ride_date'],
                        'cost' => number_format($ride['cost'], 2),
                        'booked_by' => $_SESSION['username'],
                        'seats_left' => $new_seats
                    ];
                } else {
                    $message = "Error updating seats: " . $conn->error;
                }
            } else {
                $message = "No seats available for this ride.";
            }
        } else {
            $message = "Ride not found.";
        }
    } else {
        $message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Booking Receipt - Car Pooling Service</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body class="page-background">
    <div class="login-container">
        <h2>Booking Receipt</h2>
        <?php if ($message): ?>
            <p class="message-error"><?php echo $message; ?></p>
            <a href="search_rides.php" class="btn-primary">Back to Search</a>
        <?php elseif ($receipt): ?>
            <p>Thank you, <strong><?php echo htmlspecialchars($receipt['booked_by']); ?></strong>, for booking a ride.</p>
            <table>
                <tr><td>Drivers:</td><td><?php echo htmlspecialchars($receipt['drivers']); ?></td></tr>
                <tr><td>Origin:</td><td><?php echo htmlspecialchars($receipt['origin']); ?></td></tr>
                <tr><td>Destination:</td><td><?php echo htmlspecialchars($receipt['destination']); ?></td></tr>
                <tr><td>Date:</td><td><?php echo htmlspecialchars($receipt['date']); ?></td></tr>
                <tr><td>Cost:</td><td>$<?php echo $receipt['cost']; ?></td></tr>
                <tr><td>Seats Left:</td><td><?php echo $receipt['seats_left']; ?></td></tr>
            </table>
            <a href="search_rides.php" class="btn-primary" style="margin-top: 20px;">Book Another Ride</a>
        <?php else: ?>
            <p>No booking information available.</p>
            <a href="search_rides.php" class="btn-primary">Back to Search</a>
        <?php endif; ?>
    </div>
</body>
</html>
