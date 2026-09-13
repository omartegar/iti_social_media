<?php

require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {
    require(__DIR__ . '/../config/conn.php');
    $allUsers = $pdo->prepare("SELECT id, first_name, last_name, email, phone, profile_picture, is_admin, created_at
    FROM users ORDER BY first_name ASC");

    if ($allUsers->execute()) {
        echo json_encode(['status' => "success", 'message' => "all users found", 'myself' => $logged_in_user, 'users' => $allUsers->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    } else {
        echo json_encode(['status' => 'failed', 'message' => "Operation to return users failed, try again later."]);
        exit;
    }
} catch (Exception $err) {

    echo json_encode(['status' => "failed", 'message' => "We are sorry returning all users failed at the moment, please try again later"]);
    exit;
}
