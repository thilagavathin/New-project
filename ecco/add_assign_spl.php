<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$agency_id= $_POST['agency_id'];
$spl_user= $_POST['spl_user'];

if (is_array($_POST['agency_id'])) {
    $ii = 0;
    for ($i = 0; $i < count($_POST['agency_id']); $i++) {
        $agency_code=$_POST['agency_id'][$i];
        $insert_query="INSERT INTO agency_map (user_id,agency_id) VALUES (".$spl_user.",".$agency_code.")";
        $result =mysql_query($insert_query);
    }
}
if($result==1) echo 'success'; else echo 'failure';