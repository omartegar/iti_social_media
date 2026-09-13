<?php

require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {
    require(__DIR__ . '/../config/conn.php');

    $friendsStmt = $pdo->prepare("SELECT id, first_name, last_name, email FROM users");
    $postStmt = $pdo->prepare("SELECT id, user_id, content, image, created_at FROM posts ORDER BY id DESC");

    if (!$friendsStmt->execute()) {
        echo json_encode(['status' => "failed", 'message' => "An error has occurred while returning friends list"]);
        exit;
    }

    if (!$postStmt->execute()) {
        echo json_encode(['status' => 'failed', 'message' => "An error has occured while returning posts list"]);
        exit;
    }

    $friendsList = $friendsStmt->fetchAll(PDO::FETCH_ASSOC);
    $postsList = $postStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'message' => "data loaded successfully", 'my_id' => $logged_in_user['id'], 'friends' => $friendsList, 'posts' => $postsList]);
    exit;
} catch (Exception $err) {
    echo json_encode(['status' => "failed", 'message' => "An error occured while returning home data, please try again later"]);
    exit;
}
