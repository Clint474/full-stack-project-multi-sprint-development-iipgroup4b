<?php

    header("Content-Type: application/json");

    require_once __DIR__ . "/../config/db.php";


    $data = json_decode(file_get_contents("php://input"), true);

    $userNr  = $data["userNr"] ?? null;
    $oldPassword = $data["oldPassword"] ?? null;
    $newPassword = $data["newPassword"] ?? null;

    if(!$userNr || !$oldPassword || !$newPassword)
    {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $stmt = $pdo->prepare("
    SELECT PassWord
    FROM cw_user
    WHERE UserNr = ? AND userEnabled = 1
    ");

    $stmt->execute([$userNr]);
    $user = $stmt->fetch();

    if(!$user)
    {
        http_response_code(404);
        echo json_encode(["error" => "User Not Found"]);
        exit;
    }

    if(!password_verify($oldPassword, $user["PassWord"]))    
    {
        http_response_code(401);
        echo json_encode(["error" => "Your Password is incorrect"]);
        exit;
    }

    if(strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[^a-zA-Z0-9]/', $newPassword))
    {
        http_response_code(400);
        echo json_encode(["error" => "New Password must have over 8 Characters and contain atleast one capital and special character."]);
        exit;
    }
    if (password_verify($newPassword, $user["PassWord"])) 
    {
    http_response_code(400);
    echo json_encode(["error" => "New password must be different from old password"]);
    exit;
    }



    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
   
    $update = $pdo->prepare("
    UPDATE cw_user
    SET PassWord  = ?
    WHERE UserNr = ?
    ");

    $update->execute([$hash, $userNr]);

    http_response_code(200);
    echo json_encode([
        "success" => "Password update successfully"
    ]);

    if ($update->rowCount() === 0) {
    http_response_code(500);
    echo json_encode(["error" => "Password update failed"]);
    exit;
    }

exit;


