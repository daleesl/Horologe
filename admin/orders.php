<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'EB Garamond', serif;
        }
    </style>
</head>

<body class="bg-black text-white">
    <div class="d-flex flex-column flex-md-row">
        <!-- Sidebar -->
        <?php include '../includes/adminSidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 w-100 p-3 p-sm-4">
            <div class="d-md-none mb-3">
                <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas">
                    <i class="bi bi-list"></i> Menu
                </button>
            </div>
            <!-- Search Bar -->
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control bg-dark border-secondary text-white" placeholder="Search by order ID or customer name...">
                </div>
            </div>

            <?php
            require_once __DIR__ . '/../config/connect.php';
            $orders = [];
            $sql = "SELECT o.order_id, o.user_name, o.user_email, o.product_name, o.total_amount, o.order_date, p.payment_status
                    FROM orders o
                    LEFT JOIN payment p ON p.order_id = o.order_id
                    ORDER BY o.order_date DESC";
            $res = $conn->query($sql);
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $orders[] = $row;
                }
            }
            function moneyFormat(float $value): string { return '$' . number_format($value, 2, '.', ','); }
            ?>
            <!-- Orders Table -->
            <div class="border border-secondary rounded overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead class="border-bottom border-secondary">
                            <tr>
                                <th scope="col" class="text-secondary fw-normal py-3">Order ID</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Customer</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Product</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Total</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Date</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Status</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)) : ?>
                                <?php foreach ($orders as $order) : ?>
                                    <?php
                                    $orderDate = $order['order_date'] ? date('n/j/Y', strtotime($order['order_date'])) : '--';
                                    $status = strtolower($order['payment_status'] ?? '');
                                    $badgeClass = 'bg-secondary';
                                    if ($status === 'completed' || $status === 'paid') {
                                        $badgeClass = 'bg-success';
                                    } elseif ($status === 'pending') {
                                        $badgeClass = 'bg-warning text-dark';
                                    }
                                    ?>
                                    <tr class="border-bottom border-secondary">
                                        <td class="text-white fw-semibold py-4"><?= htmlspecialchars($order['order_id']); ?></td>
                                        <td class="py-4">
                                            <div class="text-white fw-semibold mb-1"><?= htmlspecialchars($order['user_name']); ?></div>
                                            <div class="text-secondary small"><?= htmlspecialchars($order['user_email']); ?></div>
                                        </td>
                                        <td class="text-white py-4"><?= htmlspecialchars($order['product_name']); ?></td>
                                        <td class="text-white fw-semibold py-4"><?= moneyFormat((float)$order['total_amount']); ?></td>
                                        <td class="text-white py-4"><?= htmlspecialchars($orderDate); ?></td>
                                        <td class="py-4">
                                            <span class="badge <?= $badgeClass; ?>">
                                                <?= $status ? htmlspecialchars(ucfirst($status)) : 'Unpaid'; ?>
                                            </span>
                                        </td>
                                        <td class="py-4">
                                            <button class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
                                                <i class="bi bi-eye"></i>
                                                <span>View</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="7" class="text-center text-secondary py-4">No orders found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>


