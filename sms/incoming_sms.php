<?php
require_once __DIR__ . '/../config/connect.php';

date_default_timezone_set('Asia/Manila');

/**
 * Normalize PH phone numbers to 639XXXXXXXXX
 */
function normalizePHPhoneNumber(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone);

    // +639XXXXXXXXX or 639XXXXXXXXX
    if (strpos($digits, '63') === 0) {
        return $digits;
    }

    // 09XXXXXXXXX → 639XXXXXXXXX
    if (strpos($digits, '09') === 0) {
        return '63' . substr($digits, 1);
    }

    // 9XXXXXXXXX → 639XXXXXXXXX
    if (strlen($digits) === 10 && $digits[0] === '9') {
        return '63' . $digits;
    }

    return $digits;
}

/* -------------------------------
   Read and log raw payload
-------------------------------- */
$raw = file_get_contents('php://input');

file_put_contents(
    __DIR__ . '/../logs/incoming_sms.log',
    "[" . date('Y-m-d H:i:s') . "] RAW: " . $raw . PHP_EOL,
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

/* -------------------------------
   Find user by NORMALIZED number
   (handles 0993… vs 63993…)
-------------------------------- */
$user_id = null;

$stmt = $conn->prepare("
    SELECT user_id
    FROM users
    WHERE
        REPLACE(phone_number, '+', '') = ?
        OR CONCAT('63', SUBSTRING(phone_number, 2)) = ?
    LIMIT 1
");

$stmt->bind_param("ss", $phone, $phone);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

/* -------------------------------
   Log incoming SMS
-------------------------------- */
$stmt = $conn->prepare("
    INSERT INTO sms (user_id, direction, phone_number, message, source)
    VALUES (?, 'incoming', ?, ?, 'sms_forwarder')
");

$stmt->bind_param("sss", $user_id, $phone, $message);
$stmt->execute();
$stmt->close();

/* -------------------------------
   Optional auto-reply
-------------------------------- */
if ($user_id) {
    $ack = "Thank you! We have received your message.";

    $stmt = $conn->prepare("
        INSERT INTO sms (user_id, direction, phone_number, message, source)
        VALUES (?, 'outgoing', ?, ?, 'system')
    ");

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