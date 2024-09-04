<?php
session_start();
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

// Handle employee addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    // Check if username already exists
    $sql = "SELECT COUNT(*) FROM users WHERE username = '$user'";
    $result = mysqli_query($conn, $sql);
    $count = mysqli_fetch_array($result)[0];

    if ($count > 0) {
        $error = "Username already exists. Please choose a different username.";
    } else {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, role) VALUES ('$user', '$hashed_pass', 'Employee')";
        if (mysqli_query($conn, $sql)) {
            $success = "Employee added successfully.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle employee removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_employee'])) {
    $id = (int)$_POST['id'];

    // Check if the ID exists
    $sql = "SELECT COUNT(*) FROM users WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $count = mysqli_fetch_array($result)[0];

    if ($count > 0) {
        // Remove the employee
        $sql = "DELETE FROM users WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            $success = "Employee removed successfully.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Employee not found.";
    }
}

// Fetch employee details
$sql = "SELECT id, username, role FROM users WHERE role = 'Employee'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Gallery Café | Manage Employees</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        button {
            padding: 8px 16px;
            border: none;
            background-color: #200101;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #0056b3;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .message {
            text-align: center;
            font-size: 16px;
        }
        .message.error {
            color: red;
        }
        .message.success {
            color: green;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Manage Employees</h1>

    <form method="POST" action="">
        <h2>Add New Employee</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit" name="add_employee">Add Employee</button>
    </form>

    <form method="POST" action="">
        <h2>Remove Employee</h2>
        <label for="id">Employee ID:</label>
        <input type="number" id="id" name="id" required>
        <button type="submit" name="remove_employee">Remove Employee</button>
    </form>

    <?php if (isset($error)) { echo "<p class='message error'>$error</p>"; } ?>
    <?php if (isset($success)) { echo "<p class='message success'>$success</p>"; } ?>

    <h2>Employee List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
mysqli_close($conn);
?>
