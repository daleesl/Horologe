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
                        <div class="text-center py-5 text-secondary">
                            <p>No items in your collection yet</p>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="col-lg-4">
                    <div class="border border-secondary bg-dark p-4 rounded position-sticky" style="top: 100px; z-index: 1020;">
                        <h3 class="h5 text-white mb-4 text-uppercase">SUMMARY</h3>

                        <div class="d-flex justify-content-between mb-3 fs-6">
                            <span class="text-secondary">SUBTOTAL</span>
                            <span class="text-white" id="subtotal">$0</span>
                        </div>

                        <div class="d-flex justify-content-between mb-4 fs-6">
                            <span class="text-secondary">SHIPPING</span>
                            <span class="text-secondary" id="shipping">COMPLIMENTARY</span>
                        </div>

                        <div class="d-flex justify-content-between border-top border-secondary pt-3 mb-4">
                            <span class="text-white fw-bold">TOTAL</span>
                            <span class="text-white fw-bold" id="total">$0</span>
                        </div>

                        <button class="btn btn-light w-100 fw-bold py-3 text-uppercase">PROCEED TO CHECKOUT</button>
                        <p class="text-center text-secondary small mt-3" style="letter-spacing: 0.05rem;">SECURE CHECKOUT WITH ENCRYPTED PROTECTION</p>
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
        let cart = JSON.parse(localStorage.getItem('cart')) || products.map(p => ({
            ...p,
            quantity: 1,
            checked: false
        }));

        // Format price
        function formatPrice(price) {
            return '$' + price.toLocaleString();
        }

        // Render products
        function renderProducts() {
            const container = document.getElementById('productsContainer');

            if (cart.length === 0) {
                container.innerHTML = '<div class="text-center py-5 text-secondary"><p>No items in your collection yet</p></div>';
                return;
            }

            container.innerHTML = cart.map(product => `
                <div class="pb-4 mb-4 border-bottom border-secondary">
                    <div class="row align-items-center g-4">
                        <!-- Checkbox -->
                        <div class="col-auto" style="width: 40px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="product${product.id}" 
                                    ${product.checked ? 'checked' : ''} 
                                    onchange="toggleProduct(${product.id})">
                            </div>
                        </div>

                        <!-- Product Image -->
                        <div class="col-auto p-3" style="width: 175px; border: 1px solid #7c8288ff; border-radius: 4px;">
                            <img src="${product.image}" alt="${product.name}" class="w-100" style="width: 100%; height: 150px; object-fit: contain;">
                        </div>

                        <!-- Product Details (Timepiece) -->
                        <div class="col flex-grow-1">
                            <p class="text-secondary small mb-1" style="letter-spacing: 0.05rem;">${product.category}</p>
                            <h6 class="text-white mb-0" style="font-size: 1rem;">${product.name}</h6>
                        </div>

                        <!-- Quantity Control -->
                        <div class="col-auto" style="width: 140px;">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary px-2 py-1" onclick="decreaseQuantity(${product.id})">−</button>
                                <input type="number" value="${product.quantity}" readonly class="form-control form-control-sm" style="width: 50px; text-align: center;">
                                <button class="btn btn-sm btn-outline-secondary px-2 py-1" onclick="increaseQuantity(${product.id})">+</button>
                            </div>
                        </div>

                        <!-- Price & Remove -->
                        <div class="col-auto" style="width: 100px;">
                            <div class="text-end">
                                <p class="text-white fw-semibold mb-2">${formatPrice(product.price)}</p>
                                <a href="#" class="text-secondary text-decoration-none small" onclick="removeProduct(${product.id}); return false;">
                                    <i class="bi bi-trash"></i> REMOVE
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            updateSummary();
        }

        // Toggle product checkbox
        function toggleProduct(id) {
            const product = cart.find(p => p.id === id);
            if (product) {
                product.checked = !product.checked;
                saveCart();
                updateSummary();
            }
        }

        // Increase quantity
        function increaseQuantity(id) {
            const product = cart.find(p => p.id === id);
            if (product) {
                product.quantity++;
                saveCart();
                renderProducts();
            }
        }

        // Decrease quantity
        function decreaseQuantity(id) {
            const product = cart.find(p => p.id === id);
            if (product && product.quantity > 1) {
                product.quantity--;
                saveCart();
                renderProducts();
            }
        }

        // Remove product
        function removeProduct(id) {
            cart = cart.filter(p => p.id !== id);
            saveCart();
            renderProducts();
        }

        // Update summary
        function updateSummary() {
            const checkedProducts = cart.filter(p => p.checked);
            const subtotal = checkedProducts.reduce((sum, p) => sum + (p.price * p.quantity), 0);

            document.getElementById('cartCount').textContent = checkedProducts.length;
            document.getElementById('subtotal').textContent = formatPrice(subtotal);
            document.getElementById('total').textContent = formatPrice(subtotal);
        }

        // Save cart to localStorage
        function saveCart() {
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        // Initial render
        renderProducts();
    </script>
</body>

</html>