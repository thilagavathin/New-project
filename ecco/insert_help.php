<?php include_once('config.php'); ob_start();  ?>
<?php session_start();
if(isset($_POST['tta_reports'])){    
    if(is_array($_SESSION['AttachmentUpload'])){
		$UploadFolderName_temp = array();
		$UploadFileName_temp = array();
		foreach($_SESSION['AttachmentUpload'] as $key => $value){
			$UploadFolderName_temp[] = $key;
			$UploadFileName_temp[] = $value;
		}
		$UploadFolderName = serialize($UploadFolderName_temp);
		$UploadFileName = serialize($UploadFileName_temp);
	}else{
		$UploadFolderName = "";
		$UploadFileName = "";
	}
   $SQL = "INSERT INTO TTA_Reports_Uploads
    (agency, fname, lname, position, emailid, contact_no, report_note, uploadfoldername, uploadfilename)
    VALUES(
        '" . $_POST['report_agency'] . "',
        '" . $_POST['report_fname'] . "',
        '" . $_POST['report_lname'] . "',
        '" . $_POST['report_position'] . "',
        '" . $_POST['report_email'] . "',
        '" . $_POST['report_cnt_no'] . "',
        '" . $_POST['report_query'] . "',
        '" . $UploadFolderName . "',
        '" . $UploadFileName . "'
     )";
    $result = mysql_query($SQL);
}
else if(isset($_POST['email'])){
   
    $file='';
        if(is_array($_SESSION['AttachmentUpload1']) ){
            $UploadFolderName_temp = array();
            $UploadFileName_temp = array();
            foreach($_SESSION['AttachmentUpload'] as $key => $value){
                $UploadFolderName_temp[] = $key;
                $UploadFileName_temp[] = $value;
                $file.=$site_url.'/assets/uploader/php-traditional-server/files/'.$key.'/'.$value.'<br>';
            }
            $UploadFolderName = serialize($UploadFolderName_temp);
            $UploadFileName = serialize($UploadFileName_temp);
        }else{
            $UploadFolderName = "";
            $UploadFileName = "";
        }


	$get_email = [];
	$sql = "SELECT user_id, username, user_level, email FROM login_users";
	$result_mail = mysql_query($sql) or die(mysql_error());
	$num_rows = mysql_num_rows($result_mail);

	while($row = mysql_fetch_array($result_mail)) {
		$user_level = $row['user_level'];
		$list = unserialize($user_level);
		if($list[0] == 1) {
			$get_email[] = $row['email'];
		}
	}
 $_POST['enq_time']=(isset($_POST['enq_time']))? $_POST['enq_time']:'';
    $_POST['region']=(isset($_POST['region']))? $_POST['region']:'';
	$email_bcc = implode(",", $get_email);
	$key = rand(999, 9999);
	$contract_num = mt_rand(10000000, 99999999);
	$contract_num = "TTAREQ-" . $contract_num;

	$SQL = "INSERT INTO help
    (fname, lname, date_time, enq_time, position, agency, region, emailid, cnt_no, queries, active_key, contract_num, uploadfoldername, uploadfilename,filepath)
    VALUES(
        '" . $_POST['fname'] . "',
        '" . $_POST['lname'] . "',
        '" . date('Y-m-d') . "',
        '" . $_POST['enq_time'] . "',
        '" . $_POST['position'] . "',
        '" . $_POST['agency'] . "',
        '" . $_POST['region'] . "',
        '" . $_POST['email'] . "',
        '" . $_POST['cnt_no'] . "',
        '" . $_POST['query'] . "',
        '" . $key . "',
        '" . $contract_num . "',
        '" . $UploadFolderName . "',
        '" . $UploadFileName . "',
        '".$file."'
     )";

	$user_name = $_POST['fname'] . " " . $_POST['lname'];
	$result = mysql_query($SQL);
	$sql = "SELECT * FROM agency WHERE id = '" . $_POST['agency'] . "'";
	$result_agency = mysql_query($sql) or die(mysql_error());

	while($row_agency = mysql_fetch_array($result_agency)) {
		$agency_name = $row_agency['name'];
		$agency_address = $row_agency['street'] . "," . $row_agency['city'] . "," . $row_agency['state'] . "," . $row_agency['zip'];
		$manager_name = $row_agency['manager_name'];
		$agency_cntno = $row_agency['phone'];
	}

    if(is_array($_POST['document'])) {
        $documents=array();
        foreach ($_POST['document'] as $document) {
            $documents[]=$document;
        }
    }

   date_default_timezone_set('America/New_York');
	$current_date = date('Y-m-d H:i:s');
	$insert_tta = "INSERT INTO TTA_Forms (agency_id, contract_num, status, AgencyName, ManagerName, AgencyContactNumber, AgencyAddress, TTA_inquiry_notes, TTA_Contact_Phone, user_name, TTA_Email, created_date, regarding, updated_date, TTA_Referral,regarding_notes,resources)
    VALUES(
        '" . $_POST['agency'] . "',
        '" . $contract_num . "',
        'pending',
        '" . $agency_name . "',
        '" . $manager_name . "',
        '" . $agency_cntno . "',
        '" . $agency_address . "',
        '" . $_POST['query'] . "',
        '" . $_POST['cnt_no'] . "',
        '" . $user_name . "',
        '" . $_POST['email'] . "',
        '" . $current_date . "',
        '" . $_POST['regarding'] . "',
        '" . $current_date . "',
        '" . $user_name . "',
        '" . $_POST['regarding_notes']."',
        '".serialize($documents)."'
    )";
	$result_tta = mysql_query($insert_tta);
	

	if($result == 1) {
	confimation_mail_user($_POST['email'], $user_name, $agency_name, date('Y-m-d'), $_POST['position'], $_POST['region'], $_POST['cnt_no'], $_POST['query'],$_POST['regarding'], $_POST['regarding_notes'], $_POST['fname'] );
	information_mail_admin($_POST['email'], $user_name, $agency_name, date('Y-m-d'), $_POST['agency'], $_POST['position'], $_POST['region'], $_POST['cnt_no'], $_POST['query'], $_POST['regarding'], $_POST['regarding_notes'], $email_bcc);
	}
}

	function confimation_mail_user($email, $user_name, $agency, $time, $position, $region, $cnt_no, $query, $regarding, $regarding_notes, $fname ) {
        $img_path = "http://" . $_SERVER["SERVER_NAME"] . "/images/logo_white.png";
        $progroup_img_path = "http://" . $_SERVER["SERVER_NAME"] . "/assets/images/Powered_by_ProGroup.png";
		try {
			$mail = new PHPMailer(true); //New instance, with exceptions enabled
            $message = '<html>
                        <head>	  
                    	<style>
                    		@font-face {
                    		    font-family: DeliciousRoman;
                    		    src: url(https://fonts.googleapis.com/css?family=Roboto:300,500);
                    		}
                    	</style>
                        </head>
                        <body>';
            $message .= '<table width="100%" border="0"  cellpadding="10">';
			$message .= "<tr><td bgcolor='#284fa3' style='padding:10px 20px'>";
			$message .= "<table width='100%' border='0' cellpadding='5'><tr>";
			$message .= "<td><a href='http://" . $_SERVER["SERVER_NAME"] . "' style='font-size:20px;font-weight:bold'><img src='".$img_path."' style='width:250px;' alt='Georgia Strategic Prevention System'/></a></td>";
			$message .= "<td ><p style='text-align:right; color:#fff; font-family: \'Roboto\'; '>".date("M d, Y").", ".date("h:i a")."</p></td>";
			$message .= "</tr></table>";
			$message .= "</td></tr>";
            $message .= "<tr><td colspan=2>" . $fname .",<br /><br />Your Ecco request has been submitted.</td></tr>";
            $message .= "<tr><td colspan='2' font='color:#999999;'><p>Here are your requested resources</p><ul>";
            $pattern = '/(.*\/)(.*)/';
            $replacement = '$2';
            if(is_array($_POST['document'])){
                foreach($_POST['document'] as $document)
                {
                  $sql="SELECT document_name FROM documents WHERE id=".$document;
                    $res_doc=mysql_query($sql);
                    $rows=mysql_fetch_array($res_doc);
                    $doc=$rows['document_name'];
                    $message .=  '<li><a href="'.$site_url . $doc . '">' . preg_replace($pattern, $replacement, urldecode($doc)) . '</a></li>';                 
                }
            }
            $message .= "</ul></td></tr>";
            $message .= "<tr><td colspan='2' font='color:#999999;'><p>Uploaded documents for review:</p><ul>";
            if(is_array($_SESSION['AttachmentUpload'])){
                foreach($_SESSION['AttachmentUpload'] as $key => $value){
                    $message .=  '<li><a href="'.$site_url.'/assets/uploader/php-traditional-server/files/'.$key.'/'.$value.'">'.$value.'</a></li>';
                }
            }                 
            $message .= "</ul></td></tr>";

            $message .= "<tr><td colspan='2'>You should receive contact within 48 hours.</td></tr>";
            $message .= "<tr><td colspan='2'>If you need assistance faster, please call Marcus Bouligny or Krystal Lokkesmoe.</td></tr>";
            $message .= "<tr><td colspan='2' font='color:#999999;'>Marcus Bouligny<br />Workforce Development Lead<br />Prospectus Group, LLC<br />Cell: 415.516.1332</td></tr>";
            $message .= "<tr><td colspan='2' font='color:#999999;'>Krystal Lokkesmoe<br />Workforce Development Coordinator<br />Prospectus Group, LLC<br />Cell: 678.557.8711</td></tr>";
            $message .= "<tr><td colspan='2' font='color:#999999;'>--------------------------------------</td></tr>";
            $message .= "<tr><td colspan='2' font='color:#999999;'>Agency: ".$agency."<br/>Time Submitted:".date ('d M Y',strtotime($time))."<br />Requester:".$user_name."<br />Your Position:".$position."<br />Address:".$email."<br />Contact Number:".$cnt_no."<br />Nature of Query:".$query."<br />Regarding:  ".$regarding."</td></tr>";
            $message .= "<tr><td colspan='2' font='color:#999999;'>Thanks for using Ecco!</td></tr>";
            $message .= '<tr><td bgcolor="#fff" style="padding: 20px;"><table border="1" style="border-collapse: collapse; border:0;width:100%; "><tr><td align="center"><img src="http://' . $_SERVER["SERVER_NAME"].'/images/progroup_logo.png" width="250" alt="ProGroup" /></td></tr><tr><td align="center"><p style="font-family: \'Roboto\'; font-size:13px; font-weight:300; margin-bottom:0;"><span style="color:#284fa3;"><a href="" style="color:#284fa3;text-decoration:none;">Terms</a> | <a href="" style="color:#284fa3;text-decoration:none;">Privacy</a> | <a href="" style="color:#284fa3;text-decoration:none;">Unsubscribe</a></span> Copyright &copy; 2016 Prospectus Group, LLC. All rights reserved.</p></td></tr></table> </td> </tr>';
            $message .= "</table>";
            $message .= "</body></html>";

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
			$mail->Subject    = "Your ECCO Intake request has been submitted";
			echo $mail->Subject; die();
			$mail->WordWrap   = 80; // set word wrap
			$mail->MsgHTML($body);
			$mail->IsHTML(true); // send as HTML
			$mail->Send();

		} catch (phpmailerException $e) {
			echo 'Problem in the Coding';
		}
	}

	function information_mail_admin($email, $user_name, $agency, $time, $requester, $position, $region, $cnt_no, $query, $regarding, $regarding_notes, $email_bcc) {
    $img_path = "http://" . $_SERVER["SERVER_NAME"] . "/images/logo_white.png";    
    $progroup_img_path = "http://" . $_SERVER["SERVER_NAME"] . "/assets/images/Powered_by_ProGroup.png";
	$user_mail='';
        $m_a =  array("4");
        $middle_admin_user_level = serialize($m_a);
		$cc_sql=mysql_query("SELECT DISTINCT `email` FROM `login_users` WHERE `user_level` LIKE '%\"1\"%' OR `user_level` = '".$middle_admin_user_level."' AND email <> 'mbouligny@progroup.us'");
        if(mysql_num_rows($cc_sql) > 0){
            while($row_cc = mysql_fetch_array($cc_sql)) {
                $user_mail.= $row_cc['email'].',';
            }
        } 
		
	try {
			$mail = new PHPMailer(true);
        $message = '<html>
                    <head>	  
                	<style>
                		@font-face {
                		    font-family: DeliciousRoman;
                		    src: url(https://fonts.googleapis.com/css?family=Roboto:300,500);
                		}
                	</style>
                    </head>
                    <body>';
        $message .= '<table width="100%" border="0"  cellpadding="10">';
        $message .= "<tr><td bgcolor='#284fa3' style='padding:10px 20px'>";
        $message .= "<table width='100%' border='0' cellpadding='5'><tr>";
        $message .= "<td><a href='http://" . $_SERVER["SERVER_NAME"] . "' style='font-size:20px;font-weight:bold'><img src='".$img_path."' style='width:250px;' alt='Georgia Strategic Prevention System'/></a></td>";
        $message .= "<td ><p style='text-align:right; color:#fff; font-family: \'Roboto\'; '>".date("M d, Y").", ".date("h:i a")."</p></td>";
        $message .= "</tr></table>";
        $message .= "</td></tr>";
        $message .= "<tr><td colspan=2><p>An ECCO has been submitted by  <b>" . $agency . "</b></p>";

        $pattern = '/(.*\/)(.*)/';
        $replacement = '$2';

        if(is_array($_POST['document'])){
            $message .= "<p>The following resources were requested:</p><ul style='margin:0;padding:0'>";
            foreach($_POST['document'] as $document)
            {
              $sql="SELECT document_name FROM documents WHERE id=".$document;
                $res_doc=mysql_query($sql);
                $rows=mysql_fetch_array($res_doc);
                $doc=$rows['document_name'];
                $message .=  '<li><a href="'.$site_url . $doc . '">' . preg_replace($pattern, $replacement, urldecode($doc)) . '</a></li>';                 
            }
            $message .= "</ul>";
        }

        if( is_array($_SESSION['AttachmentUpload'])){
            $message .= "<p>The following documents were uploaded for review:</p><ul style='margin:0;padding:0'>";
            foreach($_SESSION['AttachmentUpload'] as $key => $value){
                $message .=  '<li style="margin:0 0 0 20px;padding:0"><a href="'.$site_url.'/assets/uploader/php-traditional-server/files/' . $key . '/'.$value.'">' . $value . '</a></li>';
            }
            $message .= "</ul>";
        }

        $message .= "</td></tr><tr><td colspan='2' font='color:#999999;'><table border='1' cellspacing='0' cellpadding='5'>";

        $message .= "<tr><th align='left' style='text-align: left;border-left: solid 1px #e9e9e9; background: #ffffff' bgcolor='ffffff'>Agency</th><td>" . $agency . "</td><tr>";
        $message .= "<tr><th align='left' style='text-align: left;border-left: solid 1px #e9e9e9; background: #ffffff' bgcolor='ffffff'>Time Submitted</th><td>" .  date('d M Y',strtotime($time)) . "</td><tr>";
        $message .= "<tr><th align='left' style='text-align: left'>Requester</th><td>" . $user_name . "</td><tr>";
        $message .= "<tr><th align='left' style='text-align: left'>Position</th><td>" . $position . "</td><tr>";
        $message .= "<tr><th align='left' style='text-align: left'>Email</th><td>" . $email . "</td><tr>";
        $message .= "<tr><th align='left' style='text-align: left'>Contact Number</th><td>" . $cnt_no . "</td><tr>";
        $message .= "<tr><th align='left' style='text-align: left'>Nature of Query</th><td>" . $query . "</td><tr>";
        $message .= "<tr><th align='left' style='text-align: left'>Regarding</th><td>" . $regarding . "</td><tr>";
        $message .= "<tr><th align='left' style='text-align: left'>Background Information</th><td>" . $regarding_notes . "</td><tr>";

        $message .= "</table></td></tr>";
        $message .='<tr><td bgcolor="#fff" style="padding: 20px;"><table border="1" style="border-collapse: collapse; border:0;width:100%; "><tr><td align="center"><img src="'.$site_url.'/images/progroup_logo.png" width="250" alt="ProGroup" /></td></tr><tr><td align="center"><p style="font-family: \'Roboto\'; font-size:13px; font-weight:300; margin-bottom:0;"><span style="color:#284fa3;"><a href="" style="color:#284fa3;text-decoration:none;">Terms</a> | <a href="" style="color:#284fa3;text-decoration:none;">Privacy</a> | <a href="" style="color:#284fa3;text-decoration:none;">Unsubscribe</a></span> Copyright &copy; 2016 Prospectus Group, LLC. All rights reserved.</p></td></tr></table> </td> </tr>';
        $message .= "</table>";
        $message .= "</body></html>";

		$body  = $message;
		$mail->IsSMTP();                               // tell the class to use SMTP
		$mail->SMTPAuth   = true;                      // enable SMTP authentication
		$mail->Port       = 25;                        // set the SMTP server port
		$mail->Host       = "email-smtp.us-west-2.amazonaws.com";    // SMTP server
		$mail->Username   = "AKIAJA7UMGZCSON3HV5Q";     // SMTP server username
		$mail->Password   = "Ak4m02gSYa7oJFZnRumWh/Q4H2rzuwE29k8cIoGUNX7n";                // SMTP server password
		$mail->IsSendmail();  
        $mail->AddReplyTo("mbouligny@progroup.us","Marcus");
        $mail->From       = $email;
		$to = 'mbouligny@progroup.us';
	    $mail->AddAddress($to);
		
		$mail->Subject    = "An ECCO request has been submitted";
		$mail->WordWrap   = 80; // set word wrap
		$mail->MsgHTML($body);
		$mail->IsHTML(true); // send as HTML
		$mail->Send();  
		} catch (phpmailerException $e) {
			//echo 'Problem in the Coding';
		}
 }
if($result == 1) {
?>
<script>
  window.location.href = 'help.php?insert=1';
</script>
<?php } else{?>

<script>
 history.back()
</script>
<?php }?>