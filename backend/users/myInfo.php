<?php

require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {
    echo json_encode(['status' => "success", 'message' => "User info found", 'user_info' => $logged_in_user]);
    exit;
} catch (Exception $err) {
    echo json_encode(['status' => "failed", 'message' => "An error occurred from the server we are so sorry, try again later", 'error' => $err->getMessage()]);
    exit;
}
