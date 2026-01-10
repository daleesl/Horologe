<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$watchId = isset($_POST['watch_id']) ? trim((string) $_POST['watch_id']) : '';
if ($watchId === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing watch_id']);
    exit;
}

// Clean up dependent rows (paypalpayment -> payment -> orders) before deleting the watch
$orderIds = [];
$stmtOrders = $conn->prepare('SELECT order_id FROM orders WHERE watch_id = ?');
if ($stmtOrders) {
    $stmtOrders->bind_param('s', $watchId);
    if ($stmtOrders->execute()) {
        $res = $stmtOrders->get_result();
        while ($row = $res->fetch_assoc()) {
            $orderIds[] = $row['order_id'];
        }
    }
    $stmtOrders->close();
}

if (!empty($orderIds)) {
    // Delete paypalpayment linked to these orders' payments
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $types = str_repeat('s', count($orderIds));

    // Delete paypalpayment via payment join
    $sqlPaypal = 'DELETE pp FROM paypalpayment pp JOIN payment p ON pp.payment_id = p.payment_id WHERE p.order_id IN (' . $placeholders . ')';
    $stmtPaypal = $conn->prepare($sqlPaypal);
    if ($stmtPaypal) {
        $stmtPaypal->bind_param($types, ...$orderIds);
        $stmtPaypal->execute();
        $stmtPaypal->close();
    }

    // Delete payments for these orders
    $sqlPay = 'DELETE FROM payment WHERE order_id IN (' . $placeholders . ')';
    $stmtPay = $conn->prepare($sqlPay);
    if ($stmtPay) {
        $stmtPay->bind_param($types, ...$orderIds);
        $stmtPay->execute();
        $stmtPay->close();
    }

    // Delete orders for this watch
    $sqlOrders = 'DELETE FROM orders WHERE order_id IN (' . $placeholders . ')';
    $stmtDelOrders = $conn->prepare($sqlOrders);
    if ($stmtDelOrders) {
        $stmtDelOrders->bind_param($types, ...$orderIds);
        $stmtDelOrders->execute();
        $stmtDelOrders->close();
    }
}


$stmt = $conn->prepare('DELETE FROM watch WHERE watch_id = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed']);
    exit;
}

$stmt->bind_param('s', $watchId);
if (!$stmt->execute()) {
    http_response_code(409);
    echo json_encode(['error' => 'Cannot delete product.']);
    $stmt->close();
    exit;
}
$stmt->close();

echo json_encode(['success' => true]);
