<?php

header('Content-Type: application/json; charset=utf-8');
require(__DIR__ . '/../config/checkToken.php');

try {
    if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'], true)) {
        http_response_code(405);
        echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
        exit;
    }

    $rawData = json_decode(file_get_contents('php://input'), true);
    if (!is_array($rawData) || !isset($rawData['post_id']) || filter_var($rawData['post_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
        http_response_code(400);
        echo json_encode(['status' => 'failed', 'message' => "A valid post_id is required"]);
        exit;
    }

    $postId = (int) $rawData['post_id'];
    require(__DIR__ . '/../config/conn.php');

    $postExists = $pdo->prepare('SELECT id FROM posts WHERE id = :post_id');
    if (!$postExists->execute(['post_id' => $postId])) {
        throw new RuntimeException('Failed to check whether the post exists');
    }
    if (!$postExists->fetchColumn()) {
        http_response_code(404);
        echo json_encode(['status' => 'failed', 'message' => "Post not found"]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $existingLike = $pdo->prepare('SELECT id FROM likes WHERE user_id = :user_id AND post_id = :post_id');
        if (!$existingLike->execute(['user_id' => $logged_in_user['id'], 'post_id' => $postId])) {
            throw new RuntimeException('Failed to check the existing like');
        }

        if ($existingLike->fetchColumn()) {
            echo json_encode(['status' => 'success', 'message' => "Post is already liked"]);
            exit;
        }

        $stmtAddLike = $pdo->prepare('INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)');
        if (!$stmtAddLike->execute(['user_id' => $logged_in_user['id'], 'post_id' => $postId])) {
            throw new RuntimeException('Failed to add the like');
        }
        echo json_encode(['status' => 'success', 'message' => "Post liked successfully"]);
        exit;
    }

    $stmtRemoveLike = $pdo->prepare('DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id');
    if (!$stmtRemoveLike->execute(['user_id' => $logged_in_user['id'], 'post_id' => $postId])) {
        throw new RuntimeException('Failed to remove the like');
    }
    echo json_encode([
        'status' => 'success',
        'message' => $stmtRemoveLike->rowCount() > 0 ? "Post unliked successfully" : "Post was not liked"
    ]);
    exit;
} catch (Throwable $err) {
    error_log('Like request failed: ' . $err->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'failed', 'message' => "Unable to process the like request"]);
    exit;
}
