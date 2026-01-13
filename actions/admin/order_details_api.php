<?php
ob_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/connect.php';

$orderId = $_GET['order_id'] ?? '';
if (!$orderId) {
    echo json_encode(['error' => 'Missing order_id']);
    exit;
}

$stmtOrder = $conn->prepare("
    SELECT o.order_id, o.order_date, o.user_name, o.user_email,
           o.ship_full_name, o.ship_street_address, o.ship_city, o.ship_province_state, o.ship_postal_code,
           o.total_amount, p.payment_status
    FROM orders o
    LEFT JOIN payment p ON p.order_id = o.order_id
    WHERE o.order_id = ?
");
$stmtOrder->bind_param('s', $orderId);
$stmtOrder->execute();
$resOrder = $stmtOrder->get_result();
$orderRow = $resOrder->fetch_assoc();
$stmtOrder->close();

if (!$orderRow) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}

$shippingAddress = trim(
    $orderRow['ship_full_name'] . ', ' .
    $orderRow['ship_street_address'] . ', ' .
    $orderRow['ship_city'] . ', ' .
    $orderRow['ship_province_state'] . ', ' .
    $orderRow['ship_postal_code']
);

$stmtProducts = $conn->prepare("
    SELECT oi.quantity, oi.price_at_purchase AS price, w.model AS product_name,
           w.description, w.image_file AS image
    FROM order_items oi
    LEFT JOIN watch w ON oi.watch_id = w.watch_id
    WHERE oi.order_id = ?
");
$stmtProducts->bind_param('s', $orderId);
$stmtProducts->execute();
$resProducts = $stmtProducts->get_result();

$products = [];
while ($row = $resProducts->fetch_assoc()) {
    $products[] = [
        'product_name' => $row['product_name'],
        'quantity' => (int)$row['quantity'],
        'price' => (float)$row['price'],
        'image' => $row['image'] ?? '',
        'description' => $row['description'] ?? '',
    ];
}

$stmtProducts->close();

ob_end_clean();
echo json_encode([
    'order' => [
        'order_id' => $orderRow['order_id'],
        'order_date' => $orderRow['order_date'],
        'user_name' => $orderRow['user_name'],
        'user_email' => $orderRow['user_email'],
        'shipping_address' => $shippingAddress
    ],
    'products' => $products
]);
exit;