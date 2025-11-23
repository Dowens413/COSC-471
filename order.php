<?php
session_start();
include 'resource/db.php';

// MUST BE LOGGED IN
if (!isset($_SESSION['username'])) {
    die("You must be logged in to place an order.");
}

$username_display = htmlspecialchars($_SESSION['username']);
$username = $_SESSION['username'];

// Totals already calculated earlier
$total_items  = isset($_SESSION['total_items'])  ? $_SESSION['total_items']  : 0;
$total_amount = isset($_SESSION['total_amount']) ? $_SESSION['total_amount'] : 0;


// ---------------------------------------------------------
// 1️⃣ PROCESS ORDER WHEN POST REQUEST
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $shipping_address = $_POST['shipping_address'];
    $order_date = date("Y-m-d H:i:s");
    $status = 0;

    // -----------------------------------------------------
    // Insert into ORDER table
    // -----------------------------------------------------
    $stmt = $conn->prepare("
        INSERT INTO `Order` 
        (username, order_date, total_items, total_amount, shipping_address, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssidss",
        $username,
        $order_date,
        $total_items,
        $total_amount,
        $shipping_address,
        $status
    );

    if (!$stmt->execute()) {
        die("ERROR INSERTING ORDER: " . $stmt->error);
    }

    $order_id = $stmt->insert_id;
    $stmt->close();

    // -----------------------------------------------------
    // Insert items into ORDER_ITEM
    // -----------------------------------------------------
    foreach ($_SESSION['cart'] as $sku => $quantity) {

        // Get price from Items table
        $price_lookup = $conn->prepare("SELECT price FROM Items WHERE sku = ?");
        $price_lookup->bind_param("i", $sku);
        $price_lookup->execute();
        $res = $price_lookup->get_result();

        if ($res->num_rows > 0) {
            $priceRow = $res->fetch_assoc();
            $item_price = $priceRow['price'];
            $item_total = $item_price * $quantity;

            $insert_item = $conn->prepare("
                INSERT INTO Order_Item (order_id, sku, quantity, item_price, item_total)
                VALUES (?, ?, ?, ?, ?)
            ");
            $insert_item->bind_param("iiidd",
                $order_id,
                $sku,
                $quantity,
                $item_price,
                $item_total
            );
            $insert_item->execute();
            $insert_item->close();
        }

        $price_lookup->close();
    }

    // Clear cart
    unset($_SESSION['cart']);
    unset($_SESSION['total_items']);
    unset($_SESSION['total_amount']);

    // Redirect
    header("Location: confirmation.php?order_id=" . $order_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Shipping Address</title>
    <link rel="stylesheet" href="resource/index.css">
</head>
<body>

<div class="top-header">
    <img id="logo" src="resource/Logo.png" alt="Logo">
    <div class="header-title">Your Click, Your Cart!</div>
</div>

<main  style="padding:20px; background: linear-gradient(180deg, #808080 0%, #ccc 50%, #fff 100%);">
    <form method="POST">
        <h2>Enter Your Shipping Address</h2>
        <h1><?php echo $username_display; ?> is checking out</h1>

        <label for="shipping_address">Shipping Address:</label>
        <input 
            type="text" 
            id="shipping_address" 
            name="shipping_address" 
            placeholder="Street address, city, state, zip code"
            required
        >

        <button type="submit">Complete Order</button>
    </form>
</main>
    <script>
          document.getElementById("logo").addEventListener("click", function() {
      window.location.href = "index.php";
    });

    </script>

</body>
</html>
