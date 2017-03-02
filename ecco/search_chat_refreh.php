<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
date_default_timezone_set('America/New_York');
function getUserImage($id){
    $query = mysql_query("SELECT user_id, user_level, username, name, user_image, region  FROM login_users WHERE approved='YES' AND user_id=".$id) or die("Query error");
    $data = array();
    while($q_row = mysql_fetch_array($query)){
        if($q_row["user_image"] <> '') $user_image=@unserialize($q_row["user_image"]); else $user_image='';
        if($user_image=='')  $img ="assets/img/photo.jpg";
        else $img ="assets/profile/".$user_image[0];
        $data['user_image']= $img;
        $data['user_name'] = $q_row["name"];
    }
    return $data;
}

$sender_id = $_POST["sender_id1"];
$receiver_id = $_POST["receiver_id1"];
$group_id = $_POST["group_id1"];

echo "ECCO";
?>