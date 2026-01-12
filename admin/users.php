<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

?>

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

            <?php
            require_once __DIR__ . '/../config/connect.php';
            $userStats = [
                'total' => 0,
                'active' => 0,
                'suspended' => 0,
            ];
            $res = $conn->query("SELECT COUNT(*) AS total, SUM(status = 'active') AS active, SUM(status = 'inactive') AS suspended FROM users");
            if ($res) {
                $row = $res->fetch_assoc();
                $userStats['total'] = (int)($row['total'] ?? 0);
                $userStats['active'] = (int)($row['active'] ?? 0);
                $userStats['suspended'] = (int)($row['suspended'] ?? 0);
            }
            ?>
            <!-- Stats Cards -->
            <div class="row g-3 mb-4">

                <!-- Active Users -->
                <div class="col-lg-4">
                    <div class="border border-secondary rounded p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary small mb-1">Active Users</p>
                                <h2 class="display-6 fw-normal mb-0 text-success"><?= htmlspecialchars($userStats['active']); ?></h2>
                            </div>
                            <div class="stat-icon icon-green">
                                <i class="bi bi-person-check fs-4 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <!-- Users Table -->
            <?php
            $users = [];
            $sql = "SELECT u.user_id, u.fname, u.lname, u.email, u.phone_number, u.status, u.created_at,
                        (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.user_id) AS order_count,
                        (SELECT COALESCE(SUM(total_amount),0) FROM orders o WHERE o.user_id = u.user_id) AS total_spent
                    FROM users u
                    WHERE role  = 'user'
                    ORDER BY u.created_at DESC";
            $res = $conn->query($sql);
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $users[] = $row;
                }
            }
            function moneyFormat(float $value): string { return '$' . number_format($value, 2, '.', ','); }
            ?>
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
           
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)) : ?>
                                <?php foreach ($users as $user) : ?>
                                    <?php
                                    $initials = strtoupper(substr($user['fname'], 0, 1) . substr($user['lname'], 0, 1));
                                    $statusClass = $user['status'] === 'active' ? 'status-active' : 'status-suspended';
                                    $joinDate = $user['created_at'] ? date('n/j/Y', strtotime($user['created_at'])) : '--';
                                    ?>
                                    <tr>
                                        <td class="text-white"><?= htmlspecialchars($user['user_id']); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="user-avatar text-white"><?= htmlspecialchars($initials); ?></div>
                                                <div>
                                                    <div class="text-white fw-semibold"><?= htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></div>
                                                    <div class="text-secondary small"><?= (int)$user['order_count']; ?> orders</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-white small"><?= htmlspecialchars($user['email']); ?></div>
                                            <div class="text-secondary small"><?= htmlspecialchars($user['phone_number']); ?></div>
                                        </td>
                                        <td class="<?= $statusClass; ?> fw-semibold"><?= htmlspecialchars(ucfirst($user['status'])); ?></td>
                                        <td><?= htmlspecialchars($joinDate); ?></td>
                                        <td><?= moneyFormat((float)$user['total_spent']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="7" class="text-center text-secondary">No users found.</td></tr>
                            <?php endif; ?>
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
