<?php
// 1. Start the session
session_start();

// 2. Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// 3. Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    
    // Sanitize and validate inputs
    $sku = filter_input(INPUT_POST, 'sku', FILTER_SANITIZE_STRING);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $redirect_url = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL);

    // Basic validationn check
    if ($sku && $quantity !== false && $quantity > 0) {
        
        // Check if the item is already in the cart
        if (isset($_SESSION['cart'][$sku])) {
            // Item exists: Increment the existing quantity
            $_SESSION['cart'][$sku] += $quantity;
        } else {
            // New item: Add the SKU and quantity to the cart
            $_SESSION['cart'][$sku] = $quantity;
        }
        
        // Optional: Set a success message
        $_SESSION['message'] = "Added " . $quantity . " unit(s) of " . htmlspecialchars($sku) . " to your cart.";
        
    } else {
        // Optional: Set an error message if input is invalid
        $_SESSION['error'] = "Invalid item or quantity submitted.";
    }

    // 4. Redirect the user back to the page they came from (or the cart page)
    if ($redirect_url) {
        header("Location: " . $redirect_url);
        exit();
    } else {
        // Fallback redirection if the URL wasn't passed
        header("Location: ../cart.php"); // Adjust to your main cart page
        exit();
    }
} else {
    // If accessed directly without a POST request
    header("Location: ../index.php"); // Redirect to home or products page
    exit();
}
?>