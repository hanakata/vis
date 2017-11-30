<?php
session_start();
include 'include/db_connect.php';

$company_date = date('Y');
$company = $_SESSION['company'];
$query = mysqli_query($link,"SELECT * FROM company_list WHERE company_name LIKE '%{$company}%'");
$result_count = mysqli_num_rows($query);
echo $result_count;
if($result_count != 0){
    while ($row = mysqli_fetch_row($query)) {
        $company_code = $row[0];
        echo "何もしませんでした";
    }
}else{
    $query = mysqli_query($link,"SELECT * FROM company_list");
    $result_count = mysqli_num_rows($query);
    $company_number = $result_count + 1;
    $company_code = $company_date."_".$company_number;
    $company_sql = mysqli_query($link,"INSERT INTO company_list VALUES ('{$company_code}','{$company}')");
    echo "登録処理を実行しました。";
}

 $file_name = $_FILES['csvfile']['name'];
 $file_tmp = $_FILES['csvfile']['tmp_name'];

 $upforder = "upload/";
 $upfile = $upforder.$file_name;

 move_uploaded_file( $file_tmp, $upfile);
 $interenc =  mb_internal_encoding();
 $i = 0;
setlocale(LC_ALL, 'ja_JP');

if (($handle = fopen($upfile, "r")) !== FALSE) {
     while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
         if($i == 0){
             $i++;
         }else{
       mb_convert_variables($interenc,"UTF-8", $data);
       mysqli_query($link,"INSERT INTO kb_info VALUES ('{$company_code}','{$data[0]}','{$data[1]}','{$data[2]}','{$data[3]}','{$data[4]}','{$data[5]}','{$data[6]}')");
         }
     }
     fclose($handle);
 }
 $url = './top.php';
 header('Location: ' . $url, true , 301);

?>