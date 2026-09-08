<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $emailPattern = '/^[a-zA-Z0-9._%]{5,}+@+[a-zA-Z0-0]{3,}+\.[a-zA-Z]{3,}$/';
    $passwordPattern = '/^[a-zA-Z0-9._%]{8,}$/';

    if (!preg_match($emailPattern, $email)) {
        echo json_encode(['status' => "failed", 'message' => "This email is invalid"]);
        exit;
    }

    if (!preg_match($passwordPattern, $password)) {
        echo json_encode(['status' => 'failed', 'message' => "Password is too small, should be 8 min characters"]);
        exit;
    }

    echo json_encode([
        'email' => $email,
        'password' => $password
    ]);
    exit;
} catch (Exception $err) {
    echo json_encode(['status' => "failed", 'messge' => "Process to login has failed, try again later", 'error' => $err->getMessage()]);
    exit;
}
