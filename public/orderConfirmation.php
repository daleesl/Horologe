<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmed - Horologe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'EB Garamond', serif;
        }

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
                                    <p class="text-white fw-semibold" id="orderId">#HR-5829-2026</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-secondary small text-uppercase mb-1">DATE</p>
                                    <p class="text-white fw-semibold" id="orderDate">January 5, 2026</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-secondary small text-uppercase mb-1">PAYMENT METHOD</p>
                            <p class="text-white fw-semibold" id="paymentMethod">PAYPAL</p>
                        </div>

                        <!-- Products Ordered -->
                        <div id="orderedProductsContainer"></div>

                        <hr class="border-secondary my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white fw-bold text-uppercase">Total Amount</span>
                            <span class="text-white fw-bold fs-5" id="totalAmount">$0</span>
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

    <script src="../assets/js/sample-products.js"></script>

    <script>
        // Get order data from localStorage
        function loadOrderConfirmation() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const checkoutInfo = JSON.parse(localStorage.getItem('checkoutInfo')) || {};
            const checkedProducts = cart.filter(p => p.checked);

            if (checkedProducts.length === 0) {
                document.getElementById('orderedProductsContainer').innerHTML =
                    '<p class="text-secondary">No products in this order</p>';
                return;
            }

            // Generate Order ID
            const orderId = '#HR-' + Math.random().toString().slice(2, 6) + '-' + new Date().getFullYear();
            document.getElementById('orderId').textContent = orderId;

            // Set Order Date
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const orderDate = new Date().toLocaleDateString('en-US', options).toUpperCase();
            document.getElementById('orderDate').textContent = orderDate;

            // Set Payment Method
            if (checkoutInfo.paymentMethod) {
                document.getElementById('paymentMethod').textContent = checkoutInfo.paymentMethod;
            }

            // Set Customer Information
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

            // Calculate Total
            const totalAmount = checkedProducts.reduce((sum, p) => sum + (p.price * p.quantity), 0);
            document.getElementById('totalAmount').textContent = '$' + totalAmount.toLocaleString();

            // Render Products
            const productsHTML = checkedProducts.map(product => `
                <div class="mb-4 pb-4 border-bottom border-secondary">
                    <div class="d-flex gap-3">
                        <div style="width: 100px; flex-shrink: 0;">
                            <img src="${product.image}" alt="${product.name}" class="w-100" style="height: 80px; object-fit: contain;">
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-secondary small mb-1">${product.category}</p>
                            <p class="text-white fw-semibold mb-2">${product.name}</p>
                            <p class="text-secondary small">QTY: ${product.quantity}</p>
                        </div>
                        <div class="text-white fw-semibold">
                            $${product.price.toLocaleString()}
                        </div>
                    </div>
                </div>
            `).join('');

            document.getElementById('orderedProductsContainer').innerHTML = productsHTML;
        }

        // Load order on page load
        loadOrderConfirmation();
    </script>
</body>

</html>