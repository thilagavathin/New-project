<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$group_id = trim( $_POST["group_id"] );
$group_members = trim( $_POST["group_members"] );
$current_user = $_SESSION['adminlogin'];

$user_admin = 0;
$q = mysql_query("SELECT user_level from login_users WHERE user_id=".$current_user) or die("Query Error");
if( mysql_num_rows($q) > 0){
    while($r = mysql_fetch_array($q)){
        $admin = unserialize($r["user_level"]);
        $user_admin =  $admin[0];
    }
}

$query = mysql_query("SELECT * FROM group_members  WHERE group_id=".$group_id) or die("Query Error");
$count = mysql_num_rows($query);
$sql = mysql_query("SELECT * FROM group_chats WHERE id=".$group_id) or die("Query Error");
if(mysql_num_rows($sql) > 0){
    while($r=mysql_fetch_array($sql)){
        $created_user_id = $r["created_user_id"];
    }
}else{
    $created_user_id = 0;
}
if( $user_admin != 1 && $count > 7){
    echo "You have exceeded maximum no of users to add user to this group."; die;
}else{
    $all_group= explode(",", $group_members);
    $cnt=count($all_group);

    for($i=0;$i<$cnt;$i++)
    {
        mysql_query("insert into group_members(group_id,member_user_id,created_at, updated_at,created_time,updated_time,created_user_id)  values ($group_id, $all_group[$i], now(),now(),now(),now(),$created_user_id)");
    }

}
echo "success"; die;

?>
