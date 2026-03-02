<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_sql = "SELECT id FROM users WHERE username='" . $conn->real_escape_string($_SESSION['username']) . "'";
    $user_result = $conn->query($user_sql);
    if ($user_result && $user_result->num_rows == 1) {
        $user = $user_result->fetch_assoc();
        $user_id = $user['id'];

        $origin = $conn->real_escape_string($_POST['origin']);
        $destination = $conn->real_escape_string($_POST['destination']);
        $ride_date = $conn->real_escape_string($_POST['ride_date']);
        $seats_available = intval($_POST['seats_available']);
        $cost = floatval($_POST['cost']);

        $sql = "INSERT INTO rides (user_id, origin, destination, ride_date, seats_available, cost) VALUES ('$user_id', '$origin', '$destination', '$ride_date', '$seats_available', '$cost')";
        if ($conn->query($sql) === TRUE) {
            $message = "Ride posted successfully.";
        } else {
            $message = "Error: " . $conn->error;
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
    <title>Post a Ride - Car Pooling Service</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body class="page-background">
    <div class="login-container">
        <h2>Post a Ride</h2>
        <p style="margin-bottom: 20px; color: #555;">Use this form to post a ride you want to offer. Fill in the details below and submit to share your ride with others.</p>
        <a href="index.html" class="btn-primary" style="margin-bottom: 20px; display: inline-block;">Return to Home</a>
        <?php if ($message): ?>
            <p class="message-success"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" action="post_ride.php">
            <label for="origin">Origin</label>
            <input type="text" id="origin" name="origin" required />

            <label for="destination">Destination</label>
            <input type="text" id="destination" name="destination" required />

            <label for="ride_date">Date of Ride</label>
            <input type="date" id="ride_date" name="ride_date" required />

            <label for="seats_available">Seats Available</label>
            <input type="number" id="seats_available" name="seats_available" min="1" max="10" required />

            <label for="cost">Cost (in USD)</label>
            <input type="number" id="cost" name="cost" min="0" step="0.01" required />

            <input type="submit" value="Post Ride" />
        </form>

        <a href="index.html" class="btn-primary" style="margin-top: 20px; display: inline-block;">Return to Home</a>

        <?php
        // Display rides posted by the logged-in user
        $user_id = null;
        if (isset($_SESSION['username'])) {
            $user_sql = "SELECT id FROM users WHERE username='" . $conn->real_escape_string($_SESSION['username']) . "'";
            $user_result = $conn->query($user_sql);
            if ($user_result && $user_result->num_rows == 1) {
                $user = $user_result->fetch_assoc();
                $user_id = $user['id'];
            }
        }

        if ($user_id) {
            echo "<h3 style='margin-top: 30px;'>Your Posted Rides</h3>";
            $rides_sql = "SELECT origin, destination, ride_date, seats_available FROM rides WHERE user_id = '$user_id' ORDER BY ride_date DESC";
            $rides_result = $conn->query($rides_sql);
            if ($rides_result && $rides_result->num_rows > 0) {
                echo "<table class='rides-table'>";
                echo "<thead><tr><th>Origin</th><th>Destination</th><th>Date</th><th>Seats Available</th></tr></thead><tbody>";
                while ($row = $rides_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['origin']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ride_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['seats_available']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No rides posted yet.</p>";
            }
        }

        // Display all available rides (cars)
        echo "<h3 style='margin-top: 30px;'>Available Rides</h3>";
        $available_sql = "SELECT rides.origin, rides.destination, rides.ride_date, rides.seats_available, users.username FROM rides JOIN users ON rides.user_id = users.id WHERE rides.ride_date >= CURDATE() ORDER BY rides.ride_date ASC";
        $available_result = $conn->query($available_sql);
        if ($available_result && $available_result->num_rows > 0) {
            echo "<table class='rides-table'>";
            echo "<thead><tr><th>Driver</th><th>Origin</th><th>Destination</th><th>Date</th><th>Seats Available</th></tr></thead><tbody>";
            while ($row = $available_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['origin']) . "</td>";
                echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ride_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['seats_available']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No available rides at the moment.</p>";
        }
        ?>

        <?php
        // Display rides posted by the logged-in user
        $user_id = null;
        if (isset($_SESSION['username'])) {
            $user_sql = "SELECT id FROM users WHERE username='" . $conn->real_escape_string($_SESSION['username']) . "'";
            $user_result = $conn->query($user_sql);
            if ($user_result && $user_result->num_rows == 1) {
                $user = $user_result->fetch_assoc();
                $user_id = $user['id'];
            }
        }

        if ($user_id) {
            echo "<h3 style='margin-top: 30px;'>Your Posted Rides</h3>";
            $rides_sql = "SELECT origin, destination, ride_date, seats_available FROM rides WHERE user_id = '$user_id' ORDER BY ride_date DESC";
            $rides_result = $conn->query($rides_sql);
            if ($rides_result && $rides_result->num_rows > 0) {
                echo "<table class='rides-table'>";
                echo "<thead><tr><th>Origin</th><th>Destination</th><th>Date</th><th>Seats Available</th></tr></thead><tbody>";
                while ($row = $rides_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['origin']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ride_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['seats_available']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No rides posted yet.</p>";
            }
        }

        // Display all available rides (cars)
        echo "<h3 style='margin-top: 30px;'>Available Rides</h3>";
        $available_sql = "SELECT rides.origin, rides.destination, rides.ride_date, rides.seats_available, users.username FROM rides JOIN users ON rides.user_id = users.id WHERE rides.ride_date >= CURDATE() ORDER BY rides.ride_date ASC";
        $available_result = $conn->query($available_sql);
        if ($available_result && $available_result->num_rows > 0) {
            echo "<table class='rides-table'>";
            echo "<thead><tr><th>Driver</th><th>Origin</th><th>Destination</th><th>Date</th><th>Seats Available</th></tr></thead><tbody>";
            while ($row = $available_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['origin']) . "</td>";
                echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ride_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['seats_available']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No available rides at the moment.</p>";
        }
        ?>

    </div>
</body>
</html>
