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

$user = [
	'first_name' => $_SESSION['fname'] ?? '',
	'last_name'  => $_SESSION['lname'] ?? '',
	'email'      => '',
	'phone'      => '',
	'country'    => '',
	'city'       => '',
	'address'    => '',
	'zip'        => '',
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
						<div class="col-12 d-flex align-items-center gap-3">
							<div>
								<img src="https://i.pravatar.cc/96" alt="Profile photo" class="rounded-circle border border-secondary" style="width: 96px; height: 96px; object-fit: cover;">
							</div>
							<div class="flex-grow-1">
								<label class="form-label mb-1 text-white fw-semibold">Change Avatar</label>
								<input type="file" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="avatar" accept="image/png, image/jpeg">
								<div class="form-text text-secondary">PNG or JPG up to 2MB.</div>
							</div>
						</div>

						<div class="col-12">
							<div class="row g-3">
								<div class="col-md-6">
									<label class="form-label text-white fw-semibold">First Name</label>
									<input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
								</div>
								<div class="col-md-6">
									<label class="form-label text-white fw-semibold">Last Name</label>
									<input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
								</div>
								<div class="col-md-6">
									<label class="form-label text-white fw-semibold">Email Address</label>
									<input type="email" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
								</div>
								<div class="col-md-6">
									<label class="form-label text-white fw-semibold">Phone Number</label>
									<input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
								</div>
							</div>
						</div>

						<div class="col-12">
							<h6 class="fw-semibold mb-3">Personal Address</h6>
							<div class="row g-3">
								<div class="col-md-6">
									<label class="form-label text-white fw-semibold">Country</label>
									<input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="country" value="<?php echo htmlspecialchars($user['country']); ?>">
								</div>
								<div class="col-md-6">
									<label class="form-label text-white fw-semibold">City</label>
									<input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="city" value="<?php echo htmlspecialchars($user['city']); ?>">
								</div>
								<div class="col-md-6">
									<label class="form-label text-white fw-semibold">Address</label>
									<input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
								</div>
								<div class="col-md-6">
									<label class="form-label text-white fw-semibold">Zip Code</label>
									<input type="text" class="form-control form-control-lg bg-dark text-secondary border-secondary rounded-3" name="zip" value="<?php echo htmlspecialchars($user['zip']); ?>">
								</div>
							</div>
						</div>

						<div class="col-12 d-flex justify-content-end gap-2">
							<form method="post" class="mb-0">
								<button type="submit" name="logout" value="1" class="btn btn-outline-light btn-lg">Logout</button>
							</form>
						</div>
					</form>
				</div>
			</div>
		</main>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>