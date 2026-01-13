<?php
require_once __DIR__ . '/../config/connect.php';

function normalizePHPhoneNumber($phone) {
    $digits = preg_replace('/\D+/', '', $phone);

    if (strpos($digits, '63') === 0) {
        return $digits;
    }

    if (strpos($digits, '09') === 0) {
        return '63' . substr($digits, 1);
    }

    if (strlen($digits) === 10 && $digits[0] === '9') {
        return '63' . $digits;
    }

    return $digits;
}

$raw = file_get_contents('php://input');

file_put_contents(
    __DIR__ . '/../logs/webhook_headers.log',
    print_r(getallheaders(), true) . "\n",
    FILE_APPEND
);

file_put_contents(
    __DIR__ . '/../logs/incoming_sms.log',
    "[" . date('Y-m-d H:i:s') . "] " . $raw . PHP_EOL,
    FILE_APPEND
);

$data = json_decode($raw, true);

$from    = $data['from'] ?? '';
$message = $data['text'] ?? '';

if (!$from || !$message) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing from or text']);
    exit;
}

$phone = normalizePHPhoneNumber($from);

$user_id = null;

$stmt = $conn->prepare(
    "SELECT user_id FROM users WHERE phone_number = ? LIMIT 1"
);
$stmt->bind_param("s", $phone);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare(
    "INSERT INTO sms (user_id, direction, phone_number, message, source)
     VALUES (?, 'incoming', ?, ?, 'sms_forwarder')"
);

$stmt->bind_param("sss", $user_id, $phone, $message);
$stmt->execute();
$stmt->close();

if ($user_id) {
    $ack = "Thank you! We have received your message.";

    $stmt = $conn->prepare(
        "INSERT INTO sms (user_id, direction, phone_number, message, source)
         VALUES (?, 'outgoing', ?, ?, 'system')"
    );

    $stmt->bind_param("sss", $user_id, $phone, $ack);
    $stmt->execute();
    $stmt->close();
}

http_response_code(200);
echo json_encode([
    'status'   => 'ok',
    'received' => true,
    'user_id'  => $user_id
]);

exit;
?>