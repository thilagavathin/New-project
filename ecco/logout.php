<?php 
session_start();
include("config.php");
$sender_id = $_SESSION['adminlogin'];
$select = mysql_query("SELECT * FROM chat_active WHERE sender_id=".$sender_id);
if( mysql_num_rows($select) > 0){
    mysql_query("DELETE FROM chat_active WHERE sender_id=".$sender_id);
}
$_SESSION['adminlogin'] ='';
$_SESSION['userrole']='';
session_destroy();
header('Location:login.php');
die;
?>
