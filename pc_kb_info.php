<div class="container" style="padding:70px 0 0 0">

<ul class="nav nav-tabs nav-justified">
	<li role="presentation"><a href="pc_info_important.php">Important</a></li>
	<li role="presentation"><a href="pc_info_critical.php">Critical</a></li>
	<li role="presentation" class="active"><a href="pc_info_moderate.php">Moderate</a></li>
	<li role="presentation"><a href="pc_info_low.php">Low</a></li>
</ul>
<br/>
<br/>

<?php
session_start();
$_SESSION['back'] = 1;

$search_company = $_SESSION['search_company'];
$kb_year = $_SESSION['kb_year'];
$severity = $_SESSION['severity'];
$search_kb = $_SESSION['search_kb'];
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
echo '    <th>KB公開年月</th>';
echo '    <th>未適用KB</th>';
echo '    <th>対象製品</th>';
echo '  </tr>';
include 'include/header.php';
include 'include/db_connect.php';

//$query = mysqli_query($link,"SELECT * FROM kb_info INNER JOIN company_list ON kb_info.company_id = company_list.company_id where company_name = '{$_SESSION['company_name']}' and pc_name = '{$_SESSION['pc_name']}'");
//while ($row = mysqli_fetch_row($query)) {
//}
?>
</table>
<div>