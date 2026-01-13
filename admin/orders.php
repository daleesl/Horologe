<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders - Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <?php include '../includes/adminSidebar.php'; ?>

    <div class="flex-grow-1 w-100 p-3 p-sm-4">

        <div class="d-md-none mb-3">
            <button class="btn btn-outline-light" type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#adminSidebarOffcanvas">
                <i class="bi bi-list"></i> Menu
            </button>
        </div>

        <div class="mb-4">
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-secondary">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control bg-dark border-secondary text-white"
                       placeholder="Search by order ID or customer name...">
            </div>
        </div>

        <?php
        require_once __DIR__ . '/../config/connect.php';

        $orders = [];

        if (!isset($conn) || !$conn) {
            die('<div class="alert alert-danger">Database connection failed.</div>');
        }

        $sql = "
            SELECT 
                order_id,
                user_name,
                user_email,
                total_amount,
                order_date
            FROM orders
            ORDER BY order_date DESC
        ";

        $res = $conn->query($sql);

        if ($res instanceof mysqli_result) {
            while ($row = $res->fetch_assoc()) {
                $orders[] = $row;
            }
            $res->free();
        }

        function moneyFormat(float $value): string {
            return '$' . number_format($value, 2);
        }
        ?>

        <div class="border border-secondary rounded overflow-hidden">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead class="border-bottom border-secondary">
                    <tr>
                        <th class="text-secondary fw-normal py-3">Order ID</th>
                        <th class="text-secondary fw-normal py-3">Customer</th>
                        <th class="text-secondary fw-normal py-3">Product</th>
                        <th class="text-secondary fw-normal py-3">Total</th>
                        <th class="text-secondary fw-normal py-3">Date</th>
                        <th class="text-secondary fw-normal py-3"></th>
                        <th class="text-secondary fw-normal py-3">Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="border-bottom border-secondary">
                                <td class="fw-semibold py-4">
                                    <?= htmlspecialchars($order['order_id'] ?? '') ?>
                                </td>

                                <td class="py-4">
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($order['user_name'] ?? '') ?>
                                    </div>
                                    <div class="text-secondary small">
                                        <?= htmlspecialchars($order['user_email'] ?? '') ?>
                                    </div>
                                </td>

                                <td class="py-4">Multiple Items</td>

                                <td class="fw-semibold py-4">
                                    <?= moneyFormat((float)($order['total_amount'] ?? 0)) ?>
                                </td>

                                <td class="py-4">
                                    <?= !empty($order['order_date'])
                                        ? date('n/j/Y', strtotime($order['order_date']))
                                        : '--' ?>
                                </td>

                                <td></td>

                                <td class="py-4">
                                    <button class="btn btn-sm btn-outline-light d-flex align-items-center gap-1 view-order-btn">
                                        <i class="bi bi-eye"></i>
                                        <span>View</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-4">
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

<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-black text-white border border-secondary rounded-4">

            <div class="modal-header border-0">
                <h4 class="modal-title fw-bold">Order Details</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-2">Order Information</h6>
                        <div>Order ID: <span id="modalOrderId"></span></div>
                        <div>Date: <span id="modalOrderDate"></span></div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold mb-2">Customer Information</h6>
                        <div id="modalCustomerName" class="fw-semibold"></div>
                        <div id="modalCustomerEmail"></div>
                        <div id="modalCustomerPhone"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold mb-2">Shipping Address</h6>
                    <div id="modalShippingAddress">N/A</div>
                </div>

                <h6 class="fw-bold mb-2">Products</h6>

                <div class="table-responsive">
                    <table class="table table-dark table-bordered align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody id="modalProductsTable"></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.view-order-btn').forEach(btn => {
    btn.addEventListener('click', () => {

        const orderId = btn.closest('tr').children[0].textContent.trim();

        fetch('/Horologe/actions/admin/order_details_api.php?order_id=' + encodeURIComponent(orderId))
            .then(res => res.json())
            .then(data => {

                if (!data || data.error) {
                    alert('Order details not found.');
                    return;
                }

                document.getElementById('modalOrderId').textContent = data.order.order_id;
                document.getElementById('modalOrderDate').textContent =
                    data.order.order_date
                        ? new Date(data.order.order_date).toLocaleDateString()
                        : '--';

                document.getElementById('modalCustomerName').textContent = data.order.user_name;
                document.getElementById('modalCustomerEmail').textContent = data.order.user_email;
                document.getElementById('modalShippingAddress').textContent = data.order.shipping_address || 'N/A';

                let html = '';
                let total = 0;

                data.products.forEach(p => {
                    const img = p.image
                        ? '/Horologe/' + p.image
                        : 'https://via.placeholder.com/60x60?text=No+Image';

                    const line = p.price * p.quantity;
                    total += line;

                    html += `
                        <tr>
                            <td><img src="${img}" class="rounded" style="max-width:60px"></td>
                            <td>${p.product_name}</td>
                            <td>${p.description || ''}</td>
                            <td>${p.quantity}</td>
                            <td>$${p.price.toFixed(2)}</td>
                            <td>$${line.toFixed(2)}</td>
                        </tr>
                    `;
                });

                html += `
                    <tr>
                        <td colspan="5" class="fw-bold text-end">Total</td>
                        <td class="fw-bold">$${total.toFixed(2)}</td>
                    </tr>
                `;

                document.getElementById('modalProductsTable').innerHTML = html;

                new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
            })
            .catch(err => {
                console.error(err);
                alert('Failed to fetch order details.');
            });
    });
});
</script>
</body>
</html>