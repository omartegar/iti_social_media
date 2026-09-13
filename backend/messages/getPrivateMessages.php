<?php
require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {
    $sender = $logged_in_user['id'];
    $receiver = $_GET['id'];

    require(__DIR__ . '/../config/conn.php');
    $stmtAllMessages = $pdo->prepare("SELECT * FROM messages 
    WHERE sender_id = :sender_id 
    AND receiver_id = :receiver_id
    OR receiver_id = :sender_id
    AND sender_id = :receiver_id
    ORDER BY id DESC");

    $receiver_data = $pdo->prepare("SELECT id, first_name, last_name, email, created_at
    FROM users WHERE id = :id");
    if (!$receiver_data->execute(['id' => $receiver])) {
        echo json_encode(['status' => 'failed', 'message' => "This receiver is not available"]);
        exit;
    }

    if ($stmtAllMessages->execute([
        'sender_id' => $logged_in_user['id'],
        'receiver_id' => $receiver
    ])) {
        echo json_encode([
            'status' => "success",
            'message' => "Private chat returned successfully",
            'sender_me' => $logged_in_user,
            'receiver_data' => $receiver_data->fetch(PDO::FETCH_ASSOC),
            'messages' => $stmtAllMessages->fetchAll(PDO::FETCH_ASSOC)
        ]);
    } else {
        echo json_encode(['status' => "failed", 'message' => "Failed to return private messages between both friends"]);
        exit;
    }
} catch (Exception $err) {
    echo json_encode(['status' => 'failed', 'message' => "An error occurred while returning messages", 'error' => $err->getMessage()]);
    exit;
}
