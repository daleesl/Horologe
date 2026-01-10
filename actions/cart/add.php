<?php
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../classes/products/ProductRepository.php';
require_once __DIR__ . '/../../classes/products/ProductService.php';
require_once __DIR__ . '/../../classes/cart/CartService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$productId = isset($_POST['product_id']) ? trim((string) $_POST['product_id']) : '';
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

if ($productId === '' || $quantity <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product or quantity']);
    exit;
}

$productService = new ProductService(new ProductRepository($conn));
$product = $productService->getProduct($productId);

if (!$product) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit;
}

$stock = isset($product['stock']) ? (int) $product['stock'] : 0;
if ($stock <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Product is out of stock']);
    exit;
}

if ($quantity > $stock) {
    http_response_code(400);
    echo json_encode(['error' => 'Requested quantity exceeds available stock']);
    exit;
}

$cart = new CartService();
$cart->addProduct($product, $quantity);

echo json_encode([
    'success' => true,
    'cart' => [
        'items' => $cart->getItems(),
        'summary' => $cart->getSummary(),
    ],
]);
