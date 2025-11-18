<?php
$servername = "localhost";
$username = "dj";       // MySQL username you created
$password = "pcplayer";   // MySQL password
$dbname = "myDB";        // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//echo "Connected successfully";
?>
