<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/connect.php';
require_once 'paypal_config.php';

$orderID = $_GET['orderID'] ?? '';
if (!$orderID) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing orderID']);
    exit;
}

function getAccessToken()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_API . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        curl_close($ch);
        http_response_code(500);
        echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)]);
        exit;
    }

    curl_close($ch);

    if ($httpCode >= 400) {
        http_response_code($httpCode);
        $tokenData = json_decode($result, true);
        echo json_encode(['error' => 'PayPal auth failed (HTTP ' . $httpCode . ')', 'details' => $tokenData]);
        exit;
    }

    $tokenData = json_decode($result, true);
    if (!isset($tokenData['access_token'])) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to retrieve access token', 'details' => $tokenData]);
        exit;
    }

    return $tokenData['access_token'];
}

$token = getAccessToken();

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, PAYPAL_API . "/v2/checkout/orders/" . urlencode($orderID) . "/capture");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    curl_close($ch);
    http_response_code(502);
    echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)]);
    exit;
}

curl_close($ch);

if ($httpCode >= 400) {
    http_response_code($httpCode);
    $responseData = json_decode($response, true);
    if ($responseData) {
        echo json_encode($responseData);
    } else {
        echo json_encode(['error' => 'PayPal API error (HTTP ' . $httpCode . ')', 'raw' => substr($response, 0, 200)]);
    }
    exit;
}

$responseData = json_decode($response, true);
if (!$responseData) {
    http_response_code(502);
    echo json_encode(['error' => 'Invalid JSON from PayPal', 'raw' => substr($response, 0, 200)]);
    exit;
}

if (($responseData['status'] ?? '') !== 'COMPLETED') {
    echo json_encode([
        "error" => "Payment not completed",
        "paypal_status" => $responseData['status'] ?? 'UNKNOWN'
    ]);
    exit;
}

$payment_id =
    $responseData['purchase_units'][0]['payments']['captures'][0]['custom_id']
    ?? $responseData['purchase_units'][0]['custom_id']
    ?? '';

if (!$payment_id) {
    echo json_encode(["error" => "Missing payment_id from PayPal"]);
    exit;
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        UPDATE payment 
        SET payment_status = 'COMPLETED', payment_date = NOW() 
        WHERE payment_id = ?
    ");
    $stmt->bind_param("s", $payment_id);
    $stmt->execute();

    $paypal_transaction_id = $responseData['id'] ?? $orderID;
    $payer_email = $responseData['payer']['email_address'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO paypalpayment 
        (paypal_transaction_id, payer_email, payment_status, payment_id) 
        VALUES (?, ?, 'COMPLETED', ?)
    ");
    $stmt->bind_param("sss", $paypal_transaction_id, $payer_email, $payment_id);
    $stmt->execute();

    $conn->commit();

    session_start();
    $_SESSION['payment_id'] = $payment_id;
    $_SESSION['paypal_order_id'] = $paypal_transaction_id;

    echo json_encode($responseData);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}
