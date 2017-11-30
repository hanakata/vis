<div class="container" style="padding:70px 0 0 0">

<ul class="nav nav-tabs nav-justified">
	<li role="presentation"><a href="pc_info_important.php">Important</a></li>
	<li role="presentation"><a href="pc_info_critical.php">Critical</a></li>
	<li role="presentation"><a href="pc_info_moderate.php">Moderate</a></li>
	<li role="presentation" class="active"><a href="pc_info_low.php">Low</a></li>
</ul>
<br/>
<br/>

<?php
session_start();
$_SESSION['back'] = 1;
echo "顧客名：".$_SESSION['company_name']."<br/>";
echo "対象PC名：".$_SESSION['pc_name']."<br/>";
echo "<br/>";
echo "<br/>";
echo '<table class="table table-striped">';
echo '  <tr>';
echo '    <th>CVE</th>';
echo '    <th>内容</th>';
echo '    <th>対象KB</th>';
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
	foreach($kb_diff as $kb){
		if($kb_query_flg == 0){
			$query = mysqli_query($link,"SELECT cve_number FROM kb_list INNER JOIN production_list ON kb_list.producrion_id = production_list.production_id where production_name like '%{$os_name}%' and production_name not like '%R2%' and kb_number = '{$kb}'");
		}
		if($kb_query_flg == 1){
			$query = mysqli_query($link,"SELECT cve_number FROM kb_list INNER JOIN production_list ON kb_list.producrion_id = production_list.production_id where production_name like '%{$os_name}%' and kb_number = '{$kb}'");
		}
		if($kb_query_flg == 2){
			$query = mysqli_query($link,"SELECT cve_number FROM kb_list INNER JOIN production_list ON kb_list.producrion_id = production_list.production_id where production_name like '%{$os_name}%' and production_name like '%{$bit}%' and kb_number = '{$kb}'");
		}
		while ($row = mysqli_fetch_row($query)) {
			$cve_list_all[] = $row[0];
		}
   }
   $cve_list = array_unique($cve_list_all);
   foreach($cve_list as $cve){
	$query = mysqli_query($link,"SELECT * from cve_list inner join kb_list on cve_list.cve_number = kb_list.cve_number where cve_list.cve_number = '{$cve}' and severity = 'Low'");
	$result_count = mysqli_num_rows($query);
	if($result_count != 0){
		while ($row = mysqli_fetch_row($query)) {
			$cve_number_all[] = $row[1];
			$note_all[] = $row[2];
			$kb_number_all[] = $row[6];
		  }
			$cves = array_unique($cve_number_all);
			$notes = array_unique($note_all);
			$kbs = array_unique($kb_number_all);
	
		   echo "<tr>";
			echo "<td>";
		foreach($cves as $cve){
			if($cve != ""){
			  echo $cve."<br/>";
			}
		}
		echo "</td>";
		echo "<td>";
		foreach($notes as $note){
		 if($note != ""){
			 echo $note."<br/>";
		   }
		}
		echo "</td>";
		echo "<td>";
		foreach($kbs as $kb){
		 if($kb != ""){
			 echo $kb."<br/>";
		   }
		}
		echo "</td>";
		echo "</tr>";
		$cve_number_all = array();
		$note_all = array();
		$kb_number_all = array();
	 }
	}
 }
?>
</table>
<div>