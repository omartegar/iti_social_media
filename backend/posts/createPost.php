<?php

require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}





try {
    // Needs text or image , one or both at least
    $text = $_POST['text'];
    $image = $_FILES['image'];

    if (!isset($text) && !isset($image)) {
        echo json_encode(['status' => "failed", 'message' => "You should add at least text or image to create a post"]);
        exit;
    }


    if (isset($text) && strlen($text) > 5 && isset($image) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // both
    }

    // text only
    if (isset($text) && $_FILES['image'] == null) {
        if (strlen($text) < 5) {
            echo json_encode(['status' => "failed", 'message' => "Post text is too short, it should be 5 min chars."]);
            exit;
        } else if (strlen(($text)) > 255) {
            echo json_encode(['status' => 'failed', 'message' => "Post text is too long, it should be 255 max chars"]);
            exit;
        }
    }

    // image only
    if (!isset($text) || strlen($text) == 0 && $_FILES['image'] !== null) {
        // 
    }
} catch (Exception $err) {
    echo json_encode(['status' => 'failed', 'message' => "An error occured while creating this post, please try again later"]);
    exit;
}
