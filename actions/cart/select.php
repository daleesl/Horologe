<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw = isset($_POST['selected_ids']) ? (string) $_POST['selected_ids'] : '';
$ids = array_values(array_filter(array_map('trim', explode(',', $raw)), static function ($v) {
    return $v !== '';
}));

$_SESSION['checkout_selected_ids'] = $ids;

echo json_encode(['success' => true, 'selected' => $ids]);
