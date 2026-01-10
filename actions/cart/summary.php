<?php
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../classes/cart/CartService.php';

header('Content-Type: application/json');

$cart = new CartService();

echo json_encode([
    'success' => true,
    'summary' => $cart->getSummary(),
]);
