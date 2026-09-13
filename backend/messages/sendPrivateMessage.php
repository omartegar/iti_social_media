<?php

require(__DIR__ . '/../config/checkToken.php');
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {

    $sender_id =  $logged_in_user['id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    if (!isset($_POST['receiver_id']) || $receiver_id == null) {
        echo json_encode(['status' => 'failed', 'message' => "You should provide receiver_id of user you want to send the message to."]);
        exit;
    }

    if (!isset($message) || empty($message) || trim($message) === "") {
        echo json_encode(['status' => 'failed', 'message' => "You cannot send an empty message, please fill message field"]);
        exit;
    }

    require(__DIR__ . '/../config/conn.php');

    $stmtSaveMessage = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content)
    VALUES (:sender_id, :receiver_id, :content)");

    if ($stmtSaveMessage->execute([
        'sender_id' => $logged_in_user['id'],
        'receiver_id' => $receiver_id,
        'content' => $message
    ])) {
        echo json_encode(['status' => 'success', 'message' => "Message sent successfully"]);
        exit;
    } else {
        echo json_encode(['status' => 'failed', 'message' => "The message failed to send, try again later or contact the admin"]);
        exit;
    }
} catch (Exception $err) {
    echo json_encode(['status' => 'failed', 'message' => "An error occured while sending the message", 'error' => $err->getMessage()]);
    exit;
}
