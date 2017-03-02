<?php ob_start(); session_start();
include_once('config.php');
$display_name_user=(isset($_POST['display_name']))? $_POST['display_name']:$_SESSION['displayname'];
$group2=(isset($_POST['group2']))? $_POST['group2']:'';
function GetImageExtension($imagetype)
{
    if(empty($imagetype)) return false;
    switch($imagetype)
    {
        case 'image/bmp': return '.bmp';
        case 'image/gif': return '.gif';
        case 'image/jpeg': return '.jpg';
        case 'image/png': return '.png';
        default: return false;
    }
}
if($group2=='useruploadphoto1')
{
    if(isset($_FILES["avatar_logo"]["name"]))
    {
        $file_name=$_FILES["avatar_logo"]["name"];
        $temp_name=$_FILES["avatar_logo"]["tmp_name"];
        $imgtype=$_FILES["avatar_logo"]["type"];
        $extent= GetImageExtension($imgtype);
        if($extent=='.bmp' || $extent=='.gif' || $extent=='.jpg' || $extent=='.png' )
        {
            $upfile=array();
            $logo_name=$_SESSION['adminlogin'].$extent;
            $target_path = "assets/logo/".$logo_name;
            $sta=move_uploaded_file($temp_name, $target_path);
            $uplogofile[]=$logo_name;
            $update_query1=" user_logo='".serialize($uplogofile)."'";
        }
        else $update_query1 ="";
    }
    else $update_query1 ="";
}
$result=0;
if($update_query1<>'') {
    $update_logo = "UPDATE login_users SET " . $update_query1 . " WHERE user_id=" . $_SESSION['adminlogin'];
    $result = @mysql_query($update_logo);
}
if($result==1) echo 'success'; else echo 'failure';
?>