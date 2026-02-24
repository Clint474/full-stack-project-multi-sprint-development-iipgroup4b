<?php
require_once __DIR__ . "/../config/db.php";

$hash = password_hash("Test@1234", PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE cw_user SET PassWord = ? WHERE UserNr = 10");
$stmt->execute([$hash]);

echo "Password reset OK";
