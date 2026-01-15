<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/sign-in.php");
    exit();
}

require_once __DIR__ . '/../config/connect.php';

$stats = [
    'total' => 0,
    'active' => 0,
    'inactive' => 0
];

$res = $conn->query("
    SELECT 
        COUNT(*) AS total,
        SUM(status = 'active') AS active,
        SUM(status = 'inactive') AS inactive
    FROM users
    WHERE role = 'user'
");

if ($res) {
    $row = $res->fetch_assoc();
    $stats['total'] = (int)$row['total'];
    $stats['active'] = (int)$row['active'];
    $stats['inactive'] = (int)$row['inactive'];
}

$users = [];
$sql = "
    SELECT 
        u.user_id,
        u.fname,
        u.lname,
        u.email,
        u.phone_number,
        u.status,
        u.created_at,
        (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.user_id) AS order_count,
        (SELECT COALESCE(SUM(total_amount),0) FROM orders o WHERE o.user_id = u.user_id) AS total_spent
    FROM users u
    WHERE role = 'user'
    ORDER BY u.created_at DESC
";

$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
}

function money($v) {
    return '$' . number_format($v, 2);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Customers - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'EB Garamond', serif; }
        .table td { vertical-align: middle; }
        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background-color: #2b2b2b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-black text-white">
<div class="d-flex flex-column flex-md-row">

<?php include '../includes/adminSidebar.php'; ?>

<div class="flex-grow-1 w-100 p-3 p-sm-4">

    <div class="d-md-none mb-3">
        <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas">
            <i class="bi bi-list"></i> Menu
        </button>
    </div>

    <div class="mb-4">
        <h1 class="display-5 fw-normal mb-2">Customers</h1>
        <p class="text-secondary">Manage customer accounts and activity</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="border border-secondary rounded p-3">
                <p class="text-secondary small mb-1">Total Customers</p>
                <h3 class="fw-normal mb-0"><?= $stats['total'] ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border border-secondary rounded p-3">
                <p class="text-secondary small mb-1">Active</p>
                <h3 class="fw-normal mb-0 text-success"><?= $stats['active'] ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border border-secondary rounded p-3">
                <p class="text-secondary small mb-1">Inactive</p>
                <h3 class="fw-normal mb-0 text-danger"><?= $stats['inactive'] ?></h3>
            </div>
        </div>
    </div>

    <div class="border border-secondary rounded overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="border-bottom border-secondary">
                    <tr>
                        <th class="text-secondary fw-normal py-3">User</th>
                        <th class="text-secondary fw-normal py-3">Contact</th>
                        <th class="text-secondary fw-normal py-3">Status</th>
                        <th class="text-secondary fw-normal py-3">Orders</th>
                        <th class="text-secondary fw-normal py-3">Total Spent</th>
                        <th class="text-secondary fw-normal py-3">Joined</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($users): ?>
                    <?php foreach ($users as $u): ?>
                        <?php
                        $initials = strtoupper(substr($u['fname'],0,1) . substr($u['lname'],0,1));
                        ?>
                        <tr class="border-bottom border-secondary">
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar"><?= $initials ?></div>
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($u['fname'].' '.$u['lname']) ?></div>
                                        <div class="text-secondary small"><?= htmlspecialchars($u['user_id']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="small"><?= htmlspecialchars($u['email']) ?></div>
                                <div class="text-secondary small"><?= htmlspecialchars($u['phone_number']) ?></div>
                            </td>
                            <td class="py-3">
                                <span class="badge <?= $u['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= strtoupper($u['status']) ?>
                                </span>
                            </td>
                            <td class="py-3"><?= (int)$u['order_count'] ?></td>
                            <td class="py-3"><?= money((float)$u['total_spent']) ?></td>
                            <td class="py-3"><?= date('n/j/Y', strtotime($u['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">
                            No users found.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>