<?php
$host = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "meal_management";
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
