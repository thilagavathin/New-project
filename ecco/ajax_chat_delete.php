<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$id = trim( $_POST["id"] );
$query = mysql_query("UPDATE chats SET deleted_at=now(), deleted_time=now() WHERE id=".$id) or die("Query Error");
if($query){
    echo "success"; die;
}else{
    echo "Failure to remove message"; die;
}

?>
