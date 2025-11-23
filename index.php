<?php
session_start();

$loggedIn = isset($_SESSION['username']) ? 'true' : 'false';

// Make sure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Click-N-Cart</title>
  <link rel="stylesheet" type="text/css" href="resource/index.css">
</head>

<body>

  <!-- Top Header -->
  <div class="top-header">
    <img id="logo" src="resource/Logo.png" alt="Logo">
    <div class="header-title">Your Click, Your Cart!</div>
    <div class="header-actions">
      <img id="emptyCart" src="resource/emptyCart.png" alt="Logo">
      <div id="login">Login</div>
      <div id="logout" style="display:none;">Logout</div>
      <div id="signUp">Signup</div>
    </div>
  </div>

  <!-- Category Bar -->
  <nav class="category-bar">
    <div id="barContent">
      <button class="category-link" data-category="Electronics">Electronics</button>
      <button class="category-link" data-category="Movies">Movies</button>
      <button class="category-link" data-category="Books">Books</button>
      <button class="category-link" data-category="Toys">Toys</button>
      <button class="category-link" data-category="Clothing">Clothing</button>
      <button class="category-link" data-category="Home">Home</button>
      <button class="category-link" data-category="Beauty">Beauty</button>
      <button class="category-link" data-category="Automotive">Automotive</button>
      <button class="category-link" data-category="Sports">Sports</button>
    </div>
  </nav>

  <!-- Main Content -->
  <main style="
  padding:20px; 
  background: linear-gradient(180deg, #808080 0%, #ccc 50%, #fff 100%);
  display: flex;
  justify-content: center;     /* Centers table horizontally */
  align-items: flex-start;      /* Change to 'center' to center vertically too */
  min-height: 80vh;             /* Gives height so centering works properly */
">
  <div id="itemsContainer" style="width: fit-content;"></div>
</main>

  <!-- Signup Modal -->
  <div class="signup-modal" style="display:none;">
    <div class="modal-content">
      <div class="close" onclick="hideForm(1)">+</div>
      <h1 id="titlePopup">Sign up</h1>
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
      <h1 id="titlePopup">Login</h1>
      <form id="loginForm">
        <input name="username" type="text" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button class="submitBtn" type="submit">Submit</button>
        <div id="loginError" style="color:red; margin-top:10px;"></div>
      </form>
    </div>
  </div>

  <!-- JS -->
  <script>
    const isLoggedIn = <?php echo $loggedIn; ?>;

    // Show or hide login/signup/logout based on session
    function hideForms() {
      if (isLoggedIn) {
        document.getElementById("login").style.display = "none";
        document.getElementById("signUp").style.display = "none";
      }
    }

    function hideLogout() {
      if (isLoggedIn) {
        document.getElementById("logout").style.display = "flex";
      }
    }

    hideForms();
    hideLogout();


    // Show/Hide Modals
    function hideForm(num) {
      if (num === 1) document.querySelector(".signup-modal").style.display = "none";
      else document.querySelector(".login-modal").style.display = "none";
    }

    document.getElementById("signUp").addEventListener("click", function(e) {
      e.preventDefault();
      document.querySelector(".signup-modal").style.display = "flex";
    });

    document.getElementById("login").addEventListener("click", function(e) {
      e.preventDefault();
      document.querySelector(".login-modal").style.display = "flex";
    });


    // AJAX Handlers
    document.addEventListener("DOMContentLoaded", function() {

      // Signup Form
      document.getElementById("signupForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("routes/signUp.php", {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest" }
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert("Signup successful! Please login.");
              hideForm(1);
            } else {
              document.getElementById("signupError").innerText = data.message;
            }
          });
      });

      // Login Form
      document.getElementById("loginForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("routes/login.php", {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest" }
          })
          .then(res => res.json())
            .then(data => {
            if (data.success) {
              hideForm(0);
              alert("Login Successful!");
              if(data.message==="Welcome, admin!")
              {
                 window.location.href = "admin.php"; //if the user is an admin redirect to the admin page .
              }
              else
                 window.location.reload();   // reload the page to update the page to now logged in 
            } else {
              document.getElementById("loginError").innerText = data.message;
            }
          });
      });

      // Category fetch
      document.querySelectorAll(".category-link").forEach(button => {
        button.addEventListener("click", function() {
          const category = this.getAttribute("data-category");

          fetch("routes/fetch_items.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "X-Requested-With": "XMLHttpRequest"
              },
              body: "category=" + encodeURIComponent(category)
            })
            .then(res => res.text())
            .then(html => {
              document.getElementById("itemsContainer").innerHTML = html;
            })
            .catch(err => {
              document.getElementById("itemsContainer").innerHTML = "<p>Error loading items.</p>";
            });
        });
      });

    });

    // Add to cart
  // Add to cart
  document.getElementById("itemsContainer").addEventListener("click", async function(e) {
  if (!e.target.classList.contains("add-btn")) return;

  const button = e.target;
  const sku = button.dataset.sku;
  const quantity = button.parentElement.querySelector(".qty-input").value;

  const formData = new FormData();
  formData.append("sku", sku);
  formData.append("quantity", quantity);
  formData.append("add", "1"); // IMPORTANT FIX

  const response = await fetch("/routes/add_to_cart.php", {
    method: "POST",
    body: formData
  });

  alert("Item added to cart!");
});


    // Cart click
    document.getElementById("emptyCart").addEventListener("click", function() {
      window.location.href = "cart.php";
    });

    // Logout
    document.getElementById("logout").addEventListener("click", function(e) {
      e.preventDefault();

      fetch("routes/logout.php")
        .then(res => res.text())
        .then(data => {
          if (data === "logged_out") {
            window.location.href = "login.php";
          }
        });
    });

    // Logo click → home
    document.getElementById("logo").addEventListener("click", function() {
      window.location.href = "index.php";
    });
  </script>

</body>

</html>
