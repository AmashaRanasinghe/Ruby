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

// Determine the selected special type
$special_type = isset($_GET['special_type']) ? $_GET['special_type'] : 'today';

// Prepare SQL query based on selected special type
$special_type_map = [
    'today' => 'Today\'s Special',
    'weekly' => 'Weekly',
    'monthly' => 'Monthly'
];

$selected_special_type = isset($special_type_map[$special_type]) ? $special_type_map[$special_type] : 'Today\'s Special';

$sql = "SELECT * FROM specials WHERE special_type = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $selected_special_type);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>

<head>
    <title>The Gallery Café | Specials</title>
    <link rel="stylesheet" href="../css/specials.css">
</head>

<body>
    <div class="container">
        <header id="main-nav">
            <nav>
                <ul>
                    <li><a href="../php/index.php">HOME</a></li>
                    <li><a href="?special_type=today">TODAY'S SPECIAL</a></li>
                    <li><a href="?special_type=weekly">WEEKLY SPECIAL</a></li>
                    <li><a href="?special_type=monthly">MONTHLY SPECIAL</a></li>
                </ul>
            </nav>
        </header>

        <div class="specials-container">
            <div class="form-container">
                <h1><?php echo ucfirst($special_type); ?> Special</h1>
                <div class="special-form">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="special-item">';
                            echo '<h3>' . htmlspecialchars($row['item_name']) . '</h3>';
                            echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                            echo '<p><strong>Price:</strong> Rs.' . number_format($row['price'], 2) . '</p>';
                            echo '<p><strong>Valid Until:</strong> ' . htmlspecialchars($row['valid_until']) . '</p>';
                            if (!empty($row['image_url'])) {
                                echo '<div class="image-container">';
                                echo '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['item_name']) . '">';
                                echo '</div>'; // Close .image-container
                            }
                            echo '</div>'; // Close .special-item
                        }
                    } else {
                        echo '<p>No specials found for the selected category.</p>';
                    }

                    // Close the statement
                    mysqli_stmt_close($stmt);

                    // Close the connection
                    mysqli_close($conn);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <div class="bottom-footer">
            <p>&copy; 2024 Ruby's. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
