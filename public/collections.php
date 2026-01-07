<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Collections - Horologe</title>
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
            <div class="row mb-5 text-center">
                <div class="col-12">
                    <h1 class="display-3 fw-normal text-white mb-3">The Collections</h1>
                    <p class="fs-5 text-secondary">Explore our range of exceptional timepieces, from timeless classics to avant-garde complications.</p>
                </div>
            </div>

            <!-- Filters & Sort -->
            <div class="row mb-5 align-items-center border-bottom border-secondary pb-4">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <span class="text-secondary fw-bold small" style="letter-spacing: 0.1rem;">
                            <i class="bi bi-funnel"></i> FILTERS
                        </span>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3">ALL</button>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3">ROLEX</button>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3">CARTIER</button>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3">MONTBLAC</button>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3">PATEK PHILIPPE</button>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="d-flex justify-content-lg-end align-items-center gap-2">
                        <span class="text-secondary small fw-bold" style="letter-spacing: 0.05rem;">SORT BY</span>
                        <select class="form-select form-select-sm bg-black border-secondary text-white" style="width: auto;">
                            <option selected class="text-secondary">Featured</option>
                            <option class="text-secondary">Price: Low to High</option>
                            <option class="text-secondary">Price: High to Low</option>
                            <option class="text-secondary">Newest</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>


        <!-- Products Grid -->
        <div class="container">
            <div id="productsRow" class="row g-3 g-lg-5">
                <!-- Products will be loaded dynamically -->
            </div>
        </div>

    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script src="../assets/js/sample-products.js"></script>
    <script src="../assets/js/cart.js"></script>
    <script>
        // Render Products Grid
        function renderProducts() {
            const productsRow = document.getElementById('productsRow');

            products.forEach(product => {
                productsRow.innerHTML += `
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="product-card  rounded-3 p-3 h-100 d-flex flex-column justify-content-between">
                            <a href="viewProduct.php?id=${product.id}" class="text-decoration-none flex-grow-1">
                                <div>
                                    <div class="mb-2 overflow-hidden rounded ratio ratio-1x1">
                                        <img src="${product.image}" alt="${product.name}" class="w-100 h-100 object-fit-contain p-3">
                                    </div>
                                    <p class="text-secondary small text-uppercase">${product.category}</p>
                                    <h5 class="text-white text-secondary-bold">${product.name}</h5>
                                    <p class="text-white mb-3">${formatPrice(product.price)}</p>
                                </div>
                            </a>
                            
                            <div class="d-flex gap-2">
                                <a href="viewProduct.php?id=${product.id}" class="btn btn-sm btn-outline-light flex-fill">VIEW</a>
                                <button class="btn btn-sm btn-outline-light flex-fill add-to-cart-btn" type="button" data-product-id="${product.id}">ADD TO CART</button>
                            </div>
                        </div>
                    </div>
                `;
            });

            attachAddToCartListeners();
        }

        // Attach Add to Cart Event Listeners
        function attachAddToCartListeners() {
            const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');

            addToCartBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = parseInt(this.dataset.productId);
                    const product = products.find(p => p.id === productId);

                    if (product) {
                        addToCart(product, 1);

                        this.textContent = 'ADDED!';
                        this.classList.add('disabled');

                        setTimeout(() => {
                            this.textContent = 'ADD TO CART';
                            this.classList.remove('disabled');
                        }, 2000);
                    }
                });
            });
        }

        renderProducts();
    </script>
</body>

</html>