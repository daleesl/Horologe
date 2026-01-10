<?php
require_once __DIR__ . '/../../config/connect.php';
header('Content-Type: application/json');
$orderId = $_GET['order_id'] ?? '';
if (!$orderId) {
    echo json_encode(['error' => 'Missing order_id']);
    exit;
}
// Fetch all rows for this order (one per product)
$stmt = $conn->prepare('SELECT o.*, p.payment_status, w.image_file AS image, w.description AS product_description FROM orders o LEFT JOIN payment p ON p.order_id = o.order_id LEFT JOIN watch w ON o.watch_id = w.watch_id WHERE o.order_id = ?');
$stmt->bind_param('s', $orderId);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();
if (empty($rows)) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}
// Use the first row for order/customer/shipping info
$first = $rows[0];
$order = [
    'order_id' => $first['order_id'],
    'order_date' => $first['order_date'],
    'user_name' => $first['user_name'],
    'user_email' => $first['user_email'],
    'user_phone' => $first['user_phone'],
    'shipping_address' => trim($first['ship_full_name'] . ', ' . $first['ship_street_address'] . ', ' . $first['ship_city'] . ', ' . $first['ship_province_state'] . ', ' . $first['ship_postal_code']),
];
// Build products array
// Build products array with correct image path
$products = [];
foreach ($rows as $row) {
    $imagePath = '';
    if (!empty($row['image'])) {
        // Always use the path as stored in the DB, just ensure it starts with a slash for web use
        $imagePath = '/' . ltrim($row['image'], '/');
    }
    $products[] = [
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'price' => $row['price_at_purchase'],
        'image' => $imagePath,
        'description' => $row['product_description'],
    ];
}
echo json_encode(['order' => $order, 'products' => $products]);
exit;
