<?php
session_start();
include('config.php');
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
        "**help" => "<a href='help.html' target='blank'>help</a>",
        "***list" => "<a href='list.php' >List of available sessions</a>"
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
// Function to generate a background color based on the username
function getUsernameColor($username) {
include('config.php');

    // Generate a hash from the username
    $hash = md5($username);
    // Extract RGB values from the hash
    $red = hexdec(substr($hash, 0, 2));
    $green = hexdec(substr($hash, 2, 2));
    $blue = hexdec(substr($hash, 4, 2));

    // Return the formatted RGB color
    //return sprintf('rgb(%d, %d, %d)', $red, $green, $blue);
    return sprintf('rgba(%d, %d, %d, %f)', $red, $green, $blue, $opacity);

}



// Display chat messages
foreach ($chatMessages as $message) {
    // Split the message into username and content
    list($messageUsername, $messageContent) = explode(':', $message, 2);

    // Trim and check if the message is not empty
    $trimmedMessageContent = trim($messageContent);
    if (!empty($trimmedMessageContent)) {
        // Check if the message contains a date inside brackets
        if (preg_match('/\((\d{2}:\d{2}:\d{2})\)/', $trimmedMessageContent, $matches)) {
            // If a date is found, change the font size
            $fontSizeStyle = 'font-size: 12px;';
            
            $trimmedMessageContent = preg_replace('/\((\d{2}:\d{2}:\d{2})\)/', '<span style="' . $fontSizeStyle . '">$0</span>', $trimmedMessageContent);
        }

        // Replace emoticons in the message content
        $trimmedMessageContent = replaceEmoticons($trimmedMessageContent);

        // Replace image links in the message content
        $trimmedMessageContent = replaceImageLinks($trimmedMessageContent);

        // Get the background color based on the username
        $backgroundColor = getUsernameColor($messageUsername);

        // Display the message with a label for the username and dynamic background color
        echo '<div class="message-container" style="background-color: ' . $backgroundColor . '"><p><span class="username-label">' . htmlspecialchars(trim($messageUsername), ENT_QUOTES, 'UTF-8') . ':</span> ' . $trimmedMessageContent . '</p></div>';
    }
}

?>
