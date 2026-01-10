<?php
require_once __DIR__ . '/../config/connect.php';

$overview = [
    'revenue' => 0.0,
    'orders' => 0,
    'avg_order' => 0.0,
    'customers' => 0,
    'active_customers' => 0,
    'stock' => 0,
    'low_stock' => 0,
    'completed' => 0,
    'pending' => 0,
];

// Revenue, total orders, average order value
$resOrders = $conn->query('SELECT COALESCE(SUM(total_amount),0) AS revenue, COUNT(*) AS orders, COALESCE(AVG(total_amount),0) AS avg_order FROM orders');
if ($resOrders) {
    $row = $resOrders->fetch_assoc();
    $overview['revenue'] = (float)($row['revenue'] ?? 0);
    $overview['orders'] = (int)($row['orders'] ?? 0);
    $overview['avg_order'] = (float)($row['avg_order'] ?? 0);
}

// Customers
$resUsers = $conn->query("SELECT COUNT(*) AS total, SUM(status = 'active') AS active FROM users");
if ($resUsers) {
    $row = $resUsers->fetch_assoc();
    $overview['customers'] = (int)($row['total'] ?? 0);
    $overview['active_customers'] = (int)($row['active'] ?? 0);
}

// Inventory
$resStock = $conn->query('SELECT COALESCE(SUM(stock_quantity),0) AS total_stock, SUM(stock_quantity <= 5) AS low_stock FROM watch');
if ($resStock) {
    $row = $resStock->fetch_assoc();
    $overview['stock'] = (int)($row['total_stock'] ?? 0);
    $overview['low_stock'] = (int)($row['low_stock'] ?? 0);
}

// Payment status counts
$resPayments = $conn->query('SELECT LOWER(payment_status) AS status, COUNT(*) AS cnt FROM payment GROUP BY LOWER(payment_status)');
if ($resPayments) {
    while ($row = $resPayments->fetch_assoc()) {
        $status = $row['status'] ?? '';
        $count = (int)($row['cnt'] ?? 0);
        if ($status === 'completed' || $status === 'paid') {
            $overview['completed'] += $count;
        } elseif ($status === 'pending') {
            $overview['pending'] += $count;
        }
    }
}

// Recent orders
$recentOrders = [];
$sqlRecent = "SELECT o.order_id, o.user_name, o.user_email, o.product_name, o.total_amount, o.order_date, p.payment_status
              FROM orders o
              LEFT JOIN payment p ON p.order_id = o.order_id
              ORDER BY o.order_date DESC
              LIMIT 5";
$resRecent = $conn->query($sqlRecent);
if ($resRecent) {
    while ($row = $resRecent->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}

// Top selling products
$topProducts = [];
$sqlTop = "SELECT o.product_name, o.watch_id, SUM(o.quantity) AS units_sold, SUM(o.total_amount) AS revenue
           FROM orders o
           GROUP BY o.watch_id, o.product_name
           ORDER BY units_sold DESC, revenue DESC
           LIMIT 3";
$resTop = $conn->query($sqlTop);
if ($resTop) {
    while ($row = $resTop->fetch_assoc()) {
        $topProducts[] = $row;
    }
}

function moneyFormat(float $value): string
{
    return '₱' . number_format($value, 2, '.', ',');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'EB Garamond', serif;
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column flex-md-row">
        <!-- Sidebar -->
        <?php include '../includes/adminSidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 w-100 p-3 p-sm-4 p-lg-5">
            <div class="d-md-none mb-3">
                <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas">
                    <i class="bi bi-list"></i> Menu
                </button>
            </div>
            <!-- Header -->
            <div class="mb-5">
                <h1 class="display-5 fw-bold text-white">Dashboard</h1>
                <p class="text-secondary">Welcome to your admin dashboard</p>
            </div>

            <!-- Key Metrics Section -->
            <div class="row g-4 mb-5">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border border-secondary p-4">
                        <p class="text-secondary text-uppercase small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-cash-coin"></i> Total Revenue
                        </p>
                        <h2 class="display-6 fw-bold text-white"><?= moneyFormat($overview['revenue']); ?></h2>
                        <p class="text-secondary small mt-2">Based on recorded orders</p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border border-secondary p-4">
                        <p class="text-secondary text-uppercase small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-bag-check"></i> Total Orders
                        </p>
                        <h2 class="display-6 fw-bold text-white"><?= htmlspecialchars($overview['orders']); ?></h2>
                        <p class="text-info small mt-2">
                            <i class="bi bi-clock"></i> <?= htmlspecialchars($overview['pending']); ?> pending payments
                        </p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border border-secondary p-4">
                        <p class="text-secondary text-uppercase small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-people"></i> Total Customers
                        </p>
                        <h2 class="display-6 fw-bold text-white"><?= htmlspecialchars($overview['customers']); ?></h2>
                        <p class="text-success small mt-2">
                            <i class="bi bi-person-plus"></i> <?= htmlspecialchars($overview['active_customers']); ?> active
                        </p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border border-secondary p-4">
                        <p class="text-secondary text-uppercase small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam"></i> In Stock
                        </p>
                        <h2 class="display-6 fw-bold text-white"><?= htmlspecialchars($overview['stock']); ?></h2>
                        <p class="text-warning small mt-2">
                            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($overview['low_stock']); ?> low stock
                        </p>
                    </div>
                </div>
            </div>

            <!-- Orders Section -->
            <div class="mb-5">
                <h3 class="text-white text-uppercase fw-bold mb-4">Order Overview</h3>
                <div class="row g-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="border border-secondary p-4 text-center">
                            <p class="text-secondary text-uppercase small mb-3">All Orders</p>
                            <h2 class="display-5 fw-bold text-white"><?= htmlspecialchars($overview['orders']); ?></h2>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="border border-secondary p-4 text-center">
                            <p class="text-secondary text-uppercase small mb-3">Completed</p>
                            <h2 class="display-5 fw-bold text-white"><?= htmlspecialchars($overview['completed']); ?></h2>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="border border-secondary p-4 text-center">
                            <p class="text-secondary text-uppercase small mb-3">Pending</p>
                            <h2 class="display-5 fw-bold text-white"><?= htmlspecialchars($overview['pending']); ?></h2>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="border border-secondary p-4 text-center">
                            <p class="text-secondary text-uppercase small mb-3">Avg Order Value</p>
                            <h2 class="display-5 fw-bold text-white"><?= moneyFormat($overview['avg_order']); ?></h2>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Recent Orders -->
            <div class="mb-5">
                <h3 class="text-white text-uppercase fw-bold mb-4">Recent Orders</h3>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr class="border-bottom border-secondary">
                                <th class="text-white py-3">Order ID</th>
                                <th class="text-white py-3">Customer</th>
                                <th class="text-white py-3">Product</th>
                                <th class="text-white py-3">Total</th>
                                <th class="text-white py-3">Date</th>
                                <th class="text-white py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentOrders)) : ?>
                                <?php foreach ($recentOrders as $order) : ?>
                                    <?php
                                    $status = strtolower($order['payment_status'] ?? '');
                                    $badgeClass = 'bg-secondary';
                                    if ($status === 'completed' || $status === 'paid') {
                                        $badgeClass = 'bg-success';
                                    } elseif ($status === 'pending') {
                                        $badgeClass = 'bg-warning text-dark';
                                    }
                                    $orderDate = $order['order_date'] ?? '';
                                    $dateFormatted = $orderDate ? date('n/j/Y', strtotime($orderDate)) : '--';
                                    ?>
                                    <tr class="border-bottom border-secondary">
                                        <td class="text-white fw-semibold py-4"><?= htmlspecialchars($order['order_id']); ?></td>
                                        <td class="py-4">
                                            <div class="text-white fw-semibold"><?= htmlspecialchars($order['user_name']); ?></div>
                                            <div class="text-secondary small"><?= htmlspecialchars($order['user_email']); ?></div>
                                        </td>
                                        <td class="text-white py-4"><?= htmlspecialchars($order['product_name']); ?></td>
                                        <td class="text-white fw-semibold py-4"><?= moneyFormat((float)($order['total_amount'] ?? 0)); ?></td>
                                        <td class="text-white py-4"><?= htmlspecialchars($dateFormatted); ?></td>
                                        <td class="py-4">
                                            <span class="badge <?= $badgeClass; ?>">
                                                <?= $status ? htmlspecialchars(ucfirst($status)) : 'Unpaid'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">No orders yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Products -->
            <div class="mb-5">
                <h3 class="text-white text-uppercase fw-bold mb-4">Top Selling Products</h3>
                <div class="row g-4">
                    <?php if (!empty($topProducts)) : ?>
                        <?php $rank = 1; foreach ($topProducts as $product) : ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="border border-secondary p-4">
                                    <p class="text-secondary text-uppercase small mb-2"><?= htmlspecialchars($rank); ?><?= $rank === 1 ? 'st' : ($rank === 2 ? 'nd' : 'rd'); ?> Place</p>
                                    <h4 class="text-white fw-bold mb-2"><?= htmlspecialchars($product['product_name']); ?></h4>
                                    <p class="text-white mb-3"><?= (int)($product['units_sold'] ?? 0); ?> units sold</p>
                                    <p class="text-success">
                                        <i class="bi bi-arrow-up"></i> Revenue: <?= moneyFormat((float)($product['revenue'] ?? 0)); ?>
                                    </p>
                                </div>
                            </div>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="col-12">
                            <div class="border border-secondary p-4 text-center text-secondary">No sales data yet.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
</body>
</html>
