<?php ob_start(); session_start(); include_once('config.php');
if($_POST['agency_id'] != "" && $_POST['contract_num']){
	$sql_agency = "SELECT `Id`, `name`, `manager_name`, `phone`, `street`, `apt`, `city`, `state`, `zip` FROM `agency` WHERE id = '".$_POST['agency_id']."'";
	$result_agency = mysql_query($sql_agency) or die(mysql_error()); 
	$row = mysql_fetch_array($result_agency);
	$agencyName = $row['name'];
	$agencyManager = $row['manager_name'];
	$agencyContactNum = $row['phone'];
	$agencyAddress = $row['street']. " ".$row['apt'].", ".$row['city']. ", ".$row['state'].", ".$row['zip'];
	$SPF_steps_assessment = $_POST['SPF_steps_assessment']=="on"?1:0;
	$SPF_steps_capacity = $_POST['SPF_steps_capacity']=="on"?1:0;
	$SPF_steps_planning = $_POST['SPF_steps_planning']=="on"?1:0;
	$SPF_steps_implment = $_POST['SPF_steps_implment']=="on"?1:0;
	$SPF_steps_evaluation = $_POST['SPF_steps_evaluation']=="on"?1:0;
	$SPF_align_assessment = $_POST['SPF_align_assessment']=="on"?1:0;
	$SPF_align_capacity = $_POST['SPF_align_capacity']=="on"?1:0;
	$SPF_align_planning = $_POST['SPF_align_planning']=="on"?1:0;
	$SPF_align_implment = $_POST['SPF_align_implment']=="on"?1:0;
	$SPF_align_evaluation = $_POST['SPF_align_evaluation']=="on"?1:0;
	$SPF_align_competency = $_POST['SPF_align_competency']=="on"?1:0;
	$SPF_align_sustainability = $_POST['SPF_align_sustainability']=="on"?1:0;
	$TTA_scheduled = !empty($_POST['TTA_scheduled']) ? $_POST['TTA_scheduled']:"NULL";
	$insert_tta = "UPDATE `TTA_Forms` SET 
			`AgencyName`= '".$agencyName."',
			`ManagerName`= '".$agencyManager."',
			`AgencyContactNumber`= '".$agencyContactNum."',
			`AgencyAddress`= '".$agencyAddress."',
			`TTA_inquiry_type` = '".$_POST['inquiry_type']."',
			`TTA_inquiry_notes` = '".$_POST['inquiry_notes']."',
			`SPF_steps_assessment` = '".$SPF_steps_assessment."',
			`SPF_align_capacity` = '".$SPF_align_capacity."',
			`SPF_align_planning` = '".$SPF_align_planning."',
			`SPF_align_implment` = '".$SPF_align_implment."',
			`SPF_align_evaluation` = '".$SPF_align_evaluation."',
			`SPF_align_competency` = '".$SPF_align_competency."',
			`SPF_align_sustainability` = '".$SPF_align_sustainability."',
			`SPF_align_notes` = '".$_POST['SPF_align_notes']."',
			`SPF_steps_capacity` = '".$SPF_steps_capacity."',
			`SPF_steps_planning` = '".$SPF_steps_planning."',
			`SPF_steps_implment` = '".$SPF_steps_implment."',
			`SPF_steps_evaluation` = '".$SPF_steps_evaluation."',
			`SPF_steps_notes` = '".$_POST['SPF_steps_notes']."',
			`SPF_align_assessment` = '".$SPF_align_assessment."',
			`TTA_problem_addressed` = '".$_POST['TTA_problem']."',
			`TTA_problem_addressed_notes` = '".$_POST['TTA_problem_notes']."',
			`TTA_desc` = '".$_POST['TTA_serv_req']."',
			`TTA_desc_notes` = '".$_POST['TTA_serv_req_notes']."',
			`TTA_outcome` = '".$_POST['TTA_desired_outcome']."',
			`TTA_outcome_notes` = '".$_POST['TTA_desired_outcome_notes']."',
			`timeframe` = '".$_POST['TTA_timeframe']."',
			`timeframe_notes` = '".$_POST['TTA_timeframe_notes']."',
			`TTA_Referral` = '".$_POST['TTA_Referral']."',
			`TTA_Contact_Phone` = '".$_POST['TTA_Contact_Phone']."',
			`TTA_Email` = '".$_POST['TTA_Email']."',
			`assigned_staff` = '".$_POST['staffname_assigned']."',
			`prelim_result` = '".$_POST['preliminary_result']."',
			`TTA_service_scheduled` = '".$TTA_scheduled."',
			`supporting_docs` = '".$_POST['supporting_docs']."',
			`TTA_service_provider`= '".$_POST['TTA_req_approval']."'
			WHERE `contract_num` = '".$_POST['contract_num']."'";
	$result = mysql_query($insert_tta);
	if($result==1) {
		header('Location:dashboard.php');
		die;
	}
	else {
        header('Location:message.php');
        die;
	}
}else{
	echo "<lable style='margin-left:450px;' align='center'><h3>Missing data<h3/></label>";
}	
?>