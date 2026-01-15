<?php
session_start();

require_once __DIR__ . '/../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/sign-in.php');
    exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT o.order_id, o.order_date, o.total_amount,
        (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) AS items_count,
        (SELECT w.image_file FROM order_items oi JOIN watch w ON oi.watch_id = w.watch_id WHERE oi.order_id = o.order_id LIMIT 1) AS thumb
     FROM orders o
     WHERE o.user_id = ?
     ORDER BY o.order_date DESC"
);
$stmt->bind_param('s', $userId);
$stmt->execute();
$res = $stmt->get_result();
$orders = [];
while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();

function moneyFormat(float $value): string {
    return '$' . number_format($value, 2);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Orders - Horologe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="bg-black text-secondary">

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<section class="py-5" style="padding-top: 100px;">
    <div class="container px-4 px-md-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-5 text-white">Order History</h1>
                <p class="text-secondary">Your previous orders are listed below. Click "View" to see order details.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <?php if (!empty($orders)): ?>
                    <div class="list-group">
                        <?php foreach ($orders as $order): ?>
                            <div class="list-group-item bg-dark border-secondary rounded-3 mb-3 p-3">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="flex-shrink-0">
                                        <?php
                                        $thumb = $order['thumb'] ?? '';
                                        $imgSrc = $thumb ? ('../' . ltrim($thumb, '/')) : 'https://via.placeholder.com/90x90?text=No+Image';
                                        ?>
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="thumb" style="width:90px;height:90px;object-fit:cover;border-radius:8px;">
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="text-secondary small">Order ID</div>
                                                <div class="text-white fw-semibold mb-1"><?= htmlspecialchars($order['order_id']) ?></div>
                                                <div class="text-secondary small">Placed: <?= !empty($order['order_date']) ? date('F j, Y', strtotime($order['order_date'])) : '--' ?></div>
                                            </div>
                                            <div class="text-end">
                                                <div class="text-white fw-semibold"><?= moneyFormat((float)$order['total_amount']) ?></div>
                                                <div class="text-secondary small">Items: <?= (int)($order['items_count'] ?? 0) ?></div>
                                                <div class="mt-2"><span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($order['status'] ?? 'Shipping') ?></span></div>
                                            </div>
                                        </div>

                                        <div class="mt-3 d-flex justify-content-end">
                                            <button class="btn btn-sm btn-outline-light view-order-btn" data-order-id="<?= htmlspecialchars($order['order_id']) ?>">
                                                <i class="bi bi-eye"></i> View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-secondary py-4">You have no orders yet.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-black text-white border border-secondary rounded-4">
            <div class="modal-header border-0">
                <h4 class="modal-title fw-bold">Order Details</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div>Order ID: <span id="modalOrderId"></span></div>
                        <div>Date: <span id="modalOrderDate"></span></div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="fw-semibold" id="modalOrderTotal"></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-bordered align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody id="modalProductsTable"></tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total</td>
                            <td id="modalTotalAmount" class="fw-bold"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.view-order-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const orderId = btn.dataset.orderId;
        fetch('../actions/order_details_api.php?order_id=' + encodeURIComponent(orderId))
            .then(res => res.json())
            .then(data => {
                if (!data || data.error) {
                    alert(data && data.error ? data.error : 'Order details not found.');
                    return;
                }

                const order = data.order;
                const products = data.products || [];

                document.getElementById('modalOrderId').textContent = order.order_id;
                document.getElementById('modalOrderDate').textContent = order.order_date ? new Date(order.order_date).toLocaleDateString() : '--';
                document.getElementById('modalOrderTotal').textContent = '$' + parseFloat(order.total_amount).toFixed(2);

                let html = '';
                let total = 0;
                products.forEach(p => {
                    const img = p.image ? '../' + p.image.replace(/^\/+/, '') : 'https://via.placeholder.com/60x60?text=No+Image';
                    const subtotal = (parseFloat(p.price) * parseInt(p.quantity, 10)) || 0;
                    total += subtotal;
                    html += `
                        <tr>
                            <td><img src="${img}" class="rounded" style="max-width:60px"></td>
                            <td>${p.product_name}</td>
                            <td>$${parseFloat(p.price).toFixed(2)}</td>
                            <td>${p.quantity}</td>
                            <td>$${subtotal.toFixed(2)}</td>
                        </tr>
                    `;
                });

                document.getElementById('modalProductsTable').innerHTML = html;
                document.getElementById('modalTotalAmount').textContent = '$' + total.toFixed(2);

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
