<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
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
/**
To get current user
 */
$current_users = mysql_query("SELECT user_id, user_level, username, name, user_image, region FROM login_users WHERE user_id=".$_SESSION['adminlogin']." AND approved = 'YES'") or die("Query Error");
while($row=mysql_fetch_array($current_users)) {
    $current_user_id =trim($row['user_id']);
    $current_user_level =trim($row['user_level']);
    $current_user_username =trim($row['username']);
    $current_user_name =trim($row['name']);
    $current_user_userimage = trim($row['user_image']);
    $current_user_region = trim($row['region']);
}

$id = 0;

$sender_id = $_POST["sender_id"];
$receiver_id = $_POST["receiver_id"];
$group_id = $_POST["group_id"];

$file_name = trim($_FILES["file"]["name"]);
$tmp_name = trim($_FILES["file"]["tmp_name"]);

$insert = mysql_query("INSERT INTO chats (upload_file, created_at, updated_at, created_time, updated_time, sender_user_id, receiver_user_id, group_id) VALUES ('".$file_name."', now(), now(), now(), now(), '".$sender_id."', '".$receiver_id."', '".$group_id."')") or die("Query Error");

if( $insert ){
    $id = $sender_id;
    $dir = "assets/chats/".$id."/";
    $target_file = $dir . basename($_FILES["file"]["name"]);
        if(!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        if (!is_dir($dir) && !mkdir($dir)){
            die("Error creating folder");
        }
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){
            echo "success"; die;
        }else{
            echo "Failure To Move Files"; die;
        }
}else{
    echo "Datas Not Inserted!!!"; die;
}



?>
