<?php

require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "DELETE") {
    echo json_encode(['status' => "failed", 'message' => "Invalid request method"]);
    exit;
}

try {
    $rawData = json_decode(file_get_contents("php://input"), true);

    if (!isset($rawData) || !isset($rawData['id'])) {
        echo json_encode(['status' => 'failed', 'message' => "Please specify post id to delete"]);
        exit;
    }

    require(__DIR__ . '/../config/conn.php');
    $findStmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
    if (!$findStmt->execute(['id' => $rawData['id']])) {
        echo json_encode(['status' => "failed", 'message' => "Failed to find this post id, or it maybe removed"]);
        exit;
    }

    if ($findStmt->rowCount() == 0) {
        echo json_encode(['status' => "failed", 'message' => "This post is unavailable"]);
        exit;
    }

    $postRow = $findStmt->fetch(PDO::FETCH_ASSOC);
    if ($postRow['user_id'] !== $logged_in_user['id']) {
        echo json_encode(['status' => "failed", 'message' => "You can not delete this post, you are not the owner."]);
        exit;
    }

    $deleteStmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    if ($deleteStmt->execute(['id' => $logged_in_user['id']])) {
        echo json_encode(['status' => 'success', 'message' => "Post deleted successfully"]);
        exit;
    } else {
        echo json_encode(['status' => 'failed', 'message' => "Failed to delete this post, try again later."]);
        exit;
    }
} catch (Exception $err) {
    echo json_encode(['status' => 'failed', 'message' => "Process to delete the post has been declined, try again later."]);
    exit;
}
