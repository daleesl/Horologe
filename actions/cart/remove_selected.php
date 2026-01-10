<?php
require_once __DIR__ . '/../../classes/cart/CartService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$ids = [];
if (isset($_POST['selected_ids'])) {
    $raw = (string) $_POST['selected_ids'];
    $ids = array_values(array_filter(array_map('trim', explode(',', $raw)), static function ($v) {
        return $v !== '';
    }));
} elseif (isset($_SESSION['checkout_selected_ids']) && is_array($_SESSION['checkout_selected_ids'])) {
    $ids = $_SESSION['checkout_selected_ids'];
}

$cart = new CartService();

foreach ($ids as $id) {
    $cart->remove($id);
}

// Clear the selection after removal
unset($_SESSION['checkout_selected_ids']);

echo json_encode([
    'success' => true,
    'cart' => [
        'items' => $cart->getItems(),
        'summary' => $cart->getSummary(),
    ],
]);
