<div class="container" style="padding:70px 0 0 0">
<table class="table table-striped">
<?php
include 'include/header.php';
include 'include/db_connect.php';
$product_list_all = "";
foreach ( $pdo->query ( 'select * from cpes;' ) as $row ) {
    $product_list_all[] = $row['product'];
}
$product_list = array_unique($product_list_all);
sort($product_list);
echo "<form method='post' action='search.php'>";
echo "<lavel>プロダクト名</lavel><br />";
echo "<select name='search_str'>";
foreach ( $product_list as $product_name) {
    echo "<option value='".$product_name."'>".$product_name."</option>";
}
echo "</select>";
echo "<button class='btn' type='submit'>検索</button>";
echo "</form>";

echo "<br/>";
echo "<br/>";
echo "<br/>";

$nvd_id_list_all = "";
$search = isset($_POST['search_str'])?$_POST['search_str']:null;
if( ! is_null($search)){
    foreach ( $pdo->query ( 'select * from cpes where product = "'.$search.'";' ) as $row ) {
        $nvd_id_list_all[] = $row['nvd_id'];
    }
    $nvd_id_list = array_unique($nvd_id_list_all);
    echo "<h4>選択プロダクト名：".$search."</h4>";

    echo "<tr>";
    echo "<th width='150'>CVE</th>";
    echo "<th>summary</th>";
    echo "<th width='70'>スコア</th>";
    echo "</tr>";
    foreach($nvd_id_list as $nvd_id){
        foreach ( $pdo->query ( 'select * from nvds where id = "'.$nvd_id.'";' ) as $row ) {
            echo "<tr>";
            echo "<td>".$row['cve_id']."</td>";
            echo "<td>".$row['summary']."</td>";
            echo "<td>".$row['score']."</td>";
            echo "</tr>";
        }
    }
}

?>
</table>
</div>