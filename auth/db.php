<?php
$host = "localhost";
    $dbUsername = "eidhl_meal_user_7A";
    $dbPassword = "a11ILDiya22C1wO+EIDHL.User@247";
    $dbname = "eidhl_meal";
    //Create a connection
    $conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

    // Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// **Ensure UTF-8 Encoding**
if (!$conn->set_charset("utf8mb4")) {
    die("Error loading character set utf8mb4: " . $conn->error);
}
?>