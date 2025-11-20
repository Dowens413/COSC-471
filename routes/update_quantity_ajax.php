<?php
session_start();
include '../resource/db.php';

header("Content-Type: application/json");

$sku = $_POST['sku'] ?? null;
$action = $_POST['action'] ?? null;

if (!$sku || !$action) {
    echo json_encode(["success" => false]);
    exit;
}

// Ensure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Increase / decrease
if (isset($_SESSION['cart'][$sku])) {

    if ($action == "increase") {
        $_SESSION['cart'][$sku]++;
    }

    if ($action == "decrease") {
        $_SESSION['cart'][$sku]--;

        if ($_SESSION['cart'][$sku] <= 0) {
            unset($_SESSION['cart'][$sku]);

            echo json_encode([
                "success" => true,
                "quantity" => 0,
                "grand_total" => calcTotal(),
            ]);
            exit;
        }
    }

    // Recalculate totals
    echo json_encode([
        "success" => true,
        "quantity" => $_SESSION['cart'][$sku],
        "item_total" => calcItemTotal($sku),
        "grand_total" => calcTotal()
    ]);
    exit;
}

echo json_encode(["success" => false]);


// --------------------------
// Helper functions
// --------------------------

function calcItemTotal($sku) {
    global $conn;
    $stmt = $conn->prepare("SELECT price FROM items WHERE sku=?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['price'] * $_SESSION['cart'][$sku];
}

function calcTotal() {
    global $conn;
    $total = 0;

    foreach ($_SESSION['cart'] as $sku => $qty) {
        $stmt = $conn->prepare("SELECT price FROM items WHERE sku=?");
        $stmt->bind_param("s", $sku);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total += $row['price'] * $qty;
    }

    return $total;
}
?>
