<?php ob_start(); session_start();
include_once('config.php');
$display_name=(isset($_POST['display_name']))? $_POST['display_name']:$_SESSION['displayname'];
$user_position = (isset($_POST['position_name']))? $_POST['position_name']:$position;
$user_agencyname = (isset($_POST['agency_name']))? $_POST['agency_name']:$Agencyname;
$user_region = (isset($_POST['region_name']))? $_POST['region_name']:$Region;
$group1=(isset($_POST['group1']))? $_POST['group1']:'';
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

// user image
if($group1=='useruploadphoto')
{
    if(isset($_FILES["avatar_img"]["name"]))
    {
        $file_name=$_FILES["avatar_img"]["name"];
        $temp_name=$_FILES["avatar_img"]["tmp_name"];
        $imgtype=$_FILES["avatar_img"]["type"];
        $ext= GetImageExtension($imgtype);
        if($ext=='.bmp' || $ext=='.gif' || $ext=='.jpg' || $ext=='.png' )
        {
            $upfile=array();
            $image_name=$_SESSION['adminlogin'].$ext;
            $target_path = "assets/profile/".$image_name;
            $sta=move_uploaded_file($temp_name, $target_path);
            $upfile[]=$image_name;
            $update_query =" user_image='".serialize($upfile)."',name='".mysql_real_escape_string($display_name)."',position='".$user_position."',AgencyName='".$user_agencyname."',region='".$user_region."'";
        }
        else $update_query =" name='".mysql_real_escape_string($display_name)."',position='".$user_position."',AgencyName='".$user_agencyname."',region='".$user_region."'";
    }
    else $update_query =" name='".mysql_real_escape_string($display_name)."',position='".$user_position."',AgencyName='".$user_agencyname."',region='".$user_region."'";
}
elseif($group1=='usedefault')
{
    $update_query =" user_image='',name='".mysql_real_escape_string($display_name)."',position='".$user_position."',AgencyName='".$user_agencyname."',region='".$user_region."'";
}
else $update_query="name='".mysql_real_escape_string($display_name)."',position='".$user_position."',AgencyName='".$user_agencyname."',region='".$user_region."'";
$result=0;
if($update_query<>'') {
    $update_profile = "UPDATE login_users SET " . $update_query . " WHERE user_id=" . $_SESSION['adminlogin'];
    $result = @mysql_query($update_profile);
    $_SESSION['displayname'] =$display_name;
}
if($result==1) echo 'success'; else echo 'failure';

?>