<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: /Horologe/auth/sign-in.php");
    exit();
}

$path = $_SERVER['REQUEST_URI'];

$isAdmin = $_SESSION['role'] === 'admin';
$isAdminPage = str_contains($path, '/admin/');
$isPublicPage = str_contains($path, '/public/');

if ($isAdmin && !$isAdminPage) {
    header("Location: /Horologe/admin/adminDashboard.php");
    exit();
}

if (!$isAdmin && $isAdminPage) {
    header("Location: /Horologe/public/index.php");
    exit();
}
