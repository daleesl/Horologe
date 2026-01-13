<?php
require_once __DIR__ . '/../config/connect.php';

file_put_contents(
    __DIR__ . '/../logs/webhook_hit.log',
    "[" . date('Y-m-d H:i:s') . "] RAW: " . file_get_contents('php://input') . "\n",
    FILE_APPEND
);

file_put_contents(
    __DIR__ . '/../logs/webhook_headers.log',
    print_r(getallheaders(), true) . "\n",
    FILE_APPEND
);

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$phone = $data['from'] ?? '';
$message = $data['text'] ?? '';

if (!$phone || !$message) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing phone or message']);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO sms (direction, phone_number, message, source)
     VALUES ('incoming', ?, ?, 'sms_forwarder')"
);
$stmt->bind_param("ss", $phone, $message);
$stmt->execute();
$stmt->close();

http_response_code(200);
echo json_encode(['status' => 'ok', 'received' => true]);

exit;
?>