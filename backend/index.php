<?php

require(__DIR__ . '/config/cors.php');
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


if ($request_uri === '/' || $request_uri === "") {
    echo "Index(Root) page is working.";
    exit;
} else if ($request_uri === '/signup') {
    require('./users/signup.php');
    exit;
} else if ($request_uri === '/login') {
    require('./users/login.php');
    exit;
} else if ($request_uri === '/getHomeData') {
    require(__DIR__ . '/homeData/getHomeData.php');
    exit;
} else {
    echo json_encode(['status' => 'failed', 'message' => "Invalid route or api request"]);
    exit;
}
