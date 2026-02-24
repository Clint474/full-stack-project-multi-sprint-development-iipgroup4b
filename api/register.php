<?php
header("Content-Type: application/json");

require_once __DIR__ . "/config/db.php";

//ensures correct http method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$contentType = $_SERVER["CONTENT_TYPE"] ?? "";

if (stripos($contentType, "application/json") !== false) {
    $data = json_decode(file_get_contents("php://input"), true);
} else {
    $data = $_POST; // HTML form submit
}

if (!is_array($data) || empty($data)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit;
}


$FirstName = trim($data["FirstName"] ?? "");
$LastName  = trim($data["LastName"] ?? "");
$email     = trim($data["email"] ?? "");
$mobile    = trim($data["mobile"] ?? "");
$PassWord  = $data["PassWord"] ?? "";

$userID      = $email;
$userEnabled = 1;
$userTypeNr  = 99;



if ($FirstName === "" || $LastName === "" || $email === "" || $PassWord === "") {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

//email format validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email"]);
    exit;
}

if ($mobile !== "" && !preg_match('/^[0-9+\s\-]{7,20}$/', $mobile)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid mobile number, must be at least 7 characters"]);
    exit;
}



//duplicate email check
$check = $pdo->prepare("SELECT UserNr FROM cw_user WHERE email = ?");
$check->execute([$email]);

if ($check->fetch()) {
    http_response_code(409);
    echo json_encode(["error" => "Email already registered"]);
    exit;
}

//password strength validation
if (strlen($PassWord) < 8) {
    http_response_code(400);
    echo json_encode(["error" => "Password must be at least 8 characters"]);
    exit;
}

$hash = password_hash($PassWord, PASSWORD_BCRYPT);


$sql = "INSERT INTO cw_user
        (FirstName, LastName, PassWord, email, mobile, userID, userEnabled, userTypeNr)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $FirstName,
    $LastName,
    $hash,
    $email,
    $mobile,
    $userID,
    $userEnabled,
    $userTypeNr
]);

http_response_code(201);
echo json_encode([
    "status" => "registered",
    "userNr" => $pdo->lastInsertId()
]);