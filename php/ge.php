<?php
// Database credentials
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $details = $_POST['details'];

    // Prepare and bind
    $stmt = mysqli_prepare($conn, "INSERT INTO event_bookings (name, email, phone, event_date, event_time, details) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $phone, $date, $time, $details);

    // Execute statement and handle success or error
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>window.onload = function() { alert('Your booking request has been submitted. We will contact you shortly.'); }</script>";
    } else {
        $error_message = mysqli_stmt_error($stmt);
        echo "<script>window.onload = function() { alert('Error: $error_message'); }</script>";
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café | Groups & Events</title>
    <link rel="stylesheet" href="../css/ge.css"> 
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>
    <header id="main-nav">
        <nav>
            <ul>
                <li><a href="../php/index.php">HOME</a></li>
                <li><a href="#booking">BOOKINGS</a></li>
            </ul>
        </nav>
    </header>
    <section class="ge">
        <h1>Groups & Events</h1>
        <p>Host your memorable events with us. From intimate gatherings to grand celebrations, we provide a perfect setting and exceptional service.</p>
        <section id="event-gallery">
            <br>
            <br>
            <br>
            <h2>Past Events</h2>
            <div class="gallery">
                <div class="gallery-item">
                    <div class="slideshow-container">
                        <div class="mySlides fade">
                            <img src="../images/1.1.jpg" alt="Event 1 Image 1">
                        </div>
                        <div class="mySlides fade">
                            <img src="../images/1.2.jpg" alt="Event 1 Image 2">
                        </div>
                    </div>
                    <div class="description">Wedding Reception</div>
                </div>
                <div class="gallery-item">
                    <div class="slideshow-container">
                        <div class="mySlides fade">
                            <img src="../images/2.1.jpg" alt="Event 2 Image 1">
                        </div>
                        <div class="mySlides fade">
                            <img src="../images/2.2.jpg" alt="Event 2 Image 2">
                        </div>
                        <div class="mySlides fade">
                            <img src="../images/2.3.jpg" alt="Event 2 Image 3">
                        </div>
                    </div>
                    <div class="description">Corporate Events</div>
                </div>
                <div class="gallery-item">
                    <div class="slideshow-container">
                        <div class="mySlides fade">
                            <img src="../images/3.1.jpg" alt="Event 3 Image 1">
                        </div>
                        <div class="mySlides fade">
                            <img src="../images/3.2.jpg" alt="Event 3 Image 2">
                        </div>
                    </div>
                    <div class="description">Parties</div>
                </div>
            </div>
        </section>
        <section id="booking">
            <h2>Book Your Event</h2>
            <p>Ready to book your next event with us? Fill out the form below to get started!</p>
            <form action="ge.php" method="post">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" required>
                
                <label for="date">Event Date</label>
                <input type="date" id="date" name="date" required>
                
                <label for="time">Event Time</label>
                <input type="time" id="time" name="time" required>
                
                <label for="details">Event Details</label>
                <textarea id="details" name="details" rows="4" required></textarea>
                
                <button type="submit" class="submit-button">Submit</button>
            </form>
        </section>
    </section>
    <footer>
        <div class="bottom-footer">
            <p>&copy; 2024 The Gallery Café. All Rights Reserved.</p>
        </div>
    </footer>
    <script src="../js/ge.js"></script>
</body>
</html>
