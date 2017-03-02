<?php include_once('config.php');
include_once('templates/header.php');
include_once('securimage-master/securimage.php');
if(isset($_POST['tta_reports'])) {
    ini_set('memory_limit', '-1');
    if($_SESSION['AttachmentUploada']!='' && $_SESSION['AttachmentUploadb']!= '') {
        $UploadFolderName_temp = $_SESSION['AttachmentUploada'];
        $UploadFileName_temp = $_SESSION['AttachmentUploadb'];      
        $UploadFolderName = $UploadFolderName_temp;
        $UploadFileName = $UploadFileName_temp;

        $agency_id= $_POST['report_agency'];
        $sql_agency = mysql_query("SELECT name FROM agency WHERE id =".$_POST['report_agency']);
        $agency_name_row=mysql_fetch_row($sql_agency);
        $agencyname = $agency_name_row[0];
        $timestamp = time();
        $path_info = pathinfo($_SERVER['SERVER_NAME']."/assets/uploader/php-traditional-server/files/".$UploadFolderName_temp."/".$UploadFileName_temp);
        $extension = $path_info['extension']; // "bill"   

         if(!empty($UploadFileName)){
            $fName = $agencyname."_".$timestamp.".".$extension;
             $test = array();
            $test = ["0"=>$fName];
            $UploadFileName = serialize($test);
        }  


       $SQL = "INSERT INTO TTA_Reports_uploads
        (agency, fname, lname, position, emailid, contact_no, report_note, uploadfoldername, uploadfilename,uploaduser,userid)
        VALUES(
            '" . $_POST['report_agency'] . "',
            '" . $_POST['report_fname'] . "',
            '" . $_POST['report_lname'] . "',
            '" . $_POST['report_position'] . "',
            '" . $_POST['report_email'] . "',
            '" . $_POST['report_cnt_no'] . "',
            '" . $_POST['report_notes'] . "',
            '" . $UploadFolderName . "',
            '" . $UploadFileName . "','Help',
            '" . $_SESSION['adminlogin'] . "'
         )";
        $result = mysql_query($SQL);
        $insert_report_id=mysql_insert_id();

        set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
        include 'Classes/PHPExcel/IOFactory.php';
        $inputFileName = $_SERVER['DOCUMENT_ROOT']."/assets/uploader/php-traditional-server/files/".$UploadFolderName_temp."/".$UploadFileName_temp;

         rename($inputFileName, $_SERVER['DOCUMENT_ROOT']."/assets/uploader/php-traditional-server/files/".$UploadFolderName_temp."/".$agencyname."_".$timestamp.".".$extension);

    $inputFileName = $_SERVER['DOCUMENT_ROOT']."/assets/uploader/php-traditional-server/files/".$UploadFolderName_temp."/".$agencyname."_".$timestamp.".".$extension;

        try {
            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);

        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

    #------------REport Import---------------
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
                    if($val[2]=='') $projected_start_date='';
                    else
                    {
                        $prj_start=explode('/',trim($val[2]));
                        $projected_start_date   = $prj_start[2].'-'.$prj_start[0].'-'.$prj_start[1];
                    }
                    if($val[3]=='') $projected_end_date     ='';
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

                    $count_feilds=@count(array_count_values($val));
                    if($count_feilds>0){
                        $created=date('Y-m-d H:i:s');
                        $uploaduser='Help';
                        if($projected_start_date<>'' && $projected_end_date)
                        {
                            $projected_start_date = date("Y-m-d", strtotime($projected_start_date));
                            $projected_end_date = date("Y-m-d", strtotime($projected_end_date));
                            $insertTable1='insert into TTA_Reports_imports (location,responsible,projected_start_date,projected_end_date,actual_start_date,actual_end_date,comment,status_code,sheet_name,report_id,agency_id,created,uploaduser) values("'.$location.'","'.$responsible.'","'.$projected_start_date.'","'.$projected_end_date.'","'.$actual_start_date.'","'.$actual_end_date.'","'.$comment.'","'.$status_code.'","'.$worksheetTitle.'","'.$insert_report_id.'","'.$agency_id.'","'.$created.'","'.$uploaduser.'")';
                            $result1=mysql_query($insertTable1);
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

                $strategy_name          = trim($Strategy_val[0]);
                $variables_factors      = trim($Strategy_val[1]);
                $strategy_intent        = trim($Strategy_val[2]);
                $target_audience        = trim($Strategy_val[3]);
                $iom_category           = trim($Strategy_val[4]);
                $estimated_reach        = trim($Strategy_val[5]);
                $strategy_dosage        = trim($Strategy_val[6]);
                $strategy_frequent      = trim($Strategy_val[7]);
                $resources              = trim($Strategy_val[8]);

                $count_feilds=@count(array_count_values($Strategy_val));
                if($count_feilds>0){
                    $insertTable1='insert into TTA_Reports_imports_strategy (strategy_name,variables_factors,strategy_intent,target_audience,iom_category,estimated_reach,strategy_dosage,strategy_frequent,resources,sheet_name,report_id,uploaduser) values("'.$strategy_name.'","'.$variables_factors.'","'.$strategy_intent.'","'.$target_audience.'","'.$iom_category.'","'.$estimated_reach.'","'.$strategy_dosage.'","'.$strategy_frequent.'","'.$resources.'","'.$worksheetTitle.'","'.$insert_report_id.'","Help")';
                    $result1=mysql_query($insertTable1);
                }

            }//--------------Evidenced-based Strategy Name:

        }

        #------------REport Import---------------
        if($insert_report_id)
        {
           $inputFileName = $_SERVER['SERVER_NAME']."/assets/uploader/php-traditional-server/files/".$UploadFolderName_temp."/".$agencyname."_".$timestamp.".".$extension;
            $fName = $agencyname."_".$timestamp.".".$extension;

            $username=$_POST['report_fname'].' '.$_POST['report_lname'];

            $sql_agency = mysql_query("SELECT name FROM agency WHERE id =".$_POST['report_agency']);
            $agency_name_row=mysql_fetch_row($sql_agency);
            $agencyname = $agency_name_row[0];
           @information_mail_admin($_POST['report_email'], $username, $agencyname, date('Y-m-d'),  $_POST['report_position'], $_POST['report_cnt_no'], $_POST['report_notes'], $inputFileName,$fName,$agency_id);
        }
    

    } else { 
        
        $note="Attach a file and upload it again...!";
    }
}
function information_mail_admin($email, $user_name, $agency, $time, $position, $cnt_no,$regarding_notes, $inputfile_withpath,$uploadfilename,$agency_id) {

    $img_path = "http://" . $_SERVER["SERVER_NAME"] . "/images/logo_white.png";
    $progroup_img_path = "http://" . $_SERVER["SERVER_NAME"] . "/assets/images/Powered_by_ProGroup.png";
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
    $message .= "<tr><td colspan=2><p>A report has been submitted to ECCO by <b>" . $agency . "</b></p>";

    if($uploadfilename){
        $message .= "<ul style='margin:0;padding:0'>";

        $message .=  '<li style="margin:0 0 0 20px;padding:0"><a href="'.$inputfile_withpath.'">' . $uploadfilename . '</a></li>';
        $message .= "</ul>";
    }
    $message .= "</td></tr><tr><td colspan='2' font='color:#999999;'><table border='1' cellspacing='0' cellpadding='5'>";

    $message .= "<tr><th align='left' style='text-align: left;border-left: solid 1px #e9e9e9; background: #ffffff' bgcolor='ffffff'>Agency</th><td>" . $agency . "</td><tr>";
    $message .= "<tr><th align='left' style='text-align: left;border-left: solid 1px #e9e9e9; background: #ffffff' bgcolor='ffffff'>Time Submitted</th><td>" .  date('d M Y',strtotime($time)) . "</td><tr>";
    $message .= "<tr><th align='left' style='text-align: left'>Uploader</th><td>" . $user_name . "</td><tr>";
    $message .= "<tr><th align='left' style='text-align: left'>Position</th><td>" . $position . "</td><tr>";
    $message .= "<tr><th align='left' style='text-align: left'>Email</th><td>" . $email . "</td><tr>";
    $message .= "<tr><th align='left' style='text-align: left'>Contact Number</th><td>" . $cnt_no . "</td><tr>";
    $message .= "<tr><th align='left' style='text-align: left'>Background Information</th><td>" . $regarding_notes . "</td><tr>";

    $message .= "</table></td></tr>";
    $message .= "<tr><td colspan=2 style='background:#000000'><img width='200px' height='56px' alt='Powered by the Prospectus Group' src='" . $progroup_img_path. "' style='width:200px;height::56px;background: #000000;'/></td></tr>";
    $message .= "</table>";
    $message .= "</body></html>";


    set_include_path(get_include_path() . PATH_SEPARATOR . 'ecco/');
    $mail = new PHPMailer(true);
    $mail->IsSMTP();                               // tell the class to use SMTP
    $mail->SMTPAuth   = true;                      // enable SMTP authentication
    $mail->Port       = 25;                        // set the SMTP server port
    $mail->Host       = "email-smtp.us-west-2.amazonaws.com";    // SMTP server
    $mail->Username   = "AKIAJA7UMGZCSON3HV5Q";     // SMTP server username
    $mail->Password   = "Ak4m02gSYa7oJFZnRumWh/Q4H2rzuwE29k8cIoGUNX7n";                // SMTP server password
    $mail->IsSendmail();                           // tell the class to use Sendmail
    $mail->Subject    = "A report has been uploaded by ".$agency;
    $mail->WordWrap   = 80; // set word wrap
    $mail->MsgHTML($message);
    $mail->IsHTML(true); // send as HTML

     $cc_sql=mysql_query("SELECT `email`,`name` FROM `login_users` WHERE `user_level` LIKE '%\"1\"%'");
    $mail->AddAddress('vanitha.m@vividinfotech.com');
    $mail->AddReplyTo('mbouligny@progroup.us', 'Marcus');
    $mail->From       = 'mbouligny@progroup.us';
    $mail->FromName   = 'Marcus';
    $mail->Send();
}
$_SESSION['AttachmentUploada'] = '';
$_SESSION['AttachmentUploadb'] = '';
?>

<html>
  <head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description" />
    <meta content="" name="author" />
<style>
label.error{
  color: red !important;
}
.qq-upload-button {
  background: #284fa3 none repeat scroll 0 0 !important;
  border-radius: 5px !important;
  box-shadow: none !important;
  font-weight: bold;
  padding: 10px 0 !important;
}
</style>
  </head>
     		<section>
	     		<div class="container">
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li class="active">Get Help </li>              
						</ol>
					</div>
				 </div>
            <div class="row">
              <div class="col-md-6 gethelp">
              <?php if($_REQUEST['insert']){ echo '<h5>Successfully Completed, Thank You!</h5>';} ?>
              
                <div class="row">
                    <div class="col-md-12">
                      <h1 class="page-title">Get Help / Upload IP</h1>
                    </div>
                </div>
                <p>From this page you can request TTA services or upload your Implementation Plans. <a href="https://docs.google.com/document/d/1Pn3vgG03fh4nUewm7P_7UW6nHotAtKH9455irD31UMM/pub">ECCO Manual</a></p>
                  <h4>TTA Requests:</h4>
                  <p>Monitor the status of your TTA requests by logging in and selecting "Training and Technical Assistance."</p>
                  <h4>Upload IPs Here!</h4>
                  <ul class="procedure_list">
                      <li>Select "Upload Report"</li>
                      <li>Select your gency</li>
                      <li>Fill form</li>
                      <li>Click submit </li>
                      <li>Done!</li>
                  </ul>
                  <p>Please keep in mind that your IPs must be the standard Excel format. No other report types will be accepted by the system. The file name should end with the extension .xls or .xlsx.</p>
                  <p>Concerns? Contact information listed below.</p>
                  <p><strong>Marcus:</strong> mbouligny@progroup.us - <b class="text_blue">415-516-1332</b></p>
                  <p><strong>Krystal:</strong> krystal@progroup.us - <b class="text_blue">678-557-87711</b></p>
              </div>
			  <?php
					if(isset($note)) {
                    echo $note; 
                    } 
                        ?>
              <div class="col-md-6">
                <div class="row form">
                   <div class="col-md-12">
                      <h2 class="page-title">ECCO Help and Report Upload</h2>
                   </div>
                   <div class="col-md-12">
                   <div class="form-group">
                      <div class="row">
                        <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                          <label>Date</label>
                          <div class = "input-group">
							  <?php  $today = date("m/d/Y"); ?>
                             <input type = "text" class = "form-control" id="start-date1" readonly name="date_time" value="<?php echo $today; ?>" placeholder="Pick a date">
                             <span class = "input-group-addon no-border"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                          </div>
                        </span>
                        <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                          <label>Time [EST]</label>
                          <div class = "input-group">
							 <?php 
							 $amNY = new DateTime('America/New_York');
                             $estTime = $amNY->format('h:i:A');                              
							 ?>
                             <input type = "text" class = "form-control" placeholder="Pick a time" readonly id="timepicker1" name="enq_time" value="<?php echo $estTime; ?>">
                             <span class = "input-group-addon no-border"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                          </div>
                        </span>
                      </div>
                   </div>
				   
                   <div class="form-group">
					           <div class="row mar0">
                        <span class="col-md-12 col-sm-12 col-xs-12 form-group help_box">
                            <label class="ele_bl">Please Select Option</label>
                            <span class="col-md-6 col-sm-12 col-xs-12 mar_t10 pad0 form-group">
                            <label class="mar0 checkbox_normal" onclick="show_form(1)">
                              <input type="radio" id="radio" value="option1" name="form_type" <?php if($_REQUEST['form']=='tta'){echo 'checked=""';} ?>>
                              <span class="custom-icon radio-icon">Request HELP</span>
                            </label>
                            </span>
                            <span class="col-md-6 col-sm-12 col-xs-12 mar_t10 pad0 form-group">
                            <label class="mar_b0 mar_l10 checkbox_normal" onclick="show_form(2)">
                              <input type="radio" id="radio" value="option1" name="form_type" <?php if($_REQUEST['form']=='report'){echo 'checked=""';} ?>>
                              <span class="custom-icon radio-icon">Upload Report</span>
                            </label>
                            </span>
                        </span>
						        </div>
						      </div>
						<?php if(isset($_REQUEST['form']) && ($_REQUEST['form']=='tta')){ ?>
						<div class="form-group"  id="tab1default">
						<form id="form-personal"  role="form" autocomplete="on" method="post" action="insert_help.php">
						<div class="form-group">
							<div class="row clearfix">
						<?php 
						$sql="SELECT distinct(name),id FROM agency";
						$result_mail = mysql_query($sql) or die(mysql_error());
						$num_rows = mysql_num_rows($result_mail);
						?>
                        <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                          <label>Agency </label>
                          <select class="form-control" id="agency" name="agency">
                            <option value="">Select Your Agency</option>
						  <?php 
							   while($row=mysql_fetch_array($result_mail)) { ?>
							   <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
						  <?php }   ?>
						  </select>
                        </span>
                      </div>
                    </div>
                     <div class="form-group">
                        <div class="row">
                            <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                              <label>First Name</label>
                              <input type="text" class="form-control" id="fname" name="fname" required="required">
                            </span>
                            <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                              <label>Last Name</label>
                              <input type="text" class="form-control" id="lname" name="lname" required="">
                            </span>
                        </div>
                     </div>
                     <div class="form-group">
                        <div class="row">
                          <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                            <label>Your Position</label>
                            <input type="text" class="form-control" id="position" name="position" placeholder="Ex. Project Coordinator" required="">
                          </span>
                          <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" onchange="return check();" id="email" name="email" placeholder="Ex. mail@domain.com" required="">
                          </span>
                        </div>
                     </div>
                      <div class="form-group">
                        <div class="row">
                            <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                              <label>Contact Number</label>
                              <input type="text" class="form-control" id="cnt_no" name="cnt_no" placeholder="Ex. (324) 234-3243" required="">
                            </span>
                            <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                              <label>Regarding</label>
                              <select name="regarding" id="regarding" class="form-control">
								<option value="Select">Select</option>
                                <option value="Implementation">Implementation</option>
                                <option value="Capacity">Capacity</option>
                                <option value="Evaluation">Evaluation</option>
								<option value="Technology">Technology</option>
								<option value="Other">Other</option>
							  </select>
                            </span>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <div class="row">
                            <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Resources <small>(Select resources related to your request)</small></label>
                              <select name="document[]" id="resources" class="form-control resources_select" multiple="">
							  <option value="">Select Your Resource</option>
							  <?php
                              
                                $query_resources=mysql_query("SELECT document_name,id FROM documents");

                                while($row1=mysql_fetch_array($query_resources)) {
                                $document_link=$row1['document_name'];
                                $document_arr=explode('/',$document_link);
                                $count_no=count($document_arr)-1;
                                $document_det=explode('.',$document_arr[$count_no]);
                                $return1=$document_det[0].' ('.$document_det[1].')';
                                $return=str_replace('-',' ',$return1);

                                ?>
                                <option value="<?php echo $row1['id'];?>" ><?php echo ucfirst($return);?></option>
                                <?php
                                }
                                ?>
							  </select>
                            </span>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="row">
                            <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>What is the nature of your query?</label>
                              <textarea class="form-control" name="query" id="query" placeholder="Ex.Having trouble recruiting for our individual strategy"></textarea>
                            </span>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="row">
                            <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Background Information</label>
                              <textarea class="form-control" name="regarding_notes" id="regarding_notes" placeholder="Ex.School backed out and we have exhausted all of our existing recruitment options"></textarea>
                            </span>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="row">
                            <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                             <label>Upload Documents For Review</label>
                             <div id="fine-uploader-manual-trigger"></div>
                                <script type="text/template" id="qq-template-manual-trigger">
                                    <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
                                        <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                                            <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
                                        </div>
                                        <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                                            <span class="qq-upload-drop-area-text-selector"></span>
                                        </div>
                                        <div class="buttons">
                                            <div class="qq-upload-button-selector qq-upload-button">
                                                <div>Select files</div>
                                            </div>
                                        </div>
                                        <span class="qq-drop-processing-selector qq-drop-processing">
                                            <span>Processing dropped files...</span>
                                            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
                                        </span>
                                        <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                                            <li>
                                                <div class="qq-progress-bar-container-selector">
                                                    <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                                                </div>
                                                <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                                                <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                                                <span class="qq-upload-file-selector qq-upload-file"></span>
                                                <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                                                <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                                                <span class="qq-upload-size-selector qq-upload-size"></span>
                                                <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
                                                <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
                                                <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
                                                <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                                            </li>
                                        </ul>
        
                                        <dialog class="qq-alert-dialog-selector">
                                            <div class="qq-dialog-message-selector"></div>
                                            <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">Close</button>
                                            </div>
                                        </dialog>
        
                                        <dialog class="qq-confirm-dialog-selector">
                                            <div class="qq-dialog-message-selector"></div>
                                            <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">No</button>
                                                <button type="button" class="qq-ok-button-selector">Yes</button>
                                            </div>
                                        </dialog>
        
                                        <dialog class="qq-prompt-dialog-selector">
                                            <div class="qq-dialog-message-selector"></div>
                                            <input type="text">
                                            <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">Cancel</button>
                                                <button type="button" class="qq-ok-button-selector">Ok</button>
                                            </div>
                                        </dialog>
                                    </div>
                                </script>
                            </span> 
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="row">
                            <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Please Solve captcha & click Verify to Continue</label>
                            </span>
                            <span class="col-md-6 col-sm-6 col-xs-6 form-group">
                              <?php echo Securimage::getCaptchaHtml(); ?> 
                              <span class="captcha_verify_image">
                              <i id="check_img" style="color: green; font-size: 20px; display: none;" class="fa fa-check" aria-hidden="true"></i>
                              <i id="times_img" style="color: red; font-size: 20px;display: none;" class="fa fa-times" aria-hidden="true"></i>
                              </span>
                            </span> 
                            <span class="col-md-3 col-sm-3 col-xs-3 form-group">
                              <button type="button" onclick="verify_code();">Verify</button>
                            </span>                               
                        </div>
                      </div>
                     <div class="col-xs-12 col-sm-12 text-center mar_b20">
                        <button type="button" id="form_submit_btn" onclick="alert('Please Solve above capcha & click verify');">Submit</button>
                        <button type="reset" class="mar_l10 cancel_btn">Reset</button>
                     </div>
					 </form>
					 </div>
					 <?php }?>
					 <?php if(isset($_REQUEST['form']) && ($_REQUEST['form']=='report')){ ?>
					<div class="form-group" id="tab2default"> 
					<form id="form-personal"  role="form" autocomplete="on" method="post" action="" onsubmit="success_msg()">
                    <div class="form-group">
					  <div class="row">
						<?php 
						$sql="SELECT distinct(name),id FROM agency";
						$result_mail = mysql_query($sql) or die(mysql_error());
						$num_rows = mysql_num_rows($result_mail);
						?>
                        <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                          <label>Agency <small><a href="<?php echo $site_url; ?>/reportdashboard.php" target="_blank" class="text_blue fb_500 text-right">See admin's comment</a></small></label>
                          <select id="report_agency" name="report_agency" class="form-control">
						      <option value="">Select Your Agency</option>
                          <?php 
							   while($row=mysql_fetch_array($result_mail)) { ?>
							   <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
						  <?php }   ?>
						  </select>
                        </span>
                      </div>
                    </div>
					<div class="form-group">
                        <div class="row">
                            <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                              <label>First Name</label>
                              <input type="text" class="form-control" id="report_fname" name="report_fname" required="required">
                            </span>
                            <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                              <label>Last Name</label>
                              <input type="text" class="form-control" id="report_lname" name="report_lname" required>
                            </span>
                        </div>
                     </div>
                     <div class="form-group">
                        <div class="row">
                          <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                            <label>Your Position</label>
                            <input type="text" class="form-control" id="report_position" name="report_position" placeholder="Ex. Project Coordinator" required>
                          </span>
                          <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" onchange="return check();" id="report_email" name="report_email" placeholder="Ex. mail@domain.com" required>
                          </span>
                        </div>
                     </div>
                      <div class="form-group">
                        <div class="row">
                            <span class="col-md-6 col-sm-12 col-xs-12 form-group">
                              <label>Contact Number</label>
                              <input type="text" class="form-control" id="report_cnt_no" name="report_cnt_no" placeholder="Ex. (324) 234-3243" required>
                            </span>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="row">
                            <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Report Notes</label>
                              <textarea class="form-control" name="report_notes" id="report_notes" placeholder="Comments on the report notes" aria-invalid="false"></textarea>
                            </span>
                        </div>
                      </div>
					   <div class="form-group">
                        <div class="row">
                            <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                             <label>Upload Your Report Here</label>
                              <div id="fine-uploader-manual-trigger-2"></div>
                                <script type="text/template" id="qq-template-manual-trigger-2">
                                    <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop report here">
                                        <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                                            <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
                                        </div>
                                        <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                                            <span class="qq-upload-drop-area-text-selector"></span>
                                        </div>
                                        <div class="buttons">
                                            <div class="qq-upload-button-selector qq-upload-button">
                                                <div>Select Report</div>
                                            </div>
                                        </div>
                                        <span class="qq-drop-processing-selector qq-drop-processing">
                                            <span>Processing dropped files...</span>
                                            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
                                        </span>
                                        <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                                            <li>
                                                <div class="qq-progress-bar-container-selector">
                                                    <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                                                </div>
                                                <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                                                <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                                                <span class="qq-upload-file-selector qq-upload-file"></span>
                                                <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                                                <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                                                <span class="qq-upload-size-selector qq-upload-size"></span>
                                                <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
                                                <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
                                                <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
                                                <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                                            </li>
                                        </ul>
    
                                        <dialog class="qq-alert-dialog-selector">
                                            <div class="qq-dialog-message-selector"></div>
                                            <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">Close</button>
                                            </div>
                                        </dialog>
    
                                        <dialog class="qq-confirm-dialog-selector">
                                            <div class="qq-dialog-message-selector"></div>
                                            <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">No</button>
                                                <button type="button" class="qq-ok-button-selector">Yes</button>
                                            </div>
                                        </dialog>
    
                                        <dialog class="qq-prompt-dialog-selector">
                                            <div class="qq-dialog-message-selector"></div>
                                            <input type="text">
                                            <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">Cancel</button>
                                                <button type="button" class="qq-ok-button-selector">Ok</button>
                                            </div>
                                        </dialog>
                                    </div>
                                </script>
                            </span> 
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="row">
                            <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Please Solve captcha & click Verify to Continue</label>
                            </span>
                            <span class="col-md-6 col-sm-6 col-xs-6 form-group">
                              <?php echo Securimage::getCaptchaHtml(); ?> 
                              <span class="captcha_verify_image">
                              <i id="check_img" style="color: green; font-size: 20px; display: none;" class="fa fa-check" aria-hidden="true"></i>
                              <i id="times_img" style="color: red; font-size: 20px;display: none;" class="fa fa-times" aria-hidden="true"></i>
                              </span>
                            </span> 
                            <span class="col-md-3 col-sm-3 col-xs-3 form-group">
                              <button type="button" onclick="verify_code();">Verify</button>
                            </span>                               
                        </div>
                      </div>
					  <div class="col-xs-12 col-sm-12 text-center mar_b20">
						<input type="hidden" name="tta_reports" value="1" />
                        <button type="button" id="form_submit_btn" onclick="alert('Please Solve above capcha & click verify');">Submit</button>
                        <button type="reset" class="mar_l10 cancel_btn">Reset</button>
                     </div>
					  </form>
					  </div>
                      <?php }?>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
     		</section>
<?php include_once('templates/footer.php'); ?> 
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-bez/jquery.bez.min.js"></script>
    <script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
    <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
    <script src="assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>    

    
    <!-- END VENDOR JS -->
    <!-- BEGIN CORE TEMPLATE JS -->
    <script type="text/javascript" src="pages/js/pages.min.js"></script>
    <!-- END CORE TEMPLATE JS -->
    <!-- BEGIN PAGE LEVEL JS -->
    <script type="text/javascript" src="assets/js/form_layouts.js" ></script>
    <script type="text/javascript" src="assets/js/scripts.js"></script>
    <!-- END PAGE LEVEL JS -->
    <link rel="stylesheet" href="pages/css/fine-uploader-new.min.css" type="text/css" />
    <script type="text/javascript" src="assets/js/all.fine-uploader.min.js"></script>
    <?php if(isset($_REQUEST['form']) && ($_REQUEST['form']=='tta')){ ?>
	<script>
    var manualUploader = new qq.FineUploader({
        element: document.getElementById('fine-uploader-manual-trigger'),
        template: 'qq-template-manual-trigger',
        request: {
            endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
        },
        deleteFile: {
            enabled: true,
            endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
        },
        chunking: {
            enabled: true,
            concurrent: {
                enabled: true
            },
            success: {
                endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php?done"
            }
        },
        resume: {
            enabled: true
        },
        retry: {
            enableAuto: true,
            showButton: true
        },
        autoUpload: true,
        debug: true
    });
    
    qq(document.getElementById("trigger-upload")).attach("click", function() {
        manualUploader.uploadStoredFiles();
    });
    </script> 
    <?php } if(isset($_REQUEST['form']) && ($_REQUEST['form']=='report')){ ?>   
    <script>
    var manualUploader = new qq.FineUploader({
        element: document.getElementById('fine-uploader-manual-trigger-2'),
        template: 'qq-template-manual-trigger-2',
        request: {
            endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
        },
        deleteFile: {
            enabled: true,
            endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
        },
        chunking: {
            enabled: true,
            concurrent: {
                enabled: true
            },
            success: {
                endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php?done"
            }
        },
        resume: {
            enabled: true
        },
        retry: {
            enableAuto: true,
            showButton: true
        },
        validation: {
            allowedExtensions: ['csv', 'xls', 'xlsx'],
            itemLimit: 1
        },
        autoUpload: true,
        debug: true
    });

    qq(document.getElementById("trigger-upload")).attach("click", function() {
        manualUploader.uploadStoredFiles();
    });
   </script>
   <?php } ?>
    <script>
    <?php if(isset($insert_report_id)){?>
        alert('Successfully Completed, Thank You!');
    <?php }if(isset($note)){ ?>
    alert('Please Attach a file and upload it again...!');
    <?php } ?>
    function success_msg() {
    
    }
    function success_msg_request()
    {
        var selectagency= $('select[name="agency"]').val();
        var selectfname= $('select[name="fname"]').val();
        var selectlname= $('select[name="lname"]').val();
        var selectemail= $('select[name="email"]').val();
        var selectphone= $('select[name="cnt_no"]').val();
        if(selectagency=='') {
            alert('Select Agency'); return false;
        }
        else if(selectfname=='' || selectlname=='')
        {
            alert('Enter First Name or Last Name'); return false;
        }
        else if(selectemail=='')
        {
            alert('Enter Email'); return false;
        }
        else
        {
            alert('Successfully Completed, Thank You!');
            return true;
        }
    }
</script>
    <script type="text/javascript">
    $(document).ready(function(){
           
        $('#accordion .panel-collapse').on('show.bs.collapse', function () {
            $(this).siblings('.panel-heading').addClass('active');
          });
        $('#accordion .panel-collapse').on('hide.bs.collapse', function () {
            $(this).siblings('.panel-heading').removeClass('active');
          });
    });
    </script>
    <script type="text/javascript">
       $(document).ready(function(){
         $(".assign_user_list ul li figure").click(function(){
            $(this).toggleClass("selected");
         });
       });
    </script>
    <script type="text/javascript">
    $(document).ready(function(){
      var maxHeight = 0;
        $(".assign_user_list ul").each(function(){
           if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
        });
        $(".assign_user_list ul").height(maxHeight);
      });
    </script>
    <script type="text/javascript">
      $('.datepicker').datepicker({
          format: 'mm/dd/yyyy',
          startDate: '-3d'
      });
    </script>
    <script type="text/javascript">
    window.onload = function()
    {
      // fix for windows 8
      if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
        document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
    }
	
	 $(document).ready(function(){
    $('input.timepicker').timepicker({ timeFormat: 'h:mm:ss p' });
});
    </script>
	
	
	<!-- email validation functionality -->
	
  <script type="text/javascript">
    function check() {
        var email_x = document.getElementById("email").value;
        filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (filter.test(email.value)) {
            document.getElementById("email").style.border = "1px solid green";
            document.getElementById("emailErr").innerHTML = "";
             $.ajax({url: "valid_email.php?id="+email_x,
              success: function(result){
                if(result==1) {
                }
                else {
                   
                }
              
             }});
            return true;
        } else {
            document.getElementById("email").style.border = "1px solid red";
            
            
            if(document.getElementById("email").value=='') {
               document.getElementById("emailErr").innerHTML =""; 
            } 
            else { 
            document.getElementById("emailErr").innerHTML = "Enter Valid Email Id";
            }
            window.setTimeout(function () {
                document.getElementById('email').focus();
            }, 0);
                        
            return false;
        }
    }
</script>
	
	 <script>
        $(function() {
            $('#timepicker').timepicker();
        });
        function show_form(tab){
            if(tab==1){
                window.location.href = 'help.php?form=tta';
                $('#tab2default').hide();
                $('#tab1default').show();
            }else{
                window.location.href = 'help.php?form=report';
                $('#tab1default').hide();
                $('#tab2default').show();
            }
        }
        $(document).ready(function(){
          $('.resources_select').select2();
        });
        function verify_code(){
            var captcha_code=$('#captcha_code').val();
            $.ajax({
            url: "check_captcha.php",
            type: "POST",
            data: '&captcha_code='+captcha_code,
            success: function(data) {
                    if(data==1){
                        $('.captcha_verify_image #check_img').show();
                        $('.captcha_verify_image #times_img').hide();
                        $('#form_submit_btn').prop('onclick','');
                        $('#form_submit_btn').prop('type','submit');
                        
                    }else{
                        $('.captcha_verify_image #times_img').show();
                        $('.captcha_verify_image #check_img').hide();
                    }
                }
            });
        }        
    </script>
	<!-- time picker -->
    
  </body>
</html>
