<?php
session_start(); // Start session to use session variables

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

// Handle reservation status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $reservation_id = (int)$_POST['reservation_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_status_sql = "UPDATE reservations SET status = '$new_status' WHERE reservation_id = $reservation_id";
    if (mysqli_query($conn, $update_status_sql)) {
        echo "<script>alert('Reservation status updated successfully.'); window.location.href='emp_reservation.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating reservation status: " . mysqli_error($conn) . "');</script>";
    }
}

// Handle reservation cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $reservation_id = (int)$_POST['reservation_id'];

    $cancel_reservation_sql = "UPDATE reservations SET status = 'Cancelled' WHERE reservation_id = $reservation_id";
    if (mysqli_query($conn, $cancel_reservation_sql)) {
        echo "<script>alert('Reservation cancelled successfully.'); window.location.href='emp_reservation.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error cancelling reservation: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch all reservations
$reservations_sql = "SELECT * FROM reservations";
$result = mysqli_query($conn, $reservations_sql);

// Fetch items
$items_sql = "
    SELECT id, item_name FROM appetizers
    UNION ALL
    SELECT id, item_name FROM starters
    UNION ALL
    SELECT id, item_name FROM beverages
    UNION ALL
    SELECT id, item_name FROM breakfast
    UNION ALL
    SELECT id, item_name FROM lunch
    UNION ALL
    SELECT id, item_name FROM dinner
    UNION ALL
    SELECT id, item_name FROM specials
";
$items_result = mysqli_query($conn, $items_sql);
$items = [];
while ($row = mysqli_fetch_assoc($items_result)) {
    $items[$row['id']] = $row['item_name'];
}

// Function to get pre-order items
function get_pre_order_items($reservation_id, $category, $conn) {
    $query = "SELECT order_details FROM pre_orders_$category WHERE reservation_id = '$reservation_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['order_details'];
    }
    return '';
}

// Close connection
// mysqli_close($conn); // Ensure this is not closing prematurely
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Gallery Café | Employee Reservations</title>
    <link rel="stylesheet" href="../css/common.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['reservation_success'])): ?>
                alert('<?php echo $_SESSION['reservation_success']; ?>');
                <?php unset($_SESSION['reservation_success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['pre_order_success'])): ?>
                alert('<?php echo $_SESSION['pre_order_success']; ?>');
                <?php unset($_SESSION['pre_order_success']); ?>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                alert('<?php echo $error; ?>');
            <?php endif; ?>
        });
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

    <div class="container">
        <h2>Manage Reservations</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Reservation ID</th>
                        <th>User ID</th>
                        <th>Customer Name</th>
                        <th>Reservation Date</th>
                        <th>Status</th>
                        <th>Order Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reservation = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['reservation_id']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['reservation_date']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                            <td>
                                <?php
                                // Fetch and display all pre-order items for this reservation
                                $categories = ['appetizers', 'starters', 'beverages', 'breakfast', 'lunch', 'dinner', 'specials'];
                                $pre_order_details = '';
                                foreach ($categories as $category):
                                    $pre_order_items = get_pre_order_items($reservation['reservation_id'], $category, $conn);
                                    if (!empty($pre_order_items)):
                                        $pre_order_details .= '<h4>' . ucfirst($category) . '</h4>';
                                        $pre_order_details .= '<ul>';
                                        $order_array = explode(',', $pre_order_items);
                                        foreach ($order_array as $item):
                                            list($item_id, $quantity) = explode(':', $item);
                                            if (isset($items[$item_id])):
                                                $pre_order_details .= '<li>' . htmlspecialchars($items[$item_id]) . ' - Qty: ' . htmlspecialchars($quantity) . '</li>';
                                            endif;
                                        endforeach;
                                        $pre_order_details .= '</ul>';
                                    endif;
                                endforeach;
                                echo $pre_order_details;
                                ?>
                            </td>
                            <td>
                                <!-- Update Status Form -->
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['reservation_id']); ?>">
                                    <select name="status">
                                        <option value="Pending" <?php echo ($reservation['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Confirmed" <?php echo ($reservation['status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="Cancelled" <?php echo ($reservation['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <br>
                                    <button type="submit" name="update_status">Update Status</button>
                                </form>

                                <!-- Cancel Reservation Form -->
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['reservation_id']); ?>">
                                    <button type="submit" name="cancel_reservation">Cancel Reservation</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No reservations found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
