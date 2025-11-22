<?php
session_start();
include './resource/db.php';  //importing database connection

// Only allow admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$items = $conn->query("SELECT * FROM items");      //view items from the items table 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background-color: #f0f0f0; }
    h1 { margin-bottom: 10px; }
  </style>
</head>
<body>
  <h1>Items Table</h1>
  <table>
    <tr>
      <th>SKU</th>
      <th>Name</th>
      <th>Description</th>
      <th>Category</th>
      <th>Quantity</th>
      <th>Price</th>
    </tr>
    <?php while ($row = $items->fetch_assoc()): ?>
    <tr>
      <td><?= $row['sku'] ?></td>
      <td><?= $row['name'] ?></td>
      <td><?= $row['description'] ?></td>
      <td><?= $row['category'] ?></td>
      <td><?= $row['quantity'] ?></td>
      <td>$<?= number_format($row['price'], 2) ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
