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

// Get search term
$search = isset($_GET['term']) ? mysqli_real_escape_string($conn, $_GET['term']) : '';

// Prepare SQL queries for each menu
$sql_starters = "SELECT * FROM starters WHERE item_name LIKE '%$search%'";
$sql_appetizers = "SELECT * FROM appetizers WHERE item_name LIKE '%$search%'";
$sql_beverages = "SELECT * FROM beverages WHERE item_name LIKE '%$search%'";

$result_starters = mysqli_query($conn, $sql_starters);
$result_appetizers = mysqli_query($conn, $sql_appetizers);
$result_beverages = mysqli_query($conn, $sql_beverages);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>The Gallery Café | Meals</title>
    <link rel="stylesheet" href="../css/cuisine.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
    <script src="../js/meals.js" defer></script>
</head>
<body>
    <div class="container">
        <header id="main-nav">
            <nav>
                <ul>
                    <li><a href="../php/index.php">HOME</a></li>
                    <li><a href="../php/meals.php">MEALS</a></li>
                    <li><a href="../html/menu.html">MENUS</a></li>
                    <li><a href="../php/specials.php">SPECIALS</a></li>
                    <li><a href="../php/promotions.php">PROMOTIONS</a></li>
                </ul>
            </nav>
        </header>
        <h1>Pick Your Meal</h1>
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" id="search-input" name="term" placeholder="Search for a meal..." class="search-bar">
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
        <div id="search-results">
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
                    echo '</div></div>';
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
                    echo '</div></div>';
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
                    echo '</div></div>';
                }
            } else {
                echo '<p>No beverages found matching your search.</p>';
            }
            echo '</div>';

            // Free result sets
            mysqli_free_result($result_starters);
            mysqli_free_result($result_appetizers);
            mysqli_free_result($result_beverages);

            // Close the connection
            mysqli_close($conn);
            ?>
        </div>
    </div>
    <footer>
        <div class="bottom-footer">
            <p>&copy; 2024 Ruby's. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
