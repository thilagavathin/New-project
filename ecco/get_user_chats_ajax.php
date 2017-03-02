<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$receiver_id = $_POST["receiver_id"];
$sender_id = $_POST["sender_id"];
if( empty($receiver_id) || empty($sender_id)){
    echo "Required Fields Missing"; die;
}
if(isset($_SESSION["receiver_id"])){
    $_SESSION["receiver_id"] = $receiver_id;
}else{
    $_SESSION["receiver_id"] = $receiver_id;
}

$sql = mysql_query("SELECT * FROM chats WHERE status='Y' AND  sender_user_id=".$sender_id." AND receiver_user_id=".$receiver_id." OR sender_user_id=".$receiver_id." AND receiver_user_id=".$sender_id." ORDER BY created_at ASC") or die("Query Error");
$count = mysql_num_rows($sql);
if($count > 0){
    $msg = "";
   while($row = mysql_fetch_array($sql)){
       $query = mysql_query("SELECT name, user_image FROM login_users WHERE user_id=".$row['sender_user_id']) or die("Query1 error");
       while($r = mysql_fetch_array($query)){
           $user = $r["name"];
           $user_image = $r["user_image"];
       }
       if($user_image <> '') $user_img=@unserialize($user_image); else $user_img='';
       if($user_img=='')  $img_val ="assets/img/photo.jpg";
        else $img_val ="assets/profile/".$user_img[0];

       if($row['sender_user_id'] == $sender_id){
           $class = "right";
       }else{
           $class = "left";
       }
       $msg .="<div class='full'><div class='".$class."'>
        <h3>Sender: ".$user."</h3>
        <p><span><img src='".$img_val."' alt='user' width='80px'></span>".$row["comments"]."</p>
        </div></div>";
   }
    echo $msg;
}else{
    echo "No Messages Found"; die;
}
?>
