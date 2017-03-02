<?php ob_start(); session_start(); 
    include_once('config.php'); 

	$sql="SELECT count(*) as cnt,user_id,username,name,user_level,approved FROM login_users where username ='".trim($_POST['username'])."' and password ='".trim(md5($_POST['password']))."'";

	$result_mail = mysql_query($sql) or die(mysql_error());   
    $row=mysql_fetch_array($result_mail);	
	if($row['cnt'] == 1) 	{
        if($row['approved']=='NO') {  header('Location:login.php?id=2'); die; }
        $user_level=unserialize($row['user_level']);
        $user_role= min($user_level);
		$_SESSION['adminlogin'] = $row['user_id'];
		$_SESSION['adminlogin1'] = $row['username'];
        $_SESSION['displayname'] = $row['name'];
        $_SESSION['userrole'] = $user_role;
        header('Location:systemdashboard.php');
		die;
    }else{
        header('Location:login.php?id=1');
		die;
    }	  
   
?>