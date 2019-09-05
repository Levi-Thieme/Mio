<?php

?>

<!DOCTYPE html>
    <html lang="en">
        <head>
        </head>
        <body>
            <ul>
            <li><button onclick=<?php header("Location: ./chat_server_tests/clientTests.php"); ?>>Run Client Tests</button></li>
            <li><button onclick=<?php header("Location: ./chat_server_tests/ChannelManagerTests.php"); ?>>Run ChannelManager Tests</button></li>
            </ul>
        </body>
    </html>