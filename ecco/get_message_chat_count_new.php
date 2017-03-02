<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$sender_id = $_SESSION['adminlogin'];
$group_chat_count123 = $chats_count123 = 0;
$group_chat_count123 = mysql_query("SELECT COUNT(*) FROM group_chat_views WHERE receiver_id=".$sender_id." AND group_id<>'0' AND view_status='N'") or die("Query 123 Error");
if(mysql_num_rows($group_chat_count123) > 0){
    while($g_row = mysql_fetch_array($group_chat_count123)){
        $g_count123 = $g_row[0];
    }
}else{
    $g_count123 = 0;
}
$reg = mysql_query('SELECT COUNT(DISTINCT sender_user_id) FROM chats WHERE receiver_user_id="' . $_SESSION["adminlogin"] . '" AND view_status="N"') or die("Query Error");
$chat_count123 = 0;
if(mysql_num_rows($reg) > 0){
    while ($reg_row = mysql_fetch_array($reg)) {
        $chat_count123 = $reg_row["0"];
    }
}

$chats_count123 = $g_count123 + $chat_count123;
if($chats_count123!=0){
echo "<span class='notification'>".$chats_count123."</span>";
}else{
	echo "<span class='empty-notification'></span>";
}
