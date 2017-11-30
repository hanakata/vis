<div class="container" style="padding:70px 0 0 0">

<ul class="nav nav-tabs nav-justified">
	<li role="presentation" class="active"><a href="pc_info_kb.php">KB一覧</a></li>
	<li role="presentation"><a href="pc_info_important.php">Important</a></li>
	<li role="presentation"><a href="pc_info_critical.php">Critical</a></li>
	<li role="presentation"><a href="pc_info_moderate.php">Moderate</a></li>
	<li role="presentation"><a href="pc_info_low.php">Low</a></li>
</ul>
<br/>
<br/>

<?php
session_start();

echo "顧客名：".$_SESSION['company_name']."<br/>";
echo "対象PC名：".$_SESSION['pc_name']."<br/>";
echo "<br/>";
echo "<br/>";
echo '<table class="table table-striped">';
echo '  <tr>';
echo '    <th>KB</th>';
echo '    <th>対象CVE</th>';
echo '  </tr>';

include 'include/header.php';
include 'include/db_connect.php';

$query = mysqli_query($link,"SELECT * FROM kb_info INNER JOIN company_list ON kb_info.company_id = company_list.company_id where company_name = '{$_SESSION['company_name']}' and pc_name = '{$_SESSION['pc_name']}'");
while ($row = mysqli_fetch_row($query)) {
	$inst_kb_list = explode(":",$row[6]);
	$os_name = $row[3];
	$bit = $row[4];
	if(strpos($os_name,'Windows Server 2016') !== false or strpos($os_name,'Windows Server 2012') !== false ){
		if(strpos($os_name,'R2') === false ){
			$query = mysqli_query($link,"SELECT * FROM kb_list INNER JOIN production_list ON kb_list.producrion_id = production_list.production_id where production_name like '%{$os_name}%' and production_name not like '%R2%'");
			$kb_query_flg = 0;
		}else{
			$query = mysqli_query($link,"SELECT * FROM kb_list INNER JOIN production_list ON kb_list.producrion_id = production_list.production_id where production_name like '%{$os_name}%'");
			$kb_query_flg = 1;
		}
	}else{
		$query = mysqli_query($link,"SELECT * FROM kb_list INNER JOIN production_list ON kb_list.producrion_id = production_list.production_id where production_name like '%{$os_name}%' and production_name like '%{$bit}%'");
		$kb_query_flg = 2;
	}
	while ($row = mysqli_fetch_row($query)) {
		$kb_list_all[] = $row[2];
	}
	$kb_diff_all = array_diff($kb_list_all,$inst_kb_list);
	$kb_diff = array_unique($kb_diff_all);
	echo "未適用KB数：".count($kb_diff)."<br/>";
	foreach($kb_diff as $kb){
		echo "<tr>";
		echo "<td>".$kb."</td>";
	 	echo"<td>";
	 	$query = mysqli_query($link,"SELECT * FROM kb_list where kb_number = '{$kb}'");
	 	while ($row = mysqli_fetch_row($query)) {
	 		$cve_list_all[] = $row[1];
		}
		$cve_list = array_unique($cve_list_all);
		foreach($cve_list as $cve){
			echo $cve."<br/>";
		}
		echo "</td>";
		echo "</tr>";
	}
}
?>
</table>
<div>