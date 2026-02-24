<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

try {
    $sql = "SELECT 
                UserNr,
                FirstName,
                LastName,
                email,
                mobile,
                userID,
                userEnabled,
                userTypeNr,
                idcounty
            FROM cw_user
            ORDER BY UserNr DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "users" => $users
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error"]);
}
