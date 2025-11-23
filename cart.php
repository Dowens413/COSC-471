<?php
session_start();
include './resource/db.php';

// Check if user is logged in
$loggedIn = isset($_SESSION['username']);

// Make sure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Total quantity
$total_quantity = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="resource/index.css">
</head>
<body>

<!-- Top Header -->
<div class="top-header">
    <img id="logo" src="./resource/Logo.png" alt="Logo">
    <div class="header-title">Your Click, Your Cart!</div>
    <div class="header-actions">
        <img id="emptyCart" src="resource/emptyCart.png" alt="Empty Cart">
        <div id="login">Login</div>
        <div id="logout">Logout</div>
        <div id="signUp">Signup</div>
    </div>
</div>

<!-- Signup Modal -->
<div class="signup-modal" style="display:none;">
    <div class="modal-content">
        <div class="close" onclick="hideForm(1)">+</div>
        <h1>Sign up</h1>
        <form id="signupForm">
            <input name="username" type="text" placeholder="Username" required>
            <input name="password" type="password" placeholder="Password" required>
            <button class="submitBtn" type="submit">Submit</button>
            <div id="signupError" style="color:red; margin-top:10px;"></div>
        </form>
    </div>
</div>

<!-- Login Modal -->
<div class="login-modal" style="display:none;">
    <div class="modal-content">
        <div class="close" onclick="hideForm(0)">+</div>
        <h1>Login</h1>
        <form id="loginForm">
            <input name="username" type="text" placeholder="Username" required>
            <input name="password" type="password" placeholder="Password" required>
            <button class="submitBtn" type="submit">Submit</button>
            <div id="loginError" style="color:red; margin-top:10px;"></div>
        </form>
    </div>
</div>

<!-- Main Content -->
<main style="
  padding:20px; 
  background: linear-gradient(180deg, #808080 0%, #ccc 50%, #fff 100%);
  display: flex;
  justify-content: center;     /* Centers table horizontally */
  align-items: flex-start;      /* Change to 'center' to center vertically too */
  min-height: 80vh;             /* Gives height so centering works properly */
">

<?php if (count($_SESSION['cart']) === 0): ?>
    <div class="empty-cart-message">Your cart is empty.</div>
<?php else: ?>
    <?php
    $cart_total = 0;
    $stmt = $conn->prepare("SELECT name, price FROM items WHERE sku = ?");
    ?>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($_SESSION['cart'] as $sku => $quantity): ?>
            <?php
            $stmt->bind_param("s", $sku);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) continue;
            $item = $result->fetch_assoc();
            $item_name = htmlspecialchars($item['name']);
            $item_price = (float)$item['price'];
            $item_total = $item_price * $quantity;
            $cart_total += $item_total;
            ?>
            <tr id="row-<?php echo $sku; ?>">
                <td><?php echo $item_name; ?></td>
                <td><?php echo htmlspecialchars($sku); ?></td>
                <td>$<?php echo number_format($item_price, 2); ?></td>
                <td style="display:flex; gap:10px; align-items:center;">
                    <button class="qty-btn" data-sku="<?php echo $sku; ?>" data-action="decrease">-</button>
                    <span id="qty-<?php echo $sku; ?>"><?php echo $quantity; ?></span>
                    <button class="qty-btn" data-sku="<?php echo $sku; ?>" data-action="increase">+</button>
                </td>
                <td id="total-<?php echo $sku; ?>">
                    $<?php echo number_format($item_total, 2); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Grand Total</strong></td>
                <td>
                    <strong id="grand-total">
                        $<?php echo number_format($cart_total, 2); ?>
                        <?php
                        $_SESSION["total_amount"] = $cart_total;
                        $_SESSION["total_items"] = $total_quantity;
                        ?>
                    </strong>
                </td>
            </tr>
        </tfoot>
    </table>
    <div style="margin-top:20px;">
        <button id="checkout" style="padding:10px 20px; font-size:1.2em;">Checkout</button>
    </div>
<?php endif; ?>

</main>

<script>
// Check if user is logged in
const isLoggedIn = <?php echo $loggedIn ? 'true' : 'false'; ?>;

// Show/hide header buttons
function updateHeaderButtons() {
    if(isLoggedIn) {
        document.getElementById("logout").style.display = "flex";
        document.getElementById("login").style.display = "none";
        document.getElementById("signUp").style.display = "none";
    } else {
        document.getElementById("logout").style.display = "none";
        document.getElementById("login").style.display = "flex";
        document.getElementById("signUp").style.display = "flex";
    }
}

// Hide modal function
function hideForm(num) {
    if(num === 1) document.querySelector(".signup-modal").style.display = "none";
    else document.querySelector(".login-modal").style.display = "none";
}

// Initialize
document.addEventListener("DOMContentLoaded", () => {
    updateHeaderButtons();
    document.getElementById("logo").addEventListener("click", () => window.location.href="index.php");
    // Modals
    document.getElementById("signUp").addEventListener("click", e => {
        e.preventDefault();
        document.querySelector(".signup-modal").style.display = "flex";
    });
    document.getElementById("login").addEventListener("click", e => {
        e.preventDefault();
        document.querySelector(".login-modal").style.display = "flex";
    });
    document.querySelectorAll(".close").forEach(btn => btn.addEventListener("click", () => {
        hideForm(0); hideForm(1);
    }));

    // Logout
    document.getElementById("logout").addEventListener("click", e => {
        e.preventDefault();
        fetch("./routes/logout.php")
            .then(res => res.text())
            .then(data => {
                if(data.trim() === "logged_out") {
                    window.location.href = "/index.php";
                }
            });
    });

    // Checkout
    const checkoutBtn = document.getElementById("checkout");
if (checkoutBtn) {
    checkoutBtn.addEventListener("click", () => {
        if (!isLoggedIn) {
            alert("You must be logged in to checkout.");
            return;
        }
        window.location.href = "order.php";
    });
}


    // Quantity buttons
    document.querySelectorAll(".qty-btn").forEach(button => {
        button.addEventListener("click", function() {
            const sku = this.dataset.sku;
            const action = this.dataset.action;

            fetch("routes/update_quantity_ajax.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `sku=${sku}&action=${action}`
            })
            .then(res => res.json())
            .then(data => {
                if(!data.success) return;
                if(data.quantity === 0) document.getElementById("row-" + sku).remove();
                else document.getElementById("qty-" + sku).textContent = data.quantity;
                if(data.item_total !== undefined) document.getElementById("total-" + sku).textContent = "$" + data.item_total.toFixed(2);
                if(data.grand_total !== undefined) document.getElementById("grand-total").textContent = "$" + data.grand_total.toFixed(2);
            });
        });
    });

    // Logo click
    //document.getElementById("logo").addEventListener("click", () => window.location.href="index.php");

    // Signup form AJAX
    document.getElementById("signupForm").addEventListener("submit", e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        document.getElementById("signupError").innerText = "";

        fetch("routes/signUp.php", {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Signup successful! Please login.");
                hideForm(1);
            } else {
                document.getElementById("signupError").innerText = data.message;
            }
        })
        .catch(err => document.getElementById("signupError").innerText = "Error: " + err);
    });

    // Login form AJAX
    document.getElementById("loginForm").addEventListener("submit", e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        document.getElementById("loginError").innerText = "";

        fetch("routes/login.php", {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                hideForm(0);
                if(data.message.toLowerCase().includes("admin")) window.location.href = "admin.php";
                else alert("Login Successful");
                updateHeaderButtons();
            } else {
                document.getElementById("loginError").innerText = data.message;
            }
        })
        .catch(err => document.getElementById("loginError").innerText = "Error: " + err);
    });
});
</script>

</body>
</html>
