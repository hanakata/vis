<?php
include 'include/header.php';
include 'include/db_connect.php';
foreach ( $pdo->query ( 'select * from cpes LIMIT 10;' ) as $row ) {
    echo "<p>";
    echo $row['nvd_id'];
    echo $row['vendor'];
    echo $row['product'];
    echo "</p>";
}
?>