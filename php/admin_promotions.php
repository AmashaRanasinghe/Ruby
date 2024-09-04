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

// Handle deletion of promotion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM promotions WHERE id = $delete_id";
    
    if (mysqli_query($conn, $sql)) {
        echo '<p>Promotion deleted successfully.</p>';
    } else {
        echo '<p>Error deleting promotion: ' . mysqli_error($conn) . '</p>';
    }
}

// Handle adding new promotion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_promotion'])) {
    $promotion_name = htmlspecialchars($_POST['promotion_name']);
    $description = htmlspecialchars($_POST['description']);
    $discount_percentage = htmlspecialchars($_POST['discount_percentage']);
    $valid_until = htmlspecialchars($_POST['valid_until']);

    $sql = "INSERT INTO promotions (promotion_name, description, discount_percentage, valid_until) 
            VALUES ('$promotion_name', '$description', '$discount_percentage', '$valid_until')";
    
    if (mysqli_query($conn, $sql)) {
        echo '<p>Promotion added successfully.</p>';
    } else {
        echo '<p>Error adding promotion: ' . mysqli_error($conn) . '</p>';
    }
}

// Retrieve promotions from the database
$sql = "SELECT * FROM promotions";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café | Manage Promotions</title>
    <link rel="stylesheet" href="../css/admin_promos.css">
</head>
<body>
    <div class="container">
        <div class="management-container">
            <!-- Promotions List -->
            <h2>Manage Promotions</h2>
            <div class="promotion-list">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="promotion-item">';
                        echo '<p><strong>' . htmlspecialchars($row['promotion_name']) . '</strong></p>';
                        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                        echo '<p><strong>Discount:</strong> ' . htmlspecialchars($row['discount_percentage']) . '%</p>';
                        echo '<p><strong>Valid Until:</strong> ' . htmlspecialchars($row['valid_until']) . '</p>';
                        echo '<a href="?delete_id=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to delete this promotion?\');"><i class="fas fa-trash"></i> Delete</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No promotions found.</p>';
                }
                ?>
            </div>

            <!-- Add New Promotion Form -->
            <div class="add-promotion-form">
                <h2>Add New Promotion</h2>
                <form method="POST" action="">
                    <label for="promotion_name">Promotion Name:</label>
                    <input type="text" id="promotion_name" name="promotion_name" required>

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>

                    <label for="discount_percentage">Discount Percentage:</label>
                    <input type="number" id="discount_percentage" name="discount_percentage" required>

                    <label for="valid_until">Valid Until:</label>
                    <input type="date" id="valid_until" name="valid_until" required>

                    <input type="submit" name="add_promotion" value="Add Promotion">
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the connection
mysqli_close($conn);
?>
