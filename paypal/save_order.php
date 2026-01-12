<?php
date_default_timezone_set('Asia/Manila');
ob_start();
header('Content-Type: application/json');

// ==================================================
// ERROR LOGGING
// ==================================================
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/paypal_error.log');

// ==================================================
// SESSION + DB
// ==================================================
session_start();
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../helpers/id_generator.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// ==================================================
// USER VALIDATION
// ==================================================
$userID = $_SESSION['userID'] ?? $_SESSION['user_id'] ?? null;
if (!$userID) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// ==================================================
// READ INPUT
// ==================================================
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit;
}

// ==================================================
// EXTRACT DATA
// ==================================================
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

// ==================================================
// CALCULATE TOTAL
// ==================================================
$total = 0;
foreach ($cart as $item) {
    $total += $item['quantity'] * $item['price'];
}

// ==================================================
// EMAIL FUNCTION (GMAIL SAFE CONFIG)
// ==================================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

function sendReceiptEmail($toEmail, $name, $orderID, $cart, $total, $address, $paymentMethod)
{
    try {
        $mail = new PHPMailer(true);

        // 🔍 DEBUG (writes to paypal_error.log)
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str, $level) {
            error_log("SMTP: $str");
        };

        // ✅ GMAIL SAFE SETTINGS
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

        // Load receipt template
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

// ==================================================
// SAVE ORDER
// ==================================================
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

    $conn->commit();

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
        'emailSent' => $emailSent
    ]);
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
