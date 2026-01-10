<?php
// CartRepository: Handles DB operations for persistent user carts
class CartRepository {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCartIdByUserId($userId) {
        $stmt = $this->conn->prepare('SELECT cart_id FROM cart WHERE user_id = ? LIMIT 1');
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $cartId = null;
        $stmt->bind_result($cartId);
        if ($stmt->fetch()) {
            $stmt->close();
            return $cartId;
        }
        $stmt->close();
        return null;
    }

    public function createCart($userId, $cartId) {
        $stmt = $this->conn->prepare('INSERT INTO cart (cart_id, user_id) VALUES (?, ?)');
        $stmt->bind_param('ss', $cartId, $userId);
        $stmt->execute();
        $stmt->close();
    }

    public function getCartItems($cartId) {
        $sql = 'SELECT ci.cart_item_id, ci.watch_id, ci.quantity, ci.subtotal, w.brand, w.model, w.description, w.price, w.stock_quantity, w.image_file
                FROM cartitems ci
                JOIN watch w ON ci.watch_id = w.watch_id
                WHERE ci.cart_id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $cartId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            // Map DB fields to expected cart item keys
            $items[] = [
                'id' => $row['watch_id'],
                'name' => $row['brand'] . ' ' . $row['model'],
                'category' => $row['brand'],
                'description' => $row['description'],
                'price' => $row['price'],
                'stock' => $row['stock_quantity'],
                'image' => $row['image_file'],
                'quantity' => $row['quantity'],
                'subtotal' => $row['subtotal'],
            ];
        }
        $stmt->close();
        return $items;
    }

    public function addOrUpdateCartItem($cartId, $watchId, $quantity, $subtotal) {
        // Try update first
        $stmt = $this->conn->prepare('UPDATE cartitems SET quantity = quantity + ?, subtotal = subtotal + ? WHERE cart_id = ? AND watch_id = ?');
        $stmt->bind_param('idss', $quantity, $subtotal, $cartId, $watchId);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            $stmt->close();
            // Insert if not exists
            $cartItemId = uniqid('ci_');
            $stmt = $this->conn->prepare('INSERT INTO cartitems (cart_item_id, watch_id, cart_id, quantity, subtotal) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssii', $cartItemId, $watchId, $cartId, $quantity, $subtotal);
            $stmt->execute();
        }
        $stmt->close();
    }

    public function removeCartItem($cartId, $watchId) {
        $stmt = $this->conn->prepare('DELETE FROM cartitems WHERE cart_id = ? AND watch_id = ?');
        $stmt->bind_param('ss', $cartId, $watchId);
        $stmt->execute();
        $stmt->close();
    }

    public function clearCart($cartId) {
        $stmt = $this->conn->prepare('DELETE FROM cartitems WHERE cart_id = ?');
        $stmt->bind_param('s', $cartId);
        $stmt->execute();
        $stmt->close();
    }
}
