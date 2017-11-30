<?php
include 'include/header.php';
?>
<div class="container" style="padding:70px 0 0 0">
<form action="./company_choice.php" method="post">
  <div class="form-inline">
  	<lavel>顧客名</lavel><br/>
  	<input type="text" name="search_str" class="form-control" autofocus>
    <button class="btn" type="submit">検索</button>
    <br />
    <br />
    <br />
  </div>
</form>

<?php
  include 'include/db_connect.php';
  $i = 0;
  $search = isset($_POST['search_str'])?$_POST['search_str']:null;
  if( ! is_null($search)){
    $search = mysqli_real_escape_string($link, $search);
    if( $search != ""){
      $query = mysqli_query($link,"SELECT * FROM company_list WHERE company_name LIKE '%{$search}%'");
      $result_count = mysqli_num_rows($query);
      if($result_count != 0){
        echo '<table class="table table-striped">';
        echo '  <tr>';
        echo '    <th>顧客名</th>';
        echo '    <th></th>';
        echo '  </tr>';
        while ($row = mysqli_fetch_row($query)) {
          echo "<tr>";
          echo "<td>".$row[1]."</td>";
          echo '<td>';
          echo '<form action="./csv_import.php" method="post">';
          echo '<button class="btn btn-info" input type="hidden" value="'.$row[1].'" name="company" id="company" type="submit">選択</button>';
          echo '</form>';
          echo '</td>';
          echo "</tr>";
        }
      echo "</table>";
        }else{
          echo "登録はありませんでした";          
        }
    }else{
      echo "登録はありませんでした";
    }
      echo "<br/><h4>新規登録</h4>";
      echo '<form action="./csv_import.php" method="post">';
      echo '<div class="input-group">';
      echo '	<lavel>顧客名</lavel>';
      echo '	<input type="text" name="company" class="form-control" placeholder="正式名称を入れてください。" required autofocus>';
      echo '</div>';
      echo '<button class="btn pull-right" type="submit">CSVインポート画面へ</button>';
      echo '</form>';
      echo '</div>';
  }

?>
</div>