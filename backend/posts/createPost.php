<?php

require(__DIR__ . '/../config/checkToken.php');

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}


try {
    // Needs text or image , one or both at least
    $text = $_POST['text'];
    $image = $_FILES['image'];
    $success_img_url = "";

    if (!isset($text) && !isset($image)) {
        echo json_encode(['status' => "failed", 'message' => "You should add at least text or image to create a post"]);
        exit;
    }


    if (isset($text) && strlen($text) > 5 && isset($image) && $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        // both
        if (strlen($text) > 1000) {
            echo json_encode(['status' => 'failed', 'message' => "Post text is too long, maximum post text is 1000 characters."]);
            exit;
        }

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'failed', 'message' => "Image error appeared, please try again or choose another image"]);
            exit;
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            echo json_encode(['status' => "failed", 'message' => "This image size is too big, it should be less than 5MB"]);
            exit;
        }

        $allowedExt = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
        $imageExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        if (!in_array($imageExt, $allowedExt)) {
            echo json_encode(['status' => 'failed', 'message' => "We do not accept this image file extension, choose another one"]);
            exit;
        }


        $allowedMimeType = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $imageMime = $finfo->file($_FILES['image']['tmp_name']);
        if (!in_array($imageMime, $allowedMimeType)) {
            echo json_encode(['status' => 'failed', 'message' => "Invalid image file mimetype, please choose another image"]);
            exit;
        }


        $new_file_name = bin2hex(random_bytes(10)) . "." . $imageExt;
        $from = $_FILES['image']['tmp_name'];
        $to = "../assets/images/" . $new_file_name;
        if (move_uploaded_file($from, $to)) {
            $success_img_url = $new_file_name;
        } else {
            echo json_encode(['status' => 'failed', 'message' => "Failed to upload the image inside the server, please try again later"]);
            exit;
        }

        require(__DIR__ . '/../config/conn.php');
        $saveQuery = $pdo->prepare("INSERT INTO posts (user_id, content, image) VALUES (:user_id, :content,:image)");
        if ($saveQuery->execute([
            'user_id' => $logged_in_user['id'],
            'content' => $text,
            'image' => $success_img_url
        ])) {
            echo json_encode(['status' => 'success', 'message' => "Post created successfully, text and image added"]);
            exit;
        } else {
            echo json_encode(['status' => 'failed', 'message' => "Post failed to create"]);
            exit;
        }
    }

    // text only
    if (isset($text) && $_FILES['image'] == null) {
        if (strlen($text) < 5) {
            echo json_encode(['status' => "failed", 'message' => "Post text is too short, it should be 5 min chars."]);
            exit;
        } else if (strlen(($text)) > 255) {
            echo json_encode(['status' => 'failed', 'message' => "Post text is too long, it should be 1000 max chars"]);
            exit;
        }

        require(__DIR__ . '/../config/conn.php');
        $saveQuery = $pdo->prepare("INSERT INTO posts (user_id, content, image) VALUES (:user_id, :content, :image)");
        if ($saveQuery->execute([
            'user_id' => $logged_in_user['id'],
            'content' => $text,
            'image' => null
        ])) {
            echo json_encode(['status' => 'success', 'message' => "Post created successfully, text only added"]);
            exit;
        } else {
            echo json_encode(['status' => 'failed', 'message' => "Post failed to create with text only now, try again later"]);
            exit;
        }
    }

    // image only
    if (!isset($text) || trim(strlen($text)) == 0 && $_FILES['image'] !== null) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'failed', 'message' => "Uploading of this image only post corrupted, try again or choose another image"]);
            exit;
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            echo json_encode(['status' => 'failed', 'message' => "Image size is too big, it should be less than 5MB"]);
            exit;
        }

        $allowedExt = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
        $imageExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        if (!in_array($imageExt, $allowedExt)) {
            echo json_encode(['status' => 'failed', 'message' => "This file extension is not allowed, please choose a valid image extension"]);
            exit;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $allowedMimeType = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
        $imageMime = $finfo->file($_FILES['image']['tmp_name']);
        if (!in_array($imageMime, $allowedMimeType)) {
            echo json_encode(['status' => 'failed', 'message' => "This image mime is not allowed, choose another valid image mimetype please."]);
            exit;
        }


        $new_image_name = bin2hex(random_bytes(10)) . "." . $imageExt;
        $from = $_FILES['image']['tmp_name'];
        $to = "/../assets/images/" . $new_image_name;
        if (move_uploaded_file($from, $to)) {
            $success_img_url = $new_image_name;
            require(__DIR__ . '/../config/conn.php');
            $saveImageQuery = $pdo->prepare("INSERT INTO posts (user_id, content, image) VALUES (:user_id, :content,:image)");
            if ($saveImageQuery->execute([
                'user_id' => $logged_in_user['id'],
                'content' => null,
                'image' => $new_image_name
            ])) {
                echo json_encode(['status' => 'success', 'message' => "Post created successfully, image only"]);
                exit;
            } else {
                echo json_encode(['status' => 'failed', 'message' => "failed to create this post, image only"]);
                exit;
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => "Process to upload the image on server failed, Please try again later or contact admin if issue persists"]);
            exit;
        }
    }
} catch (Exception $err) {
    echo json_encode(['status' => 'failed', 'message' => "An error occured while creating this post, please try again later", 'error' => $err->getMessage()]);
    exit;
}
