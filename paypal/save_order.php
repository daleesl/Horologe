<?php
ob_start();
header('Content-Type: application/json');

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'error'   => 'PHP Fatal Error',
            'details'=> $error
        ]);
    }
});

// ==================================================
// ERROR LOGGING
// ==================================================
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/paypal_error.log');

// ==================================================
// SESSION + DB
// ==================================================
session_start();
require_once __DIR__ . '/../config/connect.php';

if (!isset($conn) || !$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// ==================================================
// USER VALIDATION
// ==================================================
$userID = $_SESSION['userID'] ?? $_SESSION['user_id'] ?? null;
if (!$userID) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// ==================================================
// READ INPUT
// ==================================================
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit;
}

// ==================================================
// EXTRACT DATA
// ==================================================
$firstName     = trim($input['firstName'] ?? '');
$lastName      = trim($input['lastName'] ?? '');
$city          = trim($input['city'] ?? '');
$postalCode    = trim($input['postalCode'] ?? '');
$country       = trim($input['country'] ?? '');
$address       = trim($input['address'] ?? '');
$email         = trim($input['email'] ?? $_SESSION['email'] ?? '');
$paymentMethod = trim($input['paymentMethod'] ?? 'PAYPAL');
$paymentID     = trim($input['paymentID'] ?? '');
$cart          = $input['cart'] ?? [];

if (!$firstName || !$lastName || !$address || !$city || !$postalCode || !$email || !$paymentID || empty($cart)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Missing required checkout data']);
    exit;
}

// ==================================================
// CALCULATE TOTAL
// ==================================================
$total = 0;
foreach ($cart as $item) {
    $total += ((int)$item['quantity']) * ((float)$item['price']);
}

// ==================================================
// SAVE ORDER
// ==================================================
$order_id = uniqid('ORD');
$fullName = $firstName . ' ' . $lastName;

try {
    $conn->begin_transaction();

    // 1️⃣ ORDER HEADER
    $stmt = $conn->prepare("
        INSERT INTO orders (
            order_id, user_id, user_name, user_email,
            ship_full_name, ship_street_address, ship_city,
            ship_postal_code, ship_province_state,
            total_amount, payment_method, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "ssssssssssd",
        $order_id,
        $userID,
        $fullName,
        $email,
        $fullName,
        $address,
        $city,
        $postalCode,
        $country,
        $total,
        $paymentMethod
    );

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }
    $stmt->close();

    // 2️⃣ PAYMENT
    $paymentStmt = $conn->prepare("
        INSERT INTO payment (
            payment_id, payment_method, payment_status,
            amount, order_id, created_at
        ) VALUES (?, ?, 'COMPLETED', ?, ?, NOW())
    ");

    $paymentStmt->bind_param(
        "ssds",
        $paymentID,
        $paymentMethod,
        $total,
        $order_id
    );

    if (!$paymentStmt->execute()) {
        throw new Exception($paymentStmt->error);
    }
    $paymentStmt->close();

    // 3️⃣ ORDER ITEMS
    $itemStmt = $conn->prepare("
        INSERT INTO order_items (
            order_id, watch_id, product_name,
            product_description, quantity, price_at_purchase
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stockStmt = $conn->prepare("
        UPDATE watch
        SET stock_quantity = stock_quantity - ?
        WHERE watch_id = ? AND stock_quantity >= ?
    ");

    foreach ($cart as $item) {
        $watchId = $item['id'] ?? $item['watch_id'];
        $qty     = (int)$item['quantity'];
        $price   = (float)$item['price'];

        $itemStmt->bind_param(
            "ssssid",
            $order_id,
            $watchId,
            $item['name'],
            $item['description'],
            $qty,
            $price
        );

        if (!$itemStmt->execute()) {
            throw new Exception($itemStmt->error);
        }

        $stockStmt->bind_param("isi", $qty, $watchId, $qty);
        $stockStmt->execute();

        if ($stockStmt->affected_rows === 0) {
            throw new Exception("Insufficient stock for watch $watchId");
        }
    }

    $itemStmt->close();
    $stockStmt->close();

    $conn->commit();

    ob_end_clean();
    echo json_encode([
        'success'   => true,
        'order_id'  => $order_id,
        'total'     => $total,
        'emailSent'=> $emailSent
    ]);
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
    exit;
}
