<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../config/connect.php';

// Search logic
$search = $_GET['search'] ?? '';
$searchSql = '';
$types = '';
$params = [];

if (!empty($search)) {
    $searchSql = "WHERE phone_number LIKE CONCAT('%', ?, '%') OR message LIKE CONCAT('%', ?, '%')";
    $types = "ss";
    $params = [$search, $search];
}

// Query SMS
$sql = "
    SELECT direction, phone_number, message, created_at
    FROM sms
    $searchSql
    ORDER BY created_at DESC
";

$stmt = $conn->prepare($sql);
if (!empty($searchSql)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$res = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SMS Inbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'EB Garamond', serif;
        }
    </style>
</head>

<body class="bg-dark text-white">
<div class="d-flex">

    <?php include '../includes/adminSidebar.php'; ?>

    <div class="flex-grow-1 p-4">
        <h1 class="fw-bold mb-4">SMS Inbox</h1>

        <form method="GET" class="mb-3">
            <input type="text" name="search" class="form-control w-50 d-inline" placeholder="Search phone or text..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-light ms-2 bg">Search</button>
        </form>

        <div class="card bg-black border border-secondary rounded-4">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Direction</th>
                            <th>Phone</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($res->num_rows > 0): ?>
                        <?php while ($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                                <td><span class="badge <?= $row['direction'] === 'incoming' ? 'bg-success' : 'bg-primary' ?>"><?= strtoupper($row['direction']) ?></span></td>
                                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                                <td><?= htmlspecialchars($row['message']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-secondary">No SMS Found</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>