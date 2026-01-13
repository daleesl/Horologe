<?php
session_start();

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../helpers/id_generator.php';
require_once __DIR__ . '/../../classes/cart/CartService.php';
require_once __DIR__ . '/../../classes/products/ProductRepository.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'You must be signed in to place an order.']);
    exit;
}

$cart = new CartService();
$items = $cart->getItems();

// Limit to items selected on the cart page if provided.
$selectedIds = isset($_SESSION['checkout_selected_ids']) && is_array($_SESSION['checkout_selected_ids'])
    ? $_SESSION['checkout_selected_ids']
    : [];

if (!empty($selectedIds)) {
    $selectedMap = array_flip($selectedIds);
    $items = array_values(array_filter($items, static function ($item) use ($selectedMap) {
        return isset($selectedMap[(string) ($item['id'] ?? '')]);
    }));
}

if (empty($items)) {
    http_response_code(400);
    echo json_encode(['error' => 'No items to checkout.']);
    exit;
}

// Gather shipping/contact payload
$firstName = trim((string)($_POST['firstName'] ?? ''));
$lastName = trim((string)($_POST['lastName'] ?? ''));
$address = trim((string)($_POST['address'] ?? ''));
$city = trim((string)($_POST['city'] ?? ''));
$postal = trim((string)($_POST['postalCode'] ?? ''));
$country = trim((string)($_POST['country'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$paymentMethod = (string)($_POST['payment_method'] ?? 'PAYPAL');

$shipName = trim($firstName . ' ' . $lastName);


$orderIds = [];
$grandTotal = 0.0;

foreach ($items as $item) {
    $watchId = (string) ($item['id'] ?? '');
    $qty = (int) ($item['quantity'] ?? 0);
    $price = (float) ($item['price'] ?? 0);
    $lineTotal = $qty * $price;
    $grandTotal += $lineTotal;

 
    $productRepo = new ProductRepository($conn);
    $product = $productRepo->getById($watchId);
    $currentStock = isset($product['stock']) ? (int)$product['stock'] : 0;
    if ($qty > $currentStock) {
        http_response_code(400);
        echo json_encode(['error' => 'Not enough stock for product: ' . ($item['name'] ?? $watchId)]);
        exit;
    }
    $newStock = $currentStock - $qty;
    $productRepo->updateStock($watchId, $newStock);

    $orderId = generateId($conn, 'orders', 'order_id', 'ORD', 4);
    $orderIds[] = $orderId;

    $stmt = $conn->prepare("INSERT INTO orders (
            order_id, total_amount, user_id, user_name, user_email, user_phone,
            watch_id, product_name, product_description, quantity, price_at_purchase,
            ship_full_name, ship_phone_number, ship_street_address, ship_city, ship_province_state, ship_postal_code
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to prepare order insert.']);
        exit;
    }

    $empty = '';
    $productName = (string) ($item['name'] ?? '');
    $productDescription = '';
    $userPhone = '';
    $shipPhone = '';
    $province = $country !== '' ? $country : 'N/A';

    $stmt->bind_param(
        'sdsssssssidssssss',
        $orderId,
        $lineTotal,
        $userId,
        $shipName,
        $email,
        $userPhone,
        $watchId,
        $productName,
        $productDescription,
        $qty,
        $price,
        $shipName,
        $shipPhone,
        $address,
        $city,
        $province,
        $postal
    );

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save order.']);
        exit;
    }
    $stmt->close();
}


$_SESSION['pending_clear_ids'] = array_map(static function ($item) {
    return (string) ($item['id'] ?? '');
}, $items);
unset($_SESSION['checkout_selected_ids']);


$_SESSION['last_order'] = [
    'order_id' => $orderIds[0] ?? '',
    'order_date' => date('Y-m-d H:i:s'),
    'total' => $grandTotal,
    'payment_method' => $paymentMethod,
    'items' => array_map(static function ($item) {
        return [
            'id' => (string) ($item['id'] ?? ''),
            'name' => (string) ($item['name'] ?? ''),
            'category' => (string) ($item['category'] ?? ''),
            'price' => (float) ($item['price'] ?? 0),
            'quantity' => (int) ($item['quantity'] ?? 0),
            'image' => (string) ($item['image'] ?? ''),
        ];
    }, $items),
];

echo json_encode(['success' => true, 'order_id' => $orderIds[0] ?? '']);
