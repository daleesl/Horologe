<?php
require_once __DIR__ . '/../../config/connect.php';
header('Content-Type: application/json');

$orderId = $_GET['order_id'] ?? '';
if (!$orderId) {
    echo json_encode(['error' => 'Missing order_id']);
    exit;
}

$stmtOrder = $conn->prepare('
    SELECT o.order_id, o.order_date, o.user_name, o.user_email, o.user_phone,
           o.ship_full_name, o.ship_street_address, o.ship_city, o.ship_province_state, o.ship_postal_code,
           o.total_amount, p.payment_status
    FROM orders o
    LEFT JOIN payment p ON p.order_id = o.order_id
    WHERE o.order_id = ?
');
$stmtOrder->bind_param('s', $orderId);
$stmtOrder->execute();
$resOrder = $stmtOrder->get_result();
$orderRow = $resOrder->fetch_assoc();
$stmtOrder->close();

if (!$orderRow) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}

$order = [
    'order_id' => $orderRow['order_id'],
    'order_date' => $orderRow['order_date'],
    'user_name' => $orderRow['user_name'],
    'user_email' => $orderRow['user_email'],
    'user_phone' => $orderRow['user_phone'],
    'shipping_address' => trim(
        $orderRow['ship_full_name'] . ', ' . 
        $orderRow['ship_street_address'] . ', ' . 
        $orderRow['ship_city'] . ', ' . 
        $orderRow['ship_province_state'] . ', ' . 
        $orderRow['ship_postal_code']
    ),
];

$stmt = $conn->prepare("
    SELECT oi.*, w.image_file, w.description AS product_description
    FROM order_items oi
    LEFT JOIN watch w ON oi.watch_id = w.watch_id
    WHERE oi.order_id = ?
");
$stmt->bind_param('s', $orderId);
$stmt->execute();
$res = $stmt->get_result();
$products = [];
while ($row = $res->fetch_assoc()) {
    $imagePath = '';
    if (!empty($row['image_file'])) {
        $imagePath = $row['image_file'];
    }

    $products[] = [
        'product_name' => $row['product_name'],
        'quantity' => (int)$row['quantity'],
        'price' => (float)$row['price_at_purchase'],
        'image' => $row['image_file'], // IMPORTANT
        'description' => $row['product_description'] ?? '',
    ];
}

$stmt->close();

echo json_encode([
    'order' => $order,
    'products' => $products
]);
exit;
?>