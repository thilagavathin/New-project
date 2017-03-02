<?php ob_start(); session_start(); include_once('config.php');

if(isset($_POST['profile'])){
	foreach($_POST['profile'] as $key => $value){
		$search = "SELECT * FROM login_profiles WHERE pfield_id = '".$key."' AND user_id = '".$_POST['user_id']."'";
		$result_search = mysql_query($search);
		$count = mysql_num_rows($result_search);
		
		if($key == '14' && $value == 'on'){ $value = '1'; }
		if($key == '15' && $value == 'on'){ $value = '1'; }
		if($key == '16' && $value == 'on'){ $value = '1'; }
		if($key == '17' && $value == 'on'){ $value = '1'; }
		if($key == '18' && $value == 'on'){ $value = '1'; }
		if($key == '19' && $value == 'on'){ $value = '1'; }
		if($key == '20' && $value == 'on'){ $value = '1'; }
		if($key == '21' && $value == 'on'){ $value = '1'; }
		
		if($count == '1'){
			$update_profile = "UPDATE login_profiles SET profile_value = '".$value."' WHERE pfield_id = '".$key."' AND user_id = '".$_POST['user_id']."'";
			$result = mysql_query($update_profile);
		}else{
			$insert_profile = "INSERT INTO login_profiles (profile_value,pfield_id,user_id) VALUES ('".$value."','".$key."','".$_POST['user_id']."')";
			$result = mysql_query($insert_profile);
		}
	}
}

if(isset($_POST['user'])){
	if($_POST['user']['restricted'] == "on"){
		$restricted = 1;
	}else{
		$restricted = 0;
	}
    $get_user_role="SELECT user_level FROM login_users where username='".$_POST['user']['username']."'";
    $result_user = mysql_query($get_user_role);
    $row=mysql_fetch_array($result_user);
    $user_level_role=unserialize($row['user_level']);
    $update_user_role_min=min($_POST['user']['user_level']);
    if($_SESSION['userrole']==1) { $update_approved=", approved='".$_POST['user_approved']."'";  }
	else $update_approved='';
    $user_role_min= min($user_level_role);
    if($update_user_role_min<>$user_role_min )
    {
        if($user_role_min<>1)
        {
           $update_roles = "UPDATE TTA_Forms SET assignedUser = 'admin' WHERE assignedUser = '".$_POST['user']['username']."'";
            mysql_query($update_roles);
        }
        else
        {
           $update_roles = "UPDATE TTA_Forms SET assignedUser = 'admin' WHERE assignedUser = '".$_POST['user']['username']."'";
            mysql_query($update_roles);
        }
    }
	$update_user = "UPDATE login_users SET name = '".$_POST['user']['name']."', username = '".$_POST['user']['username']."', email = '".$_POST['user']['email']."',position = '".$_POST['user']['position']."', phone='".$_POST['user']['phone']."' ,user_level = '".serialize($_POST['user']['user_level'])."', restricted = '".$restricted."' ".$update_approved." WHERE user_id = '".$_POST['user_id']."'";
	$result = mysql_query($update_user);
	
	if($_POST['user']['password'] != "" && $_POST['user']['cpassword'] != ""){
		if($_POST['user']['password'] == $_POST['user']['cpassword']){
			$update_pass = "UPDATE login_users SET password = '".md5($_POST['user']['password'])."' WHERE user_id = '".$_POST['user_id']."'";
			$result = mysql_query($update_pass);
		}
	}
	
	if($_POST['user']['delete'] == "on"){
		$update_delete = "DELETE FROM login_users WHERE user_id = '".$_POST['user_id']."'";
		$result = mysql_query($update_delete);
		if($result==1) {
			header('Location:users.php');
			die;
		}
	}

}
if($result==1) {
    $_SESSION['update']=1;
    header('Location:userprofile.php?uid='.$_POST['user_id']);
    die;
}
?>