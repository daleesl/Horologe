<?php
session_start();
require_once "../config/connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT 
            user_id,
            fname,
            lname,
            password,
            status,
            role
         FROM users
         WHERE email = ?
         LIMIT 1"
    );

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (!password_verify($password, $user['password'])) {
            $error = "Invalid email or password.";
        }
        elseif ($user['status'] !== 'active') {
            $error = "Your account is inactive. Contact admin.";
        }
        else {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['fname']   = $user['fname'];
            $_SESSION['lname']   = $user['lname'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: ../admin/adminDashboard.php");
            } else {
                header("Location: ../public/index.php");
            }
            exit();
        }

    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - Horologe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="bg-black">
    <nav class="navbar navbar-dark bg-black border-bottom border-secondary">
        <div class="container-fluid d-flex justify-content-center py-2 py-md-3">
             <div class="navbar-brand position-absolute top-50 start-50 translate-middle d-none d-lg-block" href="index.php">
            <span class="display-6 header font-primary text-white">HOROLOGE</span>
        </div>
        </div>
    </nav>

    <div class="d-flex align-items-center justify-content-center py-5 px-3 px-sm-0">
        <div class="w-100" style="max-width: 500px;">
            <form method="POST" action="" class="border rounded border-secondary bg-dark p-3 p-sm-4">
                <h1 class="fs-3 fs-sm-2 fw-bold text-white mb-3 text-uppercase">Sign In</h1>
                <p class="fs-6 text-secondary text-uppercase mb-4">Enter your credentials to access your account</p>

                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <label for="email" class="form-label fs-6 text-secondary text-uppercase">Email Address</label>
                <input type="email" name="email" id="email" class="form-control form-control-lg bg-dark border-secondary text-white mb-3 mb-sm-4" placeholder="sample@account@gmail.com" required>

                <label for="password" class="form-label fs-6 text-secondary text-uppercase">Password</label>
                <input type="password" name="password" id="password" class="form-control form-control-lg bg-dark border-secondary text-white mb-4 mb-sm-5" required>

                <button type="submit" class="btn btn-light w-100 fw-bold py-2 py-sm-3 text-uppercase mb-3 mb-sm-4">Sign In</button>

                <div class="text-center mb-3 mb-sm-4"><span class="text-secondary fs-6 text-uppercase">Or</span></div>
                <p class="text-center fs-6 text-secondary text-uppercase">
                    Don't have an account? 
                    <a href="register.php" class="text-white text-decoration-none fw-bold">Become a Member</a>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
