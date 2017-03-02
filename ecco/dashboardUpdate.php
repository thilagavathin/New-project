<?php

function folderName($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if(isset($_POST['ttaEditEnquiry'])){

    $push_notification = $_POST['push_notification'];
    $updated_date = date('Y-m-d H:i:s');
    $contract_num=isset($_POST['contract_num'])? $_POST['contract_num']:'';
    $ttdEditArray['agency_id'] = $_POST['agency_id'];
    $ttdEditArray['push_notification'] = implode(',',$push_notification);
    $ttdEditArray['updated_date'] = $updated_date;
    $ttdEditArray['TTA_Referral'] = $_POST['requester_name'];
    $ttdEditArray['TTA_Contact_Phone'] = $_POST['contact_number'];
    $ttdEditArray['service_frame_start'] = $_POST['service_frame_start'];
    $ttdEditArray['service_frame_end'] = $_POST['service_frame_end'];
    $ttdEditArray['training_date'] = $_POST['training_date'];
    $ttdEditArray['estimate_q1'] = $_POST['estimate_q1'];
    $ttdEditArray['estimate_q2'] = $_POST['estimate_q2'];
    $ttdEditArray['estimate_q3'] = $_POST['estimate_q3'];
    $ttdEditArray['estimate_q4'] = $_POST['estimate_q4'];
    $ttdEditArray['estimate_total'] = $_POST['estimate_total'];
    $ttdEditArray['prelim_result'] = $_POST['prelim_result'];
    $ttdEditArray['TTA_desc'] = $_POST['TTA_desc'];
    $ttdEditArray['push_notify_email'] = $_POST['push_notify_email'];
    $ttdEditArray['push_notify_comments'] = $_POST['push_notify_comments'];
    $ttdEditArray['status'] = $_POST['status'];
    $ttdEditArray['TTA_Email'] = $_POST['email'];
    $ttdEditArray['resources'] = $_POST['resources'];
    $resources = (is_array($_POST['resources']))? $_POST['resources']:array();
    $ttdEditArray['TTA_inquiry_type'] = $_POST['TTA_inquiry_type'];
    $ttdEditArray['TTA_inquiry_notes'] = $_POST['TTA_inquiry_notes'];
    $ttdEditArray['regarding'] = $_POST['regarding'];
    $ttdEditArrayKeys = array_keys($ttdEditArray);


    $modality['modality_combination'] = $_POST['modality_combination'];
    $modality['modality_other'] = $_POST['modality_other'];
    $modality['modality_faceface'] = $_POST['modality_faceface'];
    $modality['mod_correspondence'] = $_POST['modality_correspondence'];
    $modality['modality_web'] = $_POST['modality_web'];
    $modality['other_email'] =  $_POST['other_email'];
    $modalityArray=implode(', ', $modality);
    // Count # of uploaded files in array
	if(isset($_SESSION['AttachmentUpload1'])){
    if(count($_SESSION['AttachmentUpload1']) > 0){
    foreach($_SESSION['AttachmentUpload1'] as $key => $value){        
        $folderValue[] = $key;
        $fileKey[] = $value;
        $newFilePath.=$site_url.'/assets/uploader/php-traditional-server/files/'.$key.'/'.$value.'<br>';
    }
	
    $help_query="SELECT uploadfoldername,uploadfilename,filepath FROM help WHERE contract_num='".$contract_num."'";
    $help_upload = mysql_query($help_query);
    $upload_help = mysql_fetch_array($help_upload);
    $upload_help['uploadfoldername'];
    $arrayfoldername=unserialize($upload_help['uploadfoldername']);
    $arrayfilename=unserialize($upload_help['uploadfilename']);
    
    $newFilePath="";
    $fileName=array();
    $folderNameRandom=array();
    if(count($arrayfilename) > 0){
        $fileName = array_merge($arrayfilename,$fileKey);
        $folderNameRandom = array_merge($arrayfoldername,$folderValue);
        $newFilePath.=$upload_help['filepath'];
    }else{
        $fileName = $fileKey;
        $folderNameRandom = $folderValue;
        $newFilePath.=$newFilePath;
    }
    
    $uploadfilename = serialize($fileName);    
    $uploadfoldername = serialize($folderNameRandom);
    $updatehelptable = "update help set uploadfoldername='".$uploadfoldername."' ,uploadfilename='".$uploadfilename."',filepath='".$newFilePath."' WHERE contract_num='".$contract_num."'";
    
    //Handle other code here
    mysql_query($updatehelptable);
    }
}
    // Resources Field set value
    $get = "SELECT resources FROM TTA_Forms WHERE contract_num ='".$contract_num."'";
    $get_details = mysql_query($get);
    $old_documents = "";
    if(mysql_num_rows($get_details) > 0){
        while($row = mysql_fetch_array($get_details)){
            $old_documents .= $row["resources"];
        }
    }
    $old_documents_array = unserialize($old_documents);
    if(count($old_documents_array) > 0){
        $all_selected_documents = array_merge($resources, $old_documents_array);
    }else{
        $all_selected_documents = $resources;
	}


        $info_arr=array();
        $info_arr['from']='mbouligny@progroup.us';
        $info_arr['BCC']='';
        $info_arr['CC']='';
        $info_arr['subject']='TTA Request has been successfully updated';
        $info_arr['username']=$u_name;
        $info_arr['message_title']='';
        $message_uplaod='';
		$update_attach = mysql_query("SELECT uploadfoldername,uploadfilename FROM help WHERE contract_num='".$contract_num."'");
		
		while($row=mysql_fetch_array($update_attach)) {
			$attchment_folder = $row['uploadfoldername'];
			$attchment_folder = @unserialize($attchment_folder);			
			$attchment_file = $row['uploadfilename'];
			$attchment_file = @unserialize($attchment_file);
            $fileCount = count($attchment_file);
			for($i=0;$i<$fileCount;$i++){   if(!empty($attchment_file[$i])&&!empty($attchment_file[$i])){
			$message_uplaod .= '<tr><td style="font-size:14px;line-height:19px; text-decoration:none; font-family: ' . $font_family . ',sans-serif ;"><a style="color:#6d5cae;text-decoration:none;" href="'.$site_url.'/assets/uploader/php-traditional-server/files/'.$attchment_folder[$i].'/'.$attchment_file[$i].'">'.$attchment_file[$i].'</a></td></tr>';
			} }
		} 
		
		$push_notification = $ttdEditArray['push_notification'];
        $push_notify_email = $ttdEditArray['push_notify_email'];
        $splt_push_sql=explode(',',$push_notification);
        $agency_id = $ttdEditArray['agency_id'];
		//Get Agency Details
        $sql="SELECT name FROM agency where id =".$agency_id;
        $result_agency = mysql_fetch_row(mysql_query($sql));
        $agency_name = $result_agency[0];

		$resource_ids = implode(',', $resources);
        if ($resource_ids == '') $resource_ids = 0;
        $sql = mysql_query("SELECT document_name FROM documents WHERE id in (" . $resource_ids . ")");
        $return = '';
        if( mysql_num_rows($sql)>0)
        {
            $return='';
            while($row_resource=mysql_fetch_array($sql)) {
                $document_link=$row_resource['document_name'];
                $document_arr=explode('/',$document_link);
                $count_no=count($document_arr)-1;
                $document_det=explode('.',$document_arr[$count_no]);
                $return.='<tr><td style="font-family: '.$font_family.',sans-serif ; margin-bottom:5px;font-size:14px; line-height:19px; padding:10px 0;"> <a style="color:#6d5cae;text-decoration:none;" target="_blank" href="'.$site_url.'/'.$document_link.'">'.$document_det[0].' ('.$document_det[1].')</a></p></td></tr>';
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

                            <tr><th align="left">Email</th><td style="padding:5px;"><a style="color:#6d5cae;text-decoration:none;" href="https://e-aj.my.com/compose?To='.$ttdEditArray['TTA_Email'].'" target="_blank">'.$ttdEditArray['TTA_Email'].'</a></td></tr>

                            <tr><th align="left">Contact Number</th><td style="padding:5px;"><a style="color:#6d5cae;text-decoration:none;" href="tel:'.$ttdEditArray['TTA_Contact_Phone'].'" value="+'.$ttdEditArray['TTA_Contact_Phone'].'" target="_blank">'.$ttdEditArray['TTA_Contact_Phone'].'</a></td></tr>

                            <tr><th align="left">Nature of Query</th><td style="padding:5px;">'.$ttdEditArray['TTA_inquiry_notes'].'</td></tr>

                            <tr><th align="left">Regarding</th><td style="padding:5px;">'.$ttdEditArray['regarding'].'</td></tr>

                            <tr><th align="left">Notification </th><td style="padding:5px;">'.$ttdEditArray['push_notify_comments'].'</td></tr>

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
                }
            }
        }
    $Update_tta = "UPDATE TTA_Forms  SET
				agency_id = '".$ttdEditArray['agency_id']."',
                updated_date = '".$ttdEditArray['updated_date']."',
                user_updated='".$_SESSION['adminlogin1']."',
				status='".$ttdEditArray['status']."',
				TTA_inquiry_type='".$ttdEditArray['TTA_inquiry_type']."',
				TTA_inquiry_notes='".mysql_real_escape_string($ttdEditArray['TTA_inquiry_notes'])."',
				TTA_desc='".mysql_real_escape_string($ttdEditArray['TTA_desc'])."',
				TTA_Referral='".mysql_real_escape_string($ttdEditArray['TTA_Referral'])."',
				TTA_Contact_Phone='".mysql_real_escape_string($ttdEditArray['TTA_Contact_Phone'])."',
				TTA_Email='".mysql_real_escape_string($ttdEditArray['TTA_Email'])."',
				timeframe='".mysql_real_escape_string($ttdEditArray['timeframe'])."',
				timeframe_w='".mysql_real_escape_string($ttdEditArray['timeframe_w'])."',
				timeframe_notes='".mysql_real_escape_string($ttdEditArray['timeframe_notes'])."',
				assigned_staff='".mysql_real_escape_string($ttdEditArray['assigned_staff'])."',
				prelim_result='".mysql_real_escape_string($ttdEditArray['prelim_result'])."',
				TTA_service_provider='".$ttdEditArray['agency_id']."',
				regarding='".$ttdEditArray['regarding']."',
				regarding_notes='".mysql_real_escape_string($ttdEditArray['regarding_notes'])."',
				requestedUser='admin',
                service_frame_start='".mysql_real_escape_string($ttdEditArray['service_frame_start'])."',
                service_frame_end='".mysql_real_escape_string($ttdEditArray['service_frame_end'])."',
                estimate_q1='".mysql_real_escape_string($ttdEditArray['estimate_q1'])."',
                estimate_q2='".mysql_real_escape_string($ttdEditArray['estimate_q2'])."',
                estimate_q3='".mysql_real_escape_string($ttdEditArray['estimate_q3'])."',
                estimate_q4='".mysql_real_escape_string($ttdEditArray['estimate_q4'])."',
                estimate_total='".mysql_real_escape_string($ttdEditArray['estimate_total'])."',
                training_date='".mysql_real_escape_string($ttdEditArray['training_date'])."',
                push_notification='".mysql_real_escape_string($ttdEditArray['push_notification'])."',
                push_notify_email='".mysql_real_escape_string($ttdEditArray['push_notify_email'])."',
                push_notify_comments='".mysql_real_escape_string($ttdEditArray['push_notify_comments'])."',
                resources='".serialize($ttdEditArray['resources'])."',
                modality='" . $modalityArray . "',
				modality_other='" . $modality['other_email'] . "'
				WHERE contract_num ='".$contract_num."'";
		$result = mysql_query($Update_tta);
		unset($_SESSION['AttachmentUpload']);
		unset($_SESSION['AttachmentUpload1']);
		
}

?>