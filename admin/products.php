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
        body {
            font-family: 'EB Garamond', serif;
        }
        .nav-link.active {
            background-color: #495057 !important;
        }
        .product-card {
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-black text-white">
    <div class="d-flex flex-column flex-md-row">
        <!-- Sidebar -->
        <?php include '../includes/adminSidebar.php'; ?>

        <!-- Main Content -->
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
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control bg-dark border-secondary text-white" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-lg-4 text-end">
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Product
                    </button>
                </div>
            </div>

            <!-- Products by Category -->
            <div class="mb-5">
                <h3 class="h5 text-uppercase mb-4 border-bottom border-secondary pb-2">ROLEX</h3>
                <div class="row g-4">
                    <!-- Product Card 1 -->
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card border border-secondary bg-dark p-3 rounded">
                            <img src="../assets/images/products/rolex/rolex-1.png" alt="Rolex Cosmograph" class="img-fluid mb-3" style="height: 250px; object-fit: contain; width: 100%;">
                            <h5 class="text-white mb-2">Rolex Cosmograph</h5>
                            <p class="text-white fw-bold mb-2">$12,400</p>
                            <p class="text-secondary small mb-3">Stock: 5</p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-light flex-fill" data-bs-toggle="modal" data-bs-target="#editProductModal">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger flex-fill">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 2 -->
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card border border-secondary bg-dark p-3 rounded">
                            <img src="../assets/images/products/rolex/rolex-1.png" alt="Rolex Cosmograph" class="img-fluid mb-3" style="height: 250px; object-fit: contain; width: 100%;">
                            <h5 class="text-white mb-2">Rolex Cosmograph</h5>
                            <p class="text-white fw-bold mb-2">$12,400</p>
                            <p class="text-secondary small mb-3">Stock: 5</p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-light flex-fill" data-bs-toggle="modal" data-bs-target="#editProductModal">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger flex-fill">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 3 -->
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card border border-secondary bg-dark p-3 rounded">
                            <img src="../assets/images/products/rolex/rolex-1.png" alt="Rolex Cosmograph" class="img-fluid mb-3" style="height: 250px; object-fit: contain; width: 100%;">
                            <h5 class="text-white mb-2">Rolex Cosmograph</h5>
                            <p class="text-white fw-bold mb-2">$12,400</p>
                            <p class="text-secondary small mb-3">Stock: 5</p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-light flex-fill" data-bs-toggle="modal" data-bs-target="#editProductModal">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger flex-fill">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARTIER Category -->
            <div class="mb-5">
                <h3 class="h5 text-uppercase mb-4 border-bottom border-secondary pb-2">CARTIER</h3>
                <div class="row g-4">
                    <!-- Add more product cards here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" id="productName">
                        </div>
                        <div class="mb-3">
                            <label for="productCategory" class="form-label">Category</label>
                            <select class="form-select bg-dark border-secondary text-white" id="productCategory">
                                <option>ROLEX</option>
                                <option>CARTIER</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Price</label>
                            <input type="number" class="form-control bg-dark border-secondary text-white" id="productPrice">
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Stock</label>
                            <input type="number" class="form-control bg-dark border-secondary text-white" id="productStock">
                        </div>
                        <div class="mb-3">
                            <label for="productImage" class="form-label">Product Image</label>
                            <input type="file" class="form-control bg-dark border-secondary text-white" id="productImage" accept="image/*">
                            <small class="text-secondary">Upload the product image (JPG, PNG).</small>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Description</label>
                            <textarea class="form-control bg-dark border-secondary text-white" id="productDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-light">Add Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="editProductId">
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Product Name</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" id="editProductName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductCategory" class="form-label">Category</label>
                            <select class="form-select bg-dark border-secondary text-white" id="editProductCategory" required>
                                <option value="">Select category</option>
                                <option>ROLEX</option>
                                <option>CARTIER</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editProductPrice" class="form-label">Price</label>
                            <input type="number" class="form-control bg-dark border-secondary text-white" id="editProductPrice" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductStock" class="form-label">Stock</label>
                            <input type="number" class="form-control bg-dark border-secondary text-white" id="editProductStock" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductImage" class="form-label">Product Image</label>
                            <input type="file" class="form-control bg-dark border-secondary text-white" id="editProductImage" accept="image/*">
                            <small class="text-secondary">Upload the product image (JPG, PNG).</small>
                        </div>
                        <div class="mb-3">
                            <label for="editProductDescription" class="form-label">Description</label>
                            <textarea class="form-control bg-dark border-secondary text-white" id="editProductDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-light">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
