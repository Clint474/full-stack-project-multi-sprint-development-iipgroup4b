<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../includes/style.css">

</head>
<body>
  

<?php include __DIR__ . "/../includes/nav.php"; ?>








<h2>Login</h2>
<div class="container">
<form method="POST" action="../auth/Login.php">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

<p>
    <a href="../auth/me.php">Check session (me.php)</a> |
    <a href="../auth/logout.php">Logout</a>
</p>
    </div>
</body>

</html>