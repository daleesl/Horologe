<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "horologe"; // database name

// Create connection
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function to execute queries
function executeQuery($query) {
    global $conn;
    return $conn->query($query);
}
?>