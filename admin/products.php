<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../classes/products/ProductRepository.php';
require_once __DIR__ . '/../classes/products/ProductService.php';

$productService = new ProductService(new ProductRepository($conn));
$products = $productService->getAllProducts();


$grouped = [];
foreach ($products as $p) {
    $brandKey = strtoupper((string)($p['brand'] ?? 'UNCATEGORIZED'));
    if (!isset($grouped[$brandKey])) {
        $grouped[$brandKey] = [];
    }
    $grouped[$brandKey][] = $p;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'EB Garamond', serif; }
        .nav-link.active { background-color: #495057 !important; }
        .product-card { transition: transform 0.2s; }
        .product-card:hover { transform: translateY(-5px); }
        .product-img { height: 250px; object-fit: contain; width: 100%; }
    </style>
</head>

<body class="bg-black text-white">
    <div class="d-flex flex-column flex-md-row">
        <?php include '../includes/adminSidebar.php'; ?>

        <div class="flex-grow-1 w-100 p-3 p-sm-4">
            <div class="d-md-none mb-3">
                <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas">
                    <i class="bi bi-list"></i> Menu
                </button>
            </div>

            <!-- Header -->
            <div class="mb-4">
                <h1 class="display-5 fw-normal mb-2">PRODUCTS</h1>
                <p class="text-secondary">Manage your luxury timepiece collection</p>
            </div>

            <!-- Search and Add Button -->
            <div class="row mb-4 g-3 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="productSearch" class="form-control bg-dark border-secondary text-white" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#productModal" data-mode="add">
                        <i class="bi bi-plus-circle me-2"></i>Add Product
                    </button>
                </div>
            </div>

            <?php if (empty($products)) : ?>
                <div class="text-center text-secondary py-5 border border-secondary rounded">No products yet.</div>
            <?php else : ?>
                <?php foreach ($grouped as $brand => $items) : ?>
                    <div class="mb-5">
                        <h3 class="h5 text-uppercase mb-4 border-bottom border-secondary pb-2"><?= htmlspecialchars($brand, ENT_QUOTES) ?></h3>
                        <div class="row g-4">
                            <?php foreach ($items as $p) : ?>
                                <div class="col-lg-4 col-md-6 product-tile" data-name="<?= htmlspecialchars(strtolower($p['name'] ?? ''), ENT_QUOTES) ?>" data-brand="<?= htmlspecialchars(strtolower($brand), ENT_QUOTES) ?>">
                                    <div class="product-card border border-secondary bg-dark p-3 rounded h-100 d-flex flex-column">
                                        <div class="mb-3 text-center">
                                            <?php if (!empty($p['image'])) : ?>
                                                <img src="<?= htmlspecialchars($p['image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>" class="img-fluid product-img border border-secondary rounded">
                                            <?php else : ?>
                                                <div class="border border-secondary rounded d-flex align-items-center justify-content-center" style="height:250px;"> <span class="text-secondary small">No image</span> </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-2">
                                            <h5 class="text-white mb-0" style="line-height:1.3; font-size: 1.05rem;">
                                                <?= htmlspecialchars($p['model'], ENT_QUOTES) ?>
                                            </h5>
                                        </div>
                                        <p class="text-secondary mb-3" style="font-size: 0.9rem; min-height: 48px;">
                                            <?= htmlspecialchars($p['description'] ?: 'No description provided.', ENT_QUOTES) ?>
                                        </p>
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="text-white fw-bold" style="font-size: 1.05rem;">$<?= number_format((float)$p['price'], 2) ?></div>
                                            <div class="text-secondary small">Stock: <?= (int)($p['stock'] ?? 0) ?> units</div>
                                        </div>
                                        <div class="d-flex gap-2 mt-auto">
                                            <button class="btn btn-sm btn-outline-light flex-fill edit-btn"
                                                data-bs-toggle="modal" data-bs-target="#productModal"
                                                data-mode="edit"
                                                data-id="<?= htmlspecialchars($p['id'], ENT_QUOTES) ?>"
                                                data-brand="<?= htmlspecialchars($p['brand'], ENT_QUOTES) ?>"
                                                data-model="<?= htmlspecialchars($p['model'], ENT_QUOTES) ?>"
                                                data-category="<?= htmlspecialchars($p['category'], ENT_QUOTES) ?>"
                                                data-price="<?= htmlspecialchars($p['price'], ENT_QUOTES) ?>"
                                                data-stock="<?= htmlspecialchars($p['stock'] ?? 0, ENT_QUOTES) ?>"
                                                data-description="<?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES) ?>"
                                                data-image="<?= htmlspecialchars($p['image'] ?? '', ENT_QUOTES) ?>">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger flex-fill delete-btn"
                                                data-id="<?= htmlspecialchars($p['id'], ENT_QUOTES) ?>"
                                                data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>">
                                                <i class="bi bi-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="productForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="watch_id" id="watchId">
                        <input type="hidden" name="current_image" id="currentImage">
                        <div class="mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <select class="form-select bg-dark border-secondary text-white" id="brand" name="brand" required>
                                <option value="" disabled selected>Select brand</option>
                                <option value="Rolex">Rolex</option>
                                <option value="Cartier">Cartier</option>
                                <option value="MontBlac">MontBlac</option>
                                <option value="Patek Philippe">Patek Philippe</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="model" class="form-label">Model / Name</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" id="model" name="model" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" min="0" class="form-control bg-dark border-secondary text-white" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock Quantity</label>
                            <input type="number" min="0" class="form-control bg-dark border-secondary text-white" id="stock" name="stock_quantity" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control bg-dark border-secondary text-white" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control bg-dark border-secondary text-white" id="image" name="image" accept="image/*">
                            <small class="text-secondary">Upload JPG or PNG. Leave empty to keep current image.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-light" id="saveBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Delete Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete <span id="deleteProductName" class="fw-semibold"></span>?</p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        const productModal = document.getElementById('productModal');
        const productForm = document.getElementById('productForm');
        const modalTitle = document.getElementById('productModalLabel');
        const saveBtn = document.getElementById('saveBtn');

        productModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const mode = button.getAttribute('data-mode');
            modalTitle.textContent = mode === 'edit' ? 'Edit Product' : 'Add Product';
            saveBtn.textContent = mode === 'edit' ? 'Save Changes' : 'Add Product';

            document.getElementById('watchId').value = mode === 'edit' ? (button.getAttribute('data-id') || '') : '';
            document.getElementById('brand').value = mode === 'edit' ? (button.getAttribute('data-brand') || '') : '';
            document.getElementById('model').value = mode === 'edit' ? (button.getAttribute('data-model') || '') : '';
            document.getElementById('price').value = mode === 'edit' ? (button.getAttribute('data-price') || '') : '';
            document.getElementById('stock').value = mode === 'edit' ? (button.getAttribute('data-stock') || '') : '';
            document.getElementById('description').value = mode === 'edit' ? (button.getAttribute('data-description') || '') : '';
            document.getElementById('currentImage').value = mode === 'edit' ? (button.getAttribute('data-image') || '') : '';
            document.getElementById('image').value = '';
        });

        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(productForm);
            try {
                const res = await fetch('../actions/admin/save_product.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (!res.ok || !data || data.error) {
                    alert(data && data.error ? data.error : 'Save failed');
                    return;
                }
                window.location.reload();
            } catch (err) {
                console.error(err);
                alert('Unable to save product right now.');
            }
        });

        // Simple client-side search filter by name/brand
        const searchInput = document.getElementById('productSearch');
        const tiles = document.querySelectorAll('.product-tile');
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.trim().toLowerCase();
            tiles.forEach(tile => {
                const name = tile.getAttribute('data-name') || '';
                const brand = tile.getAttribute('data-brand') || '';
                const match = name.includes(term) || brand.includes(term);
                tile.classList.toggle('d-none', term && !match);
            });
        });

        // Delete handler via modal
        const deleteModalEl = document.getElementById('deleteModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl);
        const deleteNameEl = document.getElementById('deleteProductName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        let pendingDeleteId = null;

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                pendingDeleteId = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name') || 'this product';
                deleteNameEl.textContent = name;
                deleteModal.show();
            });
        });

        confirmDeleteBtn.addEventListener('click', async () => {
            if (!pendingDeleteId) {
                deleteModal.hide();
                return;
            }
            try {
                const res = await fetch('../actions/admin/delete_product.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ watch_id: pendingDeleteId })
                });
                const data = await res.json();
                if (!res.ok || !data || data.error) {
                    alert(data && data.error ? data.error : 'Delete failed');
                    return;
                }
                window.location.reload();
            } catch (err) {
                console.error(err);
                alert('Unable to delete product right now.');
            }
        });
    </script>
</body>

</html>
