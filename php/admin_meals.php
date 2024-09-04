<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "the_gallery_cafe";

$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to escape and sanitize user input
function sanitizeInput($input, $conn) {
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input)));
}

// Handle adding a new item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $item_name = sanitizeInput($_POST['item_name'], $conn);
    $category = sanitizeInput($_POST['category'], $conn);
    $price = sanitizeInput($_POST['price'], $conn);

    $sql = "INSERT INTO $category (item_name, price) VALUES ('$item_name', $price)";
    if (mysqli_query($conn, $sql)) {
        $success = "Item added successfully.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle editing an item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_item'])) {
    $item_id = sanitizeInput($_POST['item_id'], $conn);
    $item_name = sanitizeInput($_POST['item_name'], $conn);
    $category = sanitizeInput($_POST['category'], $conn);
    $price = sanitizeInput($_POST['price'], $conn);

    $sql = "UPDATE $category SET item_name = '$item_name', price = $price WHERE id = $item_id";
    if (mysqli_query($conn, $sql)) {
        $success = "Item updated successfully.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle deleting an item
if (isset($_GET['delete_id']) && isset($_GET['category'])) {
    $item_id = sanitizeInput($_GET['delete_id'], $conn);
    $category = sanitizeInput($_GET['category'], $conn);
    $sql = "DELETE FROM $category WHERE id = $item_id";
    if (mysqli_query($conn, $sql)) {
        $success = "Item deleted successfully.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Get search term
$search = isset($_GET['term']) ? sanitizeInput($_GET['term'], $conn) : '';

// Prepare SQL queries for each menu
$sql_starters = "SELECT * FROM starters WHERE item_name LIKE '%$search%'";
$sql_appetizers = "SELECT * FROM appetizers WHERE item_name LIKE '%$search%'";
$sql_beverages = "SELECT * FROM beverages WHERE item_name LIKE '%$search%'";

$result_starters = mysqli_query($conn, $sql_starters);
$result_appetizers = mysqli_query($conn, $sql_appetizers);
$result_beverages = mysqli_query($conn, $sql_beverages);

// Fetch item for editing
$item_to_edit = [];
$current_category = isset($_GET['category']) ? sanitizeInput($_GET['category'], $conn) : '';

if (isset($_GET['edit_id']) && $current_category) {
    $item_id = sanitizeInput($_GET['edit_id'], $conn);
    $sql = "SELECT * FROM $current_category WHERE id = $item_id";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $item_to_edit = mysqli_fetch_assoc($result);
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>The Gallery Café | Meals</title>
    <link rel="stylesheet" href="../css/cuisine.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
    <script src="../js/meals.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Edit Meals</h1>

        <!-- Add/Edit Form -->
        <div class="form-container">
            <h2><?php echo isset($item_to_edit['id']) ? 'Edit Item' : 'Add New Item'; ?></h2>
            <form action="" method="post">
                <?php if (isset($item_to_edit['id'])): ?>
                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_to_edit['id']); ?>">
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($current_category); ?>">
                <?php else: ?>
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="starters" <?php echo $current_category === 'starters' ? 'selected' : ''; ?>>Starters</option>
                        <option value="appetizers" <?php echo $current_category === 'appetizers' ? 'selected' : ''; ?>>Appetizers</option>
                        <option value="beverages" <?php echo $current_category === 'beverages' ? 'selected' : ''; ?>>Beverages</option>
                    </select><br><br>
                <?php endif; ?>
                <label for="item_name">Item Name:</label>
                <input type="text" id="item_name" name="item_name" value="<?php echo isset($item_to_edit['item_name']) ? htmlspecialchars($item_to_edit['item_name']) : ''; ?>" required><br>
                <br>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo isset($item_to_edit['price']) ? htmlspecialchars($item_to_edit['price']) : ''; ?>" required><br>
                <br>
                <button type="submit" name="<?php echo isset($item_to_edit['id']) ? 'edit_item' : 'add_item'; ?>">
                    <?php echo isset($item_to_edit['id']) ? 'Update Item' : 'Add Item'; ?>
                </button>
            </form>
        </div>

        <?php
        // Display Starters
        echo '<div class="menu-section">';
        echo '<h2>Starters</h2>';
        if (mysqli_num_rows($result_starters) > 0) {
            while ($row = mysqli_fetch_assoc($result_starters)) {
                echo '<div class="menu-item">';
                echo '<h3>' . htmlspecialchars($row['item_name']) . '</h3>';
                echo '<div class="price-list">';
                echo '<div class="price"> Rs.' . number_format($row['price'], 2) . '</div>';
                echo '</div>';
                echo '<a href="?edit_id=' . htmlspecialchars($row['id']) . '&category=starters">Edit</a> | ';
                echo '<a href="?delete_id=' . htmlspecialchars($row['id']) . '&category=starters" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No starters found matching your search.</p>';
        }
        echo '</div>';

        // Display Appetizers
        echo '<div class="menu-section">';
        echo '<h2>Appetizers</h2>';
        if (mysqli_num_rows($result_appetizers) > 0) {
            while ($row = mysqli_fetch_assoc($result_appetizers)) {
                echo '<div class="menu-item">';
                echo '<h3>' . htmlspecialchars($row['item_name']) . '</h3>';
                echo '<div class="price-list">';
                echo '<div class="price"> Rs.' . number_format($row['price'], 2) . '</div>';
                echo '</div>';
                echo '<a href="?edit_id=' . htmlspecialchars($row['id']) . '&category=appetizers">Edit</a> | ';
                echo '<a href="?delete_id=' . htmlspecialchars($row['id']) . '&category=appetizers" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No appetizers found matching your search.</p>';
        }
        echo '</div>';

        // Display Beverages
        echo '<div class="menu-section">';
        echo '<h2>Beverages</h2>';
        if (mysqli_num_rows($result_beverages) > 0) {
            while ($row = mysqli_fetch_assoc($result_beverages)) {
                echo '<div class="menu-item">';
                echo '<h3>' . htmlspecialchars($row['item_name']) . '</h3>';
                echo '<div class="price-list">';
                echo '<div class="price"> Rs.' . number_format($row['price'], 2) . '</div>';
                echo '</div>';
                echo '<a href="?edit_id=' . htmlspecialchars($row['id']) . '&category=beverages">Edit</a> | ';
                echo '<a href="?delete_id=' . htmlspecialchars($row['id']) . '&category=beverages" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No beverages found matching your search.</p>';
        }
        echo '</div>';

        // Close the connection
        mysqli_close($conn);
        ?>
    </div>
</body>
</html>
