<?php include_once('config.php');
session_start();
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
            $test = ["0"=>$fName];
            $UploadFileName = serialize($test);
        }  


       $SQL = "INSERT INTO TTA_Reports_uploads
        (agency, fname, lname, position, emailid, contact_no, report_note, uploadfoldername, uploadfilename,uploaduser)
        VALUES(
            '" . $_POST['report_agency'] . "',
            '" . $_POST['report_fname'] . "',
            '" . $_POST['report_lname'] . "',
            '" . $_POST['report_position'] . "',
            '" . $_POST['report_email'] . "',
            '" . $_POST['report_cnt_no'] . "',
            '" . $_POST['report_notes'] . "',
            '" . $UploadFolderName . "',
            '" . $UploadFileName . "','Help'
         )";
        $result = mysql_query($SQL);
        $insert_report_id=mysql_insert_id();


       set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
        include 'Classes/PHPExcel/IOFactory.php';
        $inputFileName = "http://".$_SERVER['SERVER_NAME']."/assets/uploader/php-traditional-server/files/".$UploadFolderName_temp."/".$UploadFileName_temp;
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
            $inputFileName = $_SERVER['SERVER_NAME']."/assets/uploader/php-traditional-server/files/".$UploadFolderName_temp."/".$UploadFileName_temp;
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

    $img_path = "http://www." . $_SERVER["SERVER_NAME"] . "/assets/images/logo-gasps.png";
    $progroup_img_path = "http://www." . $_SERVER["SERVER_NAME"] . "/assets/images/Powered_by_ProGroup.png";
    $message = '<html><body>';
    $message .= '<table width="100%" border="0"  cellpadding="10">';
    $message .= "<tr><td colspan=2 style='border: 1px solid #98002e; background-color: #ffffff; border-radius: 3px'><a href='http://ga-sps.org'><img src='".$img_path."' style='width:250px;' alt='Georgia Strategic Prevention System'/></a></td></tr>";
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
    $message .= "<tr><th align='left' style='text-align: left'>Regarding Notes</th><td>" . $regarding_notes . "</td><tr>";

    $message .= "</table></td></tr>";
    $message .= "<tr><td colspan=2 style='background:#000000'><img width='200px' height='56px' alt='Powered by the Prospectus Group' src='" . $progroup_img_path. "' style='width:200px;height::56px;background: #000000;'/></td></tr>";
    $message .= "</table>";
    $message .= "</body></html>";


    set_include_path(get_include_path() . PATH_SEPARATOR . 'ecco/');
    require 'mail/class.phpmailer.php';
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
     while($row_cc = mysql_fetch_array($cc_sql)) {
         if(!empty($row_cc['email'])){
             $mail->AddAddress($row_cc['email']);
             $mail->From       = $row_cc['email'];
             $mail->FromName   = $row_cc['name'];
             $mail->Send();
         }
         $mail->ClearAllRecipients();
     }
     $mail->AddAddress('vanitha.m@vividinfotech.com');
     $mail->AddReplyTo('aslamssn@gmail.com', 'Mambo');
     $mail->From       = 'aslamssn@gmail.com';
     $mail->FromName   = 'Mambo';
     $mail->Send();
}
$_SESSION['AttachmentUploada'] = '';
$_SESSION['AttachmentUploadb'] = '';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Georgia Strategic Prevention System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
    <link href="assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" media="screen">
    <link href="pages/css/pages-icons.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/simple-line-icons/simple-line-icons.css" rel="stylesheet" type="text/css" media="screen" />
    <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css" />
    <link class="" href="pages/css/themes/simples.css" rel="stylesheet" type="text/css" />
    
    <link rel="stylesheet" href="pages/css/fine-uploader-new.min.css" type="text/css" />
	
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
	
	
	
	<!-- time picker -->
	<script type="text/javascript" src="time/jquery.timepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="time/jquery.timepicker.css" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	 <script>
                $(function() {
                    $('#timepicker').timepicker();
                });
            </script>
	<!-- time picker -->
	
	
	
	
	
  </head>
  <body class="fixed-header   ">
    <!-- BEGIN SIDEBPANEL-->
    <nav class="page-sidebar" data-pages="sidebar">
      

      <!-- START SIDEBAR MENU -->
      <div class="sidebar-menu">
        <!-- BEGIN SIDEBAR MENU ITEMS-->
        <ul class="menu-items">
          <li class="m-t-15">
            <a href="chk.php">
              <span class="title">Dashboard</span>
            </a>
            <span class="icon-thumbnail"><i class="pg-home"></i></span>
          </li>
          <li class="">
            <a href="javascript:;"><span class="title">Menu Levels</span>
            <span class="arrow"></span></a>
            <span class="icon-thumbnail"><i class="pg-menu_lv"></i></span>
            <ul class="sub-menu">
              <li>
                <a href="javascript:;">Level 1</a>
              </li>
              <li>
                <a href="javascript:;"><span class="title">Level 2</span>
                <span class="arrow"></span></a>
                <ul class="sub-menu">
                  <li>
                    <a href="javascript:;">Sub Menu</a>

                  </li>
                  <li>
                    <a href="ujavascript:;">Sub Menu</a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
        <div class="clearfix"></div>
          

      </div>
      <!-- END SIDEBAR MENU -->
    </nav>
    <!-- END SIDEBAR -->
    <!-- END SIDEBPANEL-->
    <!-- START PAGE-CONTAINER -->
    <div class="page-container">
      <!-- START HEADER -->
      <div class="header ">
        <!-- START MOBILE CONTROLS -->
        <!-- LEFT SIDE -->
        <div class="pull-left full-height visible-sm visible-xs">
          <!-- START ACTION BAR -->
          <div class="sm-action-bar">
            <a href="#" class="btn-link toggle-sidebar" data-toggle="sidebar">
              <span class="icon-set menu-hambuger"></span>
            </a>
          </div>
          <!-- END ACTION BAR -->
        </div>
        <!-- RIGHT SIDE -->
        <div class="pull-right full-height visible-sm visible-xs">
          <!-- START ACTION BAR -->
          <div class="sm-action-bar">
            <a href="#" class="btn-link" data-toggle="quickview" data-toggle-element="#quickview">
              <i class="fs-14 sl-user"></i>
            </a>
          </div>
          <!-- END ACTION BAR -->
        </div>
        <!-- END MOBILE CONTROLS -->
        <div class=" pull-left sm-table">
          <div class="header-inner">
            <div class="brand inline">
			<img src="assets/img/gaspa-logo.png" alt="logo"  title="Georgia Strategic Prevention System"  width="163" height="34">
			</div>


 
              
              <div class="search-link b-grey b-l "><i class="sl-magnifier p-l-20"></i><input type="text" id="search-table" class=" no-border bg-transparent" placeholder="Type here to Search"></div>
              
            </div>
        </div>

        <div class="pull-right" style="display:none;">
          <!-- START User Info-->
          <div class="visible-lg visible-md m-t-10 m-l-20">
            <div class="pull-left p-r-10 p-t-10 fs-16 font-heading">
              <span class="semi-bold">Clarence</span></div>
            <div class="dropdown pull-right">
              <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="thumbnail-wrapper d32 circular inline m-t-5">
                <img src="assets/img/photo.jpg" alt="" data-src="assets/img/photo.jpg" data-src-retina="assets/img/profiles/avatar_small2x.jpg" width="32" height="32">
            </span>
              </button>
              <ul class="dropdown-menu profile-dropdown" role="menu">
                <li><a href="#"><i class="sl-settings"></i> Settings</a>
                </li>
                </li>
                <li><a href="#"><i class="sl-question"></i> Help</a>
                </li>
                <li class="bg-master-lighter">
                  <a href="logout.php" class="clearfix">
                    <span class="pull-left">Logout</span>
                    <span class="pull-right"><i class="sl-logout"></i></span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <!-- END User Info-->
        </div>
        <div class="pull-right" style="display:none;">
            <div class="header-inner">
                <div class="b-grey b-r p-l-30 p-r-20 m-r-15">
                    <a href="javascript:;" id="notification-center" class="sl-globe">
                    <span class="bubble">3</span>
                  </a>
                </div>
            </div>
        </div>
      </div>
      <!-- END HEADER -->
      <!-- START PAGE CONTENT WRAPPER -->
      <div class="page-content-wrapper">
        <!-- START PAGE CONTENT -->
        <div class="content">
          <!-- START CONTAINER FLUID -->
          <div class="container-fluid container-fixed-lg">
            <!-- START BREADCRUMB -->
            <ul class="breadcrumb">
              <li>
                <a href="#">Dashboard</a>
              </li>
              <li><a href="#" class="active">Help</a>
              </li>
            </ul>
            <!-- END BREADCRUMB -->
            
            <div class="hr-line"></div>
              
              <!-- START PAGE CONTENT -->
              <div class="page-content-transparent">
                <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <h1 class="page-title semi-bold">Help</h1>
                    <p class="m-b-30 text-info">Need some help? GSU, UGA, and RPS are here to assist and help build capacity among our APP and Gen Rx providers. Its easy to get help. From here you can submit a real time request for help, view past slide sets, and even join the prevention conversation on our blog.</p>
                        <div style="color:red">
                        <?php
                            if(isset($note)) {
                              echo $note; 
                            } 
                        ?>
                        </div>
                    <div class="panel-title bold m-t-50 m-b-30">ECCO Intake and Report Upload</div>
               
                
                <!-- START HELP FORM -->
                <div class="help-form m-b-50">
                    
                     <div class="row clearfix">
                        <div class="col-sm-6">
                        <label>Date</label>
                      <div class="form-group form-group-default input-group">
                      <?php  $today = date("m/d/Y"); ?>
                      <input type="text" class="form-control f-c-adj" placeholder="Pick a date" style="font-weight:normal;color:#000;" id="start-date1" readonly name="date_time" value="<?php echo $today; ?>" >
                      <span class="input-group-addon"><i class="sl-calendar"></i></span>
                    </div>
                        </div>
                          
                        <div class="col-sm-6">
                            <label>Time [EST]</label>
                            <div class="form-group form-group-default input-group bootstrap-timepicker">
                             <?php 
							 $amNY = new DateTime('America/New_York');
                             $estTime = $amNY->format('h:i:A');                              
							 ?>
							  <input type="text" class="form-control f-c-adj" placeholder="Pick a time" style="font-weight:normal;color:#000;" readonly id="timepicker1" name="enq_time" value="<?php echo $estTime; ?>">
                              <span class="input-group-addon"><i class="sl-clock"></i></span>
                            </div>
                        </div>

                      </div>
                      
                      <div class="help_sec">
                        <div>
                          <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1default" data-toggle="tab">Request TTA</a></li>
                            <li><a href="#tab2default" data-toggle="tab">Upload Report</a></li>
                          </ul>
                        </div>
                  <form id="form-personal"  role="form" autocomplete="on" method="post" action="insert_help.php" onsubmit="success_msg_request()">      
                        <div class="panel-body border_needs">
                          <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1default">
                            <div class="row clearfix">
                       <!-- agency dropdown datas from database --> 
					   <?php 
						$sql="SELECT distinct(name),id FROM agency";
						$result_mail = mysql_query($sql) or die(mysql_error());
						$num_rows = mysql_num_rows($result_mail);

						
						?>		   
					   <!-- agency dropdown datas from database -->
						
						
						
						
						
						<div class="col-sm-12">
                          <label>Your Agency</label>
                          <div class="form-group form-group-default form-group-default-select2">
                            
                            
                               <select class="full-width" data-init-plugin="select2" id="agency" name="agency" required>
                               <option value="">Select Agency</option>
							  <?php 
							   while($row=mysql_fetch_array($result_mail)) { ?>
							   <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
							  <?php }   ?>
                               </select>
                            
                          </div>
                        </div>
                      </div>
                      
                        
                      <div class="row clearfix">
                        <div class="col-sm-6">
                          <label>First name</label>
                          <div class="form-group form-group-default">
                            <input type="text" class="form-control" id="fname" name="fname" required>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label>Last name</label>
                          <div class="form-group form-group-default">
                            <input type="text" class="form-control" id="lname" name="lname" required>
                          </div>
                        </div>
                      </div>
                        
                      <div class="row">
                        <div class="col-sm-12">
                          <label>Your Position</label>
                          <div class="form-group form-group-default">
                            <input type="text" class="form-control" id="position" name="position" placeholder="Type Here" required>
                          </div>
                        </div>
                      </div>
                                             
                      <div class="row clearfix">
                        <div class="col-sm-6">
                           <label>Email</label>
                          <div class="form-group form-group-default">
                            <input type="email" class="form-control" onchange="return check();" id="email" name="email" placeholder="e.g. mail@domain.com" required>
                             
						  </div>
                        </div>
                        <div class="col-sm-6">
                          <label>Contact Number</label>
                          <div class="form-group form-group-default">
                            <input type="text" class="form-control" id="cnt_no" name="cnt_no" placeholder="e.g (324) 234-3243" required>
                          </div>
                        </div>
                      </div>
                        
                        
                      <div class="row clearfix">
                        <div class="col-sm-12">
                          <label>What is the nature of your query?</label>
                          <div class="form-group form-group-default">
                            <textarea class="form-control" name="query" id="query" placeholder="Write your query here" aria-invalid="false"></textarea>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-sm-12">
                          <label>Resources <small class="hint-text">(Select resources related to your request)</small></label>
                          <div class="form-group form-group-default">
                             <select name="document[]" id="resources" class="full-width" data-init-plugin="select2" multiple>
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
                          </div>
                        </div>
                      </div>
                     
					  <div class="row clearfix">
                        <div class="col-sm-12">
                          <label>Regarding</label>
                          <div class="form-group form-group-default form-group-default-select2">
                            <select name="regarding" id="regarding" class="full-width" data-init-plugin="select2" required>
							<option value="Select">Select</option>
                                <option value="Implementation">Implementation</option>
                                <option value="Capacity">Capacity</option>
                                <option value="Evaluation">Evaluation</option>
								<option value="Technology">Technology</option>
								<option value="Other">Other</option>								
                            </select>
                            
                          </div>
                        </div>
                      </div>
					 
					 <div class="row clearfix">
                        <div class="col-sm-12">
                          <label>Regarding Notes</label>
                          <div class="form-group form-group-default">
                            <textarea class="form-control" name="query" id="query" placeholder="Write your Regarding Notes here" aria-invalid="false"></textarea>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row clearfix">
                        <div class="col-sm-12">
                                                            <label>Have a document for us to review?</label>
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
                                                        </div></div>
					 
					 
					 


                      <div class="clearfix m-b-30"></div>
                       <button class="btn btn-success" type="submit">Submit</button>
                       <button class="btn btn-link" type="reset">Reset</button> 
                      </form>
                            </div>




                            <div class="tab-pane fade" id="tab2default">
            <form id="form-personal"  role="form" autocomplete="on" method="post" action="" onsubmit="success_msg()">                  
                              <div class="row clearfix">
                       <!-- agency dropdown datas from database --> 
					   <?php 
						$sql="SELECT distinct(name),id FROM agency";
						$result_mail = mysql_query($sql) or die(mysql_error());
						$num_rows = mysql_num_rows($result_mail);

						
						?>		   
					   <!-- agency dropdown datas from database -->
						
						
						
						
						
						<div class="col-sm-12">
                          <label>Your Agency</label><label style="padding-left: 200px;"> <a target="_blank" href="#"> See admin's comment </a> </label>
                          <div class="form-group form-group-default form-group-default-select2">
                            
                            
                               <select class="full-width" data-init-plugin="select2" id="report_agency" name="report_agency">
                               <option value="">Select Agency</option>
							  <?php 
							   while($row=mysql_fetch_array($result_mail)) { ?>
							   <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
							  <?php }   ?>
                               </select>
                            
                          </div>
                        </div>
                      </div>
                      
                        
                      <div class="row clearfix">
                        <div class="col-sm-6">
                          <label>First name</label>
                          <div class="form-group form-group-default">
                            <input type="text" class="form-control" id="report_fname" name="report_fname" required>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label>Last name</label>
                          <div class="form-group form-group-default">
                            <input type="text" class="form-control" id="report_lname" name="report_lname" required>
                          </div>
                        </div>
                      </div>
                        
                      <div class="row">
                        <div class="col-sm-12">
                          <label>Your Position</label>
                          <div class="form-group form-group-default">
                            <input type="text" class="form-control" id="report_position" name="report_position" placeholder="Type Here" required>
                          </div>
                        </div>
                      </div>                      
                        
                      <div class="row clearfix">
                        <div class="col-sm-6">
                           <label>Email</label>
                          <div class="form-group form-group-default">
                            <input type="email" class="form-control" onchange="return check();" id="report_email" name="report_email" placeholder="e.g. mail@domain.com" required>
                             
						  </div>
                        </div>
                        <div class="col-sm-6">
                          <label>Contact Number</label>
                          <div class="form-group form-group-default">
                            <input type="text" class="form-control" id="report_cnt_no" name="report_cnt_no" placeholder="e.g (324) 234-3243" required>
                          </div>
                        </div>
                      </div>
                        
					 <div class="row clearfix">
                        <div class="col-sm-12">
                          <label>Report Notes</label>
                          <div class="form-group form-group-default">
                            <textarea class="form-control" name="report_notes" id="report_notes" placeholder="Write your Report Notes here" aria-invalid="false"></textarea>
                          </div>
                        </div>
                      </div>
                      
                       <div class="row clearfix">
                        <div class="col-sm-12">
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
                                            </div>
					                      </div>
					 
					 


                      <div class="clearfix m-b-30"></div>
                      <input type="hidden" name="tta_reports" value="1" />
                       <button class="btn btn-success" type="submit">Upload</button>
                       <button class="btn btn-link" type="reset">Reset</button> 
                            </div>
                          </div>
                        </div>
                      </div>
                    
                    
                      
                      
                      
                    </form>
                    
                </div>
                <!-- END HELP FORM -->
                    
                </div>
                
              </div>
              </div>
              <!-- END PAGE CONTENT -->
              
          </div>
          <!-- END CONTAINER FLUID -->
        
        </div>
        <!-- END PAGE CONTENT -->
        <!-- START COPYRIGHT -->
        <!-- START CONTAINER FLUID -->
        <div class="container-fluid container-fixed-lg footer">
          <div class="copyright sm-text-center">
            <p class="small no-margin pull-left sm-pull-reset">
              <span class="hint-text">Copyright © 2015 </span>
              <span class="font-montserrat">Georgia Strategic Prevention System</span>.
              <span class="hint-text">All rights reserved. </span>

            </p>
            <p class="small no-margin pull-right sm-pull-reset">
                <span class="sm-block"><a href="#" class="m-l-10 m-r-10">Terms of use</a> <span class="muted">&#8226;</span> <a href="#" class="m-l-10">Privacy Policy</a></span>
            </p>
            <div class="clearfix"></div>
          </div>
        </div>
        <!-- END COPYRIGHT -->
      </div>
      <!-- END PAGE CONTENT WRAPPER -->
    </div>
    <!-- END PAGE CONTAINER -->
  
    <!-- BEGIN VENDOR JS -->
    <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-bez/jquery.bez.min.js"></script>
    <script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
    <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script type="text/javascript" src="assets/plugins/bootstrap-select2/select2.min.js"></script>
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
    <script type="text/javascript" src="assets/js/all.fine-uploader.min.js"></script>
    <script>
    var manualUploader = new qq.FineUploader({
        element: document.getElementById('fine-uploader-manual-trigger'),
        template: 'qq-template-manual-trigger',
        request: {
            endpoint: "/assets/uploader/php-traditional-server/endpoint.php"
        },
        deleteFile: {
            enabled: true,
            endpoint: "/assets/uploader/php-traditional-server/endpoint.php"
        },
        chunking: {
            enabled: true,
            concurrent: {
                enabled: true
            },
            success: {
                endpoint: "/assets/uploader/php-traditional-server/endpoint.php?done"
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
    <script>
    var manualUploader = new qq.FineUploader({
        element: document.getElementById('fine-uploader-manual-trigger-2'),
        template: 'qq-template-manual-trigger-2',
        request: {
            endpoint: "/assets/uploader/php-traditional-server/endpoint.php"
        },
        deleteFile: {
            enabled: true,
            endpoint: "/assets/uploader/php-traditional-server/endpoint.php"
        },
        chunking: {
            enabled: true,
            concurrent: {
                enabled: true
            },
            success: {
                endpoint: "/assets/uploader/php-traditional-server/endpoint.php?done"
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
<script>
function success_msg() {
alert('Successfully Completed, Thank You!');
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
  </body>
</html>
