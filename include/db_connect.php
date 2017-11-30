<?php
$pdo = new PDO ( 'mysql:dbname=vuls; host=localhost;port=3306; charset=utf8', 'root', 'P@ssw0rd' ,  array(
    PDO::MYSQL_ATTR_READ_DEFAULT_FILE => '/etc/my.cnf', 
   ));
mysql_query('SET NAMES utf8', $sql );
?>