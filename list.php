<?php
// Get the list of session files in the "sessions" folder
$sessionFiles = glob("sessions/*_session.txt");

// Extract session names from file names
$sessions = array_map(function ($file) {
    return basename($file, "_session.txt");
}, $sessionFiles);
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
    <h2>Session List</h2>
    <ul>
        <?php
        // Loop through each session and create a clickable link
        foreach ($sessions as $session) {
            echo '<li><a href="chat.php?session=' . htmlspecialchars($session) . '">' . htmlspecialchars($session) . '</a></li>';
        }
        ?>
    </ul>
</body>
</html>
