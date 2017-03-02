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

$select_region = trim($_POST["select_region"]);
$agency_id = trim($_POST["agency_id"]);
$message = "";

if( empty($select_region) && empty($agency_id)){
    $users = mysql_query("SELECT user_id, user_level, username, name, user_image, region  FROM login_users WHERE approved='YES' AND user_id <> ".$_SESSION['adminlogin']." ORDER BY name ASC");
}else{
    $q = "SELECT id FROM agency WHERE region='".$select_region."'";
    $select_region_query = mysql_query($q) or die("Query error");
    $agency_id_all = "";
    if( mysql_num_rows($select_region_query) > 0 ){
        while($sr_row = mysql_fetch_array($select_region_query)){
            $agency_id_all .= $sr_row["id"];
            $agency_id_all .= ",";
        }
    }
    if(!empty($agency_id)){
        $agency_id_all .= $agency_id;
    }
    $agency_id_all = rtrim($agency_id_all, ',');
    $all_user_id = mysql_query("SELECT DISTINCT user_id FROM agency_map WHERE agency_id IN (".$agency_id_all.")") or die("Query Error");
    $all_id = "";
    if( mysql_num_rows($all_user_id) > 0){
        while($a_row = mysql_fetch_array($all_user_id)){
            $all_id .= $a_row["user_id"];
            $all_id .= ",";
        }
    }
    $all_id = rtrim($all_id, ',');
    $q = "SELECT * FROM login_users WHERE user_id IN (".$all_id.")";
    $users = mysql_query($q) or die("Query Error");
}

if( mysql_num_rows($users) > 0) {
    while ($row = mysql_fetch_array($users)) {
        $d = getUserImage($row["user_id"]);
        $img_url = $d["user_image"];
        if( $row["user_id"] != $_SESSION['adminlogin'] ){
            $chat_msg = mysql_query("SELECT * FROM chats WHERE receiver_user_id=".$_SESSION["adminlogin"]." AND group_id='0' AND view_status='N' AND sender_user_id=".$row["user_id"]." ORDER BY id ASC") or die("Query z Error");
            if(mysql_num_rows($chat_msg) > 0){
                while($w = mysql_fetch_array($chat_msg)){
                    $r_chat = $w["comments"];
                    if( $w["upload_file"] != "" ){
                        $r_chat = "New File is Uploaded.".$w["upload_file"];
                    }
                    $date = date_create($w["created_at"]);
                    $r_date =  date_format($date, 'M d,Y');
                }
            }else{
                $r_chat = $r_date = "";
            }
            $message .=  '
            <div class="user-partview">
                <div class="col-md-2 col-sm-2 col-xs-2 n-user pad-0"><img src="'.$img_url.'"></div>
                <div class="col-md-10 col-sm-10 col-xs-10 n-user-text">
                    <p><strong><a href="?r='.$row["user_id"].'" id="'.$row["user_id"].'">'.$row["name"].'</a> (3)</strong></p>
                    <p>'.$r_chat.'</p>
                    <p class="u-date">'.$r_date.'</p>
                </div>
            </div>
        ';
        }
    }
    echo $message; die;
}else{
    echo "No users found"; die;
}
