<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "the_gallery_cafe";

// Database connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle adding a new special
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_special'])) {
    $special_name = mysqli_real_escape_string($conn, $_POST['special_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $valid_until = mysqli_real_escape_string($conn, $_POST['valid_until']);
    $special_type = mysqli_real_escape_string($conn, $_POST['special_type']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);

    $sql = "INSERT INTO specials (special_name, description, price, valid_until, special_type, image_url) 
            VALUES ('$special_name', '$description', '$price', '$valid_until', '$special_type', '$image_url')";
    
    if (mysqli_query($conn, $sql)) {
        echo '<p>Special added successfully.</p>';
    } else {
        echo '<p>Error adding special: ' . mysqli_error($conn) . '</p>';
    }
}

// Handle editing an existing special
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_special'])) {
    $special_id = intval($_POST['special_id']);
    $special_name = mysqli_real_escape_string($conn, $_POST['special_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $valid_until = mysqli_real_escape_string($conn, $_POST['valid_until']);
    $special_type = mysqli_real_escape_string($conn, $_POST['special_type']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);

    $sql = "UPDATE specials SET special_name = '$special_name', description = '$description', price = '$price', 
            valid_until = '$valid_until', special_type = '$special_type', image_url = '$image_url' WHERE id = $special_id";
    
    if (mysqli_query($conn, $sql)) {
        echo '<p>Special updated successfully.</p>';
    } else {
        echo '<p>Error updating special: ' . mysqli_error($conn) . '</p>';
    }
}

// Handle deleting a special
if (isset($_GET['delete_id'])) {
    $special_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM specials WHERE id = $special_id";
    
    if (mysqli_query($conn, $sql)) {
        echo '<p>Special deleted successfully.</p>';
    } else {
        echo '<p>Error deleting special: ' . mysqli_error($conn) . '</p>';
    }
}

// Determine the selected special type
$special_type = isset($_GET['special_type']) ? $_GET['special_type'] : 'today';
$special_type_map = [
    'today' => 'Today\'s Special',
    'weekly' => 'Weekly',
    'monthly' => 'Monthly'
];
$selected_special_type = $special_type_map[$special_type] ?? 'Today\'s Special';

// Fetch specials for editing
$specials = [];
if (isset($_GET['edit_id'])) {
    $special_id = intval($_GET['edit_id']);
    $sql = "SELECT * FROM specials WHERE id = $special_id";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $specials = mysqli_fetch_assoc($result);
    }
}

// Fetch all specials for the list
$sql = "SELECT * FROM specials";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>The Gallery Café | Specials</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/specials.css">
</head>

<body>
    <div class="container">
        <div class="specials-container">
            <div class="form-container">
                <h2>Edit Specials</h2>

                <!-- Add/Edit Form -->
                <form action="" method="post">
                    <input type="hidden" name="special_id" value="<?php echo isset($specials['id']) ? htmlspecialchars($specials['id']) : ''; ?>">
                    <label for="special_name">Special Name:</label>
                    <input type="text" id="special_name" name="special_name" value="<?php echo isset($specials['special_name']) ? htmlspecialchars($specials['special_name']) : ''; ?>" required><br>
                    <br>
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required><?php echo isset($specials['description']) ? htmlspecialchars($specials['description']) : ''; ?></textarea><br>
                    <br>
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo isset($specials['price']) ? htmlspecialchars($specials['price']) : ''; ?>" required><br>
                    <br>
                    <label for="valid_until">Valid Until:</label>
                    <input type="date" id="valid_until" name="valid_until" value="<?php echo isset($specials['valid_until']) ? htmlspecialchars($specials['valid_until']) : ''; ?>" required><br>
                    <br>
                    <label for="special_type">Special Type:</label>
                    <select id="special_type" name="special_type" required>
                        <option value="Today’s Special" <?php echo isset($specials['special_type']) && $specials['special_type'] == 'Today\'s Special' ? 'selected' : ''; ?>>Today’s Special</option>
                        <option value="Weekly" <?php echo isset($specials['special_type']) && $specials['special_type'] == 'Weekly' ? 'selected' : ''; ?>>Weekly</option>
                        <option value="Monthly" <?php echo isset($specials['special_type']) && $specials['special_type'] == 'Monthly' ? 'selected' : ''; ?>>Monthly</option>
                    </select><br>
                    <br>
                    <label for="image_url">Image URL:</label>
                    <input type="text" id="image_url" name="image_url" value="<?php echo isset($specials['image_url']) ? htmlspecialchars($specials['image_url']) : ''; ?>"><br>
                    <br>
                    <button type="submit" name="<?php echo isset($specials['id']) ? 'edit_special' : 'add_special'; ?>">
                        <?php echo isset($specials['id']) ? 'Update Special' : 'Add Special'; ?>
                    </button>
                    <br>
                    <br>
                </form>

                <div class="special-form">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="special-item">';
                            echo '<h3>' . htmlspecialchars($row['special_name']) . '</h3>';
                            echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                            echo '<p><strong>Price:</strong> Rs.' . number_format($row['price'], 2) . '</p>';
                            echo '<p><strong>Valid Until:</strong> ' . htmlspecialchars($row['valid_until']) . '</p>';
                            if (!empty($row['image_url'])) {
                                echo '<div class="image-container">';
                                echo '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['special_name']) . '">';
                                echo '</div>'; // Close .image-container
                            }
                            echo '<a href="?edit_id=' . htmlspecialchars($row['id']) . '">Edit</a> | ';
                            echo '<a href="?delete_id=' . htmlspecialchars($row['id']) . '" onclick="return confirm(\'Are you sure you want to delete this special?\');">Delete</a>';
                            echo '</div>'; // Close .special-item
                        }
                    } else {
                        echo '<p>No specials found for the selected category.</p>';
                    }

                    // Close the connection
                    mysqli_close($conn);
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
