<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../classes/cart/CartService.php';

$cartService = new CartService();
$cartItems = $cartService->getItems();
$cartSummary = $cartService->getSummary();

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
    <title>Collections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body class="bg-black text-secondary">

    <?php include '../includes/navbar.php'; ?>

    <section class="py-5" style="padding-top: 100px;">
        <div class="container-fluid px-4 px-md-5">
            <!-- Header -->
            <div class="row mb-5">
                <div class="col-12">
                    <h1 class="display-4 fw-normal text-white mb-2">Your Collection</h1>
                    <p class="text-secondary fs-6"><span id="cartCount">0</span> COLLECTIONS</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row g-4">
                <!-- Products List -->
                <div class="col-lg-8">
                    <!-- Header Row -->
                    <div class="row align-items-center g-4 mb-4 pb-3 border-bottom border-secondary">
                        <div class="col-auto" style="width: 40px;"></div>
                        <div class="col flex-grow-1">
                            <p class="text-secondary small fw-bold mb-0" style="letter-spacing: 0.1rem;">TIMEPIECE</p>
                        </div>
                        <div class="col-auto" style="width: 140px;">
                            <p class="text-secondary small fw-bold mb-0 text-center" style="letter-spacing: 0.1rem;">QUANTITY</p>
                        </div>
                        <div class="col-auto" style="width: 100px;">
                            <p class="text-secondary small fw-bold mb-0 text-end" style="letter-spacing: 0.1rem;">PRICE</p>
                        </div>
                    </div>

                    <div id="productsContainer">
                        <?php if (empty($cartItems)) : ?>
                            <div class="text-center py-5 text-secondary">
                                <p>No items in your collection yet</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($cartItems as $item) : ?>
                                <div class="pb-4 mb-4 border-bottom border-secondary cart-item" data-product-id="<?= htmlspecialchars($item['id'], ENT_QUOTES) ?>" data-unit-price="<?= htmlspecialchars($item['price'], ENT_QUOTES) ?>">
                                    <div class="row align-items-center g-4">
                                        <!-- Checkbox -->
                                        <div class="col-auto" style="width: 40px;">
                                            <div class="form-check">
                                                <input class="form-check-input select-item" type="checkbox" checked data-product-id="<?= htmlspecialchars($item['id'], ENT_QUOTES) ?>">
                                            </div>
                                        </div>

                                        <!-- Product Image -->
                                        <div class="col-auto p-3" style="width: 175px;">
                                            <?php
                                            $imgPath = $item['image'];
                                            if (strpos($imgPath, 'http') !== 0 && strpos($imgPath, '../') !== 0) {
                                                $imgPath = '../' . ltrim($imgPath, '/');
                                            }
                                            ?>
                                            <img src="<?= htmlspecialchars($imgPath, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" class="w-100" style="width: 100%; height: 150px; object-fit: contain;">
                                        </div>

                                        <!-- Product Details (Timepiece) -->
                                        <div class="col flex-grow-1">
                                            <p class="text-secondary small mb-1" style="letter-spacing: 0.05rem;">
                                                <?= htmlspecialchars($item['category'], ENT_QUOTES) ?>
                                            </p>
                                            <h6 class="text-white mb-0" style="font-size: 1rem;">
                                                <?= htmlspecialchars($item['name'], ENT_QUOTES) ?>
                                            </h6>
                                        </div>

                                        <!-- Quantity Control -->
                                        <div class="col-auto" style="width: 140px;">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-secondary px-2 py-1 btn-decrease" data-product-id="<?= htmlspecialchars($item['id'], ENT_QUOTES) ?>">−</button>
                                                <input type="number" value="<?= htmlspecialchars($item['quantity'], ENT_QUOTES) ?>" readonly class="form-control form-control-sm item-qty" style="width: 50px; text-align: center;">
                                                <button class="btn btn-sm btn-outline-secondary px-2 py-1 btn-increase" data-product-id="<?= htmlspecialchars($item['id'], ENT_QUOTES) ?>">+</button>
                                            </div>
                                        </div>

                                        <!-- Price & Remove -->
                                        <div class="col-auto" style="width: 100px;">
                                            <div class="text-end">
                                                <p class="text-white fw-semibold mb-2 item-price" data-raw-price="<?= htmlspecialchars($item['price'], ENT_QUOTES) ?>"><?= formatPrice($item['price']) ?></p>
                                                <a href="#" class="text-secondary text-decoration-none small link-remove" data-product-id="<?= htmlspecialchars($item['id'], ENT_QUOTES) ?>">
                                                    <i class="bi bi-trash"></i> REMOVE
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Summary -->
                <div class="col-lg-4">
                    <div class="border border-secondary bg-dark p-4 rounded position-sticky" style="top: 100px; z-index: 1020;">
                        <h3 class="h5 text-white mb-4 text-uppercase">SUMMARY</h3>

                        <div class="d-flex justify-content-between mb-3 fs-6">
                            <span class="text-secondary">SUBTOTAL</span>
                            <span class="text-white" id="subtotal"><?= formatPrice($cartSummary['subtotal']) ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-4 fs-6">
                            <span class="text-secondary">SHIPPING</span>
                            <span class="text-secondary" id="shipping">COMPLIMENTARY</span>
                        </div>

                        <div class="d-flex justify-content-between border-top border-secondary pt-3 mb-4">
                            <span class="text-white fw-bold">TOTAL</span>
                            <span class="text-white fw-bold" id="total"><?= formatPrice($cartSummary['subtotal']) ?></span>
                        </div>

                        <button id="proceedCheckout" class="btn btn-light w-100 fw-bold py-3 text-uppercase">PROCEED TO CHECKOUT</button>
                        <p class="text-center text-secondary small mt-3" style="letter-spacing: 0.05rem;">SECURE CHECKOUT WITH ENCRYPTED PROTECTION</p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script src="../assets/js/cart.js"></script>
    <script>
        const IS_LOGGED_IN = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        async function postForm(url, data) {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            });
            return res.json();
        }

        function collectSelectedIds() {
            const ids = [];
            document.querySelectorAll('.select-item').forEach(box => {
                if (box.checked) {
                    ids.push(box.dataset.productId);
                }
            });
            return ids;
        }

        function recalcSummary() {
            let subtotal = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const checkbox = item.querySelector('.select-item');
                if (!checkbox || !checkbox.checked) {
                    return;
                }
                const unit = parseFloat(item.dataset.unitPrice || '0');
                const qtyInput = item.querySelector('.item-qty');
                const qty = qtyInput ? parseInt(qtyInput.value, 10) || 0 : 0;
                subtotal += unit * qty;
            });

            const subtotalEl = document.getElementById('subtotal');
            const totalEl = document.getElementById('total');
            if (subtotalEl) {
                subtotalEl.textContent = formatPrice(subtotal);
            }
            if (totalEl) {
                totalEl.textContent = formatPrice(subtotal);
            }
        }

        function attachQuantityHandlers() {
            document.querySelectorAll('.btn-increase').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const id = btn.dataset.productId;
                    const input = btn.parentElement.querySelector('input');
                    const newQty = parseInt(input.value, 10) + 1;
                    await postForm('../actions/cart/update.php', { product_id: id, quantity: newQty });
                    window.location.reload();
                });
            });

            document.querySelectorAll('.btn-decrease').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const id = btn.dataset.productId;
                    const input = btn.parentElement.querySelector('input');
                    const current = parseInt(input.value, 10);
                    const newQty = Math.max(1, current - 1);
                    await postForm('../actions/cart/update.php', { product_id: id, quantity: newQty });
                    window.location.reload();
                });
            });
        }

        function attachRemoveHandlers() {
            document.querySelectorAll('.link-remove').forEach(link => {
                link.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const id = link.dataset.productId;
                    await postForm('../actions/cart/remove.php', { product_id: id });
                    window.location.reload();
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            attachQuantityHandlers();
            attachRemoveHandlers();
            document.querySelectorAll('.select-item').forEach(box => {
                box.addEventListener('change', recalcSummary);
            });
            recalcSummary();

            const checkoutBtn = document.getElementById('proceedCheckout');
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    if (!IS_LOGGED_IN) {
                        window.location.href = '../auth/sign-in.php';
                        return;
                    }
                    const selectedIds = collectSelectedIds();
                    if (selectedIds.length === 0) {
                        alert('Select at least one item to checkout.');
                        return;
                    }
                    await postForm('../actions/cart/select.php', { selected_ids: selectedIds.join(',') });
                    window.location.href = 'checkout.php';
                });
            }
        });
    </script>
</body>

</html>