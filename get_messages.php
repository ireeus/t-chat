<?php
session_start();

// Get the selected session from the session variable
$selectedSession = isset($_SESSION['selected_session']) ? $_SESSION['selected_session'] : '';

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

// Function to replace emoticons in the message content
function replaceEmoticons($text) {
    $emoticonMapping = array(
        ":D" => "😁",
        ":)" => "😃",
        ":(" => "😢",
        ";)" => "😉",
        ":p" => "😛",
        ":o" => "😲",
        ":|" => "😐",
        ":*" => "😘",
        ":/" => "😕",
        "@)" => "😏",
        "@W" => "😔",
        ":^)" => "😄",
        ":,(" => "😢",
        "XD" => "😆",
        ":-D" => "😃",
        "@D" => "😂",
        ";o)" => "😇",
        ":K" => "😺",
        "@h" => "❤️",
        "*(" => "😥",
        "@rrr" => "😡",
        ":ii" => "😖",
        ":S" => "😖",
        "O:)" => "😇",
        "o.O" => "😳",
        ":|" => "😐",
        "(y)" => "👍",
        "@b" => "🐦",
        "(H)" => "🏠",
        "(C)" => "©️",
        "(R)" => "®️",
        "(T)" => "™️",
        ":+1" => "👍",
        ":-1" => "👎",
        "(A)" => "🅰️",
        "(B)" => "🅱️",
        "(AB)" => "🆎",
        "(O)" => "🅾️",
        "***help" => "<a href='help.html' target='blank'>help</a>"
    );

    foreach ($emoticonMapping as $key => $value) {
        $text = str_replace($key, $value, $text);
    }
    return $text;
}

// Function to replace image links with HTML image tags
function replaceImageLinks($text) {
    $pattern = '/\[file: ([^\]]+)\]/';
    $replacement = '<a href="images/$1" target="blank" ><img src="images/$1" width="50" alt="Image"></a>';
    return preg_replace($pattern, $replacement, $text);
}

// Get chat messages from the chat session file and not reverse the order
$chatMessages = getChatMessages($chatFilePath);

// Display chat messages
foreach ($chatMessages as $message) {
    // Split the message into username and content
    $messageParts = explode(':', $message, 2);

    // Check if the explode function returned an array with at least two elements
    if (count($messageParts) >= 2) {
        // Assign the array elements to variables
        list($messageUsername, $messageContent) = $messageParts;

        // Trim and check if the message is not empty
        $trimmedMessageContent = trim($messageContent);

        // Continue with the rest of your code...
        
        // Replace emoticons in the message content
        $trimmedMessageContent = replaceEmoticons($trimmedMessageContent);

        // Replace image links in the message content
        $trimmedMessageContent = replaceImageLinks($trimmedMessageContent);

        // Display the message with a label for the username
        echo '<p><span class="username-label">' . htmlspecialchars(trim($messageUsername)) . ':</span> ' . $trimmedMessageContent . '</p>';
    } 
}

?>
