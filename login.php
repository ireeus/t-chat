<?php
session_start();

// Placeholder for user authentication logic (to be implemented)
function authenticateUser($username, $password) {
    // Replace this with your authentication logic (e.g., check against a database)
    $filePath = "users/" . $username . ".txt";

    // Check if the user file exists
    if (file_exists($filePath)) {
        // Read the content of the user file
        $userContent = file_get_contents($filePath);

        // Extract hashed password from user file
        preg_match("/Password: (.*)/", $userContent, $matches);
        $hashedPassword = isset($matches[1]) ? $matches[1] : null;

        // Verify password
        if ($hashedPassword !== null && password_verify($password, $hashedPassword)) {
            return true; // Authentication successful
        }
    }

    return false; // Authentication failed
}

// Function to send a message to the chat session file
function sendMessageToSession($sessionFileName, $message) {
    $sessionFolder = "sessions/";
    $chatFilePath = $sessionFolder . $sessionFileName . "_session.txt";

    // Save the new message to the chat session file with a system username
    $systemUsername = 'System';
    $messageWithUsername = $systemUsername . ': ' . $message;
    file_put_contents($chatFilePath, $messageWithUsername . "\n", FILE_APPEND);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $selectedSession = $_POST['session'];

    // Authenticate the user
    if (authenticateUser($username, $password)) {
        $_SESSION['username'] = $username;

        if ($selectedSession === 'new') {
            // Create a new session
            $newSessionName = date("Ymd_His"); // Using timestamp for uniqueness
            $sessionFolder = "sessions/";
            $chatFilePath = $sessionFolder . $newSessionName . "_session.txt";

            // Create the new session file
            file_put_contents($chatFilePath, ''); // Start with an empty file

            $_SESSION['selected_session'] = $newSessionName;
            header("Location: chat.php?session=$newSessionName");
        } elseif (!empty($selectedSession)) {
            // Log in to an existing session
            $_SESSION['selected_session'] = $selectedSession;
            header("Location: chat.php?session=$selectedSession");
            $timestamp = date('H:i:s'); // Add time to the message

            // Send a message to the selected session
            $joinMessage = '('.$timestamp.') '. $username . ' joined the chat';   //////////////////////////
            sendMessageToSession($selectedSession, $joinMessage);
        } else {
            // Handle the case where no session is selected
            $error_message = 'Please select a session.';
        }

        exit();
    } else {
        $error_message = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Login</title>
</head>
<body>
    <div id="login-container">
        <h2>Login</h2><ul>
            
          
                <p>No account? </p>
              
                </ul><li><a href="register.php">Register</a></li>
        <?php
        if (isset($error_message)) {
            echo '<p class="error">' . $error_message . '</p>';
        }
        ?>
        <form method="post" action="">
            <div>
                <p>
                    <label for="username">Username:</label><br>
                    <input type="text" id="username" name="username" required>
                </p>
                <p>
                    <label for="password">Password:</label><br>
                    <input type="password" id="password" name="password" required>
                </p>
                <p>
     <label for="session">Session:</label><br>
<select name="session" required>
    <option value="new">Create New Session</option>
    <?php
    // List existing sessions
    $sessionFolder = "sessions/";
    $sessions = glob($sessionFolder . "*_session.txt");
    foreach ($sessions as $session) {
        $sessionName = basename($session, '_session.txt');
        $selected = ($sessionName === $_GET['session']) ? 'selected' : ''; // Check if the session matches the parameter
        echo '<option value="' . $sessionName . '" ' . $selected . '>' . $sessionName . '</option>';
    }
    ?>
</select>

                </p>
                <p>
                    <button type="submit">Login</button>
                </p>
            </div>
        </form>
    </div>
</body>
</html>
