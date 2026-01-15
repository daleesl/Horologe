<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../config/env.php';

function normalizePHPhoneNumber(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone);

    if (strpos($digits, '63') === 0) return $digits;
    if (strpos($digits, '09') === 0) return '63' . substr($digits, 1);
    if (strlen($digits) === 10 && $digits[0] === '9') return '63' . $digits;

    return $digits;
}

function sendSMS(?string $userId, string $phoneNumber, string $message): bool
{
    global $conn;

    $gatewayUrl = getenv('SMS_GATEWAY_URL');
    $username   = getenv('SMS_GATEWAY_USER');
    $password   = getenv('SMS_GATEWAY_PASS');

    if (!$gatewayUrl || !$username || !$password) {
        file_put_contents(
            __DIR__ . '/../logs/sms_error.log',
            "[" . date('Y-m-d H:i:s') . "] ENV CONFIG MISSING\n",
            FILE_APPEND
        );
        return false;
    }

    $phone = normalizePHPhoneNumber($phoneNumber);
    $url   = rtrim($gatewayUrl, '/') . '/messages';

    $payload = json_encode([
        'phoneNumbers' => [$phone],
        'message'      => $message
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("$username:$password")
        ],
        CURLOPT_TIMEOUT        => 10
    ]);

    $response   = curl_exec($ch);
    $curlError  = curl_error($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlError || $statusCode < 200 || $statusCode >= 300) {
        file_put_contents(
            __DIR__ . '/../logs/sms_gateway_error.log',
            "[" . date('Y-m-d H:i:s') . "] HTTP:$statusCode ERROR:$curlError RESPONSE:$response\n",
            FILE_APPEND
        );
        return false;
    }

    $stmt = $conn->prepare("
        INSERT INTO sms (user_id, direction, phone_number, message, source, created_at)
        VALUES (?, 'outgoing', ?, ?, 'system', NOW())
    ");
    $stmt->bind_param("sss", $userId, $phone, $message);
    $stmt->execute();
    $stmt->close();

    return true;
}
