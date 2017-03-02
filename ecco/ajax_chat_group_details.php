<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$sender_id = $_SESSION['adminlogin'];

$id = trim( $_POST["id"] );

$query = mysql_query("SELECT * FROM group_members  WHERE group_id=".$id) or die("Query Error");
$user_id = "";
$html = $admin_user_name = $admin_id  = "";
$admin = $noAdmin  = "N";
if( mysql_num_rows($query) > 0 ){
    while( $row = mysql_fetch_array($query) ){
        $user_id  .= $row["member_user_id"];
        $user_id .= ",";
        $admin_id  = $row["created_user_id"];
    }

    $user_id = rtrim($user_id, ",");
    $sql = mysql_query("SELECT * FROM login_users WHERE user_id IN (".$user_id.") ORDER BY name ASC") or die("Query Error");
    $user_name = "";
    if( mysql_num_rows($sql) > 0 ){
        while($r = mysql_fetch_array($sql)){
            $user_id1 = $r["user_id"];
            if( $r["user_id"] == $admin_id  ){
                $admin_user_name = $r["name"];
            }else if( $r["user_id"] != $admin_id ){
                $noAdmin = "Y";
            }
            if( $admin_id == $sender_id ){
                $admin = "Y";
            }
            $user_name .= '<li><span>';
            $user_name .= '<a href="?r='.$user_id1.'">'.$r["name"].'</a>';
            if($admin_id!=$user_id1 && $admin_id == $sender_id) {
                $user_name .= '<i class="fa fa-trash gr text_light_red" id="gr'.$user_id1.'" onclick="return removeUserFromGroup('.$user_id1.', '.$id.');"></i></span>';
            }
            $user_name .= '</li>';
        }
    }
    $user_name = rtrim($user_name, ", ");
    $html .= $user_name."***<span class='admin_sec'><b>Admin</b>: <a href='?r=".$admin_id."'>".$admin_user_name."</a></span><span class='admin_sec'></span>";
}else{
    $html .= "<div>No users found.</div>***";
}

if($query){
    echo $html; die;
}else{
    echo "Failure to remove message***"; die;
}

?>
