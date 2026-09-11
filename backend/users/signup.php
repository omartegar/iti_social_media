<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(['status' => "failed", 'message' => "Invalid request method"]);
    exit;
}

function validateInputs($firstname,  $lastname,  $email, $phone,  $password,  $confirmpassword)
{
    $namePattern = '/^[a-zA-Z]{3,}$/';
    $emailPattern = '/^[a-zA-Z0-9._%]{4,}+@+[a-zA-Z]{3,}+\.[a-zA-Z]{3,}$/';
    $passwordPattern = '/^[a-zA-Z0-9._%]{8,}$/';
    $phonePattern = '/^(010|011|012|015)+[0-9]{8}$/'; // 011 47275486

    if (!preg_match($namePattern, $firstname)) {
        echo json_encode(['status' => 'failed', 'message' => "Firstname is not valid"]);
        exit;
    }

    if (!preg_match($namePattern, $lastname)) {
        echo json_encode(['status' => "failed", 'message' => "Lastname is not valid"]);
        exit;
    }

    if (!preg_match($emailPattern, $email)) {
        echo json_encode(['status' => 'failed', 'message' => "This email is not valid or real"]);
        exit;
    }

    if (!preg_match($passwordPattern, $password)) {
        echo json_encode(['status' => 'failed', 'message' => "Invalid password, it should be 8 characters min."]);
        exit;
    }

    if (!preg_match($passwordPattern, $confirmpassword)) {
        echo json_encode(['status' => 'failed', 'message' => "Invalid confirm password, it should be 8 characters min."]);
        exit;
    }

    if ($password !== $confirmpassword) {
        echo json_encode(['status' => "failed", 'message' => "Passwords are not the same"]);
        exit;
    }

    if (!preg_match($phonePattern, $phone)) {
        echo json_encode(['status' => 'failed', 'message' => "Please enter a valid egyptian phone number, Ex: 01234567890."]);
        exit;
    }
}

function validateImage()
{
    if (!isset($_FILES['image'])) {
        echo json_encode(['status' => 'failed', 'message' => "Please upload a profile picture"]);
        exit;
    }

    if ($_FILES['image']['error'] === UPLOAD_ERR_INI_SIZE) {
        echo json_encode(['status' => 'failed', 'message' => "We are sorry our server cannot accept this image size"]);
        exit;
    } else if ($_FILES['image']['error'] === UPLOAD_ERR_EXTENSION) {
        echo json_encode(['status' => 'failed', 'message' => "We are sorry this image type out server refuses it"]);
        exit;
    } else if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'failed', 'message' => "An error occurred with this image, try again or choose another image file"]);
        exit;
    }

    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        echo json_encode(['status' => 'failed', 'message' => "This image is too big, Your image should be less than 5MB."]);
        exit;
    }

    $allowedExt = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExt)) {
        echo json_encode(['status' => 'failed', 'message' => "This image extension is not allowed, upload another image"]);
        exit;
    }

    $allowedMimeTypes = ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'];
    $fileMime = mime_content_type($_FILES['image']['tmp_name']);
    if (!in_array($fileMime, $allowedMimeTypes)) {
        echo json_encode(['status' => 'failed', 'message' => "Invalid image type, please upload another image"]);
        exit;
    }


    // Move image to /assets/images
    $new_image_name = bin2hex(random_bytes(5)) . '.' . $fileExtension;
    $from = $_FILES['image']['tmp_name'];
    $to = __DIR__ . '/../assets/images/' . $new_image_name;
    if (move_uploaded_file($from, $to)) {
        // Image saved , continue
        $_FILES['image']['name'] = $new_image_name;
    } else {
        echo json_encode(['status' => "failed", 'message' => "Image failed to move in server"]);
        exit;
    }
}


try {

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $phone = $_POST['phone'];
    $image = $_FILES['image'];

    validateInputs($firstname, $lastname, $email, $phone, $password, $confirmpassword);
    validateImage();


    require(__DIR__ . '/../config/conn.php');

    $stmt = $pdo->prepare("INSERT INTO users 
        (first_name, last_name, email, password, phone, is_admin, token, profile_picture)
        VALUES
        (:first_name, :last_name, :email, :password, :phone, :is_admin, :token, :profile_picture);
    ");

    if ($stmt->execute([
        'first_name' => $firstname,
        'last_name' => $lastname,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'phone' => $phone,
        'is_admin' => $isAdmin,
        'token' => null,
        'profile_picture' => $_FILES['image']['name']
    ])) {
        echo json_encode(['status' => 'success', 'message' => "Account created successfully"]);
        exit;
    } else {
        echo json_encode(['status' => 'failed', 'message' => "An error occured while creating account"]);
        exit;
    }
} catch (Exception $err) {
    if ($err->getCode() == 23000) {
        echo json_encode(['status' => 'failed', 'message' => "These credentials is already available or added recently."]);
        exit;
    }
    echo json_encode(['status' => "failed", 'message' => "An error occurred while signing up, try again later.", 'error' => $err->getMessage()]);
    exit;
}
