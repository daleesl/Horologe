<?php
function generateId($conn, $table, $column, $prefix, $pad = 3) {

    $query = "SELECT $column FROM $table ORDER BY $column DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        $number = (int) substr($row[$column], strlen($prefix));
        $number++;
    } else {
        $number = 1;
    }

    return $prefix . str_pad($number, $pad, '0', STR_PAD_LEFT);
}
?>