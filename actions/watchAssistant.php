<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli("127.0.0.1", "root", "", "horologe");
if ($conn->connect_error) {
    echo json_encode(['ok' => false, 'reply' => 'Database connection failed']);
    exit;
}

$OLLAMA_API_URL = 'http://127.0.0.1:11434/api/chat';
$MODEL = 'qwen3:4b';
$OLLAMA_TIMEOUT = 60;
$NO_DATA_RESPONSE = "I do not have enough information to answer that.";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'reply' => 'Invalid request']);
    exit;
}

$userMsg = trim($_POST['prompt'] ?? $_POST['message'] ?? '');
if ($userMsg === '') {
    echo json_encode(['ok' => false, 'reply' => 'Empty message']);
    exit;
}
$msgNorm = strtolower($userMsg);
$msgNorm = preg_replace('/[^a-z0-9\s]/', '', $msgNorm);
$msgNorm = trim(preg_replace('/\s+/', ' ', $msgNorm));

$dbOutput = '';


if (preg_match('/\bmy name\b|\bwho am i\b/', $msgNorm)) {
    if (!isset($_SESSION['user_id'])) {
        $dbOutput = "You are not currently logged in.";
    } else {
        $stmt = $conn->prepare("SELECT fname, lname FROM users WHERE user_id=?");
        $stmt->bind_param("s", $_SESSION['user_id']);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($res) {
            $dbOutput = "Your name is {$res['fname']} {$res['lname']}.";
        }
    }
}

elseif (preg_match('/order history|my orders|show my orders/', $msgNorm)) {
    if (!isset($_SESSION['user_id'])) {
        $dbOutput = "Please log in to view your order history.";
    } else {
        $stmt = $conn->prepare("
            SELECT order_id, order_date, total_amount
            FROM orders
            WHERE user_id=?
            ORDER BY order_date DESC
            LIMIT 5
        ");
        $stmt->bind_param("s", $_SESSION['user_id']);
        $stmt->execute();
        $orders = $stmt->get_result();
        $stmt->close();

        if ($orders->num_rows === 0) {
            $dbOutput = "You have not placed any orders yet.";
        } else {
            $dbOutput = "Here is your recent order history:\n";
            while ($o = $orders->fetch_assoc()) {
                $dbOutput .= "\nOrder ID: {$o['order_id']}\n";
                $dbOutput .= "Date: {$o['order_date']}\n";
                $dbOutput .= "Total: $" . number_format($o['total_amount'], 2) . "\n";

                $items = $conn->prepare("
                    SELECT product_name, quantity, price_at_purchase
                    FROM order_items
                    WHERE order_id=?
                ");
                $items->bind_param("s", $o['order_id']);
                $items->execute();
                $resItems = $items->get_result();

                while ($i = $resItems->fetch_assoc()) {
                    $dbOutput .= "- {$i['product_name']} x{$i['quantity']} ($" .
                        number_format($i['price_at_purchase'], 2) . ")\n";
                }
                $items->close();
            }
        }
    }
}

elseif (preg_match('/available brands|what brands|list brands|brands available|brands in horologe|what are the brands/', $msgNorm)) {
    $res = $conn->query("SELECT DISTINCT brand FROM watch ORDER BY brand");

    if ($res->num_rows > 0) {
        $dbOutput = "The available brands in Horologe are:\n";
        while ($r = $res->fetch_assoc()) {
            $dbOutput .= "- {$r['brand']}\n";
        }
    }
}

else {
    $brands = [];
    $res = $conn->query("SELECT DISTINCT brand FROM watch");
    while ($r = $res->fetch_assoc()) {
        $brands[] = strtolower($r['brand']);
    }

    foreach ($brands as $brand) {
        if (strpos($msgNorm, $brand) !== false) {
            $stmt = $conn->prepare("
                SELECT model, price, stock_quantity
                FROM watch
                WHERE LOWER(brand)=?
                ORDER BY model
            ");
            $stmt->bind_param("s", $brand);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $dbOutput = ucfirst($brand) . " models available:\n";
                $i = 1;
                while ($w = $result->fetch_assoc()) {
                    $dbOutput .= "{$i}. {$w['model']}\n";
                    $dbOutput .= "   Price: $" . number_format($w['price'], 2) . "\n";
                    $dbOutput .= "   Stock: {$w['stock_quantity']} available\n";
                    $i++;
                }
            }
            break;
        }
    }

    if ($dbOutput === '') {
        $stmt = $conn->prepare("
            SELECT brand, model, description, price, stock_quantity
            FROM watch
        ");
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();

        while ($w = $res->fetch_assoc()) {

            $modelNorm = strtolower($w['model']);
            $modelNorm = preg_replace('/[^a-z0-9\s]/', '', $modelNorm);

            $words = explode(" ", $modelNorm);
            $matchCount = 0;

            foreach ($words as $word) {
                if (strlen($word) > 3 && strpos($msgNorm, $word) !== false) {
                    $matchCount++;
                }
            }

            if ($matchCount >= 1) {
                $dbOutput  = "{$w['brand']} {$w['model']} details:\n";
                $dbOutput .= "Description: {$w['description']}\n";
                $dbOutput .= "Price: $" . number_format($w['price'], 2) . "\n";
                $dbOutput .= "Stock: {$w['stock_quantity']} available\n";
                break;
            }
        }
    }
}
if ($dbOutput === '') {
    echo json_encode(['ok' => true, 'reply' => $NO_DATA_RESPONSE]);
    exit;
}

$userNameContext = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT fname FROM users WHERE user_id=?");
    $stmt->bind_param("s", $_SESSION['user_id']);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($u) {
        $userNameContext = "The logged-in user is {$u['fname']}.\n\n";
    }
}

$payload = json_encode([
    'model' => $MODEL,
    'stream' => false,
    'messages' => [
        [
            'role' => 'system',
            'content' =>
                "You are a luxury watch assistant.\n" .
                "Answer ONLY using the data below.\n" .
                "Do NOT add or guess any information.\n\n" .
                $userNameContext .
                "DATA:\n$dbOutput"
        ],
        ['role' => 'user', 'content' => $userMsg]
    ]
]);

$ch = curl_init($OLLAMA_API_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => $OLLAMA_TIMEOUT
]);

$result = curl_exec($ch);
curl_close($ch);

$response = json_decode($result, true);
$reply = $response['message']['content'] ?? $dbOutput;

echo json_encode(['ok' => true, 'reply' => trim($reply)]);
$conn->close();
