<?php
include 'include/db_connect.php';

$kb_year = isset($_POST['kb_year'])?$_POST['kb_year']:null;
$severity = isset($_POST['severity'])?$_POST['severity']:null;
$search_kb = isset($_POST['search_kb'])?$_POST['search_kb']:null;
$search = isset($_POST['search'])?$_POST['search']:null;
$filepath = "./tmp/test.csv";
$filename = "kb.csv";
unlink($filepath);
$query = mysqli_query($link,"SELECT DISTINCT update_id FROM kb_list");
while ($row = mysqli_fetch_row($query)) {
    $kb_update_id_all[] = $row[0];
}
if( ! is_null($search)){
    $i = 0;
    $query = mysqli_query($link,"SELECT * FROM kb_info INNER JOIN company_list ON kb_info.company_id = company_list.company_id where company_name = '{$search}'");
    while ($row = mysqli_fetch_row($query)) {
        $pc_list_all[] = $row[1];
        $logon_user_all[] = $row[2];
        $get_date = $row[7];
    }
    foreach ( $pc_list_all as $pc_name) {
        $query = mysqli_query($link,"SELECT * FROM kb_info where pc_name = '{$pc_name}'");
        while ($row = mysqli_fetch_row($query)) {
            if($row[5] == 1 ){
                $ary = array(array($pc_name, "", "長期間利用されていないPCの可能性があります。","","","","",""));
            }
            if($row[5] == 2 ){
                $ary = array(array($pc_name, "", "FWのポートが開いていないもしくは電源が入っていない可能性があります。","","","","",""));
            }
            if($row[5] == 0 ){
                $kb_list_all = array();
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
                while($row = mysqli_fetch_row($query)){
                    $kb_list_all[] = $row[0];    
                }
                $kb_list_uniq = array_unique($kb_list_all);
                $kb_diff_all = array_diff($kb_list_uniq,$inst_kb_list);
                $kb_diff = array_unique($kb_diff_all);
                $logon_user = $logon_user_all[$i];
                $logon_user = mb_ereg_replace("\r\n|\n|\r"," ",$logon_user);
                if(count($kb_diff) != 0){
                    $m = 0;
                    foreach($kb_diff as $kb){
                        if($m == 0){
                            $kb_diff_list = "'".$kb."'";
                            $m = 1;
                        }else{
                            $kb_diff_list .= ":'".$kb."'";
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
                    $ary = array(array($pc_name, $logon_user, "KB情報は正常に取得できました。",$kb_diff_list,$cve_important,$cve_critical,$cve_moderate,$cve_low));
                }else{
                    $ary = array(array($pc_name, $logon_user, "KB情報は正常に取得できました。","","","","",""));
                }
            }
            $fp = fopen($filepath, 'a');
            foreach ($ary as $value) {
                fputcsv($fp,$value);
            }
            $i = $i + 1;
        }
    }
}
fclose($filepath);
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"".$filename."\"");
readfile($filepath);
?>