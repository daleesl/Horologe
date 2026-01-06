<?php
include "../config/connect.php";
include "sms.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = uniqid("USR");
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $status = "active";

    $stmt = $conn->prepare(
        "INSERT INTO users 
        (user_id, fname, lname, email, password, phone_number, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

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

    if ($stmt->execute()) {

        $phoneFormatted = '+63' . substr($phone, 1);

        $response = sendSMS(
            $phoneFormatted,
            "Hello $fname! Your Horologe account has been successfully registered."
        );

        header("Location: sign-in.php");
        exit();

    } else {
        echo "Execute failed: " . $stmt->error;
    }

    $stmt->close();
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up - Horologe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body class="bg-black">

    <nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-secondary">
        <div class="container-fluid d-flex justify-content-center py-2 py-md-3">
            <div class="navbar-brand text-center">
                <span class="text-white">HOROLOGE</span>
            </div>
        </div>
    </nav>

    <div class="d-flex align-items-center justify-content-center py-5 px-3 px-sm-0">
        <div class="w-100" style="max-width: 500px;">
            <form action="register.php" method="POST" class="border rounded border-secondary bg-dark p-3 p-sm-4">
                <h1 class="fs-3 fs-sm-2 fw-bold text-white mb-3 text-uppercase">Become a Member</h1>
                <p class="fs-6 text-secondary text-uppercase mb-3">Create account to start your horological journey</p>

                <div class="row g-3 mb-4 mb-md-3">
                    <div class="col-md-6 col-12">
                        <label for="firstName" class="form-label fs-6 text-secondary text-uppercase">First Name</label>
                        <input type="text" class="form-control form-control-lg bg-dark border-secondary text-white"
                            id="firstName" name="first_name" placeholder="" required>
                    </div>
                    <div class="col-md-6 col-12">
                        <label for="lastName" class="form-label fs-6 text-secondary text-uppercase">Last Name</label>
                        <input type="text" class="form-control form-control-lg bg-dark border-secondary text-white"
                            id="lastName" name="last_name" placeholder="" required>
                    </div>
                </div>

                <label for="email" class="form-label fs-6 text-secondary text-uppercase">Email Address</label>
                <input type="email"
                    class="form-control form-control-lg bg-dark border-secondary text-white mb-3 mb-sm-4" id="email"
                    name="email" placeholder="" required>

                <label for="phone" class="form-label fs-6 text-secondary text-uppercase">Mobile Number</label>
                <input type="tel" class="form-control form-control-lg bg-dark border-secondary text-white mb-3 mb-sm-4"
                    id="phone" name="phone" placeholder="09XXXXXXXXX" pattern="^09\d{9}$" required>

                <label for="password" class="form-label fs-6 text-secondary text-uppercase">Password</label>
                <input type="password"
                    class="form-control form-control-lg bg-dark border-secondary text-white mb-4 mb-sm-5" id="password"
                    name="password" placeholder="" required>

                <button type="submit"
                    class="btn btn-light w-100 fw-bold py-2 py-sm-3 text-uppercase mb-3 mb-sm-4">Create Profile</button>

                <p class="text-center fs-6 text-secondary text-uppercase">Already a member? <a href="sign-in.php"
                        class="text-white text-decoration-none fw-bold">Sign In</a></p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>