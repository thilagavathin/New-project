<?php
include_once('config.php');
ob_start(); session_start();
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$agency_id = trim($_POST["up_agency"]);
$filename = addslashes($_FILES["exampleInputFile"]["name"]);
$user_id = $_SESSION['adminlogin'];

$allowed =  array('pdf','xls' ,'xlsx', 'doc', 'docx');
$ext = pathinfo($filename, PATHINFO_EXTENSION);
if(!in_array($ext,$allowed) ) {
    echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('This file is not allowed to upload. Only PDF, EXCEL files allowed to  upload.');
        window.location.href='briefcase.php';
        </SCRIPT>");
    die;
}

$con = "SELECT COUNT(*) FROM briefcase_uploads WHERE agency_id=".$agency_id." AND status='Y'";
$query1 = mysql_query($con);
$count = 0;
if(mysql_num_rows($query1) > 0){
    while($row = mysql_fetch_array($query1)){
        $count = $row[0];
    }
}

if($count > 6){
    echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Already this agency having maximum amount of items. If you want to upload, please remove any one file');
        window.location.href='briefcase.php';
        </SCRIPT>");
    die;
}
date_default_timezone_set('America/New_York');
$mon = date("jS F Y");
$date = date("g:i:s");
$combined_date_and_time = $mon . ' ' . $date;
$date_time = date("Y-m-d g:i:s",strtotime($combined_date_and_time));
$query = "INSERT INTO briefcase_uploads (agency_id, user_id, file_name, created_at, updated_at) VALUES ('".$agency_id."', '".$user_id."','".$filename."', '".$date_time."', '".$date_time."')";

$insert = mysql_query($query);
if($insert){
    $insert_id = mysql_insert_id();
    $dir = "assets/briefcase/".$insert_id."/";
    $target_file = $dir . basename($_FILES["exampleInputFile"]["name"]);
    if(!is_dir($dir)) {
        mkdir($dir, 0777);
    }
    if (!is_dir($dir) && !mkdir($dir)){
        echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Error creating folder');
        window.location.href='briefcase.php';
        </SCRIPT>");
        die;
    }
    if (move_uploaded_file($_FILES["exampleInputFile"]["tmp_name"], $target_file)){
        echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('File Uploaded Successfully');
        window.location.href='briefcase.php';
        </SCRIPT>");
        die;
    }else{
        echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Failure To Move Files');
        window.location.href='briefcase.php';
        </SCRIPT>");
        die;
    }
}

