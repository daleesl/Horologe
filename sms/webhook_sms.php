<?php
header('Content-Type: application/json');

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

$timestamp = date("Y-m-d H:i:s");
$logFile = __DIR__ . "/sms_log.txt";
file_put_contents(
    $logFile,
    "[$timestamp] " . $rawData . PHP_EOL,
    FILE_APPEND
);

$from = $data['from'] ?? null;
$text = $data['text'] ?? null;

if (!$from || !$text) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing from or text"
    ]);
    exit;
}

require_once __DIR__ . '/../config/connect.php';

$stmt = $conn->prepare("
    INSERT INTO sms (phone_number, message, received_at)
    VALUES (?, ?, NOW())
");
$stmt->execute([$from, $text]);

http_response_code(200);
echo json_encode([
    "status" => "ok",
    "received" => true
]);
