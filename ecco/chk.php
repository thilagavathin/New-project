<?php ob_start(); session_start();
      include_once('config.php');
	  if(isset($_SESSION['adminlogin'])) {
		  header('Location: dashboard.php');
	  }
	  else {
		 header('Location: login.php');
         exit;
		  
	  }
?>

