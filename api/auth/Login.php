<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");


// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed. Use POST."]);
    exit;
}

// DB connection
require_once __DIR__ . "/../config/db.php";


// reading JSON + HTML input
$data = $_POST ?: (json_decode(file_get_contents("php://input"), true) ?: []);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';


// checking if field are filled
if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(["error" => "Email and Password must be filled"]);
    exit;
}

try {
    //SQL query - prevents SQL injection
    $stmt = $pdo->prepare(
        "SELECT UserNr, FirstName, LastName, email, userID, userEnabled, userTypeNr, idcounty, PassWord
        FROM cw_user
        WHERE email = :email
        LIMIT 1"
    );
    $stmt->execute([':email' => $email]);

    // fetching user
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    // password check
  // password check (CORRECT)
    if (!$user || !password_verify($password, $user['PassWord'])) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password"]);
    exit;
    }



    // rejecting disabled accounts
    if((int)$user['userEnabled'] !== 1) {
        http_response_code(403);
        echo json_encode(["error" => "User account is disabled"]);
        exit;
    }

    // setting session after login
    $_SESSION['user'] = [
        'UserNr' => $user['UserNr'],
        'FirstName' => $user['FirstName'],
        'LastName' => $user['LastName'],
        'email' => $user['email'],
        'userID' => $user['userID'],
        'userTypeNr' => $user['userTypeNr'],
        'idcounty' => $user['idcounty'],
    ];

    // unloading the password
    unset($user['PassWord']);


    // success message
    http_response_code(200);
    echo json_encode([
        "message" => "Login successful",
        "user" => $user
    ]);



} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error"]);
}