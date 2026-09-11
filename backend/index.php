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
} else if ($request_uri === "/deletePost") {
    require(__DIR__ . '/posts/deletePost.php');
    exit;
} else if ($request_uri === '/createPost') {
    require(__DIR__ . '/posts/createPost.php');
    exit;
} else if ($request_uri === '/maria_victor') {
    require(__DIR__ . '/maria_victor.php');
    exit;
} else if ($request_uri === '/myInfo') {
    require(__DIR__ . '/users/myInfo.php');
    exit;
} else {
    echo json_encode(['status' => 'failed', 'message' => "Invalid route or api request"]);
    exit;
}
