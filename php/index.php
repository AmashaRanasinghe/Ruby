<?php
// Start the session to use session variables
session_start();

// Check if user is logging out
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header('Location: index.php'); // Redirect to the home page
    exit();
}

// Initialize variables
$logged_in = false;
$role = '';

// Check if user is logged in
if (isset($_SESSION['username'])) {
    $logged_in = true;
    $role = $_SESSION['role']; // Get user role from session
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-...your-sha512-hash-here..." crossorigin="anonymous" />
</head>
<body>
    <div class="container">
        <header>
            <img class="logo" src="../images/tlogo.png" alt="Logo">
            <br>
            <video autoplay muted loop id="video-header">
                <source src="../videos/mainvid.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <br>
            <nav> 
                <ul>
                    <li><a href="../php/index.php">HOME</a></li>
                    <li class="dropdown">
                        <a class="dropbtn">FOOD</a>
                        <div class="dropdown-content">
                            <a href="../php/meals.php">MEALS</a>
                            <a href="../html/menu.html">MENUS</a>
                            <a href="../php/specials.php">SPECIALS</a>
                            <a href="../php/promotions.php">PROMOTIONS</a>
                        </div>
                    </li>
                    <li><a href="../php/ge.php">GROUPS & EVENTS</a></li>
                    <li><a href="#testimonials">TESTIMONIALS</a></li>
                    <li><a href="#footer-left">ABOUT US</a></li>
                    <li><a href="#footer-right">CONTACT</a></li>
                    <li class="image-dropdown">
                        <img src="../images/Picture2.png" alt="More Options" class="dropdown-image">
                        <div class="dropdown-content">
                            <?php if ($logged_in): ?>
                                <?php if ($role === 'Administrator'): ?>
                                    <a href="../php/admin_meals.php">Edit Meals</a>
                                    <a href="../php/admin_breakfast.php">Edit Breakfast</a>
                                    <a href="../php/admin_lunch.php">Edit Lunch</a>
                                    <a href="../php/admin_dinner.php">Edit Dinner</a>
                                    <a href="../php/admin_promotions.php">Edit Promotions</a>
                                    <a href="../php/admin_specials.php">Edit Specials</a>
                                    <a href="../php/admin_employee.php">Edit Employees</a>
                                <?php elseif ($role === 'Employee'): ?>
                                    <a href="../php/emp_reservation.php">Edit Reservations</a>
                                    <a href="../php/emp_booking.php">Check Bookings</a>
                                <?php elseif ($role === 'Customer'): ?>
                                    <a href="../php/reservation.php">Reservations</a>
                                <?php endif; ?>
                                <a href="../php/profile.php">Profile</a>
                                <a href="index.php?action=logout">Log Out</a>
                            <?php else: ?>
                                <a href="../php/login.php">Log In</a>
                                <a href="../php/signup.php">Sign Up</a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="lens-overlay"></div>
        </header>
        <div class="content">
            <h2>Overview</h2>
            <p>Restaurants are the heart of social gatherings, where diverse cultures intertwine through the universal language of food. From bustling city eateries to quaint countryside bistros, they cater to a myriad of tastes and preferences. A successful restaurant harmonizes culinary expertise with exceptional service, creating memorable experiences for patrons.</p>
            <br>
            <hr width="1000px">
            <h2>Our Philosophy</h2>
            <p>At <b>TGC</b>, we believe in the harmony of nature and nourishment. Our menu is crafted with the freshest organic produce, sourced from local farms that practice sustainable and eco-friendly methods.</p>
            <br>
            <hr width="1000px">
            <h2>Why Choose Us</h2>
            <h3>Organic Excellence</h3>
            <p>Every dish is prepared with organic vegetables, herbs, and spices, ensuring not only delightful flavors but also a commitment to your health and well-being.</p>
            <h3>Diverse Menu</h3>
            <p>Our diverse menu celebrates the richness and diversity of cuisine, offering a variety of dishes that cater to all taste preferences.</p>
            <h3>Eco-Friendly Practices</h3>
            <p>From our kitchen to our table settings, we embrace green practices, minimizing waste and promoting a sustainable lifestyle.</p> 
        </div>
        <section id="testimonials" class="testimonials">
            <div class="container">
                <h2>What Our Customers Say</h2>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"TGC has become my favorite spot for a casual meal. The food is always fresh and delicious!"</p>
                        <span class="author">Ruby R.</span>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"Great variety and even better taste. Each visit is a new adventure for my taste buds!"</p>
                        <span class="author">Bruno Ranasinghe</span>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"TGC's cozy atmosphere and friendly staff make it a perfect place for a relaxed meal with friends."</p>
                        <span class="author">Stephanie</span>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"I always leave TGC satisfied. The menu has something for everyone, and it never disappoints."</p>
                        <span class="author">Rowdy Thompsons</span>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"Every dish at TGC feels like a treat. The global menu is a delightful experience for any food lover."</p>
                        <span class="author">Teddy Bear</span>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"TGC’s variety and quality keep me coming back. It’s a great spot for any occasion!"</p>
                        <span class="author">Hunter De Zoysa</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <footer>
        <div id="fcontainer">
            <div id="footer-left">
                <h3>About Us</h3>
                <br>
                <p>Welcome to The Gallery Cafe, where we bring you a global culinary experience with authentic and organic dishes made with love.</p>
                <p>Located in the heart of Bambalapitiya, our restaurant is committed to offering you a culinary experience that celebrates the rich heritage and diversity of Sri Lankan cuisine.</p>
                <p>At TGC, we source the finest organic ingredients, handpicked from local farms and markets, ensuring freshness and quality in every dish.</p>
                <p>Whether you're craving traditional rice and curry, fragrant seafood dishes, or our chef's special creations, each bite at Ruby's is crafted with passion and care.</p>
                <p>Join us and indulge in a journey through culinary delights, where every meal tells a story of tradition, authenticity, and a deep-rooted love for food.</p>
                <ul class="social-links">
                    <li><a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="https://www.twitter.com/"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="https://www.facebook.com/"><i class="fab fa-facebook"></i></a></li>
                    <li><a href="https://www.tiktok.com/"><i class="fab fa-tiktok"></i></a></li>
                </ul>
            </div>
            <div class="footer-center">
                <h3>Opening Hours</h3>
                <br>
                <ul>
                    <li>Monday - Friday: 12:00 AM - 11:00 PM</li>
                    <li>Saturday - Sunday: 1:00 PM - 12:00 PM</li>
                </ul>
            </div>
            <div id="footer-right">
                <h3>Contact</h3>
                <br>
                <p>Galle Road, Bambalapitiya</p>
                <address><a href="mailto:amapiumiranasinghe@gmail.com">amapiumranasinghe@gmail.com</a></address>
                <p>Phone:078 5821320</p>
                <br><br>
                <p>Find us on Google Maps: <br><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.9316609954235!2d79.85229727482233!3d6.898776993100476!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae259602cb3bc09%3A0x677419394138f674!2sThe%20Gallery%20Caf%C3%A9!5e0!3m2!1sen!2slk!4v1722618418476!5m2!1sen!2slk" width="400" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></a></p>
            </div>
        </div>
        <div class="bottom-footer">
            <p>&copy; 2024 Ruby's. All Rights Reserved.</p>
        </div>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>
