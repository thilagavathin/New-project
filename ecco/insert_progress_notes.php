<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
date_default_timezone_set('America/New_York');
$current_date = date('Y-m-d H:i:s');
$agency_id= $_POST['agency'];
$contract= $_POST['contract'];
$progress_notes=mysql_real_escape_string($_POST['progress_notes']);
$user_id=$_SESSION['adminlogin'];
$username=$_SESSION['displayname'];
$insert_query="INSERT INTO tta_progress_notes (username,user_id,agency_id,contract_num,comments,created_date) VALUES ('".$username."',".$user_id.",".$agency_id.",'".$contract."','".$progress_notes."','".$current_date."')";
$result =mysql_query($insert_query);
if($result==1) echo 'success'; else echo 'failure';
?>
