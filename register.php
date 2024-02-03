<?php
// Placeholder for user registration logic (to be implemented)
function registerUser($username, $password) {
    // Validate the input (add more validation as needed)
    if (empty($username) || empty($password)) {
        return false;
    }

    // Hash the password (use a secure hashing algorithm like password_hash)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Create a new user file
    $filePath = "users/" . $username . ".txt";
    
    // Check if the user already exists
    if (file_exists($filePath)) {
        return false; // User already exists
    }

    // Save user information to the file
    $userContent = "Username: $username\nPassword: $hashedPassword\n";
    if (file_put_contents($filePath, $userContent) !== false) {
        return true; // Registration successful
    }

    return false; // Registration failed
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Register the user
    if (registerUser($username, $password)) {
        // Redirect to login page after successful registration
        header('Location: login.php');
        exit();
    } else {
        $error_message = 'Registration failed. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Register</title>
</head>

<body>
    <div id="Register-container">
        <h2>Register</h2>
        <?php
        if (isset($error_message)) {
            echo '<p class="error">' . $error_message . '</p>';
        }
        ?>
        <form method="post" action="">
            <div>
          <p>
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
        </p>
        <p>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </p>
        <p>

            <button type="submit">Register</button>
        </p>
            </div>
        </form>
    </div>
</body>
</html>
