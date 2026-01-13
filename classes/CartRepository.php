<?php
require_once __DIR__ . '/ProductRepository.php';

class CartRepository
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function getCartIdByUser(string $userId): ?string
    {
        $stmt = $this->conn->prepare('SELECT cart_id FROM cart WHERE user_id = ? LIMIT 1');
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res['cart_id'] ?? null;
    }

    public function createCart(string $userId): string
    {
        $cartId = uniqid('cart_', true);
        $stmt = $this->conn->prepare('INSERT INTO cart (cart_id, user_id) VALUES (?, ?)');
        $stmt->bind_param('ss', $cartId, $userId);
        $stmt->execute();
        return $cartId;
    }

    public function getOrCreateCartId(string $userId): string
    {
        $existing = $this->getCartIdByUser($userId);
        if ($existing) {
            return $existing;
        }
        return $this->createCart($userId);
    }

    public function getItems(string $userId): array
    {
        $stmt = $this->conn->prepare(
            'SELECT ci.cart_item_id, ci.watch_id, ci.quantity, ci.subtotal, w.brand, w.model, w.price, w.image_file
             FROM cartitems ci
             INNER JOIN cart c ON ci.cart_id = c.cart_id
             INNER JOIN watch w ON ci.watch_id = w.watch_id
             WHERE c.user_id = ?'
        );
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC) ?: [];
    }

    public function findItem(string $cartId, string $watchId): ?array
    {
        $stmt = $this->conn->prepare('SELECT cart_item_id, quantity, subtotal FROM cartitems WHERE cart_id = ? AND watch_id = ? LIMIT 1');
        $stmt->bind_param('ss', $cartId, $watchId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ?: null;
    }

    public function insertItem(string $cartId, string $watchId, int $quantity, float $price): void
    {
        $cartItemId = uniqid('ci_', true);
        $subtotal = $price * $quantity;
        $stmt = $this->conn->prepare('INSERT INTO cartitems (cart_item_id, watch_id, cart_id, quantity, subtotal) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssid', $cartItemId, $watchId, $cartId, $quantity, $subtotal);
        $stmt->execute();
    }

    public function updateItem(string $cartItemId, int $quantity, float $price): void
    {
        $subtotal = $price * $quantity;
        $stmt = $this->conn->prepare('UPDATE cartitems SET quantity = ?, subtotal = ? WHERE cart_item_id = ?');
        $stmt->bind_param('ids', $quantity, $subtotal, $cartItemId);
        $stmt->execute();
    }

    public function removeItem(string $cartId, string $watchId): void
    {
        $stmt = $this->conn->prepare('DELETE FROM cartitems WHERE cart_id = ? AND watch_id = ?');
        $stmt->bind_param('ss', $cartId, $watchId);
        $stmt->execute();
    }

    public function clearCart(string $cartId): void
    {
        $stmt = $this->conn->prepare('DELETE FROM cartitems WHERE cart_id = ?');
        $stmt->bind_param('s', $cartId);
        $stmt->execute();
    }

    public function updateCartTotal(string $cartId): void
    {
        $stmt = $this->conn->prepare('SELECT COALESCE(SUM(subtotal), 0) AS total FROM cartitems WHERE cart_id = ?');
        $stmt->bind_param('s', $cartId);
        $stmt->execute();
        $stmt->get_result()->fetch_assoc();
    }
}
