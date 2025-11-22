<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../resource/db.php';

$response = ["success" => false, "message" => ""];   //defining the respone variable 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  //post handle
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");  //return the passaword of the username
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($dbPassword);
    $stmt->fetch();
    $stmt->close();

    if ($dbPassword && $dbPassword === $password) { //checks if the client passwords matches what's stored in the databss 
        $_SESSION['username'] = $username;
        $response["success"] = true;
        $response["message"] = ($username === "admin") ? "Welcome, admin!" : "Login successful!";
    } else {
        $response["message"] = "Invalid username or password.";
    }
}

echo json_encode($response);// returning a json response back to the user
exit;

