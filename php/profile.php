<?php
session_start(); // Start session to use session variables

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

// Get user details from session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch the username from the database
$username_query = "SELECT username FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $username_query);

if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $username = htmlspecialchars($user_data['username']);
} else {
    $username = "User";
}

// Initialize variables for content
$greeting = "Hello " . $username . "!";
$help_page = '';

switch ($role) {
    case 'Administrator':
        $help_page = '../html/admin_help.html';
        break;
    case 'Employee':
        $help_page = '../html/emp_help.html';
        break;
    case 'Customer':
        $help_page = '../html/cus_help.html';
        break;
}

// Handle password change request
$password_change_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
    if (mysqli_query($conn, $update_query)) {
        $password_change_message = "Password changed successfully.";
    } else {
        $password_change_message = "Error changing password: " . mysqli_error($conn);
    }
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café |User Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
    <script>
        // JavaScript to show alert message if set
        <?php if (!empty($password_change_message)): ?>
            alert('<?php echo htmlspecialchars($password_change_message); ?>');
        <?php endif; ?>
    </script>
</head>
<body>
    <div class="container">
        <header class="profile-header">
            <p><?php echo htmlspecialchars($greeting); ?></p>
        </header>

        <div class="profile-content">
            <!-- Password Change Form -->
            <div class="password-change">
                <h1>Change Password</h1>
                <form method="POST" action="">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <button type="submit" name="change_password">Change Password</button>
                </form>
            </div>

            <!-- Include help content below the password change form -->
            <?php
            if (!empty($help_page) && file_exists($help_page)) {
                include($help_page);
            } else {
                echo '<p>Help content is not available.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
