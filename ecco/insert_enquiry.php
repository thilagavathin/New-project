<?php
include_once('config.php');


	 $key =rand(999, 9999);
	 $contract_num = mt_rand( 10000000, 99999999);
	 $contract_num = "TTAREQ-".$contract_num;
	 
	date_default_timezone_set('America/New_York');
	$created_date = date('Y-m-d h:i:s A'); 
	
	

	 $SQL ="INSERT INTO help
      (fname,lname,date_time,enq_time,position,agency,region,emailid,cnt_no,queries,regarding,active_key,contract_num)
       VALUES(
       '".$_POST['fname']."',
       '".$_POST['lname']."',
       '".date('Y-m-d',strtotime($_POST['date_time']))."',
	   '".$_POST['enq_time']."',
       '".$_POST['position']."',
       '".$_POST['agency']."',
       '".$_POST['region']."',
       '".$_POST['email']."',
       '".$_POST['cnt_no']."',
       '".$_POST['regarding']."',
	   '".$_POST['query']."',
       '".$key."',
	   '".$contract_num."'              
       )";
	   
        $user_name = $_POST['fname']." ".$_POST['lname'];
        $result = mysql_query($SQL);
       
		$sql="SELECT * FROM agency where id ='".$_POST['agency']."'";
		$result_agency = mysql_query($sql) or die(mysql_error());
		while($row_agency=mysql_fetch_array($result_agency))
		{
		    $agency_name = $row_agency['name'];
			$agency_address = $row_agency['street'].",".$row_agency['city'].",".$row_agency['state'].",".$row_agency['zip'];
		    $Manage_name = $row_agency['manager_name'];
			$agency_cntno = $row_agency['phone'];
		  }
		 $insert_tta ="INSERT INTO TTA_Forms 
		              (agency_id,contract_num,status,AgencyName,ManagerName,AgencyContactNumber,AgencyAddress,TTA_inquiry_notes,created_date) 
					  values(
					  '".$_POST['agency']."',
					  '".$contract_num."',
					  'Pending',
					  '".$agency_name."',
					  '".$Manage_name."',
					  '".$agency_cntno."',
					  '".$agency_address."',
					  '".$_POST['query']."',
					  '".$created_date."'
					  )";
        $result_tta = mysql_query($insert_tta);
		
		
		if($result==1) {
            confimation_mail_user($_POST['email'],$user_name,$agency_name,$_POST['date_time'],$_POST['position'],$_POST['region'],$_POST['cnt_no'],$_POST['query']);
            information_mail_admin($_POST['email'],$user_name,$agency_name,$_POST['agency'],$_POST['date_time'],$_POST['position'],$_POST['region'],$_POST['cnt_no'],$_POST['query']);
        }

 function confimation_mail_user($email,$user_name,$agency,$time,$position,$region,$cnt_no,$query) {   
    
    /** user mail start here */
	$img_path = "http://www.".$_SERVER["SERVER_NAME"]."/"."image/email.png";
	
    $to   = $email;
    $from = 'ceoden@ingowhiz.com';
    $subject ="Your TTA Request has been successfully submitted";
 
    $headers = "From: " . strip_tags($from) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $message = '<html><body>';
    
    $message .= '<table width="100%"; border="0"  cellpadding="10">';
    
    $message .= "<tr><td colspan=2 font='colr:#999999;'>".$user_name."</td></tr>"; 
    $message .= "<tr><td colspan=2>You will receive contact back with in 48 hours. If you need assistance faster, please call your Marcus Bouligny or Krystal  Lokkesmoe </td></tr>";
    $message .= "<tr><td colspan=2 font='colr:#999999;'>Marcus Bouligny<br />Workforce Development Lead<br />Prospectus Group, LLC<br />Cell: 415.516.1332</td></tr>"; 
    $message .= "<tr><td colspan=2 font='colr:#999999;'>Krystal Lokkesmoe<br />Workforce Development Coordinator<br />Prospectus Group, LLC<br />Cell: 678.557.8711</td></tr>"; 
    $message .= "<tr><td colspan=2 font='colr:#999999;'>Ecco Intake</td></tr>";
	$message .= "<tr><td colspan=2 font='colr:#999999;'>--------------------------------------</td></tr>";
	$message .= "<tr><td colspan=2 font='colr:#999999;'>Agency: ".$agency."<br/>Time Submitted:".date ('d M Y',strtotime($time))."<br />Requester:".$user_name."<br />Your Position:".$position."<br />Region:".$region."<br />Address:".$email."<br />Contact Number:".$cnt_no."<br />Nature of Query:".$query."</td></tr>";
	$message .= "<tr><td colspan=2 font='colr:#999999;'>Thank you for using ECCO!</td></tr>"; 
	$message .= "<tr><td colspan=2><img src='".$img_path."' style='width:200px;'/></td></tr>";
    $message .= "</table>";
    $message .= "</body></html>";  
    mail($to, $subject, $message, $headers);
    
}
 function information_mail_admin($email,$user_name,$agency,$time,$requester,$position,$region,$cnt_no,$query) {
	$img_path = "http://www.".$_SERVER["SERVER_NAME"]."/"."image/email.png";
   /** user mail start here */
    $to   = 'mbouligny@progroup.us';
	$bcc =  'nateforshop@gmail.com,muhammedgn@gmail.com';
    $from = $email;
    $subject ="An ECCO has been submitted";
 
    $headers = "From: " . strip_tags($from) . "\r\n";
	$headers .= "CC: ceoden@ingowhiz.com\r\n";
	$headers .= 'BCC: '. $bcc . "\r\n";
    
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $message = '<html><body>';
    
    $message .= '<table width="100%"; border="0"  cellpadding="10">';    
    $message .= "<tr><td colspan=2>Marcus,<br /><br />An Ecco has been submitted by  ".$agency."</td></tr>";
    $message .= "<tr><td colspan=2 font='colr:#999999;'>Ecco Intake</td></tr>";
	$message .= "<tr><td colspan=2 font='colr:#999999;'>--------------------------------------</td></tr>";
	$message .= "<tr><td colspan=2 font='colr:#999999;'>Agency: ".$agency."<br/>Time Submitted:".date ('d M Y',strtotime($time))."<br />Requester:".$user_name."<br />Your Position:".$position."<br />Region:".$region."<br />Address:".$email."<br />Contact Number:".$cnt_no."<br />Nature of Query:".$query."</td></tr>";
	$message .= "<tr><td colspan=2 font='colr:#999999;'>Thanks for using Ecco!</td></tr>"; 
	$message .= "<tr><td colspan=2><img src='".$img_path."' style='width:200px;'/></td></tr>";
    $message .= "</table>";
    $message .= "</body></html>";  
    mail($to, $subject, $message, $headers);
    
 }
 
 
 echo '<h3>Your Request has been Processed. We will contact soon</h3>';
 echo '<a href="http://ecco.ga-sps.org">Home</a>';
 
 
?>