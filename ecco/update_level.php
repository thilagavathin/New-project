<?php ob_start(); session_start(); include_once('config.php');

if($_POST['delete'] == "on"){
	$delete = "DELETE FROM login_levels WHERE id = '".$_POST['level_id']."'";					
	$result = mysql_query($delete);
}else{
	if($_POST['disable'] == "on")
		$disable = '1';
	else
		$disable = '0';

	if($_POST['welcome_email'] == "on")
		$welcome_email = '1';
	else
		$welcome_email = '0';

	$update = "UPDATE login_levels SET 
				level_name = '".$_POST['level_name']."',
				level_level = '".$_POST['level_level']."',
				level_disabled = '".$disable."',
				redirect =  '".$_POST['redirect']."',
				welcome_email = '".$welcome_email."'
				WHERE id = '".$_POST['level_id']."'";					
	$result = mysql_query($update);
}
if($result==1) {
    header('Location:userlevels.php');
    die;
}
else {
    header('Location:message.php');
    die;
}
?>