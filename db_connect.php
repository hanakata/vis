<?php
$link = mysqli_connect('localhost', 'root', 'ppppp0!!','kb_checker');
if (! $link) {
    die('not connect ' . mysqli_error());
}

// $firstname = 'fred';
// $lastname  = 'fox';

// $query = sprintf("SELECT firstname, lastname, address, age FROM friends 
//     WHERE firstname='%s' AND lastname='%s'",
//     mysql_real_escape_string($firstname),
//     mysql_real_escape_string($lastname));

// $result = mysql_query($query);

// if (!$result) {
//     $message  = 'Invalid query: ' . mysql_error() . "\n";
//     $message .= 'Whole query: ' . $query;
//     die($message);
// }

// while ($row = mysql_fetch_assoc($result)) {
//     echo $row['firstname'];
//     echo $row['lastname'];
//     echo $row['address'];
//     echo $row['age'];
// }

// mysql_free_result($result);
?>