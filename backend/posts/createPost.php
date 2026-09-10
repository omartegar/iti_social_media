<?php

require(__DIR__ . '/../config/checkToken.php');


if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {
    // Needs text or image , one or both at least


} catch (Exception $err) {
    echo json_encode(['status' => 'failed', 'message' => "An error occured while creating this post, please try again later"]);
    exit;
}
