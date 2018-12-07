<div class="container" style="padding:70px 0 0 0">

<?php
try{
session_start();
$_SESSION['back'] = 1;

$search_company = $_SESSION['search_company'];

$_SESSION['pc_name'] = $_POST['pc_info'];
$kb_diff_list = $_POST['kb_diff_list'];
$os_name = $_POST['os_name'];
$bit = $_POST['bit'];
$os_query_flg = $_POST['os_query_flg'];
echo "顧客名：".$search_company."<br/>";
echo "対象PC名：".$_SESSION['pc_name']."<br/>";
echo "<br/>";
echo "<br/>";
echo '<table class="table table-striped">';
echo '  <tr>';
echo '    <th>未適用KB</th>';
echo '    <th>対象製品</th>';
echo '  </tr>';
include 'include/header.php';
include 'include/db_connect.php';

switch($os_query_flg){
	case 0:
	$query_str_tmp = "select kb_list.update_id,kb_list.cve_number,severity,production_name from kb_list join cve_list on (kb_list.cve_number = cve_list.cve_number) left join production_list on (kb_list.producrion_id = production_list.production_id) where production_name like '%{$os_name}%' and production_name not like '%R2%'";
	break;
	case 1:
	$query_str_tmp = "select kb_list.update_id,kb_list.cve_number,severity,production_name from kb_list join cve_list on (kb_list.cve_number = cve_list.cve_number) left join production_list on (kb_list.producrion_id = production_list.production_id) where production_name like '%{$os_name}%'";
	break;
	case 2:
	$query_str_tmp = "select kb_list.update_id,kb_list.cve_number,severity,production_name from kb_list join cve_list on (kb_list.cve_number = cve_list.cve_number) left join production_list on (kb_list.producrion_id = production_list.production_id) where production_name like '%{$os_name}%' and production_name like '%{$bit}%'";
	break;
}
$kb_list = explode(",",$kb_diff_list);
sort($kb_list);
foreach($kb_list as $kb_tmp){
	$kb = str_replace("'","",$kb_tmp);
 	$query_str = $query_str_tmp." and kb_number = '{$kb}'";
	 $query = mysqli_query($link,$query_str);
 	while ($row = mysqli_fetch_row($query)) {
 		$production_all[] = $row[3];
 	}
 	echo "<tr>";
 	echo "<td>".$kb."</td>";
	echo "<td>";
	$productions = array_unique($production_all);
	foreach($productions as $production){
		echo $production."<br />";
	}
	echo "</td>";
 	echo "</tr>";
}
}catch(Exception $e){
	echo "エラー";
}
?>
</table>
<div>