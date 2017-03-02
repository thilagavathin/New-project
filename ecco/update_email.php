<?php
ob_start(); session_start(); include_once('config.php');
$u_email=(isset($_POST['u_email']))? $_POST['u_email']:'';
$isChecked=(isset($_POST['isChecked']))? $_POST['isChecked']:'';
if($u_email<>'')
{
    $isChecked=($isChecked=='false')? 0:1;
    $sql_update="UPDATE login_users SET email='".$u_email."',email_notifications='".$isChecked."' WHERE user_id='".$_SESSION['adminlogin']."' ";
    if(mysql_query($sql_update)==1) echo 'success'; else echo 'failure';
}
else echo 'invalid';
?>