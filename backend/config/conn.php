<?php

try {


    $pdo = new PDO('mysql:host=localhost;dbname=social_media;port=3306', 'omartegar', 'omarpassword');
} catch (Exception $err) {
    echo json_encode(['status' => 'failed', 'message' => "Connection failed to database", 'error' => $err->getMessage()]);
    exit;
}
