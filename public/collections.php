<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../classes/products/ProductService.php';

$productService = new ProductService(new ProductRepository($conn));
$products = $productService->getAllProducts();

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
                    <div class="d-flex flex-wrap gap-3 align-items-center" id="filterButtons">
                        <span class="text-secondary fw-bold small" style="letter-spacing: 0.1rem;">
                            <i class="bi bi-funnel"></i> FILTERS
                        </span>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3 active" data-filter="all">ALL</button>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3" data-filter="rolex">ROLEX</button>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3" data-filter="cartier">CARTIER</button>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3" data-filter="montblac">MONTBLAC</button>
                        <button class="btn btn-sm btn-outline-secondary text-secondary-bold px-3" data-filter="patek philippe">PATEK PHILIPPE</button>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="d-flex justify-content-lg-end align-items-center gap-2">
                        <span class="text-secondary small fw-bold" style="letter-spacing: 0.05rem;">SORT BY</span>
                        <select id="sortSelect" class="form-select form-select-sm bg-black border-secondary text-white" style="width: auto;">
                            <option value="featured" selected class="text-secondary">Featured</option>
                            <option value="price-asc" class="text-secondary">Price: Low to High</option>
                            <option value="price-desc" class="text-secondary">Price: High to Low</option>
                            <option value="newest" class="text-secondary">Newest</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>


        <!-- Products Grid -->
        <div class="container">
            <div id="productsRow" class="row g-3 g-lg-5">
                <?php if (!empty($products)) : ?>
                    <?php $i = 0; foreach ($products as $product) : ?>
                        <?php $i++; ?>
                        <div class="col-12 col-sm-6 col-lg-3 product-item" data-category="<?= htmlspecialchars(strtolower($product['category'] ?? ''), ENT_QUOTES) ?>" data-price="<?= htmlspecialchars($product['price'], ENT_QUOTES) ?>" data-index="<?= $i ?>">
                            <div class="product-card  rounded-3 p-3 h-100 d-flex flex-column justify-content-between">
                                <a href="viewProduct.php?id=<?= htmlspecialchars($product['id'], ENT_QUOTES) ?>" class="text-decoration-none flex-grow-1">
                                    <div>
                                        <div class="mb-2 overflow-hidden rounded ratio ratio-1x1">
                                            <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>" class="w-100 h-100 object-fit-contain p-3">
                                        </div>
                                        <p class="text-secondary small text-uppercase"><?= htmlspecialchars($product['category'], ENT_QUOTES) ?></p>
                                        <h5 class="text-white text-secondary-bold"><?= htmlspecialchars($product['name'], ENT_QUOTES) ?></h5>
                                        <p class="text-white mb-1"><?= formatPrice($product['price']) ?></p>
                                        <p class="text-secondary small mb-1 product-stock" data-product-id="<?= htmlspecialchars($product['id'], ENT_QUOTES) ?>">Stock: <span class="stock-count"><?= (int)($product['stock'] ?? 0) ?></span></p>
                               
                                    </div>
                                </a>
                                
                                <div class="d-flex gap-2">
                                    <a href="viewProduct.php?id=<?= htmlspecialchars($product['id'], ENT_QUOTES) ?>" class="btn btn-sm btn-outline-light flex-fill">VIEW</a>
                                    <?php $inStock = (int)($product['stock'] ?? 0) > 0; ?>
                                    <button class="btn btn-sm btn-outline-light flex-fill add-to-cart-btn" type="button"
                                        data-product-id="<?= htmlspecialchars($product['id'], ENT_QUOTES) ?>"
                                        data-product-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
                                        data-product-price="<?= htmlspecialchars($product['price'], ENT_QUOTES) ?>"
                                        data-product-image="<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>"
                                        data-product-category="<?= htmlspecialchars($product['category'], ENT_QUOTES) ?>"
                                        data-restore-label="ADD TO CART"
                                        <?= $inStock ? '' : 'disabled' ?>>
                                        <?= $inStock ? 'ADD TO CART' : 'OUT OF STOCK' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="col-12 text-center">
                        <p class="text-secondary">Products coming soon.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterButtons = document.querySelectorAll('#filterButtons button[data-filter]');
            const productItems = Array.from(document.querySelectorAll('.product-item'));
            const productsRow = document.getElementById('productsRow');
            const sortSelect = document.getElementById('sortSelect');
            let currentFilter = 'all';

            const applyFilter = (filter) => {
                currentFilter = filter;
                filterButtons.forEach(btn => btn.classList.toggle('active', btn.dataset.filter === filter));
                productItems.forEach(item => {
                    const matches = filter === 'all' || (item.dataset.category || '').toLowerCase() === filter;
                    item.classList.toggle('d-none', !matches);
                });
            };

            const applySort = (mode) => {
                const sorted = [...productItems];
                if (mode === 'price-asc') {
                    sorted.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
                } else if (mode === 'price-desc') {
                    sorted.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
                } else if (mode === 'newest') {
                    sorted.sort((a, b) => parseInt(b.dataset.index, 10) - parseInt(a.dataset.index, 10));
                } else {
                    sorted.sort((a, b) => parseInt(a.dataset.index, 10) - parseInt(b.dataset.index, 10));
                }
                sorted.forEach(node => productsRow.appendChild(node));
                applyFilter(currentFilter);
            };

            filterButtons.forEach(button => {
                button.addEventListener('click', () => applyFilter(button.dataset.filter));
            });

            sortSelect.addEventListener('change', () => applySort(sortSelect.value));

            applyFilter(currentFilter);
        });
    </script>

    <script src="../assets/js/cart.js"></script>
</body>

</html>