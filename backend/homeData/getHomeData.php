<?php

require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {
    require(__DIR__ . '/../config/conn.php');

    $friendsStmt = $pdo->prepare("SELECT id, first_name, last_name, email, profile_picture FROM users");
    $postStmt = $pdo->prepare("SELECT id, user_id, content, image, created_at,
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes,
        EXISTS (SELECT 1 FROM likes AS user_likes
            WHERE user_likes.post_id = posts.id
            AND user_likes.user_id = :current_user_id) AS you_liked
        FROM posts ORDER BY id DESC");

    if (!$friendsStmt->execute()) {
        echo json_encode(['status' => "failed", 'message' => "An error has occurred while returning friends list"]);
        exit;
    }

    if (!$postStmt->execute(['current_user_id' => $logged_in_user['id']])) {
        echo json_encode(['status' => 'failed', 'message' => "An error has occured while returning posts list"]);
        exit;
    }

    $friendsList = $friendsStmt->fetchAll(PDO::FETCH_ASSOC);
    $postsList = $postStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($postsList as &$post) {
        $post['likes'] = (int) $post['likes'];
        $post['you_liked'] = (bool) $post['you_liked'];
    }
    unset($post);

    echo json_encode(['status' => 'success', 'message' => "data loaded successfully", 'my_id' => $logged_in_user['id'], 'friends' => $friendsList, 'posts' => $postsList]);
    exit;
} catch (Exception $err) {
    echo json_encode(['status' => "failed", 'message' => "An error occured while returning home data, please try again later"]);
    exit;
}
