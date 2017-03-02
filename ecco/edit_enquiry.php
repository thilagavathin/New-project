<?php
include_once('config.php');
error_reporting(0);
ob_start(); session_start();
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

        $sql_old_data=mysql_query("select uploadfilename,uploadfoldername,filepath from help WHERE contract_num='".$_POST['contract_num']."'");
        $old_up_file=mysql_fetch_assoc($sql_old_data);
        $filepath_old=$old_up_file['filepath'];
        if(isset($filepath_old)) $filepath_temp=$filepath_old.'<br>'.$file;
        else $filepath_temp=$file;
        if($old_up_file['uploadfilename']<>'')
        {

            $filename= unserialize($old_up_file['uploadfilename']);
            if(is_array($filename))
            {
                $filename_array=serialize(array_merge($filename,$UploadFileName_temp));
                $upfoldername=unserialize($old_up_file['uploadfoldername']);
                $upfolder_array=serialize(array_merge($upfoldername,$UploadFolderName_temp));
            }
            else
            {
                $filename_array=serialize($UploadFileName_temp);
                $upfolder_array=serialize($UploadFolderName_temp);
            }
        }
        else
        {
            $filename_array=serialize($UploadFileName_temp);
            $upfolder_array=serialize($UploadFolderName_temp);
        }



            $insert_q="UPDATE help SET uploadfilename='".$filename_array."',uploadfoldername='".$upfolder_array."',filepath='".$filepath_temp."' WHERE contract_num='".$_POST['contract_num']."'";
            mysql_query($insert_q);
        //}
    }else{
        $UploadFolderName = "";
        $UploadFileName = "";
    }
}
else{
    $UploadFolderName = "";
    $UploadFileName = "";
}



 $assigned_staff = isset($_POST['assigned_staff'])? $_POST['assigned_staff']:'';
 date_default_timezone_set('America/New_York');
$updated_date = date('Y-m-d H:i:s');

$agency_id=isset($_POST['agency_id'])? $_POST['agency_id']:'';
$status=isset($_POST['status'])? $_POST['status']:'';
$TTA_inquiry_type=isset($_POST['TTA_inquiry_type'])? $_POST['TTA_inquiry_type']:'';
$TTA_inquiry_notes=isset($_POST['TTA_inquiry_notes'])? $_POST['TTA_inquiry_notes']:'';
$TTA_problem_addressed_notes=isset($_POST['TTA_problem_addressed_notes'])? $_POST['TTA_problem_addressed_notes']:'';
$TTA_desc=isset($_POST['TTA_desc'])? $_POST['TTA_desc']:'';
$TTA_desc_notes=isset($_POST['TTA_desc_notes'])? $_POST['TTA_desc_notes']:'';
$TTA_outcome=isset($_POST['TTA_outcome'])? $_POST['TTA_outcome']:'';
$TTA_outcome_notes=isset($_POST['TTA_outcome_notes'])? $_POST['TTA_outcome_notes']:'';
$TTA_Referral=isset($_POST['TTA_Referral'])? $_POST['TTA_Referral']:'';
$TTA_Contact_Phone=isset($_POST['TTA_Contact_Phone'])? $_POST['TTA_Contact_Phone']:'';
$TTA_Email=isset($_POST['TTA_Email'])? $_POST['TTA_Email']:'';
$timeframe=isset($_POST['timeframe'])? $_POST['timeframe']:'';
$timeframe_week=isset($_POST['timeframe_week'])? $_POST['timeframe_week']:'';
$timeframe_notes=isset($_POST['timeframe_notes'])? $_POST['timeframe_notes']:'';
$prelim_result=isset($_POST['prelim_result'])? $_POST['prelim_result']:'';
$TTA_service_provider=isset($_POST['TTA_service_provider'])? $_POST['TTA_service_provider']:'';
$regarding=isset($_POST['regarding'])? $_POST['regarding']:'';
$regarding_notes=isset($_POST['regarding_notes'])? $_POST['regarding_notes']:'';
$contract_num=isset($_POST['contract_num'])? $_POST['contract_num']:'';

$service_frame_start=isset($_POST['service_frame_start'])? date("Y-m-d",strtotime($_POST['service_frame_start'])):'';
$service_frame_end=isset($_POST['service_frame_end'])? date("Y-m-d",strtotime($_POST['service_frame_end'])):'';
$estimate_q1=isset($_POST['estimate_q1'])? $_POST['estimate_q1']:'';
$estimate_q2=isset($_POST['estimate_q2'])? $_POST['estimate_q2']:'';
$estimate_q3=isset($_POST['estimate_q3'])? $_POST['estimate_q3']:'';
$estimate_q4=isset($_POST['estimate_q4'])? $_POST['estimate_q4']:'';
$estimate_total=isset($_POST['estimate_total'])? $_POST['estimate_total']:'';
$training_date=isset($_POST['training_date'])? date("Y-m-d",strtotime($_POST['training_date'])):'';
$push_notification=isset($_POST['push_notification'])? $_POST['push_notification']:'';
$push_notify_email=isset($_POST['push_notify_email'])? $_POST['push_notify_email']:'';
$push_notify_comments=isset($_POST['push_notify_comments'])? $_POST['push_notify_comments']:'';
$resources=isset($_POST['resources'])? $_POST['resources']:'';

$mod_combination=isset($_POST['mod_combination'])? $_POST['mod_combination']:'';
$mod_other=isset($_POST['mod_other'])? $_POST['mod_other']:'';
$mod_faceface=isset($_POST['mod_faceface'])? $_POST['mod_faceface']:'';
$mod_correspondence=isset($_POST['mod_correspondence'])? $_POST['mod_correspondence']:'';
$mod_web=isset($_POST['mod_web'])? $_POST['mod_web']:'';
$other_email=isset($_POST['other_email'])? $_POST['other_email']:'';
$modality=$mod_web.'#'.$mod_correspondence.'#'.$mod_faceface.'#'.$mod_other.'#'.$mod_combination;
 
$Update_tta = "UPDATE TTA_Forms  SET
				agency_id = '".$agency_id."',
                updated_date = '".$updated_date."', 
                user_updated='".$_SESSION['adminlogin1']."',
				status='".$status."',
				TTA_inquiry_type='".$TTA_inquiry_type."',
				TTA_inquiry_notes='".mysql_real_escape_string($TTA_inquiry_notes)."',
				TTA_problem_addressed_notes='".mysql_real_escape_string($TTA_problem_addressed_notes)."',
				TTA_desc='".mysql_real_escape_string($TTA_desc)."',
				TTA_desc_notes='".mysql_real_escape_string($TTA_desc_notes)."',
				TTA_outcome='".mysql_real_escape_string($TTA_outcome)."',
				TTA_outcome_notes='".mysql_real_escape_string($TTA_outcome_notes)."',
				TTA_Referral='".mysql_real_escape_string($TTA_Referral)."',
				TTA_Contact_Phone='".mysql_real_escape_string($TTA_Contact_Phone)."',
				TTA_Email='".mysql_real_escape_string($TTA_Email)."',
				timeframe='".mysql_real_escape_string($timeframe)."',
				timeframe_w='".mysql_real_escape_string($timeframe_week)."',
				timeframe_notes='".mysql_real_escape_string($timeframe_notes)."',
				assigned_staff='".mysql_real_escape_string($assigned_staff)."',
				prelim_result='".mysql_real_escape_string($prelim_result)."',
				TTA_service_provider='".$TTA_service_provider."',
				regarding='".$regarding."',
				regarding_notes='".mysql_real_escape_string($regarding_notes)."',
				requestedUser='admin', 
                service_frame_start='".mysql_real_escape_string($service_frame_start)."',
                service_frame_end='".mysql_real_escape_string($service_frame_end)."',
                estimate_q1='".mysql_real_escape_string($estimate_q1)."',
                estimate_q2='".mysql_real_escape_string($estimate_q2)."',
                estimate_q3='".mysql_real_escape_string($estimate_q3)."',
                estimate_q4='".mysql_real_escape_string($estimate_q4)."',
                estimate_total='".mysql_real_escape_string($estimate_total)."',
                training_date='".mysql_real_escape_string($training_date)."',
                push_notification='".mysql_real_escape_string($push_notification)."',
                push_notify_email='".mysql_real_escape_string($push_notify_email)."',
                push_notify_comments='".mysql_real_escape_string($push_notify_comments)."',
                resources='".serialize($resources)."',
                modality='" . $modality . "',
				modality_other='" . $other_email . "'
				WHERE contract_num ='".$contract_num."'";

	$result = mysql_query($Update_tta);

if($result==1)
{
    $info_arr=array();
    $info_arr['from']='mbouligny@progroup.us';
    $info_arr['BCC']='';
    $info_arr['CC']='';
    $info_arr['subject']='TTA Request has been successfully updated';
    $info_arr['username']=$u_name;
    $info_arr['message_title']='';
    $message_uplaod='';

    if(isset($_SESSION['AttachmentUpload']))
    {
        if(is_array($_SESSION['AttachmentUpload'])){
            foreach($_SESSION['AttachmentUpload'] as $key => $value){
                $message_uplaod .=  '<tr><td style="font-size:14px;line-height:19px; text-decoration:none; font-family: '.$font_family.',sans-serif ;"><a style="color:#6d5cae;text-decoration:none;" href="'.$site_url.'/assets/uploader/php-traditional-server/files/' . $key . '/'.$value.'">' . $value . '</a></td></tr>';
            }
        }
    }
    //Get Agency Details
    $sql="SELECT name FROM agency where id =".$agency_id;
    $result_agency = mysql_fetch_row(mysql_query($sql));

        $agency_name = $result_agency[0];


    //
    $sql_rescource=mysql_query("SELECT resources FROM TTA_Forms WHERE contract_num='".$contract_num."'");
    $resources=mysql_fetch_row($sql_rescource);
    $resource_ids=implode(',',$resources[0]);
    if($resource_ids=='') $resource_ids=0;
    $sql=mysql_query("SELECT document_name FROM documents WHERE id in (".$resource_ids.")");
    $return='';
    if( mysql_num_rows($sql)>0)
    {
        $return='';
        while($row_resource=mysql_fetch_array($sql)) {
            $document_link=$row_resource['document_name'];
            $document_arr=explode('/',$document_link);
            $count_no=count($document_arr)-1;
            $document_det=explode('.',$document_arr[$count_no]);
            $return.='<tr><td style="font-family: '.$font_family.',sans-serif ; margin-bottom:5px;font-size:14px; line-height:19px; padding:10px 0;"> <a style="color:#6d5cae;text-decoration:none;" target="_blank" href="'.$site_url.$document_link.'">'.$document_det[0].' ('.$document_det[1].')</a></p></td></tr>';
        }
    }
    $font_family="'Helvetica','Arial'";
    $email_content='';
    $email_content.='
<tr>
    <td>
        <table style="font-family: '.$font_family.',sans-serif ;padding:20px 0px 0px 0px; text-align:left; " >
            <tr>
                <td style="font-size:20px; vertical-align:top; " > An ECCO has been submitted by <strong style="color:#ef8d08;">'.$agency_name.'</strong> </td>
            </tr>
            <tr>
                <td style="font-family: '.$font_family.',sans-serif ; margin-bottom:10px;font-size:14px; line-height:19px; padding:20px 0;"> The following documents were uploaded for review: </td>
            </tr>

        </table>
    </td>
</tr>';
    $upload_content='';
    if($message_uplaod<>''){
        $upload_content='<tr>
                <td>
<table style="background:#f2f2f2; border:1px solid #d9d9d9;padding:10px; ">
'.$message_uplaod.'
</table>
                </td>
            </tr>';
    }
    $email_content.='
<tr>
    <td>
        <table>'.$upload_content.'
            <tr>
                <td style="font-family: '.$font_family.',sans-serif ; font-size:14px;line-height:19px;"><p>The following resources where uploaded for review</p>
                </td>
            </tr>
 ';
    if($return<>'')
    {
        $email_content.='<tr><td><table style="background:#f2f2f2; border:1px solid #d9d9d9;padding:5px; ">'.$return.'</table></td></tr>';
    }
    $email_content.=' </table>
    </td>
</tr>
';
    $email_content.='
 <tr>
                    <td>
                        <table style="font-family: '.$font_family.',sans-serif ; font-size:14px;line-height:19px; border-collapse:collapse; " border="1" cellspacing="0" cellpadding="5">
                            <tbody>
                            <tr><th align="left" width="140px" bgcolor="ffffff" style="background-image:initial;background-repeat:initial">Agency</th>
                                <td style="padding:5px;">'.$agency_name.'</td>
                            </tr>

                            <tr><th align="left">Email</th><td style="padding:5px;"><a style="color:#6d5cae;text-decoration:none;" href="https://e-aj.my.com/compose?To='.$TTA_Email.'" target="_blank">'.$TTA_Email.'</a></td></tr>

                            <tr><th align="left">Contact Number</th><td style="padding:5px;"><a style="color:#6d5cae;text-decoration:none;" href="tel:'.$TTA_Contact_Phone.'" value="+'.$TTA_Contact_Phone.'" target="_blank">'.$TTA_Contact_Phone.'</a></td></tr>

                            <tr><th align="left">Nature of Query</th><td style="padding:5px;">'.$TTA_inquiry_notes.'</td></tr>

                            <tr><th align="left">Regarding</th><td style="padding:5px;">'.$regarding.'</td></tr>

                            <tr><th align="left">Notification </th><td style="padding:5px;">'.$push_notify_comments.'</td></tr>

                            </tbody>
                        </table>
                    </td>
                </tr>
';
    $info_arr['message_body']=$email_content;
    $splt_push_sql=explode(',',$push_notification);
    $curr_agency = mysql_query("SELECT distinct user_id FROM agency_map where agency_id ='".$agency_id."'");

    $mapped_user = 0;
    if(mysql_num_rows($curr_agency) > 0){
        $mapped_user .= ",";
        while($row = mysql_fetch_array($curr_agency)){
            $mapped_user .= $row["user_id"];
            $mapped_user .= ",";
        }
    }
    $mapped_user = rtrim($mapped_user,",");
    $sent_email = "0";

    $map_users = mysql_query("SELECT email FROM login_users WHERE user_id IN (".$mapped_user.")");
    if(mysql_num_rows($map_users) > 0){
        while ($row_cc = mysql_fetch_array($map_users)) {
            if(!empty($row_cc['email'])){
                $info_arr['from'] = 'mbouligny@progroup.us';
                $info_arr['to']=$row_cc['email'];
                @send_mail_template($info_arr);
            }
        }
    }

    if(($splt_push_sql)>0)
    {
        foreach($splt_push_sql as $pushto)
        {
            if($pushto=='User')
            {
                $sql_email=mysql_query("SELECT email,name FROM TTA_Forms T inner join login_users L on assignedUser=username WHERE contract_num='".$contract_num."'");
                if(mysql_num_rows($sql_email) > 0){
                    $TTA_Email=mysql_fetch_row($sql_email);
                    $info_arr['to']=$TTA_Email[0];
                    $info_arr['username']=$TTA_Email[1];
                    @send_mail_template($info_arr);
                }
            }
            elseif($pushto=='Submiter')
            {
                $sql_email=mysql_query("SELECT email,name FROM TTA_Forms T inner join login_users L on user_updated=username WHERE contract_num='".$contract_num."'");
                if(mysql_num_rows($sql_email) > 0){
                    $TTA_Email=mysql_fetch_row($sql_email);
                    $info_arr['to']=$TTA_Email[0];
                    $info_arr['username']=$TTA_Email[1];
                    @send_mail_template($info_arr);
                }
            }
            elseif($pushto=='Admin')
            {
                $sql_email=mysql_query("SELECT email FROM login_users WHERE username='admin'");
                if(mysql_num_rows($sql_email) > 0){
                    $TTA_Email=mysql_fetch_row($sql_email);
                    $info_arr['to']=$TTA_Email[0];
                    $info_arr['username']='Admin';
                    @send_mail_template($info_arr);
                }
            }
            elseif($pushto=='Others')
            {
                $info_arr['to']=$push_notify_email;
                @send_mail_template($info_arr);
            }
        }
    }

    echo 'success';
}
else echo 'failure';
?>
