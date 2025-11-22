<?php
session_start();
include './resource/db.php';


if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");  //if user is not an admin or have a username redirect to another page 
    exit;
}


$username = $_SESSION['username'];

// Run query
/*
$stmt = $conn->prepare("SELECT username, order_date, total_items, total_amount,shipping_address, status FROM `order` WHERE username = ?");

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <link rel="stylesheet" type="text/css" href="./resource/adminIndex.css">
</head>

    <style>
     
    </style>
</head>
<body>
      <div class="top-header">
    <img id="logo" src="resource/Logo.png" alt="Logo">
    <div class="header-title">Your Click, Your Cart!</div>
    <div class="header-actions">
      <img id="emptyCart" src="resource/emptyCart.png" alt="Logo">
      <div id="logout" style="display:none;">Logout</div>
    </div>
  </div>

<main style="padding:20px; background: linear-gradient(180deg, #808080 0%, #ccc 50%, #fff 100%);">
<h1>Welcome Administrator </h1>

<nav class="navbar">
        <ul class="nav-list">

            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropbtn">
                    📦 Items <span class="arrow">▼</span>
                </a>
                <div class="dropdown-content">
                    <a href="admin_view_items.php">View Items</a>. <!-- Will direct to the view page if login as adminxs -->

                    <a href="admin_add_item.php">Add Item</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropbtn">
                    🚚 Orders <span class="arrow">▼</span>
                </a>
                <div class="dropdown-content">
                    <a href="admin_order.php">View Orders/Change status</a>
                </div>
            </li>

        </ul>
    </nav>

    <div style="padding: 20px;">
        <h2 id="view-item">View Items List</h2>
        <h2 id="add-item">Add New Item Form</h2>
        <hr>
        <h2 id="view-orders">View Orders List</h2>
        <h2 id="change-status">Order Status Update</h2>
    </div>



</main>

    <script>
         document.getElementById("logo").addEventListener("click", function() {
      window.location.href = "index.php";
    });
     document.getElementById("emptyCart").addEventListener("click", function() {
      window.location.href = "cart.php";
    });




    </script>
</body>
</html>
