<div class="container" style="padding:70px 0 0 0">
<?php
session_start();
include 'include/header.php';
$_SESSION['company'] = $_POST['company'];
echo $_SESSION['company'];
?>

<form action="csv_upload.php" method="post" enctype="multipart/form-data">
  CSVファイル：<br />
  <input type="file" name="csvfile" size="30" /><br />
  <input type="submit" value="アップロード" />
</form>
</div>