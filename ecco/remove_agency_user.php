<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$user_id= $_POST['splashArray'];
$agency_id= $_POST['splashArray1'];
$assignuser= $_POST['splashArray2'];

if (is_array($_POST['splashArray1'])) {
    for ($i = 0; $i < count($_POST['splashArray1']); $i++) {
        $agency_id = $_POST['splashArray1'][$i];
        $user_id = $_POST['splashArray'][$i];
        $assign= $_POST['splashArray2'][$i];
        $delete_query="DELETE FROM agency_map WHERE user_id = ".$user_id." AND agency_id = ".$agency_id;
        $result =mysql_query($delete_query);
        $update_sql="UPDATE TTA_Forms SET assignedUser='admin' where assignedUser ='".$assign."'";
        mysql_query($update_sql);
    }
}
if($result==1) echo 'success'; else echo 'failure';