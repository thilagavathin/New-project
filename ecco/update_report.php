<?php ob_start(); session_start(); include_once('config.php');
ini_set('memory_limit', '-1');
if($_SERVER['HTTP_REFERER']=='') $_SERVER['HTTP_REFERER']='dashboard.php';
function GetImageExtension($imagetype)
{
    if(empty($imagetype)) return false;
    switch($imagetype)
    {
        case 'application/vnd.ms-excel': return 'xls';
        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': return 'xlsx';

        default: return false;
    }
}
function redirect($url, $permanent = false) {
    if($permanent) {
        header('HTTP/1.1 301 Moved Permanently');
    }
    header('Location: '.$url);
    exit();
}

function information_mail_admin($email, $user_name, $agency, $time, $inputfile_withpath, $uploadfilename, $agency_id) {
	
    $img_path = "http://ga-sps.org/assets/images/logo-gasps.png";
    $progroup_img_path = "http://ga-sps.org/assets/images/Powered_by_ProGroup.png";
    $subject = "A Report has been submitted by ECCO(".$agency.")";

    $message = '<html><body>';
    $message .= '<table width="100%" border="0"  cellpadding="10">';
    $message .= "<tr><td colspan=2 style='border: 1px solid #98002e; background-color: #ffffff; border-radius: 3px'><a href='".$site_url."'><img src='".$img_path."' style='width:250px;' alt='Georgia Strategic Prevention System'/></a></td></tr>";
    $message .= "<tr><td colspan=2><p>ECCO Report has been submitted to ECCO by  (" . $agency . ")"."</p>";

    if($uploadfilename){
        $message .= "<ul style='margin:0;padding:0'>";

        $message .=  '<li style="margin:0 0 0 20px;padding:0"><a href="'.$inputfile_withpath.'">' . $uploadfilename . '</a></li>';
        $message .= "</ul>";
    }
    $message .= "</td></tr><tr><td colspan='2' font='color:#999999;'><table border='1' cellspacing='0' cellpadding='5'>";

    $message .= "<tr><th align='left' style='text-align: left;border-left: solid 1px #e9e9e9; background: #ffffff' bgcolor='ffffff'>Agency</th><td>" . $agency . "</td><tr>";
    $message .= "<tr><th align='left' style='text-align: left;border-left: solid 1px #e9e9e9; background: #ffffff' bgcolor='ffffff'>Time Submitted</th><td>" .  date('d M Y',strtotime($time)) . "</td><tr>";
    $message .= "<tr><th align='left' style='text-align: left'>Uploader</th><td>" . $user_name . "</td><tr>";

    $message .= "</table></td></tr>";
    $message .= '<tr><td bgcolor="#fff" style="padding: 20px;"><table border="1" style="border-collapse: collapse; border:0;width:100%; "><tr><td align="center"><img src="'.$site_url.'/images/progroup_logo.png" width="250" alt="ProGroup" /></td></tr><tr><td align="center"><p style="font-family: \'Roboto\'; font-size:13px; font-weight:300; margin-bottom:0;"><span style="color:#284fa3;"><a href="" style="color:#284fa3;text-decoration:none;">Terms</a> | <a href="" style="color:#284fa3;text-decoration:none;">Privacy</a> | <a href="" style="color:#284fa3;text-decoration:none;">Unsubscribe</a></span> Copyright &copy; 2016 Prospectus Group, LLC. All rights reserved.</p></td></tr></table> </td> </tr>';
    $message .= "</table>";
    $message .= "</body></html>";
    set_include_path(get_include_path() . PATH_SEPARATOR . 'ecco/');
    $mail = new PHPMailer(true); //New instance, with exceptions enabled
    $body  = $message;
    $mail->IsSMTP();                               // tell the class to use SMTP
    $mail->SMTPAuth   = true;                      // enable SMTP authentication
    $mail->Port       = 25;                        // set the SMTP server port
    $mail->Host       = "email-smtp.us-west-2.amazonaws.com";    // SMTP server
    $mail->Username   = "AKIAJA7UMGZCSON3HV5Q";     // SMTP server username
    $mail->Password   = "Ak4m02gSYa7oJFZnRumWh/Q4H2rzuwE29k8cIoGUNX7n";                // SMTP server password
    $mail->IsSendmail();                           // tell the class to use Sendmail
    $mail->AddReplyTo("mbouligny@progroup.us","Marcus");
    $mail->From       = "mbouligny@progroup.us";
    $mail->FromName   = "Marcus";

    $m_agency = @mysql_query("SELECT user_id FROM agency_map WHERE agency_id=".$agency_id);
    $mapped_user_id = "";
    if(mysql_num_rows($m_agency) > 0){
        while($r = mysql_fetch_array($m_agency)){
            $mapped_user_id .= $r["user_id"];
            $mapped_user_id .= ",";
        }
    }
    $admin_email = $to;
    $mapped_user_email = "";
    //Middle Admin Mail Notification
    $m_a =  array("4");
    $middle_admin_user_level = serialize($m_a);
    $middle_admin_sql = mysql_query("SELECT user_id, email, name FROM login_users WHERE user_level ='".$middle_admin_user_level."'");
    $mapped_middle_admin_user_email = "";
    if(mysql_num_rows($middle_admin_sql) > 0){
        while($m = mysql_fetch_array($middle_admin_sql)){
            $mapped_middle_admin_user_email .= $m["email"];
            $mapped_middle_admin_user_email .= ",";
            $mail->AddBCC($m['email'], $m['name']);
        }
    }
    $mapped_middle_admin_user_email = rtrim($mapped_middle_admin_user_email,",");

    if(!empty($mapped_middle_admin_user_email)){
        $all_admin_email = $admin_email.",".$mapped_middle_admin_user_email;
    }else{
        $all_admin_email = $admin_email;
    }
    if(!empty($mapped_user_id)){
        $new_cc_sql = @mysql_query("SELECT email, name FROM login_users WHERE user_id IN (".$mapped_user_id.") AND email NOT IN (".$all_admin_email.")");
        if(mysql_num_rows($new_cc_sql) > 0){
            while($row_e = mysql_fetch_array($new_cc_sql)) {
                $mapped_user_email .= $row_e['email'];
                $mapped_user_email .= "";
            }
        }
    }
    $all_bcc_email = $all_admin_email;
    if(!empty($mapped_user_email)){
        $all_bcc_email .= ",".$mapped_user_email;
    }
    $all_bcc_email =  implode(',', array_unique(explode(',', $all_bcc_email)));

    if(!empty($all_bcc_email)){
    }

    $bcc_sql=mysql_query("SELECT email,name FROM TTA_Forms T inner join login_users U on U.username=T.assignedUser WHERE agency_id=".$agency_id."  group by assignedUser ") or die("Q1 error");
    while($row_e = mysql_fetch_array($bcc_sql)) {
    }
   $mail->AddAddress($to);
    $mail->Subject    = "A Report has been submitted by ECCO".$agency;
    $mail->WordWrap   = 80; // set word wrap
    $mail->MsgHTML($body);
    $mail->IsHTML(true); // send as HTML
    $mail->Send();
}

if (!empty($_FILES["exampleInputFile"]["name"])) {
	$file_name=$_FILES["exampleInputFile"]["name"];
    $temp_name=$_FILES["exampleInputFile"]["tmp_name"];
    $imgtype=$_FILES["exampleInputFile"]["type"];
	$ext= GetImageExtension($imgtype);
    if($ext=='xlsx' || $ext=='xls')
    {
        $imagename=date("d-m-Y")."-".time();
        $subfolder=sha1($imagename);
        $target="assets/uploader/php-traditional-server/files/".$subfolder;
        mkdir($target);
		$target_path = "assets/uploader/php-traditional-server/files/".$subfolder.'/'.$file_name;
        if(move_uploaded_file($temp_name, $target_path)) {
            $img_path = $target_path;
        }
        require_once dirname(__FILE__).'/assets/Classes/PHPExcel.php';
       include dirname(__FILE__)."/assets/Classes/PHPExcel/IOFactory.php";
		$inputFileName = $_SERVER['DOCUMENT_ROOT']."/assets/uploader/php-traditional-server/files/".$subfolder."/".$file_name;
       try {
           $inputFileType = PHPExcel_IOFactory::identify($inputFileName); 
           $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
         die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        $created=$_POST['up_createdate'];
        $agency_id= $_POST['up_agency'];
        $splt_create=explode('-',$created);
        if($splt_create[2] < 5)
        {
            $report_start=date('Y-m-d H:i:s',mktime(0,0,0,date('m')-1,'5',date('Y')));
            $report_end=date('Y-m-d H:i:s',mktime(23,59,59,date('m'),'4',date('Y')));
        }
        else
        {
            $report_start=date('Y-m-d H:i:s',mktime(0,0,0,date('m'),'5',date('Y')));
            $report_end=date('Y-m-d H:i:s',mktime(23,59,59,date('m')+1,'4',date('Y')));
        }

        // Get Prevoius File name
        $file_array=array();$perv_report_id='';
		$reportUploadQuery = "SELECT uploadfilename,id FROM TTA_Reports_uploads WHERE date>='".$report_start."' and date <='".$report_end."' AND agency='".$agency_id."'";
        $sel_pre_query=mysql_query($reportUploadQuery) or die("Q error");
        while($upfile_name=mysql_fetch_array($sel_pre_query))
        {
            $temp='';
            $get_filename=unserialize($upfile_name['uploadfilename']);

            if($get_filename[0]==$file_name)
            {
                $perv_report_id.=$upfile_name['id'].',';
            }

        }

     $perv_report_id=rtrim($perv_report_id,',');
     if (!empty($perv_report_id)) {
         $del_query="DELETE FROM TTA_Reports_imports WHERE report_id in (".$perv_report_id.")  AND agency_id='".$agency_id."' ";
        mysql_query($del_query) or die("Q2 error");
     }
        $upfolder=array();$upfile=array();
        $upfolder[]=$subfolder;
		$upfile[]=$file_name;
        $UploadFolderName = serialize($upfolder);
        $UploadFileNameSlash = serialize($upfile);
        $UploadFileName = addslashes($UploadFileNameSlash);
		
        $uploaduser=$_SESSION['adminlogin1'];
        $userid=$_SESSION['adminlogin'];

       $SQL = "INSERT INTO TTA_Reports_uploads
    (agency, fname, lname, position, emailid, contact_no, report_note, uploadfoldername, uploadfilename,uploaduser,userid)
    VALUES(
        '" . $_POST['up_agency'] . "',
        '" . $_SESSION['adminlogin1'] . "',
        '" . $_SESSION['adminlogin1'] . "',
        '','','','','" . $UploadFolderName . "','" . $UploadFileName . "','".$_SESSION['adminlogin1']."','".$_SESSION['adminlogin']."')";
        

        $result = mysql_query($SQL) or die("Q3 error");
        $insert_report_id=mysql_insert_id();
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $cell_titles_val='';

            $worksheetTitle     = $worksheet->getTitle();
            $highestRow         = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;

            //------Check "Setting / Location" is in 2E
            $cell_titles = $worksheet->getCellByColumnAndRow(4,2);
            $cell_titles_val = $cell_titles->getValue();
            //------Check "Setting / Location" is in A3
            $cell_Strategy = $worksheet->getCellByColumnAndRow(0,3);
            $cell_Strategy_val = $cell_Strategy->getValue();

            //---------2E cell value location - sheet only insert to DB
            if($cell_titles_val == 'Setting / Location' || $cell_titles_val=='Setting/Location' || $cell_titles_val=='Setting/    Location' || $cell_titles_val=='Location'){
                for ($row = 3; $row <= $highestRow; ++ $row) {
                    $val=array();
                    for ($col = 4; $col < $highestColumnIndex; ++ $col) {
                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        if($col=='6'||$col=='7'||$col=='8'||$col=='9'){
                            $val[] = PHPExcel_Style_NumberFormat::toFormattedString($cell->getCalculatedValue(), 'm/d/Y');
                        }else{
                            $val[] = $cell->getValue();
                        }
                    }
                    $location               = trim($val[0]);
                    $responsible            = trim($val[1]);
                    if($val[2]=='')  $projected_start_date='';
                    else
                    {
                        $prj_start=explode('/',trim($val[2]));
                        $projected_start_date   = $prj_start[2].'-'.$prj_start[0].'-'.$prj_start[1];
                    }

                    if($val[3]=='')  $projected_end_date     ='';
                    else
                    {
                        $prj_end=explode('/',trim($val[3]));
                        $projected_end_date     = $prj_end[2].'-'.$prj_end[0].'-'.$prj_end[1];
                    }
                    if($val[4]=='') $actual_start_date='';
                    else
                    {
                        $act_start=explode('/',trim($val[4]));
                        $actual_start_date      = $act_start[2].'-'.$act_start[0].'-'.$act_start[1];
                    }
                    if($val[5]=='') $actual_end_date        = '';
                    else
                    {
                        $act_end=explode('/',trim($val[5]));
                        $actual_end_date        = $act_end[2].'-'.$act_end[0].'-'.$act_end[1];
                    }
                    $comment                = trim($val[6]);
                    $status_code            = trim($val[7]);
                    @$count_feilds=count(array_count_values($val));
					
                    if($count_feilds>0){
                        $created=date('Y-m-d H:i:s');
                        $projected_start_date = date("Y-m-d", strtotime($projected_start_date));
                        $projected_end_date = date("Y-m-d", strtotime($projected_end_date));
                        $actual_start_date = date("Y-m-d", strtotime($actual_start_date));
                        $actual_end_date = date("Y-m-d", strtotime($actual_end_date));

                        if($projected_start_date<>'' && $projected_end_date) {
                            $insertTable1 = 'insert into TTA_Reports_imports (location,responsible,projected_start_date,projected_end_date,actual_start_date,actual_end_date,comment,status_code,sheet_name,report_id,agency_id,created,uploaduser,userid) values("' . $location . '","' . $responsible . '","' . $projected_start_date . '","' . $projected_end_date . '","' . $actual_start_date . '","' . $actual_end_date . '","' . $comment . '","' . $status_code . '","' . $worksheetTitle . '","' . $insert_report_id . '","' . $agency_id . '","' . $created . '","' . $uploaduser . '","'.$userid.'")';
                            $result1 = @mysql_query($insertTable1);
                        }
                    }
                }
            }//--------------Setting / Location

            //---------A3 cell value as Evidenced-based Strategy Name: - sheet only insert to DB
            if($cell_Strategy_val == 'Evidenced-based Strategy Name:'){
                $Strategy_val=array();
                for($row = 3; $row <= 11; ++ $row) {
                    $cell = $worksheet->getCellByColumnAndRow(1, $row);
                    $Strategy_val[] = $cell->getValue();
                }
                $strategy_name          = "";
                $variables_factors      = "";
                $strategy_intent        = "";
                $target_audience        = "";
                $iom_category           = "";
                $estimated_reach        = "";
                $strategy_dosage        = "";
                $strategy_frequent      = "";
                $resources              = "";

                $strategy_name          = trim($Strategy_val[0]);
                $variables_factors      = trim($Strategy_val[1]);
                $strategy_intent        = trim($Strategy_val[2]);
                $target_audience        = trim($Strategy_val[3]);
                $iom_category           = trim($Strategy_val[4]);
                $estimated_reach        = trim($Strategy_val[5]);
                $strategy_dosage        = trim($Strategy_val[6]);
                $strategy_frequent 	    = trim($Strategy_val[7]);
                $resources       	    = trim($Strategy_val[8]);

                @$count_feilds=count(array_count_values($Strategy_val));
                if($count_feilds>0){
                    $insertTable1='insert into TTA_Reports_imports_strategy (strategy_name,variables_factors,strategy_intent,target_audience,iom_category,estimated_reach,strategy_dosage,strategy_frequent,resources,sheet_name,report_id,uploaduser,userid) values("'.$strategy_name.'","'.$variables_factors.'","'.$strategy_intent.'","'.$target_audience.'","'.$iom_category.'","'.$estimated_reach.'","'.$strategy_dosage.'","'.$strategy_frequent.'","'.$resources.'","'.$worksheetTitle.'","'.$insert_report_id.'","'.$uploaduser.'","'.$userid.'")';
                    $result1=@mysql_query($insertTable1);
                }

            }//--------------Evidenced-based Strategy Name:
        }

        #------------REport Import---------------
        if($insert_report_id)
        {
            $inputFileName_mail = $site_url."/assets/uploader/php-traditional-server/files/".$subfolder."/".$file_name;
            $username=$_SESSION['displayname'];

            $sql_agency = mysql_query("SELECT name FROM agency WHERE id =".$agency_id) or die("Q4 error");
            $agency_name_row=mysql_fetch_row($sql_agency);
            $agencyname = $agency_name_row[0];
            $from_mail='eccodashboard@progroup.us';
			
        }
       redirect('reportdashboard.php?message=true');
    }
    else {
       redirect($_SERVER['HTTP_REFERER']);
    }
}
else
{
  redirect('reportdashboard.php');
}
?>
