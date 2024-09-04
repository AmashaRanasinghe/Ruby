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

// Prepare SQL query to retrieve promotions
$sql = "SELECT * FROM promotions";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café | Promotions</title>
    <link rel="stylesheet" href="../css/promotions.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
</head>
<body>
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

    <div class="promotions-container">
        <!-- Slideshow Container -->
        <div class="slideshow-container">
            <div class="slides fade">
                <img src="../images/chinese.jpg" alt="Summer Sale">
            </div>
            <div class="slides fade">
                <img src="../images/indian.jpg" alt="Weekend Special">
            </div>
            <div class="slides fade">
                <img src="../images/srilankan.jpg" alt="Holiday Discount">
            </div>
            <div class="slides fade">
                <img src="../images/italian.jpg" alt="Holiday Discount">
            </div>
        </div>

        <!-- Promotions Content -->
        <div id="promotion-results">
            <?php
            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo '<main>';
                while ($row = mysqli_fetch_assoc($result)) {
                    $promotion_name = htmlspecialchars($row['promotion_name']);
                    $description = htmlspecialchars($row['description']);
                    $discount_percentage = htmlspecialchars($row['discount_percentage']);
                    $valid_until = htmlspecialchars($row['valid_until']);

                    echo '<div class="promotion-item">';
                    echo '<div class="promotion-content">';
                    echo '<p>' . $description . '</p>';
                    echo '<p><strong>Discount:</strong> ' . $discount_percentage . '%</p>';
                    echo '<p><strong>Valid Until:</strong> ' . $valid_until . '</p>';
                    echo '</div>'; // Close .promotion-content
                    echo '</div>'; // Close .promotion-item
                }
                echo '</main>';
            } else {
                echo '<p>No promotions found.</p>';
            }

            // Free result set
            mysqli_free_result($result);

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

    <!-- JavaScript for Slideshow -->
    <script>
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let i;
            let slides = document.getElementsByClassName("slides");
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  
            }
            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}    
            slides[slideIndex-1].style.display = "block";  
            setTimeout(showSlides, 3000); // Change image every 3 seconds
        }
    </script>
</body>
</html>
