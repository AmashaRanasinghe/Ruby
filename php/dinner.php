<?php
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

// Get search term
$search = isset($_GET['term']) ? mysqli_real_escape_string($conn, $_GET['term']) : '';

// Prepare SQL query based on search term
$sql = "SELECT * FROM dinner WHERE item_name LIKE '%$search%' OR cuisine_type LIKE '%$search%'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café | Dinner</title>
    <link rel="stylesheet" href="../css/cuisine.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
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
        <h1>Pick your Dinner</h1>
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" id="search-input" name="term" placeholder="Search for a cuisine (e.g., Sri Lankan, Indian, Italian, Chinese)..." class="search-bar">
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
        <div id="search-results">
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
                        echo '<h2>' . htmlspecialchars($current_cuisine) . ' Cuisine</h2>';
                    }

                    echo '<div class="menu-item">';
                    echo '<h3>' . htmlspecialchars($row['item_name']) . '</h3>';
                    echo '<div class="price-list">';
                    echo '<div class="price">Price: Rs.' . number_format($row['price'], 2) . '</div>';
                    echo '</div></div>';
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
    </div>
    <footer>
        <div class="bottom-footer">
            <p>&copy; 2024 Ruby's. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
