<?php
session_start();
include './resource/db.php';

// Only allow admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $description = $_POST['description'];    //setting the values sent from the client
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Validate category
    $category_arr = ["Electronics","Movies","Books","Toys","Clothing","Home","Beauty","Automotive","Sports"];
    if (!in_array(strtolower($category), array_map('strtolower', $category_arr))) {
        echo "Invalid category";
        exit;
    } //makes sure the admin enters a correct category that matchin g the webistes setup.

    $stmt = $conn->prepare("INSERT INTO items (sku, name, description, category, quantity, price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssii", $sku, $name, $description, $category, $quantity, $price);

    if ($stmt->execute()) {
        echo "Item added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin insert items</title>
</head>
<body >
  <h1>Add items table</h1>
<main  style="padding:20px; background: linear-gradient(180deg, #808080 0%, #ccc 50%, #fff 100%);">
<form id="itemForm">
    <div>
    <label for="sku">Item SKU:</label>
    <input type="number" id="sku" name="sku" required>
</div>
    <div>
    <label for="name">Item Name:</label>
    <input type="text" id="name" name="name" required>
    </div>
    <div>
    <label for="description">Item Description:</label>
    <input type="text" id="description" name="description" required>
    </div>
    <div>
    <label for="category">Item Category:</label>
    <input type="text" id="category" name="category" required>
    </div>
    <div>
    <label for="quantity">Quantity:</label>
    <input type="number" id="quantity" name="quantity" min="0" required>
    </div>
    <div>
    <label for="price">Item Price:</label>
    <input type="number" id="price" name="price" step="0.01" min="0" required>
    </div>

    <br><br>
    <button id="submitBtn" type="submit">Add Item</button>
</form>
</main>
<script>
const category_arr = [
    "Electronics","Movies","Books","Toys","Clothing","Home","Beauty","Automotive","Sports"
];

document.getElementById("itemForm").addEventListener("submit", async function(e) {
    e.preventDefault(); // Stop form from submitting normally

    const sku = document.getElementById("sku").value;
    const name = document.getElementById("name").value;
    const description = document.getElementById("description").value;
    const category = document.getElementById("category").value;
    const quantity = document.getElementById("quantity").value;
    const price = document.getElementById("price").value;

    // Client-side validation
    if(name.length > 25) {
        alert("Item name must be less than 25 characters");
        return;
    }
    if(description.length > 250) {
        alert("Description too long");
        return;
    }
    if(category.length > 25) {
        alert("Category must be less than 25 characters");
        return;
    }
    if(!category_arr.map(c => c.toLowerCase()).includes(category.toLowerCase())) {
        alert("Please enter a valid category");
        return;
    }

    const formData = new FormData();
    formData.append("sku", sku);
    formData.append("name", name);
    formData.append("description", description);
    formData.append("category", category);
    formData.append("quantity", quantity);
    formData.append("price", price);

    const response = await fetch("admin_add_item.php", {
        method: "POST",
        body: formData
    });

    const result = await response.text();
    alert(result); // Show success or error message
});
</script>
</body>
</html>
