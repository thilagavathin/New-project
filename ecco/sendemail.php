<?php 
ob_start(); session_start(); include_once('config.php');
if($_POST['user_level'] != "" && $_POST['subject'] != "" && $_POST['subject'] != ""){ 
$sql = "SELECT email FROM login_users where";
for($i=0; $i<count($_POST['user_level']); $i++){
	if(count($_POST['user_level']) == ($i+1)){
		$sql .= " user_level LIKE '".'%"'.$_POST['user_level'][$i].'"%'."'";
	}else{
		$sql .= " user_level LIKE '".'%"'.$_POST['user_level'][$i].'"%'."' OR";
	}
}
$result_mail = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($result_mail)) {
	$email = $row['email'];
confimation_mail_user($email, $_POST['subject'],$_POST['message']);
}
}else{
	echo "Please pass required all fields - Error";
	header('Location:users.php');
}
function confimation_mail_user($email,$subject,$message)
{ 
try
	{
	$mail = new PHPMailer(true);
	$body  = $message;
	$mail->IsSMTP();                               // tell the class to use SMTP
	$mail->SMTPAuth   = true;                      // enable SMTP authentication
	$mail->Port       = 25;                        // set the SMTP server port
	$mail->Host       = "email-smtp.us-west-2.amazonaws.com";    // SMTP server
	$mail->Username   = "AKIAJA7UMGZCSON3HV5Q";     // SMTP server username
	$mail->Password   = "Ak4m02gSYa7oJFZnRumWh/Q4H2rzuwE29k8cIoGUNX7n";                // SMTP server password
	$mail->IsSendmail();  
	$mail->AddReplyTo("mbouligny@progroup.us","Marcus");
	$mail->From       ="mbouligny@progroup.us";
	$to = $email;
	$mail->AddAddress($email);
	$mail->Subject    = $subject;
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($body);
	$mail->IsHTML(true); // send as HTML
	$mail->Send(); 
	 	
	} catch (phpmailerException $e) {
		echo 'Problem in the Coding';
	}
} 
header('Location:users.php');
?>