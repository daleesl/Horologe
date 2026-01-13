<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

// Redirect unauthenticated users to sign-in
if (!isset($_SESSION['user_id'])) {
	header('Location: ../auth/sign-in.php');
	exit();
}

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
	session_unset();
	session_destroy();
	header('Location: ../auth/sign-in.php');
	exit();
}

// Handle profile update (save changes)
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
		// Ensure email is unique (skip current user)
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
				// also keep alternate key used elsewhere
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

// Fetch user details from DB
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
						<form method="post" action="#" class="row g-4">

							<div class="col-12">
								<div class="row g-3">
									<div class="col-12">
										<label class="form-label text-white fw-semibold">First Name</label>
										<input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
									</div>
									<div class="col-12">
										<label class="form-label text-white fw-semibold">Last Name</label>
										<input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
									</div>
									<div class="col-12">
										<label class="form-label text-white fw-semibold">Email Address</label>
										<input type="email" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
									</div>
									<div class="col-12">
										<label class="form-label text-white fw-semibold">Phone Number</label>
										<input type="number" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
									</div>
								</div>
							</div>



							<div class="col-12 d-flex justify-content-end gap-2">
	
									<button type="submit" class="btn btn-success btn-lg rounded-pill shadow-sm me-3">
										Save Changes
									</button>
									<button type="submit" name="logout" value="1" class="btn btn-outline-light btn-lg">Logout</button>
							</div>
						</form>
					</div>
				</div>
			</main>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<script src="../assets/js/cart.js"></script>
</body>

</html>