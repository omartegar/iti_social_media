<?php

require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {
    echo json_encode(['status' => 'success', 'message' => "getHomeData works"]);
    exit;
} catch (Exception $err) {
    echo json_encode(['status' => "failed", 'message' => "An error occured while returning home data, please try again later"]);
    exit;
}
