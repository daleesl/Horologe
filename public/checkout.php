<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../paypal/paypal_config.php';
require_once __DIR__ . '/../classes/cart/CartService.php';

session_start();

// Initialize user info
$userID = $_SESSION['userID'] ?? $_SESSION['user_id'] ?? null;
$userEmail = $_SESSION['email'] ?? null;
$user = [
    'username' => '',
    'contact' => '',
    'address' => ''
];

if ($userID) {
    $stmt = $conn->prepare("SELECT fname, lname, phone_number, email FROM users WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetchedUser = $result->fetch_assoc();
        if ($fetchedUser !== null) {
            $user = [
                'username' => ($fetchedUser['fname'] ?? '') . ' ' . ($fetchedUser['lname'] ?? ''),
                'contact' => $fetchedUser['phone_number'] ?? '',
                'address' => ''
            ];
            if (isset($fetchedUser['email'])) {
                $userEmail = $fetchedUser['email'];
            }
        }
        $stmt->close();
    }
}

$cartService = new CartService();
$cartItems = $cartService->getItems();

// If the cart page stored a subset for checkout, filter items here.
if (isset($_SESSION['checkout_selected_ids']) && is_array($_SESSION['checkout_selected_ids'])) {
    $selectedMap = array_flip($_SESSION['checkout_selected_ids']);
    $cartItems = array_values(array_filter($cartItems, function ($item) use ($selectedMap) {
        return isset($selectedMap[(string)($item['id'] ?? '')]);
    }));
}

// Recompute summary based on filtered items.
$cartSummary = [
    'items' => 0,
    'unique' => count($cartItems),
    'subtotal' => 0,
];

foreach ($cartItems as $ci) {
    $qty = (int) ($ci['quantity'] ?? 0);
    $price = (float) ($ci['price'] ?? 0);
    $cartSummary['items'] += $qty;
    $cartSummary['subtotal'] += ($qty * $price);
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
    <title>Checkout - Horologe</title>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=USD"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'EB Garamond', serif;
        }

        .form-control,
        .form-select {
            background-color: #1a1a1a;
            border-color: #444;
            color: #e0e0e0;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #1a1a1a;
            border-color: #666;
            color: #e0e0e0;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
        }

        .form-control::placeholder {
            color: #999;
        }
    </style>
</head>

<body class="bg-black text-secondary">

    <?php include '../includes/navbar.php'; ?>

    <section class="py-5" style="padding-top: 100px;">
        <div class="container-fluid px-4 px-md-5">
            <!-- Header -->
            <div class="mb-5">
                <h1 class="display-4 fw-normal text-white mb-2">CHECKOUT</h1>
            </div>

            <!-- Main Checkout Content -->
            <div class="row g-5">
                <!-- Left Side - Shipping & Payment -->
                <div class="col-lg-7">
                    <!-- Shipping Information -->
                    <div class="mb-5">
                        <h3 class="h5 text-white text-uppercase mb-4 pb-3 border-bottom border-secondary">SHIPPING INFORMATION</h3>

                        <form id="checkoutForm">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="firstName" class="form-label text-secondary small text-uppercase">FIRST NAME</label>
                                    <input type="text" class="form-control" id="firstName" placeholder="John" required>
                                </div>
                                <div class="col-md-6"> 
                                    <label for="lastName" class="form-label text-secondary small text-uppercase">LAST NAME</label>
                                    <input type="text" class="form-control" id="lastName" placeholder="Doe" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="form-label text-secondary small text-uppercase">ADDRESS</label>
                                <input type="text" class="form-control" id="address" placeholder="123 Luxury Street" required>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="city" class="form-label text-secondary small text-uppercase">CITY</label>
                                    <input type="text" class="form-control" id="city" placeholder="New York" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="postalCode" class="form-label text-secondary small text-uppercase">POSTAL CODE</label>
                                    <input type="text" class="form-control" id="postalCode" placeholder="10001" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label for="country" class="form-label text-secondary small text-uppercase">COUNTRY</label>
                                    <input type="text" class="form-control" id="country" placeholder="United States" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label text-secondary small text-uppercase">EMAIL</label>
                                    <input type="email" class="form-control" id="email" placeholder="john@example.com" required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Side - Order Summary -->
                <div class="col-lg-5">
                    <div class="border border-secondary bg-dark p-4 rounded position-sticky" style="top: 100px; z-index: 1020;">
                        <h3 class="h5 text-white mb-4 text-uppercase border-bottom border-secondary pb-3">ORDER DETAILS</h3>

                        <!-- Products in Order -->
                        <div id="orderItemsContainer" class="mb-4">
                            <?php if (empty($cartItems)) : ?>
                                <div class="text-center py-4"><p class="text-secondary">No items in your order</p><a href="collections.php" class="text-white text-decoration-none">Continue Shopping</a></div>
                            <?php else : ?>
                                <?php foreach ($cartItems as $item) : ?>
                                    <div class="mb-4 pb-4 border-bottom border-secondary">
                                        <div class="d-flex gap-3">
                                            <div style="width: 100px; flex-shrink: 0;">
                                                <?php
                                                $imgPath = $item['image'];
                                                if (strpos($imgPath, 'http') !== 0 && strpos($imgPath, '../') !== 0) {
                                                    $imgPath = '../' . ltrim($imgPath, '/');
                                                }
                                                ?>
                                                <img src="<?= htmlspecialchars($imgPath, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" class="w-100" style="height: 80px; object-fit: contain;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="text-secondary small mb-1"><?= htmlspecialchars($item['category'], ENT_QUOTES) ?></p>
                                                <p class="text-white fw-semibold mb-2"><?= htmlspecialchars($item['name'], ENT_QUOTES) ?></p>
                                                <p class="text-secondary small">QTY: <?= htmlspecialchars($item['quantity'], ENT_QUOTES) ?></p>
                                            </div>
                                            <div class="text-white fw-semibold text-end">
                                                <?= formatPrice($item['price'] * $item['quantity']) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <hr class="border-secondary my-4">

                        <!-- Summary -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary">SUBTOTAL</span>
                                <span class="text-white fw-semibold" id="subtotal"><?= formatPrice($cartSummary['subtotal']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-secondary">SHIPPING</span>
                                <span class="text-secondary" id="shipping">COMPLIMENTARY</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between border-top border-secondary pt-3">
                            <span class="text-white fw-bold text-uppercase">TOTAL</span>
                            <span class="text-white fw-bold fs-5" id="total"><?= formatPrice($cartSummary['subtotal']) ?></span>
                        </div>

                        <div class="payment-pill mt-4 mb-4 p-3 bg-secondary bg-opacity-25 rounded text-center text-secondary" id="paymentNotice" style="letter-spacing: 0.05rem;">
                            Mode of Payment: PayPal
                        </div>

                        <div id="paypal-button-container" class="mt-3"></div>

                        <p class="text-center text-secondary small mt-4" style="letter-spacing: 0.05rem;">SECURE CHECKOUT WITH ENCRYPTED PROTECTION</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <!-- Toast Notifications -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
        <!-- Success Toast -->
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    Order placed successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        <!-- Error Toast -->
        <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    Something went wrong. Please try again.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script src="../assets/js/cart.js"></script>
    <script>
        // Get cart items and total from the page
        const cartItemsData = <?php echo json_encode($cartItems); ?>;
        const userEmailData = <?php echo json_encode($userEmail ?? null); ?>;
        let formDataSubmitted = null;

        // COMPLETE ORDER button now just validates form
        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            // Form validation happens here, PayPal works independently
            const firstName = document.getElementById('firstName').value.trim();
            if (firstName) {
                showToast('successToast', 'Form validated. Click PayPal button to pay.');
            }
        });

        // Function to collect and validate form data when PayPal needs it
        function collectFormData() {
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const address = document.getElementById('address').value.trim();
            const city = document.getElementById('city').value.trim();
            const postalCode = document.getElementById('postalCode').value.trim();
            const country = document.getElementById('country').value.trim();
            const email = document.getElementById('email').value.trim();

            // Validate all required fields
            if (!firstName || !lastName || !address || !city || !postalCode || !country || !email) {
                showToast('errorToast', 'Please fill in all required fields before paying.');
                throw new Error('Missing required checkout data');
            }

            return {
                firstName, lastName, address, city, postalCode, country, email,
                paymentMethod: 'PAYPAL'
            };
        }

        // Utility function
        function showToast(id, message = null) {
            const toastEl = document.getElementById(id);
            if (!toastEl) {
                console.error(`Toast element #${id} not found`);
                return;
            }
            if (message) {
                const body = toastEl.querySelector('.toast-body');
                if (body) body.textContent = message;
            }
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Initialize PayPal Buttons
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'gold',
                shape: 'rect',
                tagline: false
            },

            createOrder: function(data, actions) {
                // Calculate total from cart items
                let totalAmount = 0;
                cartItemsData.forEach(item => {
                    totalAmount += (item.price * item.quantity);
                });

                return fetch('../paypal/create_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: totalAmount.toFixed(2)
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        console.error('Create order response error:', data);
                        throw new Error(data.error || 'Failed to create order');
                    }
                    if (!data.id) {
                        console.error('Create order unexpected response:', data);
                        throw new Error('Failed to create order: no id returned');
                    }
                    return data.id;
                });
            },

            onApprove: function(data, actions) {
                // Collect and validate form data before capturing payment
                try {
                    formDataSubmitted = collectFormData();
                } catch (e) {
                    return Promise.reject(e);
                }

                // Capture the PayPal order first
                return fetch('../paypal/capture_order.php?orderID=' + data.orderID, {
                    method: 'POST'
                })
                .then(res => res.json())
                .then(details => {
                    if (details.error) {
                        showToast('errorToast', "Payment failed. Check console.");
                        console.error(details.error);
                        return;
                    }

                    const total = cartItemsData.reduce((s, i) => s + (i.price * i.quantity), 0);

                    // Save order to DB
                    return fetch('../paypal/save_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            paymentID: data.orderID,
                            cart: cartItemsData,
                            total: total.toFixed(2),
                            firstName: formDataSubmitted.firstName,
                            lastName: formDataSubmitted.lastName,
                            address: formDataSubmitted.address,
                            city: formDataSubmitted.city,
                            postalCode: formDataSubmitted.postalCode,
                            country: formDataSubmitted.country,
                            email: formDataSubmitted.email,
                            paymentMethod: formDataSubmitted.paymentMethod
                        })
                    })
                    .then(res => res.json())
                    .then(r => {
                        if (r.success) {
                            // Store info and show success
                            localStorage.setItem('checkoutInfo', JSON.stringify(formDataSubmitted));
                                // Update product stock displays immediately using returned mapping
                                if (r.updatedStocks && typeof r.updatedStocks === 'object') {
                                    Object.keys(r.updatedStocks).forEach(pid => {
                                        const newStock = parseInt(r.updatedStocks[pid], 10);
                                        // Update any stock-count spans
                                        document.querySelectorAll('[data-product-id="' + pid + '"]').forEach(el => {
                                            const sc = el.querySelector('.stock-count');
                                            if (sc) sc.textContent = newStock;
                                            // Update any add-to-cart buttons inside product cards
                                            // If the element itself is the product-stock container, also update sibling buttons
                                        });

                                        // Update add-to-cart buttons in listings
                                        document.querySelectorAll('button.add-to-cart-btn[data-product-id="' + pid + '"]').forEach(btn => {
                                            if (newStock > 0) {
                                                btn.disabled = false;
                                                btn.textContent = btn.dataset.restoreLabel || 'ADD TO CART';
                                            } else {
                                                btn.disabled = true;
                                                btn.textContent = 'OUT OF STOCK';
                                            }
                                        });

                                        // Update single product page add button (if present)
                                        const singleBtn = document.querySelector('#addToCartBtn[data-product-id="' + pid + '"]');
                                        if (singleBtn) {
                                            if (newStock > 0) {
                                                singleBtn.disabled = false;
                                                singleBtn.textContent = singleBtn.dataset.restoreLabel || 'ADD TO COLLECTION';
                                            } else {
                                                singleBtn.disabled = true;
                                                singleBtn.textContent = 'OUT OF STOCK';
                                            }
                                        }
                                    });
                                }
                            // Refresh cart display (calls actions/cart/summary.php) so UI reflects empty cart
                            // Also clear any client-side cart storage for completeness
                            try {
                                localStorage.removeItem('cart');
                                sessionStorage.removeItem('cart');
                            } catch (e) {
                                /* ignore */
                            }
                            if (typeof updateCartCountDisplay === 'function') {
                                updateCartCountDisplay();
                            }
                            showToast('successToast');
                            setTimeout(() => {
                                window.location.href = 'orderConfirmation.php?order_id=' + encodeURIComponent(r.order_id);
                            }, 2000);
                        } else {
                            console.error("Order save failed:", r);
                            showToast('errorToast', r.error || "Order saving failed.");
                        }
                    })
                    .catch(err => {
                        console.error("Error saving order:", err);
                        showToast('errorToast', "An error occurred while saving your order: " + err.message);
                    });
                })
                .catch(err => {
                    console.error("PayPal capture error:", err);
                    showToast('errorToast', "Payment capture failed. Check console.");
                });
            },

            onError: function(err) {
                console.error("PayPal error:", err);
                showToast('errorToast', "An error occurred with PayPal. Check console.");
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>