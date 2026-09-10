<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $emailPattern = '/^[a-zA-Z0-9._%]{4,}+@+[a-zA-Z0-0]{3,}+\.[a-zA-Z]{3,}$/';
    $passwordPattern = '/^[a-zA-Z0-9._%]{8,}$/';
    $randomToken = null;

    if (!preg_match($emailPattern, $email)) {
        echo json_encode(['status' => "failed", 'message' => "This email is invalid"]);
        exit;
    }

    if (!preg_match($passwordPattern, $password)) {
        echo json_encode(['status' => 'failed', 'message' => "Password is too small, should be 8 min characters"]);
        exit;
    }

    require(__DIR__ . '/../config/conn.php');

    $stmt = $pdo->prepare("
        SELECT * FROM users WHERE email = :email
    ");

    if ($stmt->execute(['email' => $email])) {
        $rows = $stmt->rowCount();
        $randomToken = bin2hex(random_bytes(10));

        if ($rows > 0) {
            // Email found
            $userObject = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!password_verify($password, $userObject['password'])) {
                echo json_encode(['status' => 'failed', 'message' => "Invalid password"]);
                exit;
            }

            $updateStmt = $pdo->prepare("UPDATE users SET token = :token WHERE id = :id");
            if (!$updateStmt->execute([
                'token' => $randomToken,
                'id' => $userObject['id']
            ])) {
                echo json_encode(['status' => 'failed', 'message' => "Failed to generate a token key, try again later"]);
                exit;
            }

            echo json_encode(['status' => 'success', 'message' => "Logged in successfully", 'token' => $randomToken]);
            exit;
        } else {
            echo json_encode(['status' => 'failed', 'message' => "Invalid email or password"]);
            exit;
        }
    } else {
        echo json_encode(['status' => 'failed', 'message' => "An error occured while trying to login, please try again"]);
        exit;
    }
} catch (Exception $err) {
    echo json_encode(['status' => "failed", 'messge' => "Process to login has failed, try again later", 'error' => $err->getMessage()]);
    exit;
}
