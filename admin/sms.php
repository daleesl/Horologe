<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/sign-in.php");
    exit();
}

require_once __DIR__ . '/../config/connect.php';

$search = $_GET['search'] ?? '';
$searchSql = '';
$types = '';
$params = [];

if (!empty($search)) {
    $searchSql = "
        WHERE 
            s.phone_number LIKE CONCAT('%', ?, '%')
            OR s.message LIKE CONCAT('%', ?, '%')
            OR u.fname LIKE CONCAT('%', ?, '%')
            OR u.lname LIKE CONCAT('%', ?, '%')
    ";
    $types = "ssss";
    $params = [$search, $search, $search, $search];
}

$sql = "
    SELECT 
        s.id,
        s.created_at,
        s.direction,
        s.phone_number,
        s.message,
        u.fname,
        u.lname
    FROM sms s
    LEFT JOIN users u ON s.user_id = u.user_id
    $searchSql
    ORDER BY s.created_at DESC
";

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Inbox - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'EB Garamond', serif;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body class="bg-black text-white">
<div class="d-flex flex-column flex-md-row">

    <?php include '../includes/adminSidebar.php'; ?>

    <div class="flex-grow-1 w-100 p-3 p-sm-4">

        <div class="d-md-none mb-3">
            <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas">
                <i class="bi bi-list"></i> Menu
            </button>
        </div>

        <div class="mb-4">
            <h1 class="display-5 fw-normal mb-2">SMS Inbox</h1>
            <p class="text-secondary">View and manage customer messages</p>
        </div>

        <div class="mb-4">
            <form method="GET">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary">
                        <i class="bi bi-search"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control bg-dark border-secondary text-white" 
                        placeholder="Search phone, message, or user..." 
                        value="<?= htmlspecialchars($search) ?>"
                    >
                </div>
            </form>
        </div>

        <div class="border border-secondary rounded overflow-hidden">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead class="border-bottom border-secondary">
                        <tr>
                            <th class="text-secondary fw-normal py-3">Date</th>
                            <th class="text-secondary fw-normal py-3">Direction</th>
                            <th class="text-secondary fw-normal py-3">User</th>
                            <th class="text-secondary fw-normal py-3">Phone</th>
                            <th class="text-secondary fw-normal py-3">Message</th>
                            <th class="text-secondary fw-normal py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res->num_rows > 0): ?>
                            <?php while ($row = $res->fetch_assoc()): ?>
                            <?php 
                                $userDisplay = $row['fname'] 
                                    ? htmlspecialchars($row['fname'] . ' ' . $row['lname']) 
                                    : 'Unknown';

                                $userForModal = $row['fname']
                                    ? htmlspecialchars($row['fname'] . ' ' . $row['lname'])
                                    : 'Unknown';
                            ?>
                                <tr class="border-bottom border-secondary">
                                    <td class="py-3">
                                        <?= date('n/j/Y H:i', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge <?= $row['direction'] === 'incoming' ? 'bg-success' : 'bg-primary' ?>">
                                            <?= strtoupper($row['direction']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                    <?= $row['fname'] 
                                        ? $userDisplay
                                        : '<span class="text-secondary">Unknown</span>'
                                    ?>

                                    </td>
                                    <td class="py-3"><?= htmlspecialchars($row['phone_number']) ?></td>
                                    <td class="py-3 text-truncate" style="max-width: 280px;">
                                        <?= htmlspecialchars($row['message']) ?>
                                    </td>
                                    <td class="py-3 text-end">
                                        <button 
                                            class="btn btn-sm btn-outline-light view-sms-btn"
                                            data-id="<?= $row['id'] ?>"
                                            data-phone="<?= htmlspecialchars($row['phone_number']) ?>"
                                            $dataUser = $row['fname'] ? ($row['fname'] . ' ' . $row['lname']) : null;
                                            data-message="<?= htmlspecialchars($row['message']) ?>"
                                            data-date="<?= date('n/j/Y H:i', strtotime($row['created_at'])) ?>"
                                            data-direction="<?= strtoupper($row['direction']) ?>"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button 
                                            class="btn btn-sm btn-outline-danger delete-sms-btn"
                                            data-id="<?= $row['id'] ?>"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">
                                    No SMS found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="viewSmsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-black text-white border border-secondary rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">SMS Details</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div><strong>Date:</strong> <span id="smsDate"></span></div>
                <div><strong>Direction:</strong> <span id="smsDirection"></span></div>
                <div><strong>User:</strong> <span id="smsUser"></span></div>
                <div><strong>Phone:</strong> <span id="smsPhone"></span></div>
                <hr>
                <div><strong>Message:</strong></div>
                <p id="smsMessage" class="mt-2"></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteSmsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-black text-white border border-danger rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">Delete SMS</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this SMS log?</p>
                <p class="text-secondary small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0">
                <form method="POST" action="../actions/admin/delete_sms.php">
                    <input type="hidden" name="sms_id" id="deleteSmsId">
                    <button class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.view-sms-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('smsDate').textContent = btn.dataset.date;
        document.getElementById('smsDirection').textContent = btn.dataset.direction;
        document.getElementById('smsUser').textContent = btn.dataset.user || 'Unknown';
        document.getElementById('smsPhone').textContent = btn.dataset.phone;
        document.getElementById('smsMessage').textContent = btn.dataset.message;
        new bootstrap.Modal(document.getElementById('viewSmsModal')).show();
    });
});

document.querySelectorAll('.delete-sms-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('deleteSmsId').value = btn.dataset.id;
        new bootstrap.Modal(document.getElementById('deleteSmsModal')).show();
    });
});
</script>
</body>
</html>