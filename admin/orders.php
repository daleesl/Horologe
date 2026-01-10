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
            $sql = "SELECT o.order_id, o.user_name, o.user_email, o.product_name, o.total_amount, o.order_date
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
                                <th scope="col" class="text-secondary fw-normal py-3"></th>
                                <th scope="col" class="text-secondary fw-normal py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)) : ?>
                                <?php foreach ($orders as $order) : ?>
                                    <?php
                                    $orderDate = $order['order_date'] ? date('n/j/Y', strtotime($order['order_date'])) : '--';
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
                                            <!-- Status column removed -->
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

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-black text-white border border-secondary rounded-4">
            <div class="modal-header border-0">
                <h4 class="modal-title fw-bold" id="orderDetailsModalLabel">Order Details</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h6 class="fw-bold mb-2">Order Information</h6>
                        <div>Order ID: <span class="fw-semibold" id="modalOrderId"></span></div>
                        <div>Date: <span id="modalOrderDate"></span></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-2">Customer Information</h6>
                        <div class="fw-semibold" id="modalCustomerName"></div>
                        <div id="modalCustomerEmail"></div>
                        <div id="modalCustomerPhone"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold mb-2">Shipping Address</h6>
                    <div id="modalShippingAddress">N/A</div>
                </div>
                <div>
                    <h6 class="fw-bold mb-2">Products</h6>
                    <div class="table-responsive">
                        <table class="table table-dark table-bordered align-middle mb-0 rounded-3 overflow-hidden">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="modalProductsTable">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Attach event listeners to all view buttons
document.querySelectorAll('button.btn-outline-light').forEach((btn) => {
    btn.addEventListener('click', function() {
        const tr = btn.closest('tr');
        const orderId = tr.querySelector('td').textContent.trim();
        fetch('../actions/admin/order_details_api.php?order_id=' + encodeURIComponent(orderId))
            .then(res => res.json())
            .then(data => {
                if (!data || data.error) {
                    alert('Order details not found.');
                    return;
                }
                document.getElementById('modalOrderId').textContent = data.order.order_id;
                document.getElementById('modalOrderDate').textContent = data.order.order_date ? new Date(data.order.order_date).toLocaleDateString() : '--';
                document.getElementById('modalCustomerName').textContent = data.order.user_name;
                document.getElementById('modalCustomerEmail').textContent = data.order.user_email;
                document.getElementById('modalCustomerPhone').textContent = data.order.user_phone || '';
                document.getElementById('modalShippingAddress').textContent = data.order.shipping_address || 'N/A';
                // Products
                let productsHtml = '';
                let total = 0;
                data.products.forEach(prod => {
                    let imgSrc = 'https://via.placeholder.com/60x60?text=No+Image';
                    if (prod.image) {
                        imgSrc = prod.image;
                    }
                    productsHtml += `<tr>
                        <td><img src='${imgSrc}' alt='${prod.product_name}' class='img-fluid rounded' style='max-width:60px;max-height:60px;'></td>
                        <td>${prod.product_name}</td>
                        <td>${prod.description || ''}</td>
                        <td>${prod.quantity}</td>
                        <td>$${parseFloat(prod.price).toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                        <td>$${(parseFloat(prod.price) * parseInt(prod.quantity)).toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                    </tr>`;
                    total += parseFloat(prod.price) * parseInt(prod.quantity);
                });
                productsHtml += `<tr><td colspan='5' class='fw-bold text-end'>Total</td><td class='fw-bold'>$${total.toLocaleString(undefined, {minimumFractionDigits:2})}</td></tr>`;
                document.getElementById('modalProductsTable').innerHTML = productsHtml;
                const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
                modal.show();
            })
            .catch(() => alert('Failed to fetch order details.'));
    });
});
</script>
</body>

</html>


