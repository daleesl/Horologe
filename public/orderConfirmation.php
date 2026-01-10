<?php
session_start();
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../classes/cart/CartService.php';

$cartItems = [];
$cartSummary = ['subtotal' => 0, 'items' => 0];

if (isset($_SESSION['last_order']) && is_array($_SESSION['last_order'])) {
    $last = $_SESSION['last_order'];
    $cartItems = $last['items'] ?? [];
    $cartSummary['subtotal'] = (float) ($last['total'] ?? 0);
    foreach ($cartItems as $ci) {
        $cartSummary['items'] += (int) ($ci['quantity'] ?? 0);
    }
} else {
    $cartService = new CartService();
    $cartItems = $cartService->getItems();
    $cartSummary = $cartService->getSummary();
}

// After we have the data to render, clear purchased items once per confirmation load
if (isset($_SESSION['pending_clear_ids']) && is_array($_SESSION['pending_clear_ids'])) {
    $cartService = new CartService();
    foreach ($_SESSION['pending_clear_ids'] as $cid) {
        $cartService->remove((string) $cid);
    }
    unset($_SESSION['pending_clear_ids']);
}

function formatPrice($value)
{
    return '$' . number_format((float)$value, 0, '.', ',');
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmed - Horologe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .check-icon {
            width: 120px;
            height: 120px;
            border: 4px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
        }
    </style>
</head>

<body class="bg-black text-secondary">

    <?php include '../includes/navbar.php'; ?>

    <section class="py-5" style="padding-top: 100px;">
        <div class="container">
            <!-- Success Header -->
            <div class="text-center mb-5">
                <div class="d-flex justify-content-center mb-4">
                    <div class="check-icon text-white">
                        <i class="bi bi-check"></i>
                    </div>
                </div>
                <h1 class="display-4 fw-normal text-white mb-3">ORDER CONFIRMED</h1>
                <p class="text-secondary fs-6">THANK YOU FOR CHOOSING HOROLOGE. YOUR TIMEPIECE IS BEING PREPARED</p>
            </div>

            <!-- Order Content -->
            <div class="row g-4">
                <!-- Order Details Card -->
                <div class="col-lg-6">
                    <div class="border border-secondary bg-dark p-4 rounded">
                        <h3 class="h5 text-white mb-4 text-uppercase border-bottom border-secondary pb-3">ORDER DETAILS</h3>

                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-secondary small text-uppercase mb-1">ORDER ID</p>
                                    <p class="text-white fw-semibold" id="orderId">ORDER PENDING</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-secondary small text-uppercase mb-1">DATE</p>
                                    <p class="text-white fw-semibold" id="orderDate">--</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-secondary small text-uppercase mb-1">PAYMENT METHOD</p>
                            <p class="text-white fw-semibold" id="paymentMethod">PAYPAL</p>
                        </div>

                        <!-- Products Ordered -->
                        <div id="orderedProductsContainer">
                            <?php if (empty($cartItems)) : ?>
                                <p class="text-secondary">No products in this order</p>
                            <?php else : ?>
                                <?php foreach ($cartItems as $item) : ?>
                                    <div class="mb-4 pb-4 border-bottom border-secondary">
                                        <div class="d-flex gap-3">
                                            <div style="width: 100px; flex-shrink: 0;">
                                                <img src="<?= htmlspecialchars($item['image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" class="w-100" style="height: 80px; object-fit: contain;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="text-secondary small mb-1"><?= htmlspecialchars($item['category'], ENT_QUOTES) ?></p>
                                                <p class="text-white fw-semibold mb-2"><?= htmlspecialchars($item['name'], ENT_QUOTES) ?></p>
                                                <p class="text-secondary small">QTY: <?= htmlspecialchars($item['quantity'], ENT_QUOTES) ?></p>
                                            </div>
                                            <div class="text-white fw-semibold">
                                                <?= formatPrice($item['price'] * $item['quantity']) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <hr class="border-secondary my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white fw-bold text-uppercase">Total Amount</span>
                            <span class="text-white fw-bold fs-5" id="totalAmount"><?= formatPrice($cartSummary['subtotal']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address Card -->
                <div class="col-lg-6">
                    <div class="border border-secondary bg-dark p-4 rounded">
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-truck text-white fs-5"></i>
                                <h3 class="h5 text-white mb-0 text-uppercase">WHITE GLOVE DELIVERY</h3>
                            </div>
                            <p class="text-secondary small">FREE SHIPPING & INSTALLATION</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-secondary small text-uppercase mb-2">SHIPPING ADDRESS</p>
                            <div class="text-white lh-lg">
                                <p class="mb-1"><span id="customerName">JOHN DOE</span></p>
                                <p class="mb-1"><span id="customerAddress">123 LUXURY STREET</span></p>
                                <p class="mb-1"><span id="customerCity">NEW YORK, NY 10001</span>, <span id="customerPostalCode">10001</span></p>
                                <p><span id="customerCountry">UNITED STATES</span></p>
                            </div>
                            <p class="text-secondary small mt-3">
                                <i class="bi bi-envelope me-2"></i><span id="customerEmail">john@example.com</span>
                            </p>
                        </div>

                        <p class="text-center text-secondary small mt-4" style="letter-spacing: 0.05rem;">SECURE CHECKOUT WITH ENCRYPTED PROTECTION</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-5 justify-content-center">
                <div class="col-lg-6">
                    <a href="index.php" class="btn btn-light w-100 fw-bold py-3 text-uppercase mb-3">CONTINUE EXPLORING</a>
                    <a href="index.php" class="btn btn-outline-light w-100 fw-bold py-3 text-uppercase">RETURN HOME</a>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script src="../assets/js/cart.js"></script>
    <script>
        function hydrateFromSession() {
            const params = new URLSearchParams(window.location.search);
            const orderId = params.get('order_id');
            if (orderId) {
                document.getElementById('orderId').textContent = orderId;
            }

            const checkoutInfo = JSON.parse(localStorage.getItem('checkoutInfo')) || {};
            if (checkoutInfo.paymentMethod) {
                document.getElementById('paymentMethod').textContent = checkoutInfo.paymentMethod;
            }
            if (checkoutInfo.firstName && checkoutInfo.lastName) {
                document.getElementById('customerName').textContent = (checkoutInfo.firstName + ' ' + checkoutInfo.lastName).toUpperCase();
            }
            if (checkoutInfo.address) {
                document.getElementById('customerAddress').textContent = checkoutInfo.address.toUpperCase();
            }
            if (checkoutInfo.city && checkoutInfo.postalCode) {
                document.getElementById('customerCity').textContent = (checkoutInfo.city + ', ' + checkoutInfo.postalCode).toUpperCase();
            }
            if (checkoutInfo.country) {
                document.getElementById('customerCountry').textContent = checkoutInfo.country.toUpperCase();
            }
            if (checkoutInfo.email) {
                document.getElementById('customerEmail').textContent = checkoutInfo.email;
            }

            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('orderDate').textContent = new Date().toLocaleDateString('en-US', options).toUpperCase();
        }

        document.addEventListener('DOMContentLoaded', hydrateFromSession);
    </script>
</body>

</html>