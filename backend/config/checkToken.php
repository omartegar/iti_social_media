<?php

require(__DIR__ . '/cors.php');


try {
    $token = null;

    $headers = getallheaders();
    if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
        $token = $_SERVER['HTTP_AUTHORIZATION'];
    } else if (isset($headers['Authorization'])) {
        $token = $headers['Authorization'];
    } else {
        echo json_encode(['status' => "failed", 'message' => "Token missing"]);
        exit;
    }


    // Check for it inside Database
    require(__DIR__ . '/conn.php');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE token = :token");
    if (!$stmt->execute(['token' => $token])) {
        echo json_encode(['status' => "failed", 'message' => "An error occured while checking your identity."]);
        exit;
    }

    $logged_in_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$logged_in_user) {
        echo json_encode(['status' => 'failed', 'message' => "Unauthenticated or expired token."]);
        exit;
    }


    // 
    // $logged_in_user ASSOC_ARRAY is ready for any other script
    // 


} catch (Exception $err) {
    echo json_encode(['status' => "failed", 'message' => "Error occured while checking for identity.", 'error' => $err->getMessage()]);
    exit;
}
