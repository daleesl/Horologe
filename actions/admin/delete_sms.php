<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/sign-in.php");
    exit();
}

require_once __DIR__ . '/../../config/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['sms_id'])) {
    $sms_id = $_POST['sms_id'];

    $stmt = $conn->prepare("DELETE FROM sms WHERE id = ?");
    $stmt->bind_param("i", $sms_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../../admin/sms.php?success=1");
        exit();
    } else {
        $stmt->close();
        header("Location: ../../admin/sms.php?error=1");
        exit();
    }
} else {
    header("Location: ../../admin/sms.php?error=1");
    exit();
}
