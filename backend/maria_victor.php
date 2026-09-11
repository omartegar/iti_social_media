<?php

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    echo json_encode(['status' => 'success', 'message' => "Hello Maria Victor you sent me a GET method"]);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === "POST") {
    echo json_encode(['status' => 'success', 'message' => "Hello Maria Victor you sent me a POST method"]);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === "PUT") {
    echo json_encode(['status' => 'success', 'message' => "Hello Maria Victor you sent me a PUT method"]);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === "PATCH") {
    echo json_encode(['status' => 'success', 'message' => "Hello Maria Victor you sent me a PATCH method"]);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    echo json_encode(['status' => 'success', 'message' => "Hello Maria Victor you sent me a DELETE method"]);
    exit;
} else {
    echo json_encode(['status' => "failed", 'message' => "Invalid request method, please choose a valid http request method ex,GET,POST,PUT,DELETE"]);
    exit;
}
