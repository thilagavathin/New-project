<?php ob_start(); session_start(); include_once('config.php');
$sql = "SELECT MAX(`level_level`) as max_level FROM `login_levels`";
$result = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($result);
$level_level = $row['max_level'] + 1;
echo $insert_agency = "INSERT INTO login_levels (
                level_name,
				level_level,
				redirect
               )
			   values(
			   '".$_POST['name']."',
			   '".$level_level."',
			   '".$_POST['redirect']."'
			   )";
$result = mysql_query($insert_agency);
if($result==1) {
    header('Location:userlevels.php');
    die;
}
else {
    header('Location:message.php');
    die;
}
?>