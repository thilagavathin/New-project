<?php ob_start(); session_start(); include_once('config.php');
$user_level = array("3");
$password = substr(md5(rand().rand()), 0, 6);
$insert_agency = "INSERT INTO login_users (
				user_level,
                username,
				name,
				email,
				password
               )
			   values(
			   '".serialize($user_level)."',
			   '".$_POST['username']."',
			   '".$_POST['name']."',
			   '".$_POST['email']."',
			   '".md5($password)."'
			   )";
$result = mysql_query($insert_agency);
if($result==1) {
    $u_name=$_POST['name'];
    $info_arr=array();
    $info_arr['to']=$_POST['email'];
    $info_arr['from']='mbouligny@progroup.us';
    $info_arr['BCC']='';
    $info_arr['CC']='';
    $info_arr['subject']="You're registered with ECCO TTA !";
    $info_arr['username']=$u_name;
    $info_arr['message_title']='';

$info_arr['message_body']='
 <table style="width:100%;">
<tr>
  	<!-- subject details -->
  	 <td style="padding: 20px;">
  	 	<h2 style="color:#284fa3; font-family: \'Roboto\'; line-height:20px;"><i>Thanks for registering</i></h2>
  	 	<p style="font-family: \'Roboto\'; font-weight:600; font-size:18px;">Hello, <a href="" style="color:#284fa3; font-weight:500; text-decoration:none;">'.$u_name.' </a> </p>
        
  	 </td>
  	 <!-- end subject details -->
  </tr>
  <tr>
  	<!-- Comment details -->
  	 <td bgcolor="#e9e9e9" style="padding:30px 20px;  border-top:1px solid #c2c2c2; border-bottom:1px solid #c2c2c2;">
  	 	<table style="width:100%; ">
  	 		<tr>
  	 			<td style="padding-top:0; padding-left:20px;font-family: \'Roboto\'; margin-top:0; font-size:18px; font-weight:500;">
                   <h3 style="color:#284fa3; font-family: \'Roboto\'; line-height:20px;"><i>Here are your account details:</i></h3>  
                    <table border-collapse:collapse; " border="1" cellspacing="0" cellpadding="7">
                    <tr> <td>Name </td> <td>'.$_POST['name'].'</td></tr>
                    <tr> <td>Username</td> <td>'.$_POST['username'].'</td></tr>
                    <tr> <td>Email </td> <td>'.$_POST['email'].'</td></tr>
                    <tr> <td>Password </td> <td>'.$password.'</td></tr>
                    </table>
                    <p style="font-family: \'Roboto\';  font-weight:300; text-align: right; text-decoration: underline;  font-size:14px; margin-bottom:0;"><a href="'.$site_url.'"><i>Log into Ecco with above details</i></a></p>                   
                </td>
  	 		</tr>
  	 	</table>
  	 </td>
  	 <!-- end Comment details -->
  </tr>
</table>
';

    send_mail_template($info_arr);
    header('Location:users.php');
    die;
}
else {
    header('Location:users.php?error=1');
    die;
}

function confimation_mail_user($email,$user_name,$name,$password)
{
try
	{
	$mail = new PHPMailer(true);
	$message = '<html><body>';
	$message .= '<table width="100%"; border="0"  cellpadding="10">';
	$message .= "<tr><td colspan=2 font='colr:#999999;'>Hello ".$user_name."</td></tr>"; 
	$message .= "<tr><td colspan=2>You're now registered at ".$site_url." Here are your account details:</td></tr>";
	$message .= "<tr><td colspan=2 font='colr:#999999;'>Name: ".$name."</td></tr>"; 
	$message .= "<tr><td colspan=2 font='colr:#999999;'>Username: ".$user_name."</td></tr>"; 
	$message .= "<tr><td colspan=2 font='colr:#999999;'>Email: ".$email."</td></tr>"; 
	$message .= "<tr><td colspan=2 font='colr:#999999;'>Password: ".$password."</td></tr>"; 
	$message .= "</table>";
	$message .= "</body></html>";  
	mail($email, $subject, $message, $headers);
	$body  = $message;
			$mail->IsSMTP();                               // tell the class to use SMTP
			$mail->SMTPAuth   = true;                      // enable SMTP authentication
			$mail->Port       = 25;                        // set the SMTP server port
			$mail->Host       = "email-smtp.us-west-2.amazonaws.com";    // SMTP server
			$mail->Username   = "AKIAJA7UMGZCSON3HV5Q";     // SMTP server username
			$mail->Password   = "Ak4m02gSYa7oJFZnRumWh/Q4H2rzuwE29k8cIoGUNX7n";                // SMTP server password
			$mail->IsSendmail();  
            $mail->AddReplyTo("mbouligny@progroup.us","Marcus");
            $mail->From       = "mbouligny@progroup.us";
            $mail->FromName   = "Marcus";                                     // tell the class to use Sendmail

			$to = $email;
		    $mail->AddAddress($to);
			$mail->Subject    = "You're registered with ECCO TTA !";
			$mail->WordWrap   = 80; // set word wrap
			$mail->MsgHTML($body);
			$mail->IsHTML(true); // send as HTML
			$mail->Send();

		} catch (phpmailerException $e) {
			echo 'Problem in the Coding';
		}
}
?>