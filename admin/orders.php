<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/sign-in.php");
    exit();
}

require_once __DIR__ . '/../config/connect.php';

function moneyFormat(float $v): string {
    return '$' . number_format($v, 2);
}

$overview = [
    'revenue' => 0,
    'orders'  => 0
];

$res = $conn->query("
    SELECT 
        COALESCE(SUM(total_amount), 0) AS revenue,
        COUNT(*) AS orders
    FROM orders
");

if ($res) {
    $row = $res->fetch_assoc();
    $overview['revenue'] = (float) $row['revenue'];
    $overview['orders']  = (int) $row['orders'];
}

$sort = $_GET['sort'] ?? 'newest';

$orderBy = match ($sort) {
    'oldest'  => 'o.order_date ASC',
    'highest' => 'o.total_amount DESC',
    'lowest'  => 'o.total_amount ASC',
    default   => 'o.order_date DESC'
};

$sql = "
    SELECT 
        o.order_id,
        o.user_name,
        o.user_email,
        o.total_amount,
        o.order_date,
        (
            SELECT COUNT(*) 
            FROM order_items 
            WHERE order_id = o.order_id
        ) AS items_count,
        (
            SELECT w.model
            FROM order_items oi
            JOIN watch w ON oi.watch_id = w.watch_id
            WHERE oi.order_id = o.order_id
            LIMIT 1
        ) AS first_product
    FROM orders o
    ORDER BY $orderBy
";

$orders = [];
$res = $conn->query($sql);

if ($res instanceof mysqli_result) {
    $orders = $res->fetch_all(MYSQLI_ASSOC);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Orders - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #101014;
            color: #f5f5f7;
            font-family: 'EB Garamond', serif;
        }

        .dashboard-card {
            background: linear-gradient(135deg, #181824 80%, #23233a);
            border: 1px solid #23233a;
            border-radius: 1.25rem;
            padding: 1.5rem;
        }

        .dashboard-label {
            color: #c9c9d3;
            font-size: 0.9rem;
            letter-spacing: 0.08em;
        }

        .dashboard-value {
            font-size: 1.4rem;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-black text-white">
<div class="d-flex flex-column flex-md-row">

    <?php include '../includes/adminSidebar.php'; ?>

    <div class="flex-grow-1 p-4">

        <h1 class="display-5 mb-2">Orders</h1>
        <p class="text-secondary">Monitor and manage customer orders</p>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-label">
                        <i class="bi bi-cash-coin me-2"></i>TOTAL REVENUE
                    </div>
                    <div class="dashboard-value text-success">
                        <?= moneyFormat($overview['revenue']) ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-label">
                        <i class="bi bi-bag-check me-2"></i>TOTAL ORDERS
                    </div>
                    <div class="dashboard-value text-success">
                        <?= $overview['orders'] ?>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary">
                    <i class="bi bi-funnel"></i>
                </span>
                <select
                    name="sort"
                    class="form-select bg-dark border-secondary text-white"
                    onchange="this.form.submit()"
                >
                    <option value="">Sort orders by</option>
                    <option value="newest"  <?= $sort === 'newest'  ? 'selected' : '' ?>>Newest first</option>
                    <option value="oldest"  <?= $sort === 'oldest'  ? 'selected' : '' ?>>Oldest first</option>
                    <option value="highest" <?= $sort === 'highest' ? 'selected' : '' ?>>Highest total</option>
                    <option value="lowest"  <?= $sort === 'lowest'  ? 'selected' : '' ?>>Lowest total</option>
                </select>
            </div>
        </form>

        <div class="border border-secondary rounded overflow-hidden">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if ($orders): ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="fw-semibold">
                                    <?= htmlspecialchars($o['order_id']) ?>
                                </td>

                                <td>
                                    <div><?= htmlspecialchars($o['user_name']) ?></div>
                                    <div class="text-secondary small">
                                        <?= htmlspecialchars($o['user_email']) ?>
                                    </div>
                                </td>

                                <td>
                                    <?= $o['items_count'] == 1
                                        ? htmlspecialchars($o['first_product'])
                                        : $o['items_count'] . ' Items'
                                    ?>
                                </td>

                                <td class="fw-semibold">
                                    <?= moneyFormat($o['total_amount']) ?>
                                </td>

                                <td>
                                    <?= date('n/j/Y', strtotime($o['order_date'])) ?>
                                </td>

                                <td>
                                    <button class="btn btn-sm btn-outline-light view-order-btn">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">
                                No orders found.
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