<?php
// Session-based cart manager. Keeps data sourced from the database via ProductService.
class CartService
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Add or increment a product in the cart.
     * @param array<string,mixed> $product
     */
    public function addProduct(array $product, int $quantity = 1): void
    {
        $id = (string) ($product['id'] ?? '');
        if ($id === '' || $quantity <= 0) {
            return;
        }

        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = [
                'id' => $id,
                'name' => (string) ($product['name'] ?? ''),
                'category' => (string) ($product['category'] ?? ''),
                'price' => (float) ($product['price'] ?? 0),
                'image' => (string) ($product['image'] ?? ''),
                'quantity' => 0,
            ];
        }

        $_SESSION['cart'][$id]['quantity'] += $quantity;
    }

    /**
     * Update quantity; remove item if quantity <= 0.
     */
    public function updateQuantity(string $productId, int $quantity): void
    {
        if ($productId === '') {
            return;
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
            return;
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
    }

    public function remove(string $productId): void
    {
        unset($_SESSION['cart'][$productId]);
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getItems(): array
    {
        return array_values($_SESSION['cart']);
    }

    /**
     * @return array{items:int, unique:int, subtotal:float}
     */
    public function getSummary(): array
    {
        $items = $this->getItems();
        $subtotal = 0.0;
        $count = 0;

        foreach ($items as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            $subtotal += ($price * $qty);
            $count += $qty;
        }

        return [
            'items' => $count,
            'unique' => count($items),
            'subtotal' => $subtotal,
        ];
    }
}
