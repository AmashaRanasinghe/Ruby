<?php
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

// Handle adding a new item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $cuisine_type = mysqli_real_escape_string($conn, $_POST['cuisine_type']);
    $price = $_POST['price'];

    $sql = "INSERT INTO dinner (item_name, cuisine_type, price) VALUES ('$item_name', '$cuisine_type', $price)";
    if (!mysqli_query($conn, $sql)) {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle editing an item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_item'])) {
    $item_id = $_POST['item_id'];
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $cuisine_type = mysqli_real_escape_string($conn, $_POST['cuisine_type']);
    $price = $_POST['price'];

    $sql = "UPDATE dinner SET item_name = '$item_name', cuisine_type = '$cuisine_type', price = $price WHERE id = $item_id";
    if (!mysqli_query($conn, $sql)) {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle deleting an item
if (isset($_GET['delete_id'])) {
    $item_id = $_GET['delete_id'];
    $sql = "DELETE FROM dinner WHERE id = $item_id";
    if (!mysqli_query($conn, $sql)) {
        echo "Error: " . mysqli_error($conn);
    }
}

// Get search term
$search = isset($_GET['term']) ? mysqli_real_escape_string($conn, $_GET['term']) : '';

// Prepare SQL query based on search term
$sql = "SELECT * FROM dinner WHERE item_name LIKE '%$search%' OR cuisine_type LIKE '%$search%'";
$result = mysqli_query($conn, $sql);

// Fetch item for editing
$item_to_edit = [];
if (isset($_GET['edit_id'])) {
    $item_id = $_GET['edit_id'];
    $sql = "SELECT * FROM dinner WHERE id = $item_id";
    $result_edit = mysqli_query($conn, $sql);
    if ($result_edit) {
        $item_to_edit = mysqli_fetch_assoc($result_edit);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>The Gallery Café | Dinner</title>
    <link rel="stylesheet" href="../css/cuisine.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
</head>

<body>
    <div class="container">
        <h1>Edit Dinner </h1>

        <!-- Add/Edit Form -->
        <div class="form-container">
            <h2><?php echo isset($item_to_edit['id']) ? 'Edit Item' : 'Add New Item'; ?></h2>
            <form action="" method="post">
                <?php if (isset($item_to_edit['id'])): ?>
                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_to_edit['id']); ?>">
                <?php endif; ?>
                <label for="item_name">Item Name:</label>
                <input type="text" id="item_name" name="item_name" value="<?php echo isset($item_to_edit['item_name']) ? htmlspecialchars($item_to_edit['item_name']) : ''; ?>" required><br>
                <br>
                <label for="cuisine_type">Cuisine Type:</label>
                <input type="text" id="cuisine_type" name="cuisine_type" value="<?php echo isset($item_to_edit['cuisine_type']) ? htmlspecialchars($item_to_edit['cuisine_type']) : ''; ?>" required><br>
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
        // Initialize variables for section
        $current_cuisine = '';

        // Check if we have results
        if (mysqli_num_rows($result) > 0) {
            echo '<main>';
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['cuisine_type'] !== $current_cuisine) {
                    if ($current_cuisine !== '') {
                        echo '</div>'; // Close the previous cuisine section
                    }
                    $current_cuisine = $row['cuisine_type'];
                    echo '<div id="' . strtolower(str_replace(' ', '-', $current_cuisine)) . '" class="menu-section">';
                    echo '<h2>' . $current_cuisine . ' Cuisine</h2>';
                }

                echo '<div class="menu-item">';
                echo '<h3>' . htmlspecialchars($row['item_name']) . '</h3>';
                echo '<div class="cuisine-type">' . htmlspecialchars($row['cuisine_type']) . '</div>';
                echo '<div class="price-list">';
                echo '<div class="price">Price: Rs.' . number_format($row['price'], 2) . '</div>';
                echo '<div class="actions">';
                echo '<a href="?edit_id=' . htmlspecialchars($row['id']) . '">Edit</a> | ';
                echo '<a href="?delete_id=' . htmlspecialchars($row['id']) . '" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</a>';
                echo '</div>'; // Close .actions
                echo '</div>'; // Close .price-list
                echo '</div>'; // Close .menu-item
            }
            echo '</div>'; // Close the last cuisine section
            echo '</main>';
        } else {
            echo '<p>No menu items found matching your search.</p>';
        }

        // Close the connection
        mysqli_close($conn);
        ?>
    </div>
</body>

</html>
