<!-- Example: conn.php file -->
<?php

try {


    $username = "";
    $password = "";
    $dbname   = "";
    $host     = "";
    $port     = "";

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
} catch (Exception $err) {
    echo json_encode(['status' => 'failed', 'message' => "Connection failed to database", 'error' => $err->getMessage()]);
    exit;
}
