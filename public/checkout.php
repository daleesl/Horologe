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
    return '₱' . number_format((float)$value, 0, '.', ',');
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

                            <!-- Payment Method -->
                            <div>
                                <h3 class="h5 text-white text-uppercase mb-4 pb-3 border-bottom border-secondary">PAYMENT METHOD</h3>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="paypal" value="PAYPAL" checked>
                                    <label class="form-check-label text-secondary" for="paypal">
                                        PAYPAL
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-light w-100 fw-bold py-3 text-uppercase mt-5">COMPLETE ORDER</button>
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

                        <div id="paypal-button-container" class="d-none mt-3"></div>

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
                    ✅ Order placed successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        <!-- Error Toast -->
        <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ❌ Something went wrong. Please try again.
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

        // Show PayPal button when clicking COMPLETE ORDER
        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            formDataSubmitted = {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                address: document.getElementById('address').value,
                city: document.getElementById('city').value,
                postalCode: document.getElementById('postalCode').value,
                country: document.getElementById('country').value,
                email: document.getElementById('email').value,
                paymentMethod: document.querySelector('input[name="paymentMethod"]:checked').value
            };

            // Check if PayPal is selected
            if (formDataSubmitted.paymentMethod === 'PAYPAL') {
                // Show PayPal button and scroll to it
                document.getElementById('paypal-button-container').classList.remove('d-none');
                document.getElementById('paypal-button-container').scrollIntoView({ behavior: 'smooth' });
            } else {
                // Handle other payment methods
                localStorage.setItem('checkoutInfo', JSON.stringify(formDataSubmitted));
                try {
                    const res = await fetch('../actions/order/create.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            payment_method: formDataSubmitted.paymentMethod,
                            firstName: formDataSubmitted.firstName,
                            lastName: formDataSubmitted.lastName,
                            address: formDataSubmitted.address,
                            city: formDataSubmitted.city,
                            postalCode: formDataSubmitted.postalCode,
                            country: formDataSubmitted.country,
                            email: formDataSubmitted.email
                        })
                    });
                    const data = await res.json();
                    if (!res.ok || !data || data.error) {
                        alert(data && data.error ? data.error : 'Could not place order.');
                        return;
                    }
                    window.location.href = 'orderConfirmation.php?order_id=' + encodeURIComponent(data.order_id);
                } catch (err) {
                    console.error('Failed to create order', err);
                    alert('Could not place order right now.');
                }
            }
        });

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

                    // Ensure formDataSubmitted has data - if not, gather from form
                    if (!formDataSubmitted) {
                        formDataSubmitted = {
                            firstName: document.getElementById('firstName').value,
                            lastName: document.getElementById('lastName').value,
                            address: document.getElementById('address').value,
                            city: document.getElementById('city').value,
                            postalCode: document.getElementById('postalCode').value,
                            country: document.getElementById('country').value,
                            email: document.getElementById('email').value,
                            paymentMethod: 'PAYPAL'
                        };
                    }

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