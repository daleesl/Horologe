<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/sign-in.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../auth/sign-in.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['logout'])) {
    $firstName = trim((string)($_POST['first_name'] ?? ''));
    $lastName = trim((string)($_POST['last_name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));

    $errors = [];
    if ($firstName === '' || $lastName === '') {
        $errors[] = 'First and last name are required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }

    if (empty($errors)) {
        $check = $conn->prepare('SELECT user_id FROM users WHERE email = ? AND user_id != ? LIMIT 1');
        if ($check) {
            $check->bind_param('ss', $email, $_SESSION['user_id']);
            $check->execute();
            $res = $check->get_result();
            if ($res && $res->fetch_assoc()) {
                $errors[] = 'Email is already in use by another account.';
            }
            $check->close();
        }
    }

    if (empty($errors)) {
        $upd = $conn->prepare('UPDATE users SET fname = ?, lname = ?, email = ?, phone_number = ?, updated_at = NOW() WHERE user_id = ?');
        if ($upd) {
            $upd->bind_param('sssss', $firstName, $lastName, $email, $phone, $_SESSION['user_id']);
            if ($upd->execute()) {
                $_SESSION['fname'] = $firstName;
                $_SESSION['lname'] = $lastName;
                $_SESSION['email'] = $email;
                $_SESSION['userID'] = $_SESSION['user_id'];
                $_SESSION['phone_number'] = $phone;

                $upd->close();
                header('Location: account.php?updated=1');
                exit();
            } else {
                $errors[] = 'Failed to save changes. Please try again.';
            }
            $upd->close();
        } else {
            $errors[] = 'Failed to prepare update statement.';
        }
    }
    if (!empty($errors)) {
        $_SESSION['account_update_errors'] = $errors;
        header('Location: account.php?updated=0');
        exit();
    }
}

$user = [
    'first_name' => $_SESSION['fname'] ?? '',
    'last_name'  => $_SESSION['lname'] ?? '',
    'email'      => '',
    'phone'      => '',
];

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT email, phone_number FROM users WHERE user_id = ? LIMIT 1');
if ($stmt) {
    $stmt->bind_param('s', $userId);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user['email'] = $row['email'] ?? '';
            $user['phone'] = $row['phone_number'] ?? '';
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="bg-black text-light">

<?php include '../includes/navbar.php'; ?>

<?php
if (isset($_GET['updated'])) {
    if ($_GET['updated'] === '1') {
        echo '<div class="container mt-4"><div class="alert alert-success">Account updated successfully.</div></div>';
    } else {
        $errs = $_SESSION['account_update_errors'] ?? [];
        unset($_SESSION['account_update_errors']);
        if (!empty($errs)) {
            echo '<div class="container mt-4"><div class="alert alert-danger"><ul>';
            foreach ($errs as $e) echo '<li>' . htmlspecialchars($e) . '</li>';
            echo '</ul></div></div>';
        } else {
            echo '<div class="container mt-4"><div class="alert alert-danger">Failed to update account.</div></div>';
        }
    }
}
?>

<div class="container-fluid min-vh-100">
    <div class="row flex-nowrap min-vh-100">
        <main class="col-12 col-xl-11 mx-auto py-4 px-3 px-lg-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-2">
                <div>
                    <h3 class="mb-1">Setting</h3>
                    <p class="text-secondary mb-0">Manage your account details and preferences.</p>
                </div>
            </div>

            <div class="card bg-dark border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-lg-5">

                    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark text-light">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmModalLabel">Confirm Credential Change</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body fs-6">
                                    <p class="text-white text-secondary">Are you sure you want to change your account credentials (email, password, or other sensitive information)?<br>This action will update your credentials in the database.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary text-secondary text-white" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-success text-secondary text-white" id="confirmSaveBtn">Yes, Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark text-light">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body fs-6">
                                    <p class="text-white text-secondary">Are you sure you want to logout? Any unsaved changes will be lost.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary text-secondary text-white" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger text-secondary text-white" id="confirmLogoutBtn">Yes, Logout</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="post" action="#" class="row g-4" id="accountForm">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-white fw-semibold text-secondary fs-5">First Name</label>
                                    <input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-white fw-semibold text-secondary fs-5">Last Name</label>
                                    <input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-white fw-semibold text-secondary fs-5">Email Address</label>
                                    <input type="email" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-white fw-semibold text-secondary fs-5">Phone Number</label>
                                    <input type="number" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill shadow-sm me-3 text-secondary text-white" id="saveChangesBtn">Save Changes</button>
                            <button type="button" class="btn btn-outline-light btn-lg text-secondary text-white" id="logoutBtn">Logout</button>
                        </div>
                    </form>

                    <form id="logoutForm" method="post" style="display:none;">
                        <input type="hidden" name="logout" value="1">
                    </form>

                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/cart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('accountForm');
    const logoutForm = document.getElementById('logoutForm');
    const saveBtn = document.getElementById('saveChangesBtn');
    const logoutBtn = document.getElementById('logoutBtn');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const confirmSaveBtn = document.getElementById('confirmSaveBtn');
    const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');

    let submitRequested = false;

    saveBtn.addEventListener('click', function (e) {
        e.preventDefault();
        confirmModal.show();
    });

    confirmSaveBtn.addEventListener('click', function () {
        submitRequested = true;
        form.submit();
    });

    logoutBtn.addEventListener('click', function () {
        confirmModal.hide();
        const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
        logoutModal.show();
    });

    confirmLogoutBtn.addEventListener('click', function () {
        logoutForm.submit();
    });

    form.addEventListener('submit', function (e) {
        if (!submitRequested) {
            e.preventDefault();
        } else {
            submitRequested = false;
        }
    });
});
</script>

</body>
</html>