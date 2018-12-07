<div class="container" style="padding:70px 0 0 0">
<?php
session_start();
include 'include/header.php';
include 'include/db_connect.php';
$check = 0;
$praimary_search = 1;
$query_kb_year = "";
$query_kb = "";
$query_severity = "";
$query_kb_list = "";
$severity_list = "";
$kb_diff_list = "";
$_SESSION['kb_year'] = "";
$_SESSION['severity'] = "";
$_SESSION['search_kb'] = "";

$m = 0;
$search_tmp = isset($_POST['search_company'])?$_POST['search_company']:null;
if( ! is_null($search_tmp)){
    $_SESSION['search_company'] = $search_tmp;
    $praimary_search = 0;
}
$kb_year = isset($_POST['kb_year'])?$_POST['kb_year']:null;

if (isset($_POST['item']) && is_array($_POST['item'])) {
    $severity = $_POST['item'];
}else{
    $severity = "";
}
$search_kb = isset($_POST['search_kb'])?$_POST['search_kb']:null;

$query = mysqli_query($link,"SELECT DISTINCT update_id FROM kb_list");
while ($row = mysqli_fetch_row($query)) {
    $kb_update_id_all[] = $row[0];
}

rsort($kb_update_id_all);
echo '<section>'."\n";
echo '<button type="button" style="width:300px" class="btn btn-info btn-sm" data-toggle="collapse" data-target="#search">検索パネル</button>'."\n";
echo '<div id="search" class="collapse">'."\n";
echo '<div class="panel panel-default">'."\n";
echo '<div class="panel-body">'."\n";
echo '<form action="search.php" method="post">';
echo "<lavel>KB情報公開年月</lavel><br />";
echo "<select name='kb_year'>";
echo "<option value=''>全て</option>";
foreach ( $kb_update_id_all as $kb_update_id) {
    echo "<option value='".$kb_update_id."'>".$kb_update_id."</option>";
}
echo "</select>";
echo "<br />";
echo "<br />";
echo "<lavel>重大度</lavel>";
echo '<div class="checkbox">'."\n";
echo '<label><input type="checkbox" value="Important" name="item[]" id="Important">Important</input></label>'."\n";
echo '<label><input type="checkbox" value="Critical" name="item[]" id="Critical">Critical</input></label>'."\n";
echo '<label><input type="checkbox" value="Moderate" name="item[]" id="Moderate">Moderate</input></label>'."\n";
echo '<label><input type="checkbox" value="Low" name="item[]" id="Low">Low</input></label>'."\n";
echo '</div>'."\n";
echo '<lavel>検索対象KB</lavel><br/>'."\n";
echo '<input type="text" name="search_kb" size=100 >'."\n";
echo '<button class="btn pull-right" type="submit">検索</button>';
echo '</form>'."\n";
echo '</div>'."\n";
echo '</div>'."\n";
echo '</div>'."\n";
echo '</section>'."\n";

echo "<br/>";

$search = $_SESSION['search_company'];
echo '<form action="./csv_output.php" method="post">';
if($praimary_search != 0){
    if( $kb_year != ""){
        echo '<input type="hidden" name="kb_year" value="'.$kb_year.'">';
    }else{
        echo '<input type="hidden" name="kb_year" value="">';
    }
    if( $severity != ""){
        $m = 0;
        foreach($severity as $value){
            if($m == 0){
                $severity_list = "'".$value."'";
                $m = 1;                             
            }else{
                $severity_list .= ",'".$value."'";
            }
        }
        echo '<input type="hidden" name="severity" value="'.$severity_list.'">';
    }else{
        echo '<input type="hidden" name="severity" value="">';
    }
    if($search_kb != ""){
        echo '<input type="hidden" name="search_kb" value="'.$search_kb.'">';
    }else{
        echo '<input type="hidden" name="search_kb" value="">';
    }
}
echo '<input type="hidden" name="search" value="'.$search.'">';
echo '<button class="btn btn-success pull-right" input type="hidden" value="" name="pc_info" id="pc_info" type="submit">CSV出力</button>';
echo '</form>';
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
     echo "<h4>選択項目</h4>";
     if($kb_year != ""){
        echo "<h5>KB情報公開年月：".$kb_year."</h5>";
     }else{
        echo "<h5>KB情報公開年月：選択なし</h5>";
     }
     if($severity != ""){
        $severity_search = "";
        foreach($severity as $value){
            $severity_search .= " ".$value;
        }
        echo "<h5>重大度：".$severity_search."</h5>";
     }else{
        echo "<h5>重大度：選択なし</h5>";
     }
    if($search_kb != ""){
        $kb_search = "";
       $n = explode(";",$search_kb);
       foreach($n as $value){
           $kb_search .= " ".$value;
       }
       echo "<h5>KB：".$kb_search."</h5>";
    }else{
        echo "<h5>KB：選択なし</h5>";
    }
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
                $kb_list_all = array();
                echo "<tr>";
                echo "<td>".$pc_name."</td>";
                echo "<td>".$logon_user_all[$i]."</td>";
                echo "<td>KB情報は正常に取得できました。</td>";
                $inst_kb_list = explode(":",$row[6]);
                $os_name = $row[3];
                $bit = $row[4];               
                if(strpos($os_name,'Windows Server 2016') !== false or strpos($os_name,'Windows Server 2012') !== false  or strpos($os_name,'Windows Server 2008') !== false){
                    if(strpos($os_name,'R2') === false ){
                        $os_query_flg = 0;
                    }else{
                        $os_query_flg = 1;
                    }
                }else{
                    $os_query_flg = 2;
                }
                if($praimary_search != 0){
                    if( $kb_year != ""){
                        $query_kb_year = " and kb_list.update_id = '{$kb_year}' ";
                    }else{
                        $query_kb_year = "";
                    }
                    if( $severity != ""){
                        $m = 0;
                        foreach($severity as $value){
                            if($m == 0){
                                $severity_list = "'".$value."'";
                                $m = 1;                             
                            }else{
                                $severity_list .= ",'".$value."'";
                            }
                        }
                        $query_severity = " and severity in ({$severity_list}) "; 
                    }else{
                        $query_severity = "";
                    }
                    if($search_kb != ""){
                        $m = 0;
                        $n = explode(";",$search_kb);
                        foreach($n as $value){
                            if($m == 0){
                                $query_kb_list = "'".$value."'";
                                $m = 1;
                            }else{
                                $query_kb_list .= ",'".$value."'";
                            }
                        }
                        $query_kb = " and kb_number in ({$query_kb_list}) ";
                    }else{
                        $query_kb = "";
                    }
                    switch($os_query_flg){
                        case 0:
                        $query = mysqli_query($link,"select DISTINCT kb_number from kb_list join cve_list on (kb_list.cve_number = cve_list.cve_number) left join production_list on (kb_list.producrion_id = production_list.production_id) where production_name like '%{$os_name}%' and production_name not like '%R2%'".$query_kb_year.$query_severity.$query_kb);
                        break;
                        case 1:
                        $query = mysqli_query($link,"select DISTINCT kb_number from kb_list join cve_list on (kb_list.cve_number = cve_list.cve_number) left join production_list on (kb_list.producrion_id = production_list.production_id) where production_name like '%{$os_name}%'".$query_kb_year.$query_severity.$query_kb);
                        break;
                        case 2:
                        $query = mysqli_query($link,"select DISTINCT kb_number from kb_list join cve_list on (kb_list.cve_number = cve_list.cve_number) left join production_list on (kb_list.producrion_id = production_list.production_id) where production_name like '%{$os_name}%' and production_name like '%{$bit}%'".$query_kb_year.$query_severity.$query_kb);
                        break;                        
                    }                    
                }else{
                    switch($os_query_flg){
                        case 0:
                        $query = mysqli_query($link,"select DISTINCT kb_number from kb_list join cve_list on (kb_list.cve_number = cve_list.cve_number) left join production_list on (kb_list.producrion_id = production_list.production_id) where production_name like '%{$os_name}%' and production_name not like '%R2%'");
                        break;
                        case 1:
                        $query = mysqli_query($link,"select DISTINCT kb_number from kb_list join cve_list on (kb_list.cve_number = cve_list.cve_number) left join production_list on (kb_list.producrion_id = production_list.production_id) where production_name like '%{$os_name}%'");
                        break;
                        case 2:
                        $query = mysqli_query($link,"select DISTINCT kb_number from kb_list join cve_list on (kb_list.cve_number = cve_list.cve_number) left join production_list on (kb_list.producrion_id = production_list.production_id) where production_name like '%{$os_name}%' and production_name like '%{$bit}%'");
                        break;
                    }
                }
                while($row = mysqli_fetch_row($query)){
                    $kb_list_all[] = $row[0];    
                }
                $kb_list_uniq = array_unique($kb_list_all);
                $kb_diff_all = array_diff($kb_list_uniq,$inst_kb_list);
                $kb_diff = array_unique($kb_diff_all);
                echo "<td>".count($kb_diff)."</td>";
                if(count($kb_diff) != 0){
                    $m = 0;
                    foreach($kb_diff as $kb){
                        if($m == 0){
                            $kb_diff_list = "'".$kb."'";
                            $m = 1;
                        }else{
                            $kb_diff_list .= ",'".$kb."'";
                        }
                    }
                    switch($os_query_flg){
                        case 0:
                        $query = mysqli_query($link,"SELECT cve_number FROM kb_list INNER JOIN production_list ON kb_list.producrion_id = production_list.production_id where production_name like '%{$os_name}%' and production_name not like '%R2%' and kb_number in ({$kb_diff_list})");
                        break;
                        case 1:
                        $query = mysqli_query($link,"SELECT cve_number FROM kb_list INNER JOIN production_list ON kb_list.producrion_id = production_list.production_id where production_name like '%{$os_name}%' and kb_number in ({$kb_diff_list})");
                        break;
                        case 2:
                        $query = mysqli_query($link,"SELECT cve_number FROM kb_list INNER JOIN production_list ON kb_list.producrion_id = production_list.production_id where production_name like '%{$os_name}%' and production_name like '%{$bit}%' and kb_number in ({$kb_diff_list})");
                        break;
                    }
                    $cve_list_all = array();
                    while ($row = mysqli_fetch_row($query)) {
                        $cve_list_all[] = $row[0];
                    }
                    $cve_list = array_unique($cve_list_all);
                    $cve_important = 0;
                    $cve_critical = 0;
                    $cve_moderate = 0;
                    $cve_low = 0;
                    foreach($cve_list as $cve){
                        $query = mysqli_query($link,"select severity from cve_list where cve_number = '{$cve}'");
                        while ($row = mysqli_fetch_row($query)) {
                            if($row[0] == "Important"){
                                $cve_important = $cve_important + 1;
                            }
                            if($row[0] == "Critical"){
                                $cve_critical = $cve_critical + 1;
                            }
                            if($row[0] == "Moderate"){
                                $cve_moderate = $cve_moderate + 1;
                            }
                            if($row[0] == "Low"){
                                $cve_low = $cve_low + 1;
                            }
                        }
                    }
                    echo "<td>".$cve_important."</td>";
                    echo "<td>".$cve_critical."</td>";
                    echo "<td>".$cve_moderate."</td>";
                    echo "<td>".$cve_low."</td>";
                    echo "<td>";
                    echo '<form action="./pc_kb_info.php" method="post">';
                    echo '<input type="hidden" name="os_name" value="'.$os_name.'">';
                    echo '<input type="hidden" name="bit" value="'.$bit.'">';
                    echo '<input type="hidden" name="os_query_flg" value="'.$os_query_flg.'">';
                    echo '<input type="hidden" name="kb_diff_list" value="'.$kb_diff_list.'">';
                    echo '<button class="btn" input type="hidden" value="'.$pc_name.'" name="pc_info" id="pc_info" type="submit">詳細</button>';
                    echo '</form>';
                    echo "</td>";
                    echo "</tr>";
                }else{
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                }
            }
        }
        $i = $i + 1;
    }
}
?>
</table>
</div>