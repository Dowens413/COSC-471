<?php
// Generate a random confirmation code
// Example: A7F-92C-4B1
$code = strtoupper(bin2hex(random_bytes(10)));  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
     <link rel="stylesheet" type="text/css" href="resource/index.css">
</head>
</head>
<body>
      <!-- Top Header -->
  <div class="top-header">
    <img id="logo" src="resource/Logo.png" alt="Logo">
    <div class="header-title">Your Click, Your Cart!</div>
    <div class="header-actions">
      <img id="emptyCart" src="resource/emptyCart.png" alt="Logo">
      <div id="logout">Logout</div>
    </div>
  </div>
    <div id = "confirmation">
    
    <h1>Order Confirmed!</h1>

    <p>Your confirmation code is:</p>

    <h2 style="color: green; letter-spacing: 3px;">
        <?php echo $code; ?>
    </h2>

    <p>Please save this code for your records.</p>
</div>
</body>

<script>
     document.getElementById("logout").addEventListener("click", function (e) {
    e.preventDefault();

    fetch("routes/logout.php")
        .then(res => res.text())
        .then(data => {
            if (data === "logged_out") {
                window.location.href = "login.php";
            }
        });
});
 document.getElementById("emptyCart").addEventListener("click", function (e) {
  window.location.href = "cart.php"; 


  });
</script>
</html>
