<?php
session_start();
header('Content-Type: application/json');
include '../resource/db.php';

$response = ["success" => false, "message" => ""];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];   // setting the the variables 

    // Check if user already exists
    $stmt = $conn->prepare("SELECT username FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response["message"] = "Username already exists.";  //if a value graeter than zero exist than it exist
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");     // It it doesn't exist so we insert it 
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            $response["success"] = true;    //<-- creating the success message
            $_SESSION['username'] = $username;   //<-- storing the username in the session
            $response["message"] = "Signup successful!";
        } else {
            $response["message"] = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

echo json_encode($response);   //<-- sends back a json value to the client
?>
