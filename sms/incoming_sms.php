<?php
require_once __DIR__ . '/../config/connect.php';
date_default_timezone_set('Asia/Manila');

/**
 * Normalize phone to PH international format:
 * 639XXXXXXXXX
 */
function normalizePhone(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone);

    if (str_starts_with($digits, '63')) {
        return $digits;
    }
    if (str_starts_with($digits, '09')) {
        return '63' . substr($digits, 1);
    }
    if (strlen($digits) === 10 && $digits[0] === '9') {
        return '63' . $digits;
    }

    return $digits;
}

/**
 * Read raw INPUT (JSON or POST)
 */
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data)) {
    $data = $_POST;
}

/**
 * Logging ALL for debugging
 */
file_put_contents(
    __DIR__ . '/../logs/incoming_sms.log',
    "[" . date('Y-m-d H:i:s') . "] RAW:\n" . $rawInput . "\nPARSED:\n" . print_r($data, true) . "\n\n",
    FILE_APPEND
);

/**
 * Extract phone + message with fallback keys
 */
$from = $data['from']
     ?? $data['sender']
     ?? $data['phone']
     ?? $data['number']
     ?? $data['origin']
     ?? '';

$message = $data['text']
        ?? $data['message']
        ?? $data['body']
        ?? $data['content']
        ?? '';

if ($from === '' || $message === '') {
    echo json_encode(['status' => 'ignored']);
    exit;
}

$normalized = normalizePhone($from);

/**
 * Multi-format lookup for best matching chance
 */
$formats = [
    $normalized,                        // 63915...
    '+'.$normalized,                    // +63915...
    '0'.substr($normalized, 2),         // 0915...
    substr($normalized, 2),             // 915...
];

$user_id = null;
$sql = "
    SELECT user_id FROM users
    WHERE REPLACE(REPLACE(REPLACE(REPLACE(phone_number, '+', ''), ' ', ''), '-', ''), '(', '') IN (?,?,?,?)
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", ...$formats);
$stmt->execute();
$stmt->bind_result($found_id);
if ($stmt->fetch()) {
    $user_id = $found_id;
}
$stmt->close();

/**
 * Insert ALWAYS, user optional
 */
$sql = "
    INSERT INTO sms (user_id, direction, phone_number, message, source, created_at)
    VALUES (?, 'incoming', ?, ?, 'sms_forwarder', NOW())
";
$stmt = $conn->prepare($sql);

/**
 * Bind as strings so NULL works properly
 */
$stmt->bind_param("sss",
    $user_id,
    $normalized,
    $message
);

$stmt->execute();
$stmt->close();

echo json_encode([
    'status'  => 'ok',
    'saved'   => true,
    'user_id' => $user_id ?: null,
]);
exit;
