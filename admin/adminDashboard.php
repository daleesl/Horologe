<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'EB Garamond', serif;
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column flex-md-row">
        <!-- Sidebar -->
        <?php include '../includes/adminSidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 w-100 p-3 p-sm-4 p-lg-5">
            <div class="d-md-none mb-3">
                <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas">
                    <i class="bi bi-list"></i> Menu
                </button>
            </div>
            <!-- Header -->
            <div class="mb-5">
                <h1 class="display-5 fw-bold text-white">Dashboard</h1>
                <p class="text-secondary">Welcome to your admin dashboard</p>
            </div>

            <!-- Key Metrics Section -->
            <div class="row g-4 mb-5">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border border-secondary p-4">
                        <p class="text-secondary text-uppercase small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-cash-coin"></i> Total Revenue
                        </p>
                        <h2 class="display-6 fw-bold text-white">$125,450</h2>
                        <p class="text-success small mt-2">
                            <i class="bi bi-arrow-up"></i> +12% from last month
                        </p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border border-secondary p-4">
                        <p class="text-secondary text-uppercase small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-bag-check"></i> Total Orders
                        </p>
                        <h2 class="display-6 fw-bold text-white">247</h2>
                        <p class="text-info small mt-2">
                            <i class="bi bi-clock"></i> 12 pending
                        </p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border border-secondary p-4">
                        <p class="text-secondary text-uppercase small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-people"></i> Total Customers
                        </p>
                        <h2 class="display-6 fw-bold text-white">184</h2>
                        <p class="text-success small mt-2">
                            <i class="bi bi-person-plus"></i> 8 new this month
                        </p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border border-secondary p-4">
                        <p class="text-secondary text-uppercase small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam"></i> In Stock
                        </p>
                        <h2 class="display-6 fw-bold text-white">156</h2>
                        <p class="text-warning small mt-2">
                            <i class="bi bi-exclamation-circle"></i> 5 low stock
                        </p>
                    </div>
                </div>
            </div>

            <!-- Orders Section -->
            <div class="mb-5">
                <h3 class="text-white text-uppercase fw-bold mb-4">Order Overview</h3>
                <div class="row g-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="border border-secondary p-4 text-center">
                            <p class="text-secondary text-uppercase small mb-3">All Orders</p>
                            <h2 class="display-5 fw-bold text-white">247</h2>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="border border-secondary p-4 text-center">
                            <p class="text-secondary text-uppercase small mb-3">Completed</p>
                            <h2 class="display-5 fw-bold text-white">235</h2>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="border border-secondary p-4 text-center">
                            <p class="text-secondary text-uppercase small mb-3">Pending</p>
                            <h2 class="display-5 fw-bold text-white">12</h2>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="border border-secondary p-4 text-center">
                            <p class="text-secondary text-uppercase small mb-3">Avg Order Value</p>
                            <h2 class="display-5 fw-bold text-white">$507</h2>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Recent Orders -->
            <div class="mb-5">
                <h3 class="text-white text-uppercase fw-bold mb-4">Recent Orders</h3>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr class="border-bottom border-secondary">
                                <th class="text-white py-3">Order ID</th>
                                <th class="text-white py-3">Customer</th>
                                <th class="text-white py-3">Product</th>
                                <th class="text-white py-3">Total</th>
                                <th class="text-white py-3">Date</th>
                                <th class="text-white py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-bottom border-secondary">
                                <td class="text-white fw-semibold py-4">ORD-001</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold">John Smith</div>
                                    <div class="text-secondary small">john@email.com</div>
                                </td>
                                <td class="text-white py-4">Submariner</td>
                                <td class="text-white fw-semibold py-4">$45,200</td>
                                <td class="text-white py-4">1/5/2025</td>
                                <td class="py-4">
                                    <span class="badge bg-success">Completed</span>
                                </td>
                            </tr>
                            <tr class="border-bottom border-secondary">
                                <td class="text-white fw-semibold py-4">ORD-002</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold">Emma Johnson</div>
                                    <div class="text-secondary small">emma@email.com</div>
                                </td>
                                <td class="text-white py-4">Daytona</td>
                                <td class="text-white fw-semibold py-4">$62,300</td>
                                <td class="text-white py-4">1/4/2025</td>
                                <td class="py-4">
                                    <span class="badge bg-success">Completed</span>
                                </td>
                            </tr>
                            <tr class="border-bottom border-secondary">
                                <td class="text-white fw-semibold py-4">ORD-003</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold">Michael Brown</div>
                                    <div class="text-secondary small">michael@email.com</div>
                                </td>
                                <td class="text-white py-4">Yacht-Master</td>
                                <td class="text-white fw-semibold py-4">$38,500</td>
                                <td class="text-white py-4">1/4/2025</td>
                                <td class="py-4">
                                    <span class="badge bg-warning text-dark">Pending</span>
                                </td>
                            </tr>
                            <tr class="border-bottom border-secondary">
                                <td class="text-white fw-semibold py-4">ORD-004</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold">Sarah Davis</div>
                                    <div class="text-secondary small">sarah@email.com</div>
                                </td>
                                <td class="text-white py-4">Perpetual Calendar</td>
                                <td class="text-white fw-semibold py-4">$72,500</td>
                                <td class="text-white py-4">1/3/2025</td>
                                <td class="py-4">
                                    <span class="badge bg-success">Completed</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-white fw-semibold py-4">ORD-005</td>
                                <td class="py-4">
                                    <div class="text-white fw-semibold">Robert Wilson</div>
                                    <div class="text-secondary small">robert@email.com</div>
                                </td>
                                <td class="text-white py-4">Tourbillon Skeleton</td>
                                <td class="text-white fw-semibold py-4">$95,000</td>
                                <td class="text-white py-4">1/2/2025</td>
                                <td class="py-4">
                                    <span class="badge bg-success">Completed</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Products -->
            <div class="mb-5">
                <h3 class="text-white text-uppercase fw-bold mb-4">Top Selling Products</h3>
                <div class="row g-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="border border-secondary p-4">
                            <p class="text-secondary text-uppercase small mb-2">1st Place</p>
                            <h4 class="text-white fw-bold mb-2">Submariner Classic</h4>
                            <p class="text-white mb-3">45 units sold</p>
                            <p class="text-success">
                                <i class="bi bi-arrow-up"></i> Revenue: $2,034,000
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="border border-secondary p-4">
                            <p class="text-secondary text-uppercase small mb-2">2nd Place</p>
                            <h4 class="text-white fw-bold mb-2">Daytona Limited</h4>
                            <p class="text-white mb-3">38 units sold</p>
                            <p class="text-success">
                                <i class="bi bi-arrow-up"></i> Revenue: $2,356,600
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="border border-secondary p-4">
                            <p class="text-secondary text-uppercase small mb-2">3rd Place</p>
                            <h4 class="text-white fw-bold mb-2">Perpetual Calendar</h4>
                            <p class="text-white mb-3">32 units sold</p>
                            <p class="text-success">
                                <i class="bi bi-arrow-up"></i> Revenue: $2,320,000
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
</body>
</html>
