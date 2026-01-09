<?php
header('Content-Type: application/json');
include '../config/connect.php';

$input = json_decode(file_get_contents('php://input'), true);
$usd = $input['price'] ?? 0;

// Generate payment ID
$payment_id = 'PAY-' . time() . '-' . rand(1000, 9999);

// Insert into payment table first
$stmt = $conn->prepare("INSERT INTO payment (payment_id, payment_method, payment_status, amount) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssd", $payment_id, $payment_method, $payment_status, $usd);
$payment_method = 'PAYPAL';
$payment_status = 'PENDING';
$stmt->execute();

function getAccessToken() {
    $client = "AdeUjp2y9B4ofLqlormM8Tr9L4fZuA8qsmllHRbpOKn29G8Y2YALhsXd6VMEW3Qy-BRSqTiBsbFd78s1";
    $secret = "EBKNw51zBPfe99EaMbzZnfrj5uKgcq9DNbRK9qO3rGxWlRVd2T4aa7eOwuEPlRpYf3tLnDq2_qdpnMHT";

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

$orderData = [
    "intent" => "CAPTURE",
    "purchase_units" => [[
        "amount" => ["currency_code"=>"USD","value"=>$usd],
        "custom_id" => $payment_id // Pass your payment_id to PayPal
    ]]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $token"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if(!$response) echo json_encode(["error" => curl_error($ch)]);
else echo $response;