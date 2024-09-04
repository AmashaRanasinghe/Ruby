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

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prepare SQL to select user
    $stmt = mysqli_prepare($conn, "SELECT id, password, role FROM users WHERE username = ?");
    if ($stmt === false) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $user_id, $hashed_pass, $role);
        mysqli_stmt_fetch($stmt);

        // Verify password
        if (password_verify($pass, $hashed_pass)) {
            $_SESSION['username'] = $user;
            $_SESSION['role'] = $role; // Store user role in session
            $_SESSION['user_id'] = $user_id; // Store user ID in session
            header('Location: index.php'); // Redirect to the home page
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café |Login</title>
    <link rel="stylesheet" href="../css/users.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Login</h1>
        </header>
        <?php
        if (isset($_SESSION['signup_success'])) {
            echo "<p style='color: green;'>" . $_SESSION['signup_success'] . "</p>";
            unset($_SESSION['signup_success']);
        }
        ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (!empty($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>
</body>
</html>
