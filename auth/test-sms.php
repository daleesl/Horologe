<?php
require_once __DIR__ . '/../sms/sms.php';

$debug = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = $_POST['phone'] ?? '';
    $msg = $_POST['message'] ?? '';

    $normalized = normalizePHPhoneNumber($raw);

    $sent = sendSMS($raw, $msg);

    if ($sent) {
        $status = "SMS Sent to $normalized";
    } else {
        $status = "Failed to send SMS";
    }

    $debug = "Raw: $raw → Normalized: $normalized";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>SMS Test</title>
<style>
body { font-family: Arial; max-width: 420px; margin:2rem auto; }
input, textarea { width:100%; padding:7px; margin-bottom:10px; }
button { padding:10px; width:100%; font-weight:bold; }
pre { background:#eee; padding:10px; font-size:14px; }
</style>
</head>
<body>
<h2>SMS Sending Test</h2>

<form method="POST">
    <label>Phone</label>
    <input type="text" name="phone" placeholder="09xxxxxxxxx" required>

    <label>Message</label>
    <textarea name="message" rows="3" placeholder="Hello from SMS test!" required></textarea>

    <button type="submit">Send SMS</button>
</form>

<?php if($status): ?>
    <p><strong><?php echo $status; ?></strong></p>
<?php endif; ?>

<?php if($debug): ?>
    <pre><?php echo $debug; ?></pre>
<?php endif; ?>

</body>
</html>
