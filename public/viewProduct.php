<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../classes/products/ProductService.php';

$productService = new ProductService(new ProductRepository($conn));
$productId = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$product = $productService->getProduct($productId);
$relatedProducts = [];

if ($product) {
    $relatedProducts = $productService->getRelatedProducts($product['category'], $productId, 4);
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
    <title>Product Details - Horologe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body class="bg-black text-white">

    <?php include '../includes/navbar.php'; ?>

    <section class="py-5" style="padding-top: 100px;">
        <div class="container-fluid px-4 px-md-5">
            <?php if (!$product) : ?>
                <div class="row mb-5">
                    <div class="col-12 text-center">
                        <h2 class="text-white mb-3">Product not found</h2>
                        <a href="collections.php" class="btn btn-light">Back to Collections</a>
                    </div>
                </div>
            <?php else : ?>
                <!-- Back Button -->
                <div class="row mb-5">
                    <div class="col-12">
                        <a href="collections.php" class="text-secondary text-decoration-none d-inline-flex align-items-center gap-2">
                            <i class="bi bi-arrow-left"></i>
                            <span>BACK TO COLLECTIONS</span>
                        </a>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="row g-5 align-items-center">
                    <!-- Product Image -->
                    <div class="col-lg-6 d-flex justify-content-center">
                        <div id="productImageContainer" class="ratio ratio-1x1 w-100" style="max-width: 500px;">
                            <img id="productImage" src="<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>" class="w-100 h-100 object-fit-contain">
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="col-lg-6">
                        <!-- Category -->
                        <div class="mb-4">
                            <span id="productCategory" class="border border-secondary text-secondary px-3 py-2 d-inline-block small fw-bold text-uppercase"><?= htmlspecialchars($product['category'], ENT_QUOTES) ?></span>
                        </div>

                        <!-- Title -->
                        <h1 id="productName" class="display-4 fw-normal mb-4"><?= htmlspecialchars($product['name'], ENT_QUOTES) ?></h1>

                        <!-- Price & Stock -->
                        <div class="mb-3">
                            <p id="productPrice" class="fs-3 fw-bold mb-1"><?= formatPrice($product['price']) ?></p>
                            <p class="text-secondary mb-0 product-stock" data-product-id="<?= htmlspecialchars($product['id'], ENT_QUOTES) ?>">Stock: <span class="stock-count"><?= (int)($product['stock'] ?? 0) ?></span></p>
                        </div>

                        <!-- Description -->
                        <div class="mb-5">
                            <p id="productDescription" class="fs-5 text-secondary mb-0"><?= htmlspecialchars($product['description'] ?? 'Premium luxury timepiece crafted with precision and elegance.', ENT_QUOTES) ?></p>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="mb-4">
                            <label for="quantityInput" class="form-label fw-bold text-uppercase small">QUANTITY</label>
                            <div class="input-group" style="max-width: 150px;">
                                <button class="btn btn-outline-secondary" type="button" id="decreaseQty">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="number" id="quantityInput" class="form-control text-center" value="1" min="1">
                                <button class="btn btn-outline-secondary" type="button" id="increaseQty">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <div class="mb-5">
                            <?php $inStock = (int)($product['stock'] ?? 0) > 0; ?>
                            <button class="btn btn-light w-100 fw-bold py-3 add-to-cart-btn" id="addToCartBtn"
                                data-product-id="<?= htmlspecialchars($product['id'], ENT_QUOTES) ?>"
                                data-product-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
                                data-product-price="<?= htmlspecialchars($product['price'], ENT_QUOTES) ?>"
                                data-product-image="<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>"
                                data-product-category="<?= htmlspecialchars($product['category'], ENT_QUOTES) ?>"
                                data-restore-label="ADD TO COLLECTION"
                                <?= $inStock ? '' : 'disabled' ?>>
                                <?= $inStock ? 'ADD TO COLLECTION' : 'OUT OF STOCK' ?>
                            </button>
                        </div>

                        <!-- Features -->
                        <div class="row g-4 mt-5 pt-5 border-top border-secondary">
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <i class="bi bi-truck fs-4"></i>
                                </div>
                                <p class="text-secondary small fw-bold text-uppercase mb-1">EXPRESS DELIVERY</p>
                                <p class="text-secondary small">Fast & Secure Shipping</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <i class="bi bi-shield-check fs-4"></i>
                                </div>
                                <p class="text-secondary small fw-bold text-uppercase mb-1">CERTIFIED AUTHENTIC</p>
                                <p class="text-secondary small">100% Guaranteed</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <i class="bi bi-arrow-repeat fs-4"></i>
                                </div>
                                <p class="text-secondary small fw-bold text-uppercase mb-1">LIFETIME WARRANTY</p>
                                <p class="text-secondary small">Full Protection</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Products -->
                <div class="row mt-5 pt-5">
                    <div class="col-12">
                        <h3 class="display-5 fw-normal mb-5">YOU MAY ALSO LIKE</h3>
                    </div>
                    <div id="relatedProductsRow" class="col-12">
                        <div class="row g-3 g-lg-5">
                            <?php if (!empty($relatedProducts)) : ?>
                                <?php foreach ($relatedProducts as $related) : ?>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <a href="viewProduct.php?id=<?= htmlspecialchars($related['id'], ENT_QUOTES) ?>" class="text-decoration-none">
                                            <div class="product-card text-center rounded-3 p-3 h-100 d-flex flex-column justify-content-between" style="transition: all 0.3s ease;">
                                                <div>
                                                    <div class="mb-3 overflow-hidden rounded ratio ratio-1x1">
                                                        <img src="<?= htmlspecialchars($related['image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($related['name'], ENT_QUOTES) ?>" class="w-100 h-100 object-fit-contain p-3">
                                                    </div>
                                                    <p class="text-secondary small mb-2 fw-bold text-uppercase"><?= htmlspecialchars($related['category'], ENT_QUOTES) ?></p>
                                                    <h5 class="text-white mb-3 fw-normal"><?= htmlspecialchars($related['name'], ENT_QUOTES) ?></h5>
                                                    <p class="text-white fw-bold"><?= formatPrice($related['price']) ?></p>
                                                </div>
                                                <button class="btn btn-sm btn-outline-light px-4 mt-2">VIEW PRODUCT</button>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="col-12">
                                    <p class="text-secondary">No related products found.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script src="../assets/js/cart.js"></script>
    <script>
        const quantityInput = document.getElementById('quantityInput');
        const increaseBtn = document.getElementById('increaseQty');
        const decreaseBtn = document.getElementById('decreaseQty');
        const addToCartBtn = document.getElementById('addToCartBtn');

        if (increaseBtn && quantityInput) {
            increaseBtn.addEventListener('click', function() {
                quantityInput.value = parseInt(quantityInput.value, 10) + 1;
            });
        }

        if (decreaseBtn && quantityInput) {
            decreaseBtn.addEventListener('click', function() {
                if (parseInt(quantityInput.value, 10) > 1) {
                    quantityInput.value = parseInt(quantityInput.value, 10) - 1;
                }
            });
        }

        if (addToCartBtn && quantityInput) {
            addToCartBtn.addEventListener('click', function() {
                const quantity = parseInt(quantityInput.value, 10) || 1;
                addToCartBtn.dataset.productQuantity = quantity;
            });
        }
    </script>
</body>

</html>