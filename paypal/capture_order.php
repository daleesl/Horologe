<?php
header('Content-Type: application/json');
include '../config/connect.php';

$orderID = $_GET['orderID'] ?? '';
if (!$orderID) {
    echo json_encode(['error' => 'Missing orderID']);
    exit;
}

function getAccessToken() {
    $client = "YOUR_CLIENT_ID";
    $secret = "YOUR_SECRET";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_USERPWD, $client . ":" . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    if (!$result) {
        die(json_encode(['error' => curl_error($ch)]));
    }

    curl_close($ch);
    return json_decode($result, true)['access_token'];
}

$token = getAccessToken();

/* ---- Capture Order ---- */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderID/capture");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $token"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode(["error" => "PayPal capture failed"]);
    exit;
}

$captureData = json_decode($response, true);

/* ---- Validate PayPal Success ---- */
if (($captureData['status'] ?? '') !== 'COMPLETED') {
    echo json_encode([
        "error" => "Payment not completed",
        "paypal_status" => $captureData['status'] ?? 'UNKNOWN'
    ]);
    exit;
}

/* ---- SECURE payment_id FROM PAYPAL ---- */
$payment_id =
    $captureData['purchase_units'][0]['payments']['captures'][0]['custom_id']
    ?? $captureData['purchase_units'][0]['custom_id']
    ?? '';

if (!$payment_id) {
    echo json_encode(["error" => "Missing payment_id from PayPal"]);
    exit;
}

/* ---- Database Update ---- */
$conn->begin_transaction();

try {
    // Update payment table
    $stmt = $conn->prepare("
        UPDATE payment 
        SET payment_status = 'COMPLETED', payment_date = NOW() 
        WHERE payment_id = ?
    ");
    $stmt->bind_param("s", $payment_id);
    $stmt->execute();

    // Insert PayPal payment record
    $paypal_transaction_id = $captureData['id'] ?? $orderID;
    $payer_email = $captureData['payer']['email_address'] ?? '';

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

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["error" => "Database error"]);
    exit;
}

echo $response;
