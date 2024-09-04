<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$database = "the_gallery_cafe";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['booking_id'])) {
    $action = $_POST['action'];
    $booking_id = $_POST['booking_id'];

    // Retrieve booking details
    $stmt = mysqli_prepare($conn, "SELECT * FROM event_bookings WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Define the recipient email address
    $to = $booking['email'];

    // Construct mailto link
    $mailto = "mailto:$to";

    // Define the message based on action
    $message = ($action === 'confirm' ? 'Booking to be confirmed!' : 'Booking to be canceled!');
    $confirmMessage = "Are you sure you want to proceed?";

    // Display JavaScript confirmation dialog
    echo "<script>
        var proceed = confirm('$message $confirmMessage');
        if (proceed) {
            window.location.href = '$mailto';
        } else {
            // Optionally, redirect back to a safe page or handle the cancellation here
            window.location.href = 'emp_booking.php'; // Redirect back to the management page
        }
    </script>";

    // If action is 'cancel', delete the booking
    if ($action === 'cancel') {
        $stmt = mysqli_prepare($conn, "DELETE FROM event_bookings WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Retrieve all bookings for management
$result = mysqli_query($conn, "SELECT * FROM event_bookings");

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café | Manage Bookings</title>
    <link rel="stylesheet" href="../css/common.css">
    <script>
        function openEmail(action, id) {
            // Create form and submit it
            var form = document.createElement('form');
            form.method = 'post';
            form.action = 'emp_booking.php';
            var hiddenField1 = document.createElement('input');
            hiddenField1.type = 'hidden';
            hiddenField1.name = 'action';
            hiddenField1.value = action;
            form.appendChild(hiddenField1);
            var hiddenField2 = document.createElement('input');
            hiddenField2.type = 'hidden';
            hiddenField2.name = 'booking_id';
            hiddenField2.value = id;
            form.appendChild(hiddenField2);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
    <header id="main-nav">
        <nav>
            <ul>
                <li><a href="../php/index.php">HOME</a></li>
            </ul>
        </nav>
    </header>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Date</th>
                <th>Time</th>
                <th>Details</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                <td><?php echo htmlspecialchars($row['event_time']); ?></td>
                <td><?php echo htmlspecialchars($row['details']); ?></td>
                <td>
                    <button onclick="openEmail('confirm', <?php echo $row['id']; ?>)">Confirm</button>
                    <button onclick="openEmail('cancel', <?php echo $row['id']; ?>)">Cancel</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
