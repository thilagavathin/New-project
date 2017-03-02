<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$current_user = $_SESSION['adminlogin'];
$user_admin = 0;
$q = mysql_query("SELECT user_level from login_users WHERE user_id=".$current_user) or die("Query Error");
if( mysql_num_rows($q) > 0){
    while($r = mysql_fetch_array($q)){
        $admin = unserialize($r["user_level"]);
        $user_admin =  $admin[0];
    }
}

if( isset($_POST["group_create"]) ){
    $sender_id = trim( $_POST["group_admin"] );
    $group_name = trim( $_POST["group_name"] );
    $group_description = "";
    $group_members =$_POST["group_members"];
    $group_profile = $_FILES["group_profile"]["name"];
    $count = count($group_members);

    if($user_admin != 1 && $count > 7){
        echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Each group having maximum 7 users.');
        window.location.href='messages_test.php';
        </SCRIPT>");
        die;
    }

    $insert = mysql_query("INSERT INTO group_chats (name, description, created_user_id, created_at, updated_at, created_time, updated_time) VALUES ('".$group_name."', '".$group_description."', '".$sender_id."', now(), now(), now(), now())") or die("Query Error");
    
    if( $insert ){
        $id = mysql_insert_id();
        if(!empty($group_profile)){
            $dir = "assets/groups/".$id."/";
            $target_file = $dir . basename($_FILES["group_profile"]["name"]);

            if(!is_dir($dir)) {
                mkdir($dir, 0777);
            }
            if (!is_dir($dir) && !mkdir($dir)){
                echo "<script>alert('Error creating folder');</script>";
            }
            if (move_uploaded_file($_FILES["group_profile"]["tmp_name"], $target_file)){
                $update = mysql_query("UPDATE group_chats set profile_picture='".$group_profile."' WHERE id=".$id) or die("update profile error");
            }else{
                echo "<script>alert('Failed To Move Files');</script>";
            }
        }

        $count = count($group_members);

        $q = mysql_query("INSERT INTO group_members (group_id, created_user_id, created_at, updated_at, created_time, updated_time, member_user_id) VALUES ('".$id."', '".$sender_id."', now(), now(), now(), now(), '".$sender_id."')") or die("Member query error");

        for($i=0; $i<$count; $i++){
            $member_user_id = $group_members[$i];
            $member_insert = mysql_query("INSERT INTO group_members (group_id, created_user_id, created_at, updated_at, created_time, updated_time, member_user_id) VALUES ('".$id."', '".$sender_id."', now(), now(), now(), now(), '".$member_user_id."')") or die("Member query error");
            if($member_insert){
            }else{
            }
        }
    }
    echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Group Created Successfully !!!');
        window.location.href='messages_test.php';
        </SCRIPT>");
    die;

}else{
    echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Failed To Created Group.');
        window.location.href='messages_test.php';
        </SCRIPT>");
    die;
}

?>
