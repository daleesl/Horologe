<?php
include("config/connect.php");
include "helpers/id_generator.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}


$fname = mysqli_real_escape_string($conn, $_POST['fname'] ?? '');
$lname = mysqli_real_escape_string($conn, $_POST['lname'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$password_input = mysqli_real_escape_string($conn, $_POST['password'] ?? '');
$phone = mysqli_real_escape_string($conn, $_POST['phone_number'] ?? '');


$check = "SELECT user_id FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $check);

if (mysqli_num_rows($result) > 0) {
    echo "User already exists";
    exit;
}

$user_id = generateId($conn, 'users', 'user_id', 'USR');
$password = password_hash($password_input, PASSWORD_DEFAULT);

$insert = "
    INSERT INTO users
    (user_id, fname, lname, email, password, phone_number, status, created_at, updated_at)
    VALUES
    ('$user_id', '$fname', '$lname', '$email', '$password', '$phone', 'active', NOW(), NOW())
";

if (mysqli_query($conn, $insert)) {
    echo "User synced successfully";
} else {
    http_response_code(500);
    echo "Database insert failed";
}