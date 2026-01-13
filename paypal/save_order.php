<?php
date_default_timezone_set('Asia/Manila');
ob_start();
header('Content-Type: application/json');


error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/paypal_error.log');


session_start();
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../helpers/id_generator.php';
require_once __DIR__ . '/../classes/products/ProductRepository.php';
require_once __DIR__ . '/../classes/cart/CartRepository.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$userID = $_SESSION['userID'] ?? $_SESSION['user_id'] ?? null;
if (!$userID) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit;
}

$firstName     = trim($input['firstName'] ?? '');
$lastName      = trim($input['lastName'] ?? '');
$city          = trim($input['city'] ?? '');
$postalCode    = trim($input['postalCode'] ?? '');
$country       = trim($input['country'] ?? '');
$address       = trim($input['address'] ?? '');
$email         = trim($input['email'] ?? $_SESSION['email'] ?? '');
$paymentMethod = trim($input['paymentMethod'] ?? 'PAYPAL');
$paymentID     = trim($input['paymentID'] ?? '');
$cart          = $input['cart'] ?? [];

if (!$firstName || !$lastName || !$address || !$city || !$postalCode || !$email || !$paymentID || empty($cart)) {
    echo json_encode(['success' => false, 'error' => 'Missing checkout data']);
    exit;
}

$total = 0;
foreach ($cart as $item) {
    $total += $item['quantity'] * $item['price'];
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

function sendReceiptEmail($toEmail, $name, $orderID, $cart, $total, $address, $paymentMethod)
{
    try {
        $mail = new PHPMailer(true);

        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str, $level) {
            error_log("SMTP: $str");
        };

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'horologeofficial@gmail.com';
        $mail->Password   = 'bfqd jlls oftv jsyp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('horologeofficial@gmail.com', 'Horologe');
        $mail->addAddress($toEmail, $name);

        $mail->isHTML(true);
        $mail->Subject = "Horologe Receipt - Order #$orderID";

        $template = file_get_contents(__DIR__ . '/receipt_email.html');

        $itemsTable = '';
        $itemCount  = 0;

        foreach ($cart as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $itemCount += $item['quantity'];

            $itemsTable .= "
                <tr>
                    <td>{$item['quantity']}x {$item['name']}</td>
                    <td>$" . number_format($itemTotal, 2) . "</td>
                </tr>";
        }

        $mail->Body = str_replace(
            [
                '{{DATE}}', '{{TIME}}', '{{ORDER_ID}}', '{{CUSTOMER_NAME}}',
                '{{EMAIL}}', '{{ADDRESS}}', '{{ITEM_ROWS}}', '{{ITEM_COUNT}}',
                '{{PAYMENT_METHOD}}', '{{TOTAL}}'
            ],
            [
                date('F d, Y'),
                date('h:i A'),
                $orderID,
                $name,
                $toEmail,
                $address,
                $itemsTable,
                $itemCount,
                $paymentMethod,
                number_format($total, 2)
            ],
            $template
        );

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('MAIL ERROR: ' . $e->getMessage());
        return false;
    }
}

$order_id = generateId($conn, 'orders', 'order_id', 'ORD', 6);
$fullName = $firstName . ' ' . $lastName;

try {
    $conn->begin_transaction();

    // ORDER
    $stmt = $conn->prepare("
        INSERT INTO orders (
            order_id, user_id, user_name, user_email,
            ship_full_name, ship_street_address, ship_city,
            ship_province_state, ship_postal_code,
            total_amount, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "sssssssssd",
        $order_id, $userID, $fullName, $email,
        $fullName, $address, $city, $country, $postalCode, $total
    );
    $stmt->execute();
    $stmt->close();

    // PAYMENT
    $paymentStmt = $conn->prepare("
        INSERT INTO payment (
            payment_id, payment_method, payment_status,
            payment_date, amount, order_id
        ) VALUES (?, ?, 'COMPLETED', NOW(), ?, ?)
    ");
    $paymentStmt->bind_param("ssds", $paymentID, $paymentMethod, $total, $order_id);
    $paymentStmt->execute();
    $paymentStmt->close();

    // INSERT INTO order_items
    $orderItemsStmt = $conn->prepare("
    INSERT INTO order_items 
    (order_id, watch_id, product_name, price_at_purchase, quantity)
    VALUES (?, ?, ?, ?, ?)
    ");

    if (!$orderItemsStmt) {
        throw new Exception("Failed to prepare order_items statement: " . $conn->error);
    }

    foreach ($cart as $item) {
        $prodId = (string)($item['id'] ?? $item['watch_id'] ?? '');
        $name   = $item['name'] ?? '';
        $price  = (float)($item['price'] ?? 0);
        $qty    = (int)($item['quantity'] ?? 0);

        if ($prodId === '' || $qty <= 0) continue;

        $orderItemsStmt->bind_param(
            "sssid",
            $order_id,
            $prodId,
            $name,
            $price,
            $qty
        );
        $orderItemsStmt->execute();
        }

    $orderItemsStmt->close();

    $lockStmt = $conn->prepare("SELECT stock_quantity FROM watch WHERE watch_id = ? FOR UPDATE");
    $updateStmt = $conn->prepare("UPDATE watch SET stock_quantity = ? WHERE watch_id = ?");
    if (!$lockStmt || !$updateStmt) {
        throw new Exception('Failed to prepare stock statements');
    }

    $updatedStocks = [];
    foreach ($cart as $item) {
        $prodId = (string)($item['id'] ?? $item['watch_id'] ?? '');
        $qty = max(0, (int)($item['quantity'] ?? 0));
        if ($prodId === '' || $qty <= 0) continue;

        // Lock row
        $lockStmt->bind_param('s', $prodId);
        $lockStmt->execute();
        $res = $lockStmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if (!$row) {
            throw new Exception("Product not found: $prodId");
        }
        $current = (int)$row['stock_quantity'];
        if ($qty > $current) {
            throw new Exception("Insufficient stock for product $prodId");
        }

        $newStock = $current - $qty;
        if ($newStock < 0) {
            throw new Exception("Stock would become negative for $prodId");
        }

        $updateStmt->bind_param('is', $newStock, $prodId);
        $ok = $updateStmt->execute();
        if (!$ok) {
            throw new Exception("Failed to update stock for $prodId");
        }

        $updatedStocks[$prodId] = $newStock;
    }

    $lockStmt->close();
    $updateStmt->close();

    try {
        $cartRepo = new CartRepository($conn);
        $userCartId = $cartRepo->getCartIdByUserId($userID);
        if ($userCartId) {
            foreach ($cart as $item) {
                $prodId = (string)($item['id'] ?? $item['watch_id'] ?? '');
                if ($prodId === '') continue;
                $cartRepo->removeCartItem($userCartId, $prodId);
            }
        }
    } catch (Throwable $e) {
        throw $e;
    }

    $conn->commit();

    try {
        $_SESSION['last_order'] = [
            'items' => $cart,
            'total' => $total,
            'order_id' => $order_id
        ];
        $ids = [];
        foreach ($cart as $item) {
            $ids[] = (string)($item['id'] ?? $item['watch_id'] ?? '');
        }
        $_SESSION['pending_clear_ids'] = array_values(array_filter($ids, function($v){ return $v !== ''; }));
    } catch (Throwable $e) {
        error_log('SESSION SAVE ERROR: ' . $e->getMessage());
    }

    $emailSent = sendReceiptEmail(
        $email,
        $fullName,
        $order_id,
        $cart,
        $total,
        $address,
        $paymentMethod
    );

    echo json_encode([
        'success'   => true,
        'order_id'  => $order_id,
        'total'     => $total,
        'emailSent' => $emailSent,
        'updatedStocks' => $updatedStocks
    ]);
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
