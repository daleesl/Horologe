<?php
session_start();
include '../config/connect.php';

$paypalOrderID = $_GET['token'] ?? '';
$payment_id = $_SESSION['paypal_payment_id'] ?? '';

if ($paypalOrderID && $payment_id) {
    // Capture the payment
    $captureUrl = "capture_order.php?orderID=$paypalOrderID&payment_id=$payment_id";
    $captureResponse = file_get_contents($captureUrl);
    $captureData = json_decode($captureResponse, true);
    
    if (isset($captureData['status']) && $captureData['status'] === 'COMPLETED') {
        header('Location: ../public/orderConfirmation.php?payment=success');
        exit();
    }
}

header('Location: ../public/checkout.php?payment=failed');
exit();