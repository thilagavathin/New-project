<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$user_id= $_POST['splashArray'];
foreach($user_id as $us)
{
    $delete_query="DELETE FROM login_users WHERE user_id = ".$us;
    $result =mysql_query($delete_query);
}
if($result==1) echo 'success'; else echo 'failure';
?>