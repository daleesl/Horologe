<?php

require_once __DIR__ . '/env.php';

date_default_timezone_set('Asia/Manila');

$dbhost = getenv('DB_HOST') ?: 'localhost';
$dbuser = getenv('DB_USER') ?: 'root';
$dbpass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'horologe';

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

try {
	$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
} catch (PDOException $e) {
	die("Connection failed: " . $e->getMessage());
}

/**
 * Execute a raw SQL query.
 *
 * @param string $query
 * @return mysqli_result|bool
 */
function executeQuery(string $query)
{
    return mysqli_query($GLOBALS['conn'], $query);
}
?>
