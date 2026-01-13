<?php
include 'config/connect.php';
$result = $conn->query('DESCRIBE watch');
if($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . PHP_EOL;
    }
} else {
    echo 'Error: ' . $conn->error;
}
?>
