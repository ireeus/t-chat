<?php
session_start();

// Check if the user is logged in, redirect to chat.php
if (isset($_SESSION['username'])) {
    header('Location: chat.php');
    exit();
}

// Function to get all available chat sessions
function getAvailableSessions() {
    $sessionFolder = "sessions/";
    $sessions = glob($sessionFolder . "*_session.txt");
    return array_map('basename', $sessions, array_fill(0, count($sessions), '_session.txt'));
}

$availableSessions = getAvailableSessions();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>t-chat</title>
</head>
<body>
    <div id="options-container">
        <h2>Chat Options</h2>
        <ul>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
            <li>
                <p>Available Chat Sessions:</p>
                <ul>
                    <?php
                    foreach ($availableSessions as $session) {
                        echo '<li><a href="login.php?session=' . pathinfo($session, PATHINFO_FILENAME) . '">' . pathinfo($session, PATHINFO_FILENAME) . '</a></li>';
                    }
                    ?>
                </ul>
            </li>
        </ul>
    </div>
</body>
</html>
