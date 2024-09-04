<?php
session_start(); // Start session to use session variables

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
    $role = 'Customer'; // Hardcoded role for customer sign-up

    // Validate password
    if (strlen($pass) < 6 || !preg_match('/[0-9]/', $pass) || !preg_match('/[\W_]/', $pass)) {
        $error = "Password must be at least 6 characters long and include at least one number and one symbol.";
    } else {
        // Check if username already exists
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($count > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $user, $hashed_pass, $role);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['signup_success'] = "Registration successful. You can now <a href='login.php'>login</a>.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café |Sign Up</title>
    <link rel="stylesheet" href="../css/users.css">
    <style>
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        form label,
        form input,
        form button {
            margin-bottom: 10px;
        }
        .password-requirements {
            color: #555;
            font-size: 0.9em;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Sign Up</h1>
        </header>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <!-- Hidden field for role -->
            <input type="hidden" name="role" value="Customer">
            <button type="submit">Sign Up</button>
        </form>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
