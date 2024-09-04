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

$user_id = $_SESSION['user_id'];

// Function to parse order details
function parse_order_details($order_details) {
    $items = explode(',', $order_details);
    $order_array = [];
    foreach ($items as $item) {
        list($id, $quantity) = explode(':', $item);
        $order_array[$id] = $quantity;
    }
    return $order_array;
}

// Handle reservation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $reservation_date = mysqli_real_escape_string($conn, $_POST['reservation_date']);
    $status = 'Pending'; // Default status

    $query = "INSERT INTO reservations (user_id, customer_name, reservation_date, status) VALUES ('$user_id', '$customer_name', '$reservation_date', '$status')";
    
    if (mysqli_query($conn, $query)) {
        $reservation_id = mysqli_insert_id($conn);
        $_SESSION['reservation_id'] = $reservation_id;

        if (isset($_POST['preorder']) && $_POST['preorder'] == 1) {
            $_SESSION['reservation_success'] = "Reservation made successfully. You can now select pre-order items.";
            header("Location: reservation.php?step=preorder");
            exit();
        } else {
            $_SESSION['reservation_success'] = "Reservation made successfully.";
            header("Location: reservation.php");
            exit();
        }
    } else {
        $error = "Error: " . mysqli_error($conn);
        echo "<script>alert('$error');</script>";
    }
}

// Handle pre-order form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pre_order'])) {
    $reservation_id = $_SESSION['reservation_id'];
    $order_details = $_POST['menu_items']; // Use raw array instead of JSON
    $order_date = date('Y-m-d H:i:s'); // Current date and time

    $tables = ['appetizers', 'starters', 'beverages', 'breakfast', 'lunch', 'dinner', 'specials'];
    
    foreach ($tables as $table) {
        if (isset($order_details[$table]) && !empty($order_details[$table])) {
            $order_details_arr = [];
            foreach ($order_details[$table] as $item_id => $quantity) {
                if ($quantity > 0) {
                    $order_details_arr[] = $item_id . ':' . $quantity;
                }
            }
            $order_details_str = implode(',', $order_details_arr);
            
            $query = "INSERT INTO pre_orders_{$table} (reservation_id, order_details, order_date) VALUES ('$reservation_id', '$order_details_str', '$order_date')";
            
            if (!mysqli_query($conn, $query)) {
                $error = "Error: " . mysqli_error($conn);
                echo "<script>alert('$error');</script>";
            }
        }
    }

    $_SESSION['pre_order_success'] = "Pre-order placed successfully.";
    header("Location: reservation.php");
    exit();
}

// Fetch reservations for the user
$reservations_query = "SELECT reservation_id, customer_name, reservation_date, status FROM reservations WHERE user_id = '$user_id'";
$reservations_result = mysqli_query($conn, $reservations_query);

// Fetch items
$items_queries = [
    'appetizers' => "SELECT id, item_name, price FROM appetizers",
    'starters' => "SELECT id, item_name, price FROM starters",
    'beverages' => "SELECT id, item_name, price FROM beverages",
    'breakfast' => "SELECT id, item_name, price FROM breakfast",
    'lunch' => "SELECT id, item_name, price FROM lunch",
    'dinner' => "SELECT id, item_name, price FROM dinner",
    'specials' => "SELECT id, item_name, price FROM specials"
];

$items_results = [];
foreach ($items_queries as $key => $query) {
    $items_results[$key] = mysqli_query($conn, $query);
}

// Fetch pre-order details if in reservation history
function get_pre_order_items($reservation_id, $category, $conn) {
    $query = "SELECT order_details FROM pre_orders_$category WHERE reservation_id = '$reservation_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['order_details'];
    }
    return '';
}

if (isset($_GET['step']) && $_GET['step'] === 'preorder') {
    if (!isset($_SESSION['reservation_id'])) {
        header("Location: reservation.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
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
        <?php if (!isset($_GET['step']) || $_GET['step'] !== 'preorder'): ?>
            <!-- Reservation Form -->
            <form method="POST" action="">
                <h2>Make a Reservation</h2>
                <label for="customer_name">Your Name:</label>
                <input type="text" id="customer_name" name="customer_name" required>
                <label for="reservation_date">Date and Time:</label>
                <input type="datetime-local" id="reservation_date" name="reservation_date" required>
                <label for="preorder">Would you like to pre-order items?</label>
                <input type="checkbox" id="preorder" name="preorder" value="1">
                <button type="submit" name="reserve">Reserve</button>
            </form>
        <?php elseif (isset($_GET['step']) && $_GET['step'] === 'preorder'): ?>
            <!-- Pre-Order Form -->
            <form method="POST" action="">
                <h2>Select Pre-Order Items</h2>

                <?php foreach ($items_results as $category => $result): ?>
                    <h3><?php echo ucfirst($category); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                    <td>Rs.<?php echo htmlspecialchars($item['price']); ?></td>
                                    <td>
                                        <input type="number" name="menu_items[<?php echo htmlspecialchars($category); ?>][<?php echo htmlspecialchars($item['id']); ?>]" min="0" value="0">
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>

                <button type="submit" name="pre_order">Submit Pre-Order</button>
            </form>
        <?php endif; ?>

        <!-- Reservation History -->
        <h2>Your Reservations</h2>
        <?php if (mysqli_num_rows($reservations_result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Reservation ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Pre-Orders</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reservation = mysqli_fetch_assoc($reservations_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['reservation_id']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['reservation_date']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                            <td>
                                <?php
                                // Fetch and display pre-order items
                                $categories = ['appetizers', 'starters', 'beverages', 'breakfast', 'lunch', 'dinner', 'specials'];
                                $pre_order_details = '';
                                foreach ($categories as $category):
                                    $pre_order_items = get_pre_order_items($reservation['reservation_id'], $category, $conn);
                                    if (!empty($pre_order_items)):
                                        $pre_order_details .= '<h4>' . ucfirst($category) . '</h4>';
                                        $pre_order_details .= '<ul>';
                                        $order_array = parse_order_details($pre_order_items);
                                        foreach ($order_array as $item_id => $quantity):
                                            $item_query = "SELECT item_name, price FROM $category WHERE id = $item_id";
                                            $item_result = mysqli_query($conn, $item_query);
                                            if ($item = mysqli_fetch_assoc($item_result)):
                                                $pre_order_details .= '<li>' . htmlspecialchars($item['item_name']) . ' - Rs.' . htmlspecialchars($item['price']) . ' x ' . htmlspecialchars($quantity) . '</li>';
                                            endif;
                                        endforeach;
                                        $pre_order_details .= '</ul>';
                                    endif;
                                endforeach;
                                echo $pre_order_details;
                                ?>
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
