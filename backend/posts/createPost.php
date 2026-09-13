<?php

header('Content-Type: application/json; charset=utf-8');

function ensureUploadDirectory(string $directory): void
{
    if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Unable to create image upload directory: ' . $directory);
    }

    if (!is_writable($directory)) {
        throw new RuntimeException('Image upload directory is not writable: ' . $directory);
    }
}

require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}

try {
    $text = trim($_POST['text'] ?? '');
    $image = $_FILES['image'] ?? null;

    if ($text === '' && $image === null) {
        echo json_encode(['status' => 'failed', 'message' => "You should add at least text or image to create a post"]);
        exit;
    }

    if ($image !== null && (!isset($image['error'], $image['tmp_name'], $image['name'], $image['size']) || !is_uploaded_file($image['tmp_name']))) {
        echo json_encode(['status' => 'failed', 'message' => "Invalid image upload, please try again"]);
        exit;
    }

    if ($text !== '' && $image === null) {
        if (strlen($text) < 5) {
            echo json_encode(['status' => 'failed', 'message' => "Post text is too short, it should be 5 min chars."]);
            exit;
        }
        if (strlen($text) > 255) {
            echo json_encode(['status' => 'failed', 'message' => "Post text is too long, it should be 255 max chars"]);
            exit;
        }

        require(__DIR__ . '/../config/conn.php');
        $saveQuery = $pdo->prepare("INSERT INTO posts (user_id, content, image) VALUES (:user_id, :content, :image)");
        if ($saveQuery->execute(['user_id' => $logged_in_user['id'], 'content' => $text, 'image' => null])) {
            echo json_encode(['status' => 'success', 'message' => "Post created successfully, text only added"]);
        } else {
            echo json_encode(['status' => 'failed', 'message' => "Post failed to create with text only now, try again later"]);
        }
        exit;
    }

    if ($image !== null) {
        if ($image['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'failed', 'message' => "Image upload failed, please try again or choose another image"]);
            exit;
        }

        if ($image['size'] > 5 * 1024 * 1024) {
            echo json_encode(['status' => 'failed', 'message' => "This image size is too big, it should be less than 5MB"]);
            exit;
        }

        $allowedExt = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
        $imageExt = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($imageExt, $allowedExt, true)) {
            echo json_encode(['status' => 'failed', 'message' => "We do not accept this image file extension, choose another one"]);
            exit;
        }

        $allowedMimeType = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $imageMime = $finfo->file($image['tmp_name']);
        if (!in_array($imageMime, $allowedMimeType, true)) {
            echo json_encode(['status' => 'failed', 'message' => "Invalid image file mimetype, please choose another image"]);
            exit;
        }

        $newFileName = bin2hex(random_bytes(10)) . "." . $imageExt;
        $uploadDirectory = __DIR__ . '/../assets/images';
        ensureUploadDirectory($uploadDirectory);
        $uploadPath = $uploadDirectory . '/' . $newFileName;
        if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
            echo json_encode(['status' => 'failed', 'message' => "Failed to upload the image inside the server, please try again later"]);
            exit;
        }

        require(__DIR__ . '/../config/conn.php');
        $saveQuery = $pdo->prepare("INSERT INTO posts (user_id, content, image) VALUES (:user_id, :content, :image)");
        if ($saveQuery->execute(['user_id' => $logged_in_user['id'], 'content' => $text === '' ? null : $text, 'image' => $newFileName])) {
            echo json_encode(['status' => 'success', 'message' => $text === '' ? "Post created successfully, image only" : "Post created successfully, text and image added"]);
        } else {
            echo json_encode(['status' => 'failed', 'message' => "Post failed to create"]);
        }
        exit;
    }
} catch (Throwable $err) {
    error_log('Create post failed: ' . $err->getMessage());
    echo json_encode(['status' => 'failed', 'message' => "An error occurred while creating a post, please try again later"]);
    exit;
}
