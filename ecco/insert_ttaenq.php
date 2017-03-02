<?php
error_reporting(0);
ob_start(); session_start(); include_once('config.php');
$file='';
if(isset($_SESSION['AttachmentUpload1']))
{

    if(is_array($_SESSION['AttachmentUpload1'])){
        $UploadFolderName_temp = array();
        $UploadFileName_temp = array();
        foreach($_SESSION['AttachmentUpload1'] as $key => $value){
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
}
else{
    $UploadFolderName = "";
    $UploadFileName = "";
}

@date_default_timezone_set('America/New_York');
$created_date = date('Y-m-d H:i:s');
$updated_date = date('Y-m-d H:i:s');

function GetImageExtension($imagetype)
{
    if(empty($imagetype)) return false;
    switch($imagetype)
    {
        case 'image/bmp': return '.bmp';
        case 'image/gif': return '.gif';
        case 'image/jpeg': return '.jpg';
        case 'image/png': return '.png';
        default: return false;
    }
}


if(isset($_FILES["uploadedimage"]["name"]))
{
    if (!empty($_FILES["uploadedimage"]["name"])) {

        $file_name=$_FILES["uploadedimage"]["name"];
        $temp_name=$_FILES["uploadedimage"]["tmp_name"];
        $imgtype=$_FILES["uploadedimage"]["type"];
        $ext= GetImageExtension($imgtype);
        $imagename=date("d-m-Y")."-".time().$ext;
        $target_path = "images/".$imagename;
        if(move_uploaded_file($temp_name, $target_path)) {
            $img_path = $target_path;
        }
    }
}
else
{
    $img_path='';
    $file_name='';
    $temp_name='';
    $imgtype='';
    $ext='';
    $imagename='';
    $target_path='';
}

//Get Agency Details
$sql="SELECT * FROM agency where id ='".$_POST['agency_id']."'";
$result_agency = mysql_query($sql) or die(mysql_error());
while($row_agency=mysql_fetch_array($result_agency))
{
    $agency_name = $row_agency['name'];
    $agency_address = $row_agency['street'].",".$row_agency['city'].",".$row_agency['state'].",".$row_agency['zip'];
    $Manage_name = $row_agency['manager_name'];
    $agency_cntno = $row_agency['phone'];
}

$contract_num=isset($_POST['contract_num'])? $_POST['contract_num']:'';
$TTA_inquiry_type=isset($_POST['TTA_inquiry_type'])? $_POST['TTA_inquiry_type']:'';
$TTA_inquiry_notes=isset($_POST['TTA_inquiry_notes'])? $_POST['TTA_inquiry_notes']:'';
$TTA_problem_addressed=isset($_POST['TTA_problem_addressed'])? $_POST['TTA_problem_addressed']:'';
$TTA_problem_addressed_notes=isset($_POST['TTA_problem_addressed_notes'])? $_POST['TTA_problem_addressed_notes']:'';
$TTA_outcome=isset($_POST['TTA_outcome'])? $_POST['TTA_outcome']:'';
$TTA_outcome_notes=isset($_POST['TTA_outcome_notes'])? $_POST['TTA_outcome_notes']:'';
$TTA_desc=isset($_POST['TTA_desc'])? $_POST['TTA_desc']:'';
$TTA_desc_notes=isset($_POST['TTA_desc_notes'])? $_POST['TTA_desc_notes']:'';
$timeframe=isset($_POST['timeframe'])? $_POST['timeframe']:'';
$timeframe_notes=isset($_POST['timeframe_notes'])? $_POST['timeframe_notes']:'';
$TTA_Referral=isset($_POST['TTA_Referral'])? $_POST['TTA_Referral']:'';
$TTA_Contact_Phone=isset($_POST['TTA_Contact_Phone'])? $_POST['TTA_Contact_Phone']:'';
$TTA_Email=isset($_POST['TTA_Email'])? $_POST['TTA_Email']:'';
$assigned_staff=isset($_POST['assigned_staff'])? $_POST['assigned_staff']:'';
$prelim_result=isset($_POST['prelim_result'])? $_POST['prelim_result']:'';
$TTA_service_scheduled=isset($_POST['TTA_service_scheduled'])? $_POST['TTA_service_scheduled']:'';
$supporting_docs=isset($_POST['supporting_docs'])? $_POST['supporting_docs']:'';
$regarding=isset($_POST['regarding'])? $_POST['regarding']:'';
$regarding_notes=isset($_POST['regarding_notes'])? $_POST['regarding_notes']:'';
$agency_id=isset($_POST['agency_id'])? $_POST['agency_id']:'';

$resources=(is_array($_POST['resources']))? $_POST['resources']:array();

$training_date=isset($_POST['training_date'])? $_POST['training_date']:'';
$est_tot=isset($_POST['est_tot'])? $_POST['est_tot']:'';
$est_q4=isset($_POST['est_q4'])? $_POST['est_q4']:'';
$est_q3=isset($_POST['est_q3'])? $_POST['est_q3']:'';
$est_q2=isset($_POST['est_q2'])? $_POST['est_q2']:'';
$est_q1=isset($_POST['est_q1'])? $_POST['est_q1']:'';
$frame_end=isset($_POST['frame_end'])? $_POST['frame_end']:'';
$frame_start=isset($_POST['frame_start'])? $_POST['frame_start']:'';
$mod_combination=isset($_POST['mod_combination'])? $_POST['mod_combination']:'';
$mod_other=isset($_POST['mod_other'])? $_POST['mod_other']:'';
$mod_faceface=isset($_POST['mod_faceface'])? $_POST['mod_faceface']:'';
$mod_correspondence=isset($_POST['mod_correspondence'])? $_POST['mod_correspondence']:'';
$mod_web=isset($_POST['mod_web'])? $_POST['mod_web']:'';
$other_email=isset($_POST['other_email'])? $_POST['other_email']:'';
$modality=$mod_web.'#'.$mod_correspondence.'#'.$mod_faceface.'#'.$mod_other.'#'.$mod_combination;


$TTA_service_scheduled=isset($_POST['TTA_service_scheduled'])? $_POST['TTA_service_scheduled']:'';

if(!empty($frame_start)){
    $frame_start =  date('Y-m-d', strtotime($frame_start));
}else{
    $frame_start = "0000-00-00";
}
if(!empty($frame_end)){
    $frame_end =  date('Y-m-d', strtotime($frame_end));
}else{
    $frame_end = "0000-00-00";
}
if(!empty($training_date)){
    $training_date =  date('Y-m-d', strtotime($training_date));
}else{
    $training_date = "0000-00-00";
}
if(!empty($TTA_service_scheduled)){
    $TTA_service_scheduled1 =  date('Y-m-d', strtotime($TTA_service_scheduled));
}else{
    $TTA_service_scheduled1 = "0000-00-00";
}


$insert_help ="INSERT INTO help (contract_num,uploadfoldername, uploadfilename,filepath) VALUES('".$contract_num."','".$UploadFolderName."','".$UploadFileName."','".$file."')";
$result_help = @mysql_query($insert_help);
if($result_help == 1) {
    $insert_tta = "INSERT INTO TTA_Forms (
				assignedUser,
				contract_num,
				TTA_inquiry_type,
				TTA_inquiry_notes,				
				SPF_steps_notes,							
				TTA_problem_addressed,
				TTA_problem_addressed_notes,
				TTA_outcome,
				TTA_outcome_notes,				
				TTA_desc,
				TTA_desc_notes,
				timeframe,
				timeframe_notes,
				TTA_Referral,
				TTA_Contact_Phone,
				TTA_Email,
				assigned_staff,
				prelim_result,
				TTA_service_scheduled,
				supporting_docs,
				regarding,
				regarding_notes,
				agency_id,
				created_date,
				updated_date,
				resources,
				AgencyName,
				ManagerName,
				AgencyContactNumber,
				AgencyAddress,
				modality,
				modality_other,
				service_frame_start,
				service_frame_end,
                estimate_q1,
                estimate_q2,
                estimate_q3,
                estimate_q4,
                estimate_total,
                training_date
               )
			   values(
			   '" . $_SESSION['adminlogin1'] . "',
			   '" . $contract_num . "',
			   '" . $TTA_inquiry_type . "',
			   '" . mysql_real_escape_string($TTA_inquiry_notes) . "',
			   '" . mysql_real_escape_string($TTA_inquiry_notes) . "',
			   '" . mysql_real_escape_string($TTA_problem_addressed) . "',
			   '" . mysql_real_escape_string($TTA_problem_addressed_notes) . "',
			   '" . mysql_real_escape_string($TTA_outcome) . "',
			   '" . mysql_real_escape_string($TTA_outcome_notes) . "',
			   '" . mysql_real_escape_string($TTA_desc) . "',
			   '" . mysql_real_escape_string($TTA_desc_notes) . "',
			   '" . mysql_real_escape_string($timeframe) . "',
			   '" . mysql_real_escape_string($timeframe_notes) . "',
			   '" . mysql_real_escape_string($TTA_Referral) . "',
			   '" . mysql_real_escape_string($TTA_Contact_Phone) . "',
			   '" . mysql_real_escape_string($TTA_Email) . "',
			   '" . mysql_real_escape_string($assigned_staff) . "',
			   '" . mysql_real_escape_string($prelim_result) . "',
			   '" . $TTA_service_scheduled1 . "',
			   '" . mysql_real_escape_string($supporting_docs) . "',
			   '" . mysql_real_escape_string($regarding) . "',
			   '" . mysql_real_escape_string($regarding_notes) . "',
			   '" . $agency_id . "',
			   '" . $created_date . "',
			   '" . $updated_date . "',
			   '" . serialize($resources) . "',
               '" . $agency_name . "',
               '" . $Manage_name . "',
               '" . $agency_cntno . "',
               '" . $agency_address . "',
               '" . $modality . "',
               '" . $other_email . "',
               '" . $frame_start . "',
               '" . $frame_end . "',
                '" . $est_q1 . "',
                '" . $est_q2 . "',
                '" . $est_q3 . "',
                '" . $est_q4 . "',
                '" . $est_tot . "',
                '" . $training_date . "'
			   )";
    $result = @mysql_query($insert_tta);
    $date_time = date('Y-m-d', strtotime($TTA_service_scheduled));

    if ($result == 1) {
        $u_name = $_SESSION['adminlogin1'];
        $date_of_time = date('d M Y', strtotime($date_time));
        $cnt_no = mysql_real_escape_string($TTA_Contact_Phone);
        $query = mysql_real_escape_string($TTA_inquiry_notes);
        $regarding = $regarding;
        $regarding_notes = mysql_real_escape_string($regarding_notes);
        $info_arr = array();
        $info_arr['to'] = $TTA_Email;
        $info_arr['from'] = 'mbouligny@progroup.us';
        $info_arr['BCC'] = '';
        $info_arr['CC'] = '';
        $info_arr['subject'] = 'Your TTA Request has been successfully submitted';
        $info_arr['username'] = $u_name;
        $info_arr['message_title'] = '';
        $message_uplaod = '';
        if (isset($_SESSION['AttachmentUpload1'])) {
            if (is_array($_SESSION['AttachmentUpload1'])) {
                foreach ($_SESSION['AttachmentUpload1'] as $key => $value) {
                    $message_uplaod .= '<tr><td style="font-size:14px;line-height:19px; text-decoration:none; font-family: ' . $font_family . ',sans-serif ;"><a style="color:#6d5cae;text-decoration:none;" href="'.$site_url.'/assets/uploader/php-traditional-server/files/' . $key . '/' . $value . '">' . $value . '</a></td></tr>';

                }
            }
        }
        $resource_ids = implode(',', $resources);
        if ($resource_ids == '') $resource_ids = 0;
        $sql = mysql_query("SELECT document_name FROM documents WHERE id in (" . $resource_ids . ")");
        $return = '';

        if (mysql_num_rows($sql) > 0) {
            $return = '';
            while ($row_resource = mysql_fetch_array($sql)) {
                $document_link = $row_resource['document_name'];
                $document_arr = explode('/', $document_link);
                $count_no = count($document_arr) - 1;
                $document_det = explode('.', $document_arr[$count_no]);
                $return .= '<tr><td style="font-family: ' . $font_family . ',sans-serif ; margin-bottom:5px;font-size:14px; line-height:19px; padding:10px 0;"> <a style="color:#6d5cae;text-decoration:none;" target="_blank" href="'.$site_url . $document_link . '">' . $document_det[0] . ' (' . $document_det[1] . ')</a></p></td></tr>';
            }
        }

        $font_family = "'Helvetica','Arial'";
        $email_content = '';
        $email_content .= '
<tr>
    <td>
        <table style="font-family: ' . $font_family . ',sans-serif ;padding:20px 0px 0px 0px; text-align:left; " >
            <tr>
                <td style="font-size:20px; vertical-align:top; " > An ECCO has been submitted by <strong style="color:#ef8d08;">' . $agency_name . '</strong> </td>
            </tr>
            <tr>
                <td style="font-family: ' . $font_family . ',sans-serif ; margin-bottom:10px;font-size:14px; line-height:19px; padding:20px 0;"> The following documents were uploaded for review: </td>
				
            </tr>';
			$upload_content = '';
        if ($message_uplaod <> '') {
            $upload_content = '<tr>
                <td>
<table style="background:#f2f2f2; border:1px solid #d9d9d9;padding:10px; ">
' . $message_uplaod . '
</table>
                </td>
            </tr>';
        }

       $email_content .= $upload_content . '</table>
    </td>
</tr>';
        
        $email_content .= '
<tr>
    <td>
        <table>
            <tr>
                <td style="font-family: ' . $font_family . ',sans-serif ; font-size:14px;line-height:19px;"><p>The following resources where uploaded for review</p>
                </td>
            </tr>
 ';
        if ($return <> '') {
            $email_content .= '<tr><td><table style="background:#f2f2f2; border:1px solid #d9d9d9;padding:5px; ">' . $return . '</table></td></tr>';
        }
        $email_content .= ' </table>
    </td>
</tr>
';
        $email_content .= '
 <tr>
                    <td>
                        <table style="font-family: ' . $font_family . ',sans-serif ; font-size:14px;line-height:19px; border-collapse:collapse; " border="1" cellspacing="0" cellpadding="5">
                            <tbody>
                            <tr><th align="left" width="140px" bgcolor="ffffff" style="background-image:initial;background-repeat:initial">Agency</th>
                                <td style="padding:5px;">' . $agency_name . '</td>
                            </tr>

                            <tr><th align="left" bgcolor="ffffff" style="background-image:initial;background-repeat:initial">Time Submitted</th><td style="padding:5px;">'. date("jS F Y").'</td></tr>

                            <tr><th align="left">Requester</th><td style="padding:5px;">' . $u_name . '</td></tr>
                            <tr><th align="left">Position</th><td style="padding:5px;"></td></tr>

                            <tr><th align="left">Email</th><td style="padding:5px;"><a style="color:#6d5cae;text-decoration:none;" href="https://e-aj.my.com/compose?To=' . $_POST['TTA_Email'] . '" target="_blank">' . $_POST['TTA_Email'] . '</a></td></tr>

                            <tr><th align="left">Contact Number</th><td style="padding:5px;"><a style="color:#6d5cae;text-decoration:none;" href="tel:' . $_POST['TTA_Contact_Phone'] . '" value="+' . $_POST['TTA_Contact_Phone'] . '" target="_blank">' . $_POST['TTA_Contact_Phone'] . '</a></td></tr>

                            <tr><th align="left">Nature of Query</th><td style="padding:5px;">' . $query . '</td></tr>

                            <tr><th align="left">Regarding</th><td style="padding:5px;">' . $regarding . '</td></tr>

                            <tr><th align="left">Regarding Notes</th><td style="padding:5px;">' . $regarding_notes . '</td></tr>

                            </tbody>
                        </table>
                    </td>
                </tr>
';
        $info_arr['message_body'] = $email_content;

        @send_mail_template($info_arr);
        $cc_sql = mysql_query("SELECT email FROM login_users WHERE user_level LIKE '%\"1\"%'");
        $admin_email = "";
        while ($row_cc = mysql_fetch_array($cc_sql)) {
            if(!empty($row_cc['email'])){
                $admin_email .= $row_cc['email'];
                $admin_email .= ",";
                $info_arr_admin = array();
                $info_arr_admin['to'] = $row_cc['email'];
                $info_arr_admin['from'] = 'mbouligny@progroup.us';
                $info_arr_admin['BCC'] = '';
                $info_arr_admin['CC'] = '';
                $info_arr_admin['subject'] = 'An ECCO has been submitted';
                $info_arr_admin['username'] = $u_name;
                $info_arr_admin['message_title'] = '';
                $info_arr_admin['message_body'] = $email_content;
                @send_mail_template($info_arr_admin);
            }
        }
        $admin_email = rtrim($admin_email,",");
        
        //Middle Admin Mail Notification
        $m_a =  array("4");
        $middle_admin_user_level = serialize($m_a);
        $middle_admin_sql = mysql_query("SELECT user_id, email FROM login_users WHERE user_level =".$middle_admin_user_level);
        $mapped_middle_admin_user_email = "";
        if(mysql_num_rows($middle_admin_sql) > 0){
            while($m = mysql_fetch_array($middle_admin_sql)){
                $mapped_middle_admin_user_email .= $m["email"];
                $mapped_middle_admin_user_email .= ",";
                $info_arr_admin = array();
                $info_arr_admin['to'] = $m['email'];
                $info_arr_admin['from'] = 'mbouligny@progroup.us';
                $info_arr_admin['BCC'] = '';
                $info_arr_admin['CC'] = '';
                $info_arr_admin['subject'] = 'An ECCO has been submitted';
                $info_arr_admin['username'] = $u_name;
                $info_arr_admin['message_title'] = '';
                $info_arr_admin['message_body'] = $email_content;
                @send_mail_template($info_arr_admin);
            }
        }
        $mapped_middle_admin_user_email = rtrim($mapped_middle_admin_user_email,",");       
      
        
        if(!empty($mapped_middle_admin_user_email)){
        $all_admin_email = $admin_email.",".$mapped_middle_admin_user_email;
        }else{
        $all_admin_email = $admin_email;
        }        

        $m_agency = @mysql_query("SELECT user_id FROM agency_map WHERE agency_id=".$agency_id);
        $mapped_user_id = "";
        if(mysql_num_rows($m_agency) > 0){
            while($r = mysql_fetch_array($m_agency)){
                $mapped_user_id .= $r["user_id"];
                $mapped_user_id .= ",";
            }
        }
        if(!empty($mapped_user_id)){
            if(!empty($all_admin_email)){
                $new_cc_sql = @mysql_query("SELECT email FROM login_users WHERE user_id IN (".$mapped_user_id.") AND email NOT IN (".$all_admin_email.")");
            }else{
                $new_cc_sql = @mysql_query("SELECT email FROM login_users WHERE user_id IN (".$mapped_user_id.")");
            }
            if(mysql_num_rows($new_cc_sql) > 0){
                while ($row_cc_new = mysql_fetch_array($new_cc_sql)) {
                    if(!empty($row_cc_new['email'])){
                        $info_arr_admin = array();
                        $info_arr_admin['to'] = $row_cc_new['email'];
                        $info_arr_admin['from'] = 'mbouligny@progroup.us';
                        $info_arr_admin['BCC'] = '';
                        $info_arr_admin['CC'] = '';
                        $info_arr_admin['subject'] = 'An ECCO has been submitted';
                        $info_arr_admin['username'] = $u_name;
                        $info_arr_admin['message_title'] = '';
                        $info_arr_admin['message_body'] = $email_content;
                        @send_mail_template($info_arr_admin);
                    }
                }
            }
        }


        $sql_agency_map=@mysql_query("SELECT id FROM agency_map WHERE user_id=".$_SESSION['adminlogin']." AND agency_id=".$agency_id);
        $map_agency=mysql_num_rows($sql_agency_map);
        if($map_agency==0) $insert_query=@mysql_query("INSERT INTO agency_map (user_id,agency_id) VALUES (".$_SESSION['adminlogin'].",".$agency_id.")");
        unset($_SESSION['AttachmentUpload']);
        if ($result){
            echo 'success';
        }else{
            echo 'failure';
        }
    }
    unset($_SESSION['AttachmentUpload1']);
}
else echo 'failure';
?>
