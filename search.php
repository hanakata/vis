<div class="container" style="padding:70px 0 0 0">
<?php
include 'include/header.php';
include 'include/db_connect.php';

$query = mysqli_query($link,"SELECT * FROM company_list");
while ($row = mysqli_fetch_row($query)) {
    $company_list_all[] = $row[1];
}

echo "<form method='post' action='search.php'>";
echo "<lavel>会社名</lavel><br />";
echo "<select name='search_str'>";
foreach ( $company_list_all as $company_name) {
    echo "<option value='".$company_name."'>".$company_name."</option>";
}
echo "</select>";
echo "<button class='btn' type='submit'>検索</button>";
echo "</form>";

echo "<br/>";
echo "<br/>";
echo "<br/>";

$search = isset($_POST['search_str'])?$_POST['search_str']:null;
if( ! is_null($search)){
    $i = 0;
     echo "<h4>選択会社名：".$search."</h4>";
     $query = mysqli_query($link,"SELECT * FROM kb_info INNER JOIN company_list ON kb_info.company_id = company_list.company_id where company_name = '{$search}'");
     while ($row = mysqli_fetch_row($query)) {
         $pc_list_all[] = $row[1];
         $logon_user_all[] = $row[2];
         $get_date = $row[7];
     }
     echo "<h4>取得日：".$get_date."</h4>";
     echo "<br/>";
     echo "<br/>";
     echo '<table class="table">';
     echo '  <tr>';
     echo '    <th rowspan="2">PC名</th>';
     echo '    <th rowspan="2">ログオンユーザ</th>';
     echo '    <th class="text-center" rowspan="2">状態</th>';
     echo '    <th rowspan="2">未適用KB数</th>';
     echo '    <th class="text-center" colspan="4">重大度</th>';
     echo '    <th></th>';
     echo '  </tr>';
     echo '  <tr>';
     echo '    <th>Important</th>';
     echo '    <th>Critical</th>';
     echo '    <th>Moderate</th>';
     echo '    <th>Low</th>';
     echo '    <th></th>';
     echo '  </tr>';
     foreach ( $pc_list_all as $pc_name) {
        $query = mysqli_query($link,"SELECT * FROM kb_info where pc_name = '{$pc_name}'");
        while ($row = mysqli_fetch_row($query)) {
            if($row[5] == 1 ){
                echo "<tr>";
                echo "<td>".$pc_name."</td>";
                echo "<td></td>";
                echo "<td>長期間利用されていないPCの可能性があります。</td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>"; 
            }
            if($row[5] == 2 ){
                echo "<tr>";
                echo "<td>".$pc_name."</td>";
                echo "<td></td>";
                echo "<td>FWのポートが開いていないもしくは電源が入っていない可能性があります。</td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>"; 
            }
            if($row[5] == 0 ){
                echo "<tr>";
                echo "<td>".$pc_name."</td>";
                echo "<td>".$logon_user_all[$i]."</td>";
                echo "<td>正常に取得できました。</td>";
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
                echo "<td>".count($kb_diff)."</td>";
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
                    $query = mysqli_query($link,"select severity from cve_list where cve_number = '{$cve}'");
                    while ($row = mysqli_fetch_row($query)) {
                        if($row[0] == "Important"){
                            $cve_important[] = $cve;
                        }
                        if($row[0] == "Critical"){
                            $cve_critical[] = $cve;
                        }
                        if($row[0] == "Moderate"){
                            $cve_moderate[] = $cve;
                        }
                        if($row[0] == "Low"){
                            $cve_low[] = $cve;
                        }
                    }
                }
                echo "<td>".count($cve_important)."</td>";
                echo "<td>".count($cve_critical)."</td>";
                echo "<td>".count($cve_moderate)."</td>";
                echo "<td>".count($cve_low)."</td>";
                echo "<td>";
                echo '<form action="./pc_info_important.php" method="post">';
                echo '<button class="btn" input type="hidden" value="'.$search.":".$pc_name.'" name="pc_info" id="pc_info" type="submit">詳細</button>';
                echo '</form>';
                echo "</td>";
                echo "</tr>";
            }
        }
        $i = $i + 1;
    }
}
?>
</table>
</div>