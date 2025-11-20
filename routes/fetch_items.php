<?php
// 1. Start the session to use the cart functionality
session_start();

// 2. Include the database connection
include '../resource/db.php';

// 3. Define the emoji map
$category_emojis = array(
    "Electronics" => "📱",
    "Movies" => "🎬",
    "Books" => "📚",
    "Toys" => "🧸",
    "Clothing" => "👕",
    "Home" => "🏠",
    "Beauty" => "💄",
    "Automotive" => "🚗",
    "Sports" => "⚽"
);

// 4. Get the category from the POST request (e.g., from a form submission)
$category = $_POST['category'] ?? '';

// Get the specific emoji for the selected category, or an empty string if not found
$emoji = $category_emojis[$category] ?? '';

// 5. Database Query using Prepared Statements
$stmt = $conn->prepare("SELECT sku, name, description, quantity, price FROM items WHERE category = ?");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

// 6. Display Output
echo "<h2>Items in " . htmlspecialchars($category) . "</h2>";

echo "<table border='1' cellpadding='10' cellspacing='0'>";
echo "<thead>
        <tr>
            <th>" . htmlspecialchars($emoji) . "</th> 
            <th>SKU</th>
            <th>Name</th>
            <th>Description</th>
            <th>Stock</th> <th>Price</th>
            <th>Add to Cart</th>
        </tr>
    </thead>";

echo "<tbody>";

if ($result->num_rows === 0) {
    // colspan is 7 to match the 7 columns in the header
    echo "<tr><td colspan='7'>No items found in this category.</td></tr>";
} else {
    while ($row = $result->fetch_assoc()) {
        $item_sku = htmlspecialchars($row['sku']);
        $stock_quantity = intval($row['quantity']); // Get the current stock level
        
     echo "
<tr>
    <td></td> 
    <td>$item_sku</td>
    <td>" . htmlspecialchars($row['name']) . "</td>
    <td>" . htmlspecialchars($row['description']) . "</td>
    <td>$stock_quantity</td>
    <td>$" . number_format($row['price'], 2) . "</td>

    <td>
        <div class='cart-row' style='display:flex; align-items:center; gap:5px;'>
            
            <input 
                type='number'
                class='qty-input'
                data-sku='$item_sku'
                value='1'
                min='1'
                max='$stock_quantity'
                style='width:50px; text-align:center;'
            >

            <button 
                type='button'
                class='add-btn'
                data-sku='$item_sku'
                data-stock='$stock_quantity'
                " . ($stock_quantity > 0 ? "" : "disabled") . "
            >➕ Add</button>
        </div>
    </td>
</tr>";

    }
}

echo "</tbody>";
echo "</table>";

// 7. Clean up database resources
$stmt->close();
// If $conn is not closed in db.php, close it here:
// $conn->close();

?>
