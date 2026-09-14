<?php

header('Content-Type: application/json; charset=utf-8');
require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "DELETE") {
    http_response_code(405);
    echo json_encode(['status' => "failed", 'message' => "Invalid request method"]);
    exit;
}

try {
    $rawData = json_decode(file_get_contents("php://input"), true);

    if (!is_array($rawData) || !isset($rawData['id']) || filter_var($rawData['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
        http_response_code(400);
        echo json_encode(['status' => 'failed', 'message' => "A valid post id is required"]);
        exit;
    }

    $postId = (int) $rawData['id'];
    require(__DIR__ . '/../config/conn.php');
    $findStmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
    if (!$findStmt->execute(['id' => $postId])) {
        echo json_encode(['status' => "failed", 'message' => "Failed to find this post id, or it maybe removed"]);
        exit;
    }

    $postRow = $findStmt->fetch(PDO::FETCH_ASSOC);
    if (!$postRow) {
        http_response_code(404);
        echo json_encode(['status' => "failed", 'message' => "This post is unavailable"]);
        exit;
    }

    if ((int) $postRow['user_id'] !== (int) $logged_in_user['id']) {
        http_response_code(403);
        echo json_encode(['status' => "failed", 'message' => "You can not delete this post, you are not the owner."]);
        exit;
    }

    $deleteStmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    if ($deleteStmt->execute(['id' => $postId])) {
        if (!empty($postRow['image'])) {
            $imagePath = __DIR__ . '/../assets/images/' . basename($postRow['image']);
            if (is_file($imagePath) && !unlink($imagePath)) {
                error_log('Failed to remove post image: ' . $imagePath);
            }
        }
        echo json_encode(['status' => 'success', 'message' => "Post deleted successfully"]);
        exit;
    } else {
        echo json_encode(['status' => 'failed', 'message' => "Failed to delete this post, try again later."]);
        exit;
    }
} catch (Throwable $err) {
    error_log('Delete post failed: ' . $err->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'failed', 'message' => "Process to delete the post has been declined, try again later."]);
    exit;
}
