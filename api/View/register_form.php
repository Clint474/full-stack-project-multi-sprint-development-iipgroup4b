<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register</title>
    <link rel="stylesheet" href="../includes/style.css">

</head>
<body>


<?php include __DIR__ . "/../includes/nav.php"; ?>







<div class="container">
<form method="POST" action="../register.php">

   <input name="FirstName" placeholder="First name" required><br>
   <input name="LastName" placeholder="Last name" required><br>
   <input name="email" type="email" placeholder="Email" required><br>
   <input name="mobile" placeholder="Mobile" required><br>
   <input name="PassWord" type="password" placeholder="Password" required><br>

  <button type="submit">Register</button>
</form>
    </div>
</body>
</html>