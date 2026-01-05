<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'EB Garamond', serif;
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
            <!-- Search Bar -->
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control bg-dark border-secondary text-white" placeholder="Search by order ID or customer name...">
                </div>
            </div>

            <!-- Orders Table -->
            <div class="border border-secondary rounded overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead class="border-bottom border-secondary">
                            <tr>
                                <th scope="col" class="text-secondary fw-normal py-3">Order ID</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Customer</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Products</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Total</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Date</th>
                                <th scope="col" class="text-secondary fw-normal py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Order 1 -->
                            <tr class="border-bottom border-secondary">
                                <td class="text-white fw-semibold py-4">ORD-001</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold mb-1">John Smith</div>
                                    <div class="text-secondary small">john.smith@email.com</div>
                                </td>
                                <td class="text-white py-4">Royal Oak Chronograph</td>
                                <td class="text-white fw-semibold py-4">$45,000</td>
                                <td class="text-white py-4">1/2/2025</td>
                                <td class="py-4">
                                    <button class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i>
                                        <span>View</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Order 2 -->
                            <tr class="border-bottom border-secondary">
                                <td class="text-white fw-semibold py-4">ORD-002</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold mb-1">Emma Johnson</div>
                                    <div class="text-secondary small">emma.j@email.com</div>
                                </td>
                                <td class="text-white py-4">Nautilus Moon Phase</td>
                                <td class="text-white fw-semibold py-4">$52,000</td>
                                <td class="text-white py-4">1/3/2025</td>
                                <td class="py-4">
                                    <button class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i>
                                        <span>View</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Order 3 -->
                            <tr class="border-bottom border-secondary">
                                <td class="text-white fw-semibold py-4">ORD-003</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold mb-1">Michael Brown</div>
                                    <div class="text-secondary small">m.brown@email.com</div>
                                </td>
                                <td class="text-white py-4">Submariner GMT</td>
                                <td class="text-white fw-semibold py-4">$38,000</td>
                                <td class="text-white py-4">1/3/2025</td>
                                <td class="py-4">
                                    <button class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i>
                                        <span>View</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Order 4 -->
                            <tr class="border-bottom border-secondary">
                                <td class="text-white fw-semibold py-4">ORD-004</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold mb-1">Sarah Davis</div>
                                    <div class="text-secondary small">sarah.davis@email.com</div>
                                </td>
                                <td class="py-4">
                                    <div class="text-white">Perpetual Calendar</div>
                                    <div class="text-secondary small">+1 more</div>
                                </td>
                                <td class="text-white fw-semibold py-4">$72,500</td>
                                <td class="text-white py-4">1/4/2025</td>
                                <td class="py-4">
                                    <button class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i>
                                        <span>View</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Order 5 -->
                            <tr class="border-bottom border-secondary">
                                <td class="text-white fw-semibold py-4">ORD-005</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold mb-1">Robert Wilson</div>
                                    <div class="text-secondary small">r.wilson@email.com</div>
                                </td>
                                <td class="text-white py-4">Tourbillon Skeleton</td>
                                <td class="text-white fw-semibold py-4">$95,000</td>
                                <td class="text-white py-4">1/4/2025</td>
                                <td class="py-4">
                                    <button class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i>
                                        <span>View</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>


