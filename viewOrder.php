<?php
session_start();
include './resource/db.php';

$loggedIn = isset($_SESSION['username']);


if (!$loggedIn) {
    header("Location: index.php");   //if not logged send the user back to the home page 
    exit;
}


$username = $_SESSION['username'];  //create a username variable for the query

// Run query
$stmt = $conn->prepare("SELECT username, order_date, total_items, total_amount,shipping_address, status FROM `order` WHERE username = ?");

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <link rel="stylesheet" type="text/css" href="resource/index.css">
</head>

    <style>
     
    </style>
</head>
<body>
      <div class="top-header">
    <img id="logo" src="resource/Logo.png" alt="Logo">
    <div class="header-title">Your Click, Your Cart!</div>
    <div class="header-actions">
      <img id="emptyCart" src="resource/emptyCart.png" alt="Logo">   <!-- header-->
      <div id="logout" style="display:none;">Logout</div>
    </div>
  </div>

<main style="padding:20px; background: linear-gradient(180deg, #808080 0%, #ccc 50%, #fff 100%);">
<h1>Your Orders</h1>

<table  border='1' cellpadding='10' cellspacing='0'>
    <tr>
        <th>Username</th>
        <th>Order Date</th>
        <th>Total Items</th>
        <th>Total Amount</th>
        <th>Shipping Method</th>
        <th>Shipping Address</th>
        <th>Status</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?> <!-- while theres still. results frm the query create the table  -->
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['order_date']) ?></td>
            <td><?= htmlspecialchars($row['total_items']) ?></td>
            <td>$<?= number_format($row['total_amount'], 2) ?></td>
            <td><?= htmlspecialchars($row['shipping']) ?></td>
            <td><?= htmlspecialchars($row['shipping_address']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>
    </main>

    <script>
         document.getElementById("logo").addEventListener("click", function() { //button handle for logo and cart
      window.location.href = "index.php";
    });
     document.getElementById("emptyCart").addEventListener("click", function() {
      window.location.href = "cart.php";
    });

    </script>
</body>
</html>
