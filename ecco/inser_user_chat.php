<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$receiver_id = trim($_POST["receiver_id"]);
$sender_id = trim($_POST["sender_id"]);
$message = trim(addslashes($_POST["message"]));
$group_id = trim($_POST["group_id"]);
$all_receivers = "";
$post=isset($_POST['post_message'])!=''?$_POST['post_message']:'';
$more_keywords=(isset($_POST['more_keywords'])!=''? explode(',',$_POST['more_keywords']):array()); $new_keywords=array();
$post_keywords=(isset($_POST['post_keywords'])!=''? explode(',',$_POST['post_keywords']):array());
$post_topic=isset($_POST['post_topic'])!=''?$_POST['post_topic']:'';
$new_keywords=array_merge(array_filter($post_keywords),array_filter($more_keywords));
$insert_keywords=serialize($new_keywords);

if(isset($_POST['post_message'])){
    
}
if( $group_id != 0  ){
   $get_re = mysql_query("SELECT * FROM group_members WHERE group_id=".$group_id) or die("Query 1 Error");

    mysql_query("INSERT INTO chats (sender_user_id, receiver_user_id, group_id,  comments,post,topic,keywords, created_at, updated_at, created_time, updated_time) VALUES ('".$sender_id."', '0', '".$group_id."', '".$message."', '".$post."', '".$post_topic."', '".$insert_keywords."', now(), now(), now(), now())") or die("Query 2 Error");

    $chat_id = mysql_insert_id();
        while ($row = mysql_fetch_array($get_re)){
            $all_receivers = $row["member_user_id"];

            $online = mysql_query("SELECT * FROM chat_active WHERE sender_id='".$all_receivers."' AND (receiver_id='".$sender_id."' AND group_id='".$group_id."')") or die("Query Online Error");
          if(mysql_num_rows($online) > 0){
              while($r = mysql_fetch_array($online)){
                  $s = $r["status"];
              }
          }else{
              $s = "N";
          }
            if( $sender_id == $all_receivers){
                $s = "Y";
            }

            mysql_query("INSERT INTO group_chat_views (chat_id, group_id, sender_id, receiver_id, view_status, created_at, created_time, updated_at, updated_time) VALUES ('".$chat_id."', '".$group_id."','".$sender_id."','".$all_receivers."', '".$s."',now(),now(),now(), now())") or die("Query 4 Error");

    }

    echo "success";

}else{

    $insert = mysql_query("INSERT INTO chats (sender_user_id, receiver_user_id, group_id,  comments,post,topic,keywords, created_at, updated_at, created_time, updated_time,send_status,rec_status) VALUES ('".$sender_id."', '".$receiver_id."', '".$group_id."', '".$message."', '".$post."', '".$post_topic."', '".$insert_keywords."', now(), now(), now(), now(),'1','1')") or die("Query 5 Error");

    $insert_id = mysql_insert_id();

    $online = mysql_query("SELECT * FROM chat_active WHERE sender_id='".$receiver_id."' AND (receiver_id='".$sender_id."' AND group_id='".$group_id."') AND status='Y'") or die("Query Online 1 Error");

    if( mysql_num_rows($online) > 0  ){
        while($row = mysql_fetch_array($online)){
            $status = $row["status"];
        }

        $query =  mysql_query("UPDATE chats SET view_status='y', online='Y ' WHERE id=".$insert_id) or die("Update Query Error");
    }

    if($insert){
        echo "success";
    }else{
        echo "Failure To Communicate. Please Try Again Later";
    }

}
?>
