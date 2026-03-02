<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$rides = [];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $origin = $conn->real_escape_string($_POST['origin']);
    $destination = $conn->real_escape_string($_POST['destination']);

    $sql = "SELECT rides.*, users.username FROM rides JOIN users ON rides.user_id = users.id WHERE origin LIKE '%$origin%' AND destination LIKE '%$destination%' AND ride_date >= CURDATE() ORDER BY ride_date ASC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rides[] = $row;
        }
        if (count($rides) == 0) {
            $message = "No rides found matching your criteria.";
        }
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Search Rides - Car Pooling Service</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body class="page-background">
    <div class="login-container">
        <h2>Search Rides</h2>
        <a href="index.html" class="btn-primary" style="margin-bottom: 20px; display: inline-block;">Return to Home</a>
        <p style="margin-bottom: 20px; color: #555;">Use this form to search for available rides by entering your origin and destination. Browse the results below to find a suitable ride.</p>
        <form method="POST" action="search_rides.php">
            <label for="origin">Origin</label>
            <input type="text" id="origin" name="origin" required />

            <label for="destination">Destination</label>
            <input type="text" id="destination" name="destination" required />

            <input type="submit" value="Search" />
        </form>

        <?php if ($message): ?>
            <p class="message-error"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if (count($rides) > 0): ?>
            <table class="rides-table">
                <thead>
                    <tr>
                <th>Drivers</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Date</th>
                <th>Seats Available</th>
                <th>Cost (USD)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rides as $ride): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ride['username']); ?></td>
                    <td><?php echo htmlspecialchars($ride['origin']); ?></td>
                    <td><?php echo htmlspecialchars($ride['destination']); ?></td>
                    <td><?php echo htmlspecialchars($ride['ride_date']); ?></td>
                    <td><?php echo htmlspecialchars($ride['seats_available']); ?></td>
                    <td><?php echo number_format($ride['cost'], 2); ?></td>
                    <td>
                        <form method="POST" action="book_ride.php" style="margin:0;">
                            <input type="hidden" name="ride_id" value="<?php echo $ride['id']; ?>" />
                            <input type="submit" value="Book" />
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
        <?php endif; ?>
    </div>
</body>
</html>
