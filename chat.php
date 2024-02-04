<?php

session_start();
include('config.php');

// Check if a session name is provided in the GET parameter
if (isset($_GET['session'])) {
    $newSessionName = $_GET['session'];

    // Update the selected session in the session variable
    $_SESSION['selected_session'] = $newSessionName;

    // Redirect to the updated session
    header("Location: chat.php");
    exit();
}

// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Get the username and selected session
$username = $_SESSION['username'];
$selectedSession = $_SESSION['selected_session'];

// Get the chat session file path based on the selected session
$sessionFolder = "sessions/";
$chatFilePath = $sessionFolder . $selectedSession . "_session.txt";


// Function to get chat messages from the chat session file
function getChatMessages($filePath) {
    // Check if the file exists
    if (file_exists($filePath)) {
        // Read the content of the file
        $content = file_get_contents($filePath);
        return explode("\n", $content);
    }

    return [];
}

// Function to save a new message to the chat session file
function saveMessage($filePath, $message) {
    // Append the new message to the file after sanitizing
    $sanitizedMessage = htmlspecialchars(" $message  ", ENT_QUOTES, 'UTF-8');
    file_put_contents($filePath, $sanitizedMessage . "\n", FILE_APPEND);
}






// Function to handle file uploads with image quality adjustment
function uploadFileWithQuality($file, $uploadDir, $quality = 10) {
    $fileName = time() . '_' . basename($file['name']); // Use timestamp as the unique name
    $targetFilePath = $uploadDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Check if the file is an image
    $check = getimagesize($file['tmp_name']);
    if ($check !== false) {
        // Upload the file
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            // Adjust image quality
            adjustImageQuality($targetFilePath, $quality);

            return $fileName;
        }
    }

    return false;
}

// Function to adjust the quality of an image
function adjustImageQuality($filePath, $quality) {
    // Check if the GD library is available
    if (extension_loaded('gd') && function_exists('imagejpeg')) {
        $image = imagecreatefromjpeg($filePath);

        // Save the image with the specified quality
        imagejpeg($image, $filePath, $quality);

        // Free up memory
        imagedestroy($image);
    }
}

// Get chat messages from the chat session file
$chatMessages = getChatMessages($chatFilePath);


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message'])) {
        $newMessage = $_POST['message'];
        $timestamp = date('H:i:s'); // Add time to the message

        // Check if a file is uploaded
        if (!empty($_FILES['file']['name'])) {
            // Define the upload directory
            $uploadDir = "images/";

            // Upload the file with reduced quality
            $uploadedFile = uploadFileWithQuality($_FILES['file'], $uploadDir, $imgQuality); // Set the desired quality

            if ($uploadedFile) {
                // Include the file link in the message
                $newMessage .= " [file: $uploadedFile]";
            }
        }

        // Save the new message to the chat session file with the username after sanitizing
        $messageWithUsername = $username . ': (' . $timestamp . ') ' . $newMessage;
        saveMessage($chatFilePath, $messageWithUsername);

        // Redirect to avoid form resubmission on page refresh
        header('Location: chat.php');
        exit();
    }
}

// Check if the "Exit Session" button is clicked
if (isset($_POST['exit-session'])) {
    $timestamp = date('H:i:s'); // Add time to the message

    // Save a special message indicating that the user left the chat
    $exitMessage = 'System: (' . $timestamp . ') ' . $username . ' left the chat';
    saveMessage($chatFilePath, $exitMessage);

    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to index page
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>t-chat</title>

    <!-- Include jQuery (you can download it or use a CDN) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        var chatWindow = $('#chat-window');

        function updateChat() {
            // Store the current scroll position
            var scrollPos = chatWindow.prop('scrollHeight') - chatWindow.scrollTop();

            // AJAX request to get the latest chat messages
            $.ajax({
                type: 'GET',
                url: 'get_messages.php',
                data: { session: '<?php echo $selectedSession; ?>' },
                success: function (data) {
                    // Update the chat window with the latest messages
                    chatWindow.html(data);

                    // Set the scroll position back to the stored value
                    chatWindow.scrollTop(chatWindow.prop('scrollHeight') - scrollPos);
                },
            });
        }

        // Update the chat every 2 seconds (adjust as needed)
        setInterval(updateChat, <?php echo $messageUpdate; ?>);
    });
</script>

</head>
<body>
    <div id="chat-container">
        <div id="chat-window">

        </div>

        <form id="chat-form" enctype="multipart/form-data">
            <input type="text" id="message" name="message" placeholder="Type your message... or ***help" required>
            <input type="file" id="file" name="file" accept="image/*">
            <button type="button" onclick="sendMessage()">Send</button>
        </form>

 <script>
    function sendMessage() {
        // Get the form data
        var formData = new FormData(document.getElementById('chat-form'));

        // Check if a file is being uploaded
        var fileInput = document.getElementById('file');
        if (fileInput.files.length > 0) {
            // Get the first file in the input
            var uploadedFile = fileInput.files[0];

            // Check if the file size exceeds 2MB (2097152 bytes)
            if (uploadedFile.size > <?php
$imgSize1=$imgSize*1000000;

 echo$imgSize1.') {
                alert("Uploaded image should be no larger than '.$imgSize.'MB.");';?>
                return;
            }
        }

        // Create an XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Configure it to perform a POST request
        xhr.open('POST', 'chat.php', true);

        // Set up a callback function to handle the response
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 400) {
                // Success! You can handle the response here if needed
                console.log(xhr.responseText);

                // Clear the input box after successful submission
                document.getElementById('message').value = '';

                // Clear the file input field
                document.getElementById('file').value = '';

                // Reset the form (optional)
                // document.getElementById('chat-form').reset();
            } else {
                // Error handling
                console.error(xhr.statusText);
            }
        };

        // Send the FormData object
        xhr.send(formData);
    }

    // Add an event listener to the form for submit events
    document.getElementById('chat-form').addEventListener('submit', function (event) {
        // Prevent the default form submission
        event.preventDefault();

        // Call the sendMessage function when the form is submitted
        sendMessage();
    });

    // Add an event listener to the input for keydown events
    document.getElementById('message').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            // Prevent the default Enter key behavior (e.g., new line)
            event.preventDefault();

            // Call the sendMessage function when Enter is pressed
            sendMessage();
        }
    });
</script>

        <form method="post" action="">
            <button type="submit" name="exit-session">Exit Session</button>
        </form>
    </div>
    <script src="js/main.js"></script>
</body>
</html>
