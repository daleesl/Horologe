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

$watchId = $_POST['watch_id'] ?? '';
$watchId = trim((string) $watchId);

if ($watchId === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing watch_id']);
    exit;
}

$stmtImg = $conn->prepare('SELECT image_file FROM watch WHERE watch_id = ?');
$stmtImg->bind_param('s', $watchId);
$stmtImg->execute();
$resImg = $stmtImg->get_result();
$imagePath = '';
if ($row = $resImg->fetch_assoc()) {
    $imagePath = $row['image_file'];
}
$stmtImg->close();

$orderIds = [];
$stmtOrders = $conn->prepare('SELECT DISTINCT order_id FROM order_items WHERE watch_id = ?');
$stmtOrders->bind_param('s', $watchId);
$stmtOrders->execute();
$resOrders = $stmtOrders->get_result();
while ($row = $resOrders->fetch_assoc()) {
    $orderIds[] = $row['order_id'];
}
$stmtOrders->close();

if (!empty($orderIds)) {
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $types = str_repeat('s', count($orderIds));

    $sqlPaypal = "DELETE pp FROM paypalpayment pp 
                  JOIN payment p ON pp.payment_id = p.payment_id 
                  WHERE p.order_id IN ($placeholders)";
    $stmtPaypal = $conn->prepare($sqlPaypal);
    if ($stmtPaypal) {
        $stmtPaypal->bind_param($types, ...$orderIds);
        $stmtPaypal->execute();
        $stmtPaypal->close();
    }

    $sqlPay = "DELETE FROM payment WHERE order_id IN ($placeholders)";
    $stmtPay = $conn->prepare($sqlPay);
    if ($stmtPay) {
        $stmtPay->bind_param($types, ...$orderIds);
        $stmtPay->execute();
        $stmtPay->close();
    }

    $sqlItems = "DELETE FROM order_items WHERE order_id IN ($placeholders)";
    $stmtItems = $conn->prepare($sqlItems);
    if ($stmtItems) {
        $stmtItems->bind_param($types, ...$orderIds);
        $stmtItems->execute();
        $stmtItems->close();
    }

    $sqlOrders = "DELETE FROM orders WHERE order_id IN ($placeholders)";
    $stmtDelOrders = $conn->prepare($sqlOrders);
    if ($stmtDelOrders) {
        $stmtDelOrders->bind_param($types, ...$orderIds);
        $stmtDelOrders->execute();
        $stmtDelOrders->close();
    }
}

$stmt = $conn->prepare('DELETE FROM watch WHERE watch_id = ?');
$stmt->bind_param('s', $watchId);
if (!$stmt->execute()) {
    http_response_code(409);
    echo json_encode(['error' => 'Cannot delete product. It may have dependent records.']);
    $stmt->close();
    exit;
}
$stmt->close();

if ($imagePath && file_exists(__DIR__ . '/../../' . $imagePath)) {
    unlink(__DIR__ . '/../../' . $imagePath);
}

echo json_encode(['success' => true]);