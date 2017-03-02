<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$id = trim( $_POST["id"] );
$group_id = trim( $_POST["group_id"] );
$sender_id = $_SESSION['adminlogin'];
$admin_id = "";

$query = mysql_query("SELECT * FROM group_members  WHERE group_id=".$group_id) or die("Query Error");
if( mysql_num_rows($query) > 0 ) {
    while ($row = mysql_fetch_array($query)) {
        $admin_id = $row["created_user_id"];
    }
}
if( $sender_id != $admin_id){
    echo "You have no permission to remove user"; die;
}else{
    $sql = mysql_query("DELETE FROM group_members WHERE group_id=".$group_id." AND member_user_id=".$id) or die("Query Error");
    if($sql){
        echo "User Removed Successfully"; die;
    }else{
        echo "Failure to remove user";
    }
}


?>
