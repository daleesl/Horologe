<?php
session_start();
include "../config/connect.php";
include "../sms/sms.php";
include "../helpers/id_generator.php";

$error = ""; 
$fname = $lname = $email = $phone = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password_input = $_POST['password'];
    $confirm_password_input = $_POST['confirm_password'] ?? '';

    if ($password_input !== $confirm_password_input) {
        $error = "Passwords do not match.";
    } elseif (empty($fname) || empty($lname) || empty($email) || empty($password_input)) {
        $error = "Please fill in all required fields.";
    } else {

        $user_id = generateId($conn, 'users', 'user_id', 'USR');
        $password = password_hash($password_input, PASSWORD_DEFAULT);
        $status = "active";

        try {
            $stmt = $conn->prepare(
                "INSERT INTO users 
                (user_id, fname, lname, email, password, phone_number, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

            $stmt->bind_param(
                "sssssss",
                $user_id,
                $fname,
                $lname,
                $email,
                $password,
                $phone,
                $status
            );

            $stmt->execute();

            $apiUrl = "http://172.20.10.3/Workspace/MediTrack/api.php";

            $postData = [
                'username' => $fname . ' ' . $lname,
                'email'    => $email,
                'password' => $password_input, // already hashed
                'address'  => '',         // no address in Horologe
                'contact'  => $phone
            ];

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                error_log("MediTrack API Error: " . $curlError);
            }

            if (!empty($phone)) {
                $phoneFormatted = '+63' . substr($phone, 1);
                sendSMS(
                    $user_id, $phoneFormatted, "Welcome to Horologe, $fname! Your account is now active."
                );
                file_put_contents(
                    __DIR__ . '/../logs/register_debug.log',
                    "[" . date('Y-m-d H:i:s') . "] sendSMS() about to run. Phone: $phone\n",
                    FILE_APPEND
                );                
            }

            session_regenerate_id(true);
            $_SESSION['user_id'] = $user_id;
            $_SESSION['fname']   = $fname;
            $_SESSION['lname']   = $lname;

            header("Location: ../public/index.php");
            exit();

        } catch (mysqli_sql_exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $error = "Email already exists. Try another email.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if (isset($stmt)) $stmt->close();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Horologe</title>
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
                <h1 class="fs-3 fs-sm-2 fw-bold text-white mb-3 text-uppercase">Register</h1>
                <p class="fs-6 text-secondary text-uppercase mb-4">Create your account to start shopping</p>

                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if (!empty($success)) : ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <label for="first_name" class="form-label fs-6 text-secondary text-uppercase">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control form-control-lg bg-dark border-secondary text-white mb-3 mb-sm-4" value="<?php echo htmlspecialchars($fname); ?>" required>

                <label for="last_name" class="form-label fs-6 text-secondary text-uppercase">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control form-control-lg bg-dark border-secondary text-white mb-3 mb-sm-4" value="<?php echo htmlspecialchars($lname); ?>" required>

                <label for="email" class="form-label fs-6 text-secondary text-uppercase">Email Address</label>
                <input type="email" name="email" id="email" class="form-control form-control-lg bg-dark border-secondary text-white mb-3 mb-sm-4" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="phone" class="form-label fs-6 text-secondary text-uppercase">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control form-control-lg bg-dark border-secondary text-white mb-3 mb-sm-4" value="<?php echo htmlspecialchars($phone); ?>">

                <label for="password" class="form-label fs-6 text-secondary text-uppercase">Password</label>
                <input type="password" name="password" id="password" class="form-control form-control-lg bg-dark border-secondary text-white mb-4 mb-sm-5" required>

                <label for="confirm_password" class="form-label fs-6 text-secondary text-uppercase">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-lg bg-dark border-secondary text-white mb-4 mb-sm-5" required>

                <button type="submit" class="btn btn-light w-100 fw-bold py-2 py-sm-3 text-uppercase mb-3 mb-sm-4">Register</button>

                <div class="text-center mb-3 mb-sm-4"><span class="text-secondary fs-6 text-uppercase">Or</span></div>
                <p class="text-center fs-6 text-secondary text-uppercase">
                    Already have an account? 
                    <a href="sign-in.php" class="text-white text-decoration-none fw-bold">Sign In</a>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>