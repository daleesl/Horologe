<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - Horologe</title>
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

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="CREDIT CARD">
                                    <label class="form-check-label text-secondary" for="creditCard">
                                        CREDIT CARD
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="bankTransfer" value="BANK TRANSFER">
                                    <label class="form-check-label text-secondary" for="bankTransfer">
                                        BANK TRANSFER
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
                            <p class="text-secondary small">Loading products...</p>
                        </div>

                        <hr class="border-secondary my-4">

                        <!-- Summary -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary">SUBTOTAL</span>
                                <span class="text-white fw-semibold" id="subtotal">$0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-secondary">SHIPPING</span>
                                <span class="text-secondary" id="shipping">COMPLIMENTARY</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between border-top border-secondary pt-3">
                            <span class="text-white fw-bold text-uppercase">TOTAL</span>
                            <span class="text-white fw-bold fs-5" id="total">$0</span>
                        </div>

                        <p class="text-center text-secondary small mt-4" style="letter-spacing: 0.05rem;">SECURE CHECKOUT WITH ENCRYPTED PROTECTION</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script src="../assets/js/sample-products.js"></script>

    <script>
        // Load cart and display products
        function loadOrderItems() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const checkedProducts = cart.filter(p => p.checked);

            if (checkedProducts.length === 0) {
                document.getElementById('orderItemsContainer').innerHTML =
                    '<div class="text-center py-4"><p class="text-secondary">No items in your order</p><a href="collections.php" class="text-white text-decoration-none">Continue Shopping</a></div>';
                return;
            }

            // Calculate totals
            const subtotal = checkedProducts.reduce((sum, p) => sum + (p.price * p.quantity), 0);

            // Render products
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
                        <div class="text-white fw-semibold text-end">
                            $${(product.price * product.quantity).toLocaleString()}
                        </div>
                    </div>
                </div>
            `).join('');

            document.getElementById('orderItemsContainer').innerHTML = productsHTML;

            // Update summary
            document.getElementById('subtotal').textContent = '$' + subtotal.toLocaleString();
            document.getElementById('total').textContent = '$' + subtotal.toLocaleString();
        }

        // Handle form submission
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                address: document.getElementById('address').value,
                city: document.getElementById('city').value,
                postalCode: document.getElementById('postalCode').value,
                country: document.getElementById('country').value,
                email: document.getElementById('email').value,
                paymentMethod: document.querySelector('input[name="paymentMethod"]:checked').value
            };

            // Store checkout info
            localStorage.setItem('checkoutInfo', JSON.stringify(formData));

            // Redirect to order confirmation
            window.location.href = 'orderConfirmation.php';
        });

        // Load order on page load
        loadOrderItems();
    </script>
</body>

</html>