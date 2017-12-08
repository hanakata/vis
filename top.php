<div class="container" style="padding:70px 0 0 0">
<?php
session_start();
include 'include/header.php';
?>
リストから各会社ごとの未適用KBと脆弱性情報が確認できます。<br/>
<table class="table table-striped">
    <thead>
      <tr>
        <th>顧客名</th>
        <th>取得台数</th>
        <th>更新日</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
<?php
include 'include/db_connect.php';

$query = mysqli_query($link,"SELECT * FROM company_list");
while ($row = mysqli_fetch_row($query)) {
    $company_id = $row[0];
    $company_name = $row[1];
    echo "<tr>";
    echo "<td>";
    echo $company_name;
    echo "</td>";
    $query = mysqli_query($link,"select * from kb_info where company_id = '{$company_id}'");
    while($row = mysqli_fetch_row($query)){
        $count[] = $row[1];
        $update_day = $row[7];
    }
    echo "<td>";
    echo count($count);
    echo "</td>";
    echo "<td>";
    echo $update_day;
    echo "</td>";
    echo "<td>";
    echo '<form action="./search.php" method="post">';
    echo '<button class="btn" input type="hidden" value="'.$company_name.'" name="search_company" id="search_company" type="submit">詳細情報</button>';
    echo "</td>";
}
?>
</div>