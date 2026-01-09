<?php
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../classes/cart/CartService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$productId = isset($_POST['product_id']) ? trim((string) $_POST['product_id']) : '';

if ($productId === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product']);
    exit;
}

$cart = new CartService();
$cart->remove($productId);

echo json_encode([
    'success' => true,
    'cart' => [
        'items' => $cart->getItems(),
        'summary' => $cart->getSummary(),
    ],
]);
