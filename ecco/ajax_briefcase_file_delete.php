<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$id = trim($_POST["id"]);
$user = $_SESSION['adminlogin'];
if(empty($id)){
    die("Required fields are missing.");
}

$query = "UPDATE briefcase_uploads SET status='N', deleted_at=now(), deleted_by_user_id='".$user."' WHERE id=".$id;
$delete = mysql_query($query);
if($delete){
    echo "success";
    die;
}else{
    echo "Failure To Remove File";
    die;
}

?>
