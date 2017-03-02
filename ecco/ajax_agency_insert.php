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
$insert_agency = "INSERT INTO agency (
                user_updated,
				name,
				street,
				apt,
				city,
				state,
				zip,
				phone,
				fax,
				manager_name,
				alt_num,
				region,
				SPG_SIG_priority,
				SPG_SIG_priority_notes,
				SPG_SIG_outcome,
				SPG_SIG_outcome_notes
               )
			   values(
			   '".$_SESSION['adminlogin1']."',
			   '".$name."',
			   '".$street."',
			   '".$apt_name."',
			   '".$city."',
			   '".$state."',
			   '".$zip_code."',
			   '".$phone."',
			   '".$fax."',
			   '".$manager_name."',
			   '".$manager_number."',
			   '".$region."',
			   '".$spg_sig_priority."',
			   '".$spg_sig_priority_notes."',
			   '".$spg_sig_outcome."',
			   '".$spg_sig_outcome_notes."'
			   )";
$result = mysql_query($insert_agency);
if($result==1) echo 'success'; else echo 'failure';
?>