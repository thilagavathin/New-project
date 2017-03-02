<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$id = trim( $_POST["id"] );
$message = trim($_POST["message"]);

$query = mysql_query("UPDATE chats SET comments='".$message."', updated_at=now(), updated_time=now() WHERE id=".$id) or die("Query Error");
if( $query ){
    echo "success";
}else{
    echo "Failure To update Your Message!!!";
}

?>
