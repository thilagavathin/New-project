<?php
/******** Database configuration ******************/

//Connecting to your database

if($_SERVER['HTTP_HOST']=='localhost:8080'){
    $site_url="http://localhost:8080/thilaga/ecco";
    $hostname = "localhost";
    $username = "root";
    $dbname = "devecco_db";
    //These variable values need to be changed by you before deploying
    $password = "";
$con = mysql_connect($hostname, $username, $password) OR DIE ("Unable to connect to database! Please try again later.");
mysql_select_db($dbname, $con);
}else{
    $site_url=$_SERVER['HTTP_HOST'];
    $hostname = "localhost";
    $username = "deveccoga_db";
    $dbname = "deveccoga_db";
    //These variable values need to be changed by you before deploying
    $password = "LMgBG5dccNskft4WEq";
$con = mysql_connect($hostname, $username, $password) OR DIE ("Unable to connect to database! Please try again later.");
mysql_select_db($dbname, $con);
}
date_default_timezone_set('America/New_York');
require 'mail/class.phpmailer.php';
function send_mail_template($info=array())
{
try {
	$mail = new PHPMailer(true);
    $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>	  
	<style>
		@font-face {
		    font-family: DeliciousRoman;
		    src: url(https://fonts.googleapis.com/css?family=Roboto:300,500);
		}
	</style>
</head> 
<body style="margin: 0; padding: 0;">
 <table align="center" border="1" cellpadding="0" cellspacing="0" width="650" style="border-collapse: collapse; border:0;">
   <tr>
   <td bgcolor="#284fa3" style="padding:10px 20px;">
   		<!-- logo and current month,date-->
   		<table style="width:100%;">
   			<tr>
   				<td>
   					<img src="'.$site_url.'/images/logo_white.png">
   				</td>
   				<td>
   					<p style="text-align:right; color:#fff; font-family: \'Roboto\'; ">'.date("M d, Y").', '.date("h:i a").'</p>
   				</td>
   			</tr>
   		</table>
   		<!-- end logo and current month,date-->
   </td>
  </tr>
  <tr>
            ';
    $message .=$info['message_body'];
    $message .='
  </tr>
  <tr>
  	<!-- footer -->
  	 <td bgcolor="#fff" style="padding: 20px;">
  	 	<table style="width:100%; ">
  	 		<tr>
  	 			<td align="center">
  	 				<img src="'.$site_url.'/images/progroup_logo.png" width="250" alt="ProGroup">
  	 			</td>
  	 		</tr>
  	 		<tr>
  	 			<td align="center">
  	 				<p style="font-family: \'Roboto\'; font-size:13px; font-weight:300; margin-bottom:0;"><span style="color:#284fa3;"><a href="" style="color:#284fa3;text-decoration:none;">Terms</a> | <a href="" style="color:#284fa3;text-decoration:none;">Privacy</a> | <a href="" style="color:#284fa3;text-decoration:none;">Unsubscribe</a></span> Copyright &copy; 2016 Prospectus Group, LLC. All rights reserved.</p>
  	 			</td>
  	 		</tr>
  	 	</table>
  	 </td>
  	 <!--end footer -->
  </tr>  
</table>
</body>
</html>';
	$cc = $info['CC'];
	$bcc = $info['BCC'];
	$body  = $message;
	$mail->IsSMTP();                               // tell the class to use SMTP
	$mail->SMTPAuth   = true;                      // enable SMTP authentication
	$mail->Port       = 25;                        // set the SMTP server port
	$mail->Host       = "email-smtp.us-west-2.amazonaws.com";    // SMTP server
	$mail->Username   = "AKIAJA7UMGZCSON3HV5Q";     // SMTP server username
	$mail->Password   = "Ak4m02gSYa7oJFZnRumWh/Q4H2rzuwE29k8cIoGUNX7n";                // SMTP server password
	$mail->IsSendmail();  
	$mail->AddReplyTo("mbouligny@progroup.us","Marcus");
	$mail->From       = $info['from'];
	$mail->FromName   = "Marcus";                                     // tell the class to use Sendmail

	$to = $info['to'];
	if($cc!=''){ $mail->AddCC(trim($cc)); }
	if($bcc!=''){ $mail->AddBCC(trim($bcc)); }
	$mail->AddAddress($to);
	$mail->Subject    = $info['subject'];
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($body);
	$mail->IsHTML(true); // send as HTML
	$mail->Send();
	
	} catch (phpmailerException $e) {
			echo 'Problem in the Coding';
		}
}

function convert_timezone_date($date, $format = 'h:i A', $fromTimeZone = 'UTC', $toTimeZone = 'America/New_York')
{
 try {
  $dateTime = new DateTime ($date, new DateTimeZone($fromTimeZone));
  $dateTime->setTimezone(new DateTimeZone($toTimeZone));
  return $dateTime->format($format);
 } catch (Exception $e) {
  return '';
 }
}

?>
