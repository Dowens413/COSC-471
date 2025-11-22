<?php
session_start();
include './resource/db.php';

// Only allow admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    die("Unauthorized");
}

// Handle AJAX status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {   //if a post request comes do this...
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE `order` SET status=? WHERE order_id=?");  //update the order status based on the order id 
    $stmt->bind_param("ii", $status, $order_id);

    if ($stmt->execute()) {
        echo "Order #$order_id updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    exit; // Stop further output for AJAX
}

// Fetch all orders for display
$result = $conn->query("SELECT * FROM `order` ORDER BY order_date DESC");    // call the order table  ticks(`) are needed because of order is a mysql parameter
$orders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Orders</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border:1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #eee;
        }
        select {
            padding: 4px;
        }
        button {
            padding: 4px 8px;
        }
        /* Optional: highlight by status */
        .status-0 { background: #fff3cd; } /* Pending - yellow */
        .status-1 { background: #d1ecf1; } /* Shipped - blue */
        .status-2 { background: #d4edda; } /* Delivered - green */
        .status-3 { background: #f8d7da; } /* Cancelled - red */
    </style>
</head>
<body>
<h1>All Orders</h1>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Date</th>
            <th>Total Items</th>
            <th>Total Amount</th>
            <th>Shipping Address</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($orders as $order): ?>
        <tr class="status-<?= $order['status'] ?>">
            <td><?= $order['order_id'] ?></td>
            <td><?= htmlspecialchars($order['username']) ?></td>
            <td><?= $order['order_date'] ?></td>
            <td><?= $order['total_items'] ?></td>
            <td>$<?= $order['total_amount'] ?></td>
            <td><?= htmlspecialchars($order['shipping_address']) ?></td>
            <td>
                <select data-order="<?= $order['order_id'] ?>">
                    <option value="0" <?= $order['status']==0 ? 'selected' : '' ?>>Pending</option>
                    <option value="1" <?= $order['status']==1 ? 'selected' : '' ?>>Shipped</option>
                    <option value="2" <?= $order['status']==2 ? 'selected' : '' ?>>Delivered</option>
                    <option value="3" <?= $order['status']==3 ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </td>
            <td><button class="updateBtn" data-order="<?= $order['order_id'] ?>">Update</button></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
const buttons = document.querySelectorAll(".updateBtn");

buttons.forEach(btn => {
    btn.addEventListener("click", async () => {
        const orderId = btn.dataset.order;
        const select = document.querySelector(`select[data-order='${orderId}']`);
        const status = select.value;

        const formData = new FormData();
        formData.append("update_status", 1); // flag to detect AJAX update
        formData.append("order_id", orderId);
        formData.append("status", status);

        try {
            const response = await fetch("", { // same page
                method: "POST",
                body: formData
            });
            const result = await response.text();
            alert(result);

            // Optionally: change row color based on new status
            const row = select.closest("tr");
            row.className = "status-" + status;
        } catch (err) {
            alert("Error updating order.");
            console.error(err);
        }
    });
});
</script>
</body>
</html>
