<?php ob_start(); session_start(); include_once('config.php');
$region=isset($_POST['region'])? $_POST['region']:'';
$name=isset($_POST['name'])? $_POST['name']:'';
$street=isset($_POST['street'])? $_POST['street']:'';
$apt_name=isset($_POST['apt_name'])? $_POST['apt_name']:'';
$city=isset($_POST['city'])? $_POST['city']:'';
$state=isset($_POST['state'])? $_POST['state']:'';
$zip_code=isset($_POST['zip_code'])? $_POST['zip_code']:'';
$phone=isset($_POST['phone'])? $_POST['phone']:'';
$fax=isset($_POST['fax'])? $_POST['fax']:'';
$manager_name=isset($_POST['manager_name'])? $_POST['manager_name']:'';
$manager_number=isset($_POST['manager_number'])? $_POST['manager_number']:'';
$spg_sig_priority=isset($_POST['spg_sig_priority'])? $_POST['spg_sig_priority']:'';
$spg_sig_priority_notes=isset($_POST['spg_sig_priority_notes'])? $_POST['spg_sig_priority_notes']:' ';
$spg_sig_outcome=isset($_POST['spg_sig_outcome'])? $_POST['spg_sig_outcome']:'';
$spg_sig_outcome_notes=isset($_POST['spg_sig_outcome_notes'])? $_POST['spg_sig_outcome_notes']:'';
$agency_id=isset($_POST['agency_id'])? $_POST['agency_id']:'';
$update_agency = "UPDATE agency SET
				user_updated = '".$_SESSION['adminlogin1']."',
				name = '".$name."',
				street = '".$street."',
				apt =  '".$apt_name."',
				city = '".$city."',
				state = '".$state."',
				zip = '".$zip_code."',
				phone = '".$phone."',
				fax = '".$fax."',
				manager_name = '".$manager_name."',
				alt_num = '".$manager_number."',
				region = '".$region."',
				SPG_SIG_priority = '".$spg_sig_priority."',
				SPG_SIG_priority_notes = '".$spg_sig_priority_notes."',
				SPG_SIG_outcome = '".$spg_sig_outcome."',
				SPG_SIG_outcome_notes = '".$spg_sig_outcome_notes."'
				WHERE id = '".$agency_id."'";

$result = mysql_query($update_agency);
if($result==1) echo 'success'; else echo 'failure';
?>