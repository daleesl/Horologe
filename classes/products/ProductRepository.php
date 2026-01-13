<?php
// Data access for products
class ProductRepository
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }


    public function getAll(): array
    {
        $sql = "SELECT watch_id AS id, brand, model, CONCAT(brand, ' ', model) AS name, brand AS category, price, image_file AS image, description, stock_quantity AS stock FROM watch ORDER BY watch_id DESC";
        return $this->normalizeList($this->fetchAll($sql));
    }


    // Fetch a limited set for featured display.
   
    public function getFeatured(int $limit = 4): array
    {
        $sql = "SELECT watch_id AS id, brand, model, CONCAT(brand, ' ', model) AS name, brand AS category, price, image_file AS image, description, stock_quantity AS stock FROM watch ORDER BY watch_id DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $limit);
        if (!$stmt->execute()) {
            return [];
        }
        $result = $stmt->get_result();
        return $this->normalizeList($result ? $result->fetch_all(MYSQLI_ASSOC) : []);
    }

  
     //Fetch single product by id.
    
    public function getById(string $id): ?array
    {
        $sql = "SELECT watch_id AS id, brand, model, CONCAT(brand, ' ', model) AS name, brand AS category, price, image_file AS image, description, stock_quantity AS stock FROM watch WHERE watch_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('s', $id);
        if (!$stmt->execute()) {
            return null;
        }
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        return $row ? $this->normalizeRow($row) : null;
    }

  
     // Fetch related products by brand, excluding the current one.
 
    public function getRelated(string $brand, string $excludeId, int $limit = 4): array
    {
        $sql = "SELECT watch_id AS id, brand, model, CONCAT(brand, ' ', model) AS name, brand AS category, price, image_file AS image, description, stock_quantity AS stock FROM watch WHERE brand = ? AND watch_id != ? ORDER BY watch_id DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('ssi', $brand, $excludeId, $limit);
        if (!$stmt->execute()) {
            return [];
        }
        $result = $stmt->get_result();
        return $this->normalizeList($result ? $result->fetch_all(MYSQLI_ASSOC) : []);
    }


    private function fetchAll(string $sql): array
    {
        $result = $this->conn->query($sql);
        if (!$result) {
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function normalizeList(array $rows): array
    {
        return array_map(function ($row) {
            return $this->normalizeRow($row);
        }, $rows);
    }


    private function normalizeRow(array $row): array
    {
        $row['stock'] = isset($row['stock']) ? (int) $row['stock'] : 0;
        $row['price'] = isset($row['price']) ? (float) $row['price'] : 0.0;
        if (!empty($row['image'])) {
            $img = (string) $row['image'];
            $alreadyPrefixed = str_starts_with($img, '../');
            if (strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0 && !str_starts_with($img, '/') && !$alreadyPrefixed) {
                $img = '../' . ltrim($img, '/');
            }
            $row['image'] = $img;
        }
        return $row;
    }

        public function updateStock(string $id, int $newStock): bool
    {
        $stmt = $this->conn->prepare('UPDATE watch SET stock_quantity = ? WHERE watch_id = ?');
        if (!$stmt) return false;
        $stmt->bind_param('is', $newStock, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
