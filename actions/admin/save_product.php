<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../helpers/id_generator.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$watchId = isset($_POST['watch_id']) ? trim((string) $_POST['watch_id']) : '';
$brand = isset($_POST['brand']) ? trim((string) $_POST['brand']) : '';
$model = isset($_POST['model']) ? trim((string) $_POST['model']) : '';
$price = isset($_POST['price']) ? (float) $_POST['price'] : 0;
$stock = isset($_POST['stock_quantity']) ? (int) $_POST['stock_quantity'] : 0;
$description = isset($_POST['description']) ? trim((string) $_POST['description']) : '';
$currentImage = isset($_POST['current_image']) ? trim((string) $_POST['current_image']) : '';

if ($brand === '' || $model === '' || $price < 0 || $stock < 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid fields']);
    exit;
}


$imagePath = $currentImage;
if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
    $uploadDir = __DIR__ . '/../../assets/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['image']['name']));
    $targetName = uniqid('prod_', true) . '_' . $safeName;
    $targetPath = $uploadDir . '/' . $targetName;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload image']);
        exit;
    }
  
    $imagePath = 'assets/uploads/' . $targetName;
}

if ($watchId === '') {
 
    $watchId = generateId($conn, 'watch', 'watch_id', 'W', 3);
    $stmt = $conn->prepare("INSERT INTO watch (watch_id, brand, model, stock_quantity, price, description, image_file) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed']);
        exit;
    }
    $stmt->bind_param('sssidss', $watchId, $brand, $model, $stock, $price, $description, $imagePath);
    $ok = $stmt->execute();
    $stmt->close();
    if (!$ok) {
        http_response_code(500);
        echo json_encode(['error' => 'Insert failed']);
        exit;
    }
} else {
 
    $stmt = $conn->prepare("UPDATE watch SET brand = ?, model = ?, stock_quantity = ?, price = ?, description = ?, image_file = ? WHERE watch_id = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed']);
        exit;
    }
    $stmt->bind_param('ssidsss', $brand, $model, $stock, $price, $description, $imagePath, $watchId);
    $ok = $stmt->execute();
    $stmt->close();
    if (!$ok) {
        http_response_code(500);
        echo json_encode(['error' => 'Update failed']);
        exit;
    }
}

echo json_encode(['success' => true, 'watch_id' => $watchId, 'image' => $imagePath]);
