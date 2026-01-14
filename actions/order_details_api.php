<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/connect.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$orderId = $_GET['order_id'] ?? '';
if (!$orderId) {
    echo json_encode(['error' => 'Missing order_id']);
    exit();
}

// Verify order belongs to current user
$stmt = $conn->prepare("SELECT order_id, order_date, total_amount FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param('ss', $orderId, $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();
$stmt->close();

if (!$order) {
    echo json_encode(['error' => 'Order not found or access denied']);
    exit();
}

// Fetch products for the order
$stmt = $conn->prepare("SELECT oi.quantity, oi.price_at_purchase AS price, w.model AS product_name, w.description, w.image_file AS image
    FROM order_items oi
    LEFT JOIN watch w ON oi.watch_id = w.watch_id
    WHERE oi.order_id = ?");
$stmt->bind_param('s', $orderId);
$stmt->execute();
$res = $stmt->get_result();
$products = [];
while ($row = $res->fetch_assoc()) {
    $products[] = [
        'product_name' => $row['product_name'],
        'quantity' => (int)$row['quantity'],
        'price' => (float)$row['price'],
        'image' => $row['image'] ?? '',
        'description' => $row['description'] ?? ''
    ];
}
$stmt->close();

echo json_encode(['order' => $order, 'products' => $products]);
exit();
