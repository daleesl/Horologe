<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users - Admin Panel</title>
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
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
        .status-active {
            color: #198754;
        }
        .status-suspended {
            color: #dc3545;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-icon.icon-white {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .stat-icon.icon-green {
            background-color: rgba(25, 135, 84, 0.2);
        }
        .stat-icon.icon-red {
            background-color: rgba(220, 53, 69, 0.2);
        }
        .filter-tab {
            background: transparent;
            border: 1px solid #444;
            color: #999;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .filter-tab:hover {
            border-color: #666;
            color: #fff;
        }
        .filter-tab.active {
            background-color: #fff;
            color: #000;
            border-color: #fff;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        .search-input {
            background-color: #1a1a1a;
            border: 1px solid #333;
            color: #fff;
            padding: 12px 20px 12px 45px;
        }
        .search-input:focus {
            background-color: #1a1a1a;
            border-color: #555;
            color: #fff;
            box-shadow: none;
        }
        .table-dark {
            --bs-table-bg: transparent;
        }
        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #333;
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
                <h1 class="display-5 fw-normal mb-2">Users</h1>
                <p class="text-secondary mb-0">Manage customer accounts and permissions</p>
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <!-- Total Users -->
                <div class="col-lg-4">
                    <div class="border border-secondary rounded p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary small mb-1">Total Users</p>
                                <h2 class="display-6 fw-normal mb-0">8</h2>
                            </div>
                            <div class="stat-icon icon-white">
                                <i class="bi bi-people fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="col-lg-4">
                    <div class="border border-secondary rounded p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary small mb-1">Active Users</p>
                                <h2 class="display-6 fw-normal mb-0 text-success">6</h2>
                            </div>
                            <div class="stat-icon icon-green">
                                <i class="bi bi-person-check fs-4 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Suspended -->
                <div class="col-lg-4">
                    <div class="border border-secondary rounded p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary small mb-1">Suspended</p>
                                <h2 class="display-6 fw-normal mb-0 text-danger">2</h2>
                            </div>
                            <div class="stat-icon icon-red">
                                <i class="bi bi-person-x fs-4 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="d-flex gap-2 mb-4">
                <button class="filter-tab active">All Users <span class="ms-1">8</span></button>
                <button class="filter-tab">Active <span class="ms-1">6</span></button>
                <button class="filter-tab">Suspended <span class="ms-1">2</span></button>
            </div>

            <!-- Search Bar -->
            <div class="mb-4 position-relative">
                <i class="bi bi-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                <input type="text" class="form-control search-input w-100" placeholder="Search by name or email...">
            </div>

            <!-- Users Table -->
            <div class="border border-secondary rounded overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-dark mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="text-secondary fw-normal">User ID</th>
                                <th scope="col" class="text-secondary fw-normal">Name</th>
                                <th scope="col" class="text-secondary fw-normal">Contact</th>
                                <th scope="col" class="text-secondary fw-normal">Status</th>
                                <th scope="col" class="text-secondary fw-normal">Join Date</th>
                                <th scope="col" class="text-secondary fw-normal">Total Spent</th>
                                <th scope="col" class="text-secondary fw-normal">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- User 1 -->
                            <tr>
                                <td class="text-white">USR-001</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar text-white">JS</div>
                                        <div>
                                            <div class="text-white fw-semibold">John Smith</div>
                                            <div class="text-secondary small">3 orders</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white">
                                        <div class="mb-1"><i class="bi bi-envelope me-2"></i>john.smith@email.com</div>
                                        <div class="text-secondary small"><i class="bi bi-telephone me-2"></i>+1 (555) 123-4567</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="status-active">Active</span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" checked>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-white">1/15/2024</td>
                                <td class="text-white">$128,000</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-light">
                                        <i class="bi bi-eye me-1"></i>View
                                    </button>
                                </td>
                            </tr>

                            <!-- User 2 -->
                            <tr>
                                <td class="text-white">USR-002</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar text-white">EJ</div>
                                        <div>
                                            <div class="text-white fw-semibold">Emma Johnson</div>
                                            <div class="text-secondary small">2 orders</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white">
                                        <div class="mb-1"><i class="bi bi-envelope me-2"></i>emma.j@email.com</div>
                                        <div class="text-secondary small"><i class="bi bi-telephone me-2"></i>+1 (555) 234-5678</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="status-active">Active</span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" checked>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-white">5/22/2024</td>
                                <td class="text-white">$104,000</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-light">
                                        <i class="bi bi-eye me-1"></i>View
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

    <script>
        // Handle status toggle
        document.querySelectorAll('.form-check-input').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const statusText = this.closest('td').querySelector('span');
                if (this.checked) {
                    statusText.textContent = 'Active';
                    statusText.classList.remove('status-suspended');
                    statusText.classList.add('status-active');
                } else {
                    statusText.textContent = 'Suspended';
                    statusText.classList.remove('status-active');
                    statusText.classList.add('status-suspended');
                }
            });
        });

        // Handle filter tabs
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>
