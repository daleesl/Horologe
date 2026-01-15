<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/sign-in.php");
    exit();
}

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

$resOrders = $conn->query('SELECT COALESCE(SUM(total_amount),0) AS revenue, COUNT(*) AS orders, COALESCE(AVG(total_amount),0) AS avg_order FROM orders');
if ($resOrders) {
    $row = $resOrders->fetch_assoc();
    $overview['revenue'] = (float)($row['revenue'] ?? 0);
    $overview['orders'] = (int)($row['orders'] ?? 0);
    $overview['avg_order'] = (float)($row['avg_order'] ?? 0);
}

$resUsers = $conn->query("SELECT COUNT(*) AS total, SUM(status = 'active') AS active FROM users");
if ($resUsers) {
    $row = $resUsers->fetch_assoc();
    $overview['customers'] = (int)($row['total'] ?? 0);
    $overview['active_customers'] = (int)($row['active'] ?? 0);
}

$resStock = $conn->query('SELECT COALESCE(SUM(stock_quantity),0) AS total_stock, SUM(stock_quantity <= 5) AS low_stock FROM watch');
if ($resStock) {
    $row = $resStock->fetch_assoc();
    $overview['stock'] = (int)($row['total_stock'] ?? 0);
    $overview['low_stock'] = (int)($row['low_stock'] ?? 0);
}

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

$recentOrders = [];
$sqlRecent = "
    SELECT 
        o.order_id, 
        CONCAT(u.fname, ' ', u.lname) AS user_name,
        u.email AS user_email,
        o.total_amount, 
        o.order_date,
        CASE 
            WHEN COUNT(oi.watch_id) = 1 THEN CONCAT(w.brand, ' ', w.model)
            ELSE 'Multiple Items'
        END AS product_name
    FROM orders o
    LEFT JOIN users u ON u.user_id = o.user_id
    LEFT JOIN order_items oi ON oi.order_id = o.order_id
    LEFT JOIN watch w ON w.watch_id = oi.watch_id
    GROUP BY o.order_id, u.fname, u.lname, u.email, o.total_amount, o.order_date
    ORDER BY o.order_date DESC
    LIMIT 5
";

$resRecent = $conn->query($sqlRecent);
if ($resRecent) {
    while ($row = $resRecent->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}

$topProducts = [];
$sqlTop = "
    SELECT w.watch_id, CONCAT(w.brand, ' ', w.model) AS product_name, SUM(oi.quantity) AS units_sold, SUM(oi.quantity * oi.price_at_purchase) AS revenue
    FROM order_items oi
    JOIN watch w ON oi.watch_id = w.watch_id
    GROUP BY w.watch_id, product_name
    ORDER BY units_sold DESC, revenue DESC
    LIMIT 3
";
$resTop = $conn->query($sqlTop);
if ($resTop) {
    while ($row = $resTop->fetch_assoc()) {
        $topProducts[] = $row;
    }
}

$overview['sms_inbox'] = 0;

$resSms = $conn->query("
    SELECT COUNT(*) AS total
    FROM sms
    WHERE direction = 'incoming'
");

if ($resSms) {
    $row = $resSms->fetch_assoc();
    $overview['sms_inbox'] = (int)($row['total'] ?? 0);
}

function moneyFormat(float $value): string
{
    return '$' . number_format($value, 2, '.', ',');
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
            background: #101014;
            color: #f5f5f7;
            font-family: 'EB Garamond', serif;
        }

        .dashboard-card {
            background: linear-gradient(135deg, #181824 80%, #23233a 100%);
            border: 1px solid #23233a;
            border-radius: 1.25rem;
            box-shadow: 0 2px 16px 0 rgba(0, 0, 0, 0.12);
            padding: 1.5rem 1.2rem;
            min-height: 120px;
            transition: box-shadow 0.2s, border 0.2s;
        }

        .dashboard-card:hover {
            box-shadow: 0 6px 32px 0 rgba(0, 198, 255, 0.10);
            border-color: #d1d3d4;
        }

        .dashboard-label {
            color: #c9c9d3;
            font-size: 0.95rem;
            letter-spacing: 0.08em;
            font-weight: 500;
        }

        .dashboard-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #cdd1d2;
        }

        .dashboard-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 1.2rem;
            letter-spacing: 0.08em;
        }

        .dashboard-table th,
        .dashboard-table td {
            background: transparent !important;
            color: #f5f5f7;
            font-size: 1rem;
        }

        .dashboard-table th {
            font-weight: 600;
            color: #fff;
        }

        .dashboard-table tr {
            border-bottom: 1px solid #23233a;
        }

        .dashboard-badge {
            border-radius: 1rem;
            font-size: 0.95rem;
            padding: 0.4em 1.1em;
        }

        .dashboard-card .bi {
            font-size: 1.1rem;
            vertical-align: -0.2em;
        }

        .display-6,
        h2,
        h3,
        h4,
        h5 {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            margin-bottom: 0.5rem !important;
        }

        .dashboard-value,
        .dashboard-label {
            margin-bottom: 0.2rem;
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column flex-md-row">
        <?php include '../includes/adminSidebar.php'; ?>

        <div class="flex-grow-1 w-100 p-3 p-sm-4 p-lg-5">
            <div class="d-md-none mb-3">
                <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas">
                    <i class="bi bi-list"></i> Menu
                </button>
            </div>
            <div class="mb-5">
                <h1 class="display-5 text-white">Dashboard</h1>
                <p class="text-secondary">Welcome to your admin dashboard</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-card h-100">
                        <div class="dashboard-label mb-2"><i class="bi bi-cash-coin me-2"></i>TOTAL REVENUE</div>
                        <div class="dashboard-value mb-1"><?= moneyFormat($overview['revenue']); ?></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-card h-100">
                        <div class="dashboard-label mb-2"><i class="bi bi-bag-check me-2"></i>TOTAL ORDERS</div>
                        <div class="dashboard-value mb-1"><?= htmlspecialchars($overview['orders']); ?></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-card h-100">
                        <div class="dashboard-label mb-2"><i class="bi bi-people me-2"></i>TOTAL CUSTOMERS</div>
                        <div class="dashboard-value mb-1"><?= htmlspecialchars($overview['customers']); ?></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-card h-100">
                        <div class="dashboard-label mb-2"><i class="bi bi-cash-coin me-2"></i>TOTAL INCOMING SMS</div>
                        <div class="dashboard-value mb-1"><?= htmlspecialchars($overview['sms_inbox']); ?></div>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <h3 class="text-white text-uppercase fw-bold mb-4">Order Overview</h3>
                <div class="row g-4 d-flex justify-content-start">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="dashboard-card h-100 text-center">
                            <p class="dashboard-label text-uppercase small mb-3">All Orders</p>
                            <h2 class="dashboard-value fw-bold text-white mb-0"><?= htmlspecialchars($overview['orders']); ?></h2>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="dashboard-card h-100 text-center">
                            <p class="dashboard-label text-uppercase small mb-3">Avg Order Value</p>
                            <h2 class="dashboard-value fw-bold text-white mb-0"><?= moneyFormat($overview['avg_order']); ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <div class="row mb-5 align-items-center">
                    <div class="col-lg-6">
                        <h3 class="text-white text-uppercase fw-bold mb-0">Recent Orders</h3>
                    </div>
                    <div class="col-lg-6 text-lg-end mt-2 mt-lg-0">
                        <a href="orders.php"
                        class="text-white text-decoration-none fs-6 header font-primary">
                            VIEW ALL ORDERS <span>→</span>
                        </a>
                    </div>
                </div>

                <div class="card bg-dark border-0 shadow rounded-4">
                    <div class="card-body p-0 border border-secondary rounded overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0 rounded-4 overflow-hidden">
                                <thead>
                                    <tr class="border-bottom border-secondary">
                                        <th class="text-white py-3">Order ID</th>
                                        <th class="text-white py-3">Customer</th>
                                        <th class="text-white py-3">Product</th>
                                        <th class="text-white py-3">Total</th>
                                        <th class="text-white py-3">Date</th>
                             
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
                </div>
            </div>

            <div class="mb-5">
                <div class="row mb-5 align-items-center">
                    <div class="col-lg-6">
                        <h3 class="text-white text-uppercase fw-bold mb-0">Top Selling Products</h3>
                    </div>
                    <div class="col-lg-6 text-lg-end mt-2 mt-lg-0">
                        <a href="products.php"
                        class="text-white text-decoration-none fs-6 header font-primary">
                            VIEW ALL PRODUCTS <span>→</span>
                        </a>
                    </div>
                </div>
                <div class="row g-4">
                    <?php if (!empty($topProducts)) : ?>
                        <?php $rank = 1;
                        foreach ($topProducts as $product) : ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="dashboard-card h-100 d-flex flex-column align-items-center text-center p-4">
                                    <h4 class="text-white fw-bold mb-2 w-100 mt-2"><?= htmlspecialchars($product['product_name']); ?></h4>
                                    <p class="text-white mb-2">Units Sold: <span class="fw-bold"><?= (int)($product['units_sold'] ?? 0); ?></span></p>
                                    <p class="text-success mb-0 fw-semibold">
                                        <i class="bi bi-arrow-up"></i> Revenue: <?= moneyFormat((float)($product['revenue'] ?? 0)); ?>
                                    </p>
                                </div>
                            </div>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="col-12">
                            <div class="card bg-dark border-0 shadow rounded-4 p-4 text-center text-secondary">No sales data yet.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
</body>

</html>