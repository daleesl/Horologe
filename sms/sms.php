<?php

require_once __DIR__ . '/../config/env.php';

/**
 * Normalizes Philippine phone numbers into 639XXXXXXXXXXX format.
 *
 * @param string $phoneNumber
 *
 * @return string
 */
function normalizePHPhoneNumber($phoneNumber) {
    $digitsOnly = preg_replace('/\D+/', '', $phoneNumber);

    if (strpos($digitsOnly, '63') === 0) {
        return $digitsOnly;
    }

    if (strpos($digitsOnly, '09') === 0) {
        return '63' . substr($digitsOnly, 1);
    }

    if (strlen($digitsOnly) === 10 && $digitsOnly[0] === '9') {
        return '63' . $digitsOnly;
    }

    return $digitsOnly;
}

/**
 * Sends SMS through SMSGate local HTTP API
 *
 * @param string $phoneNumber
 * @param string $message
 *
 * @return bool
 */
function sendSMS($phoneNumber, $message) {

    $gatewayUrl = getenv('SMS_GATEWAY_URL');
    $username   = getenv('SMS_GATEWAY_USER');
    $password   = getenv('SMS_GATEWAY_PASS');

    file_put_contents(
        __DIR__ . '/../logs/sms_env_debug.log',
        "[" . date('Y-m-d H:i:s') . "] " .
        "URL=" . var_export($gatewayUrl, true) . " | " .
        "USER=" . var_export($username, true) . " | " .
        "PASS_SET=" . ($password ? 'YES' : 'NO') . "\n",
        FILE_APPEND
    );

    $normalizedPhoneNumber = normalizePHPhoneNumber($phoneNumber);
    $url = rtrim($gatewayUrl, '/') . '/messages';

    $payload = json_encode([
        'phoneNumbers' => [$normalizedPhoneNumber],
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

    if ($statusCode >= 200 && $statusCode < 300) {
        $stmt = $GLOBALS['conn']->prepare(
            "INSERT INTO sms (direction, phone_number, message, source)
            VALUES ('outgoing', ?, ?, 'system')"
        );
        $stmt->bind_param("ss", $normalizedPhoneNumber, $message);
        $stmt->execute();
        $stmt->close();
    }

    return $statusCode >= 200 && $statusCode < 300;
}