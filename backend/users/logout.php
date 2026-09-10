<?php

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['status' => 'failed', 'message' => "Invalid request method"]);
    exit;
}
