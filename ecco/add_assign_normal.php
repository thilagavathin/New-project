<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$agency_id= $_POST['agency'];
$user_id= $_POST['assign_user'];
$insert_query="INSERT INTO agency_map (user_id,agency_id) VALUES (".$user_id.",".$agency_id.")";
$result =mysql_query($insert_query);
if($result==1) echo 'success'; else echo 'failure';