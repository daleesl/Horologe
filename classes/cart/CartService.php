<?php
// Session-based cart manager. Keeps data sourced from the database via ProductService.
require_once __DIR__ . '/CartRepository.php';
require_once __DIR__ . '/../../config/connect.php';

class CartService
{
    private $repo;
    private $userId;
    private $cartId;
    private $useDb = false;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->userId = $_SESSION['user_id'] ?? null;
        $this->repo = new CartRepository($GLOBALS['conn']);
        if ($this->userId) {
            $this->useDb = true;
            $this->cartId = $this->repo->getCartIdByUserId($this->userId);
            if (!$this->cartId) {
                $this->cartId = uniqid('cart_');
                $this->repo->createCart($this->userId, $this->cartId);
            }
        } else {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
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
        if ($this->useDb) {
            $subtotal = ((float)($product['price'] ?? 0)) * $quantity;
            $this->repo->addOrUpdateCartItem($this->cartId, $id, $quantity, $subtotal);
        } else {
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
    }

    /**
     * Update quantity; remove item if quantity <= 0.
     */
    public function updateQuantity(string $productId, int $quantity): void
    {
        if ($productId === '') {
            return;
        }
        if ($this->useDb) {
            if ($quantity <= 0) {
                $this->repo->removeCartItem($this->cartId, $productId);
                return;
            }
            // Fetch product price for subtotal
            $productRepo = new \ProductRepository($GLOBALS['conn']);
            $product = $productRepo->getById($productId);
            $price = isset($product['price']) ? (float)$product['price'] : 0.0;
            $subtotal = $price * $quantity;
            $this->repo->updateCartItemQuantity($this->cartId, $productId, $quantity, $subtotal);
        } else {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
                return;
            }
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
        }
    }

    public function remove(string $productId): void
    {
        if ($this->useDb) {
            $this->repo->removeCartItem($this->cartId, $productId);
        } else {
            unset($_SESSION['cart'][$productId]);
        }
    }

    public function clear(): void
    {
        if ($this->useDb) {
            $this->repo->clearCart($this->cartId);
        } else {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getItems(): array
    {
        if ($this->useDb) {
            $items = $this->repo->getCartItems($this->cartId);
            // Optionally, join with product table for details
            return $items;
        } else {
            return array_values($_SESSION['cart']);
        }
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
