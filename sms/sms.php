<?php

require_once __DIR__ . '/../config/env.php';

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


function sendSMS($userId, $phoneNumber, $message) {

    $gatewayUrl = getenv('SMS_GATEWAY_URL');
    $username   = getenv('SMS_GATEWAY_USER');
    $password   = getenv('SMS_GATEWAY_PASS');

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

    global $conn;
    $stmt = $conn->prepare(
        "INSERT INTO sms (user_id, direction, phone_number, message, source, created_at)
        VALUES (?, 'outgoing', ?, ?, 'system', NOW())"
    );
    $stmt->bind_param("sss", $userId, $normalizedPhoneNumber, $message);
    $stmt->execute();
    $stmt->close();

    return $statusCode >= 200 && $statusCode < 300;
}