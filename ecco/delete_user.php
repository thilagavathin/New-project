<?php ob_start(); session_start(); include_once('config.php');
$delete_user = "DELETE FROM `login_users` WHERE `user_id` = '".$_REQUEST['uid']."'";
$result = mysql_query($delete_user);
if($result==1) {
    header('Location:users.php');
    die;
}
else {
    header('Location:message.php');
    die;
}
?>