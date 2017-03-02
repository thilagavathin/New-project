<?php 
include_once('config.php');
session_start();
if(!isset($_SESSION['adminlogin'])){
header('Location:logout.php'); die;
}
if(isset($_POST['save_intervention_name'])){
    $intervention_name = str_replace("'", "\'", $_POST['intervention_name']);
	$intervention_community_name = str_replace("'", "\'", $_POST['intervention_community_name']);
	$intervention_zip_code = str_replace("'", "\'", $_POST['intervention_zip_code']);
	$intervention_contract_year = str_replace("'", "\'", $_POST['intervention_contract_year']);
    if($_POST['intervention_id']==0){        
        $insert_qry= "INSERT INTO `interventions` (`agency_id`, `user_id`,`intervention_name`,`intervention_community_name`,`intervention_zip_code`,`intervention_contract_year`) VALUES ('".$_POST['agency_id']."', '".$_SESSION['adminlogin']."','".$intervention_name."','".$intervention_community_name."','".$intervention_zip_code."','".$intervention_contract_year."')";
        $insert=mysql_query($insert_qry);
        if($insert){
            echo $insert_id=mysql_insert_id();;
        }else{
            echo '0';
        }
    }else{
        $insert_qry= "UPDATE `interventions` SET `intervention_name` = '".$intervention_name."', `intervention_community_name` = '".$intervention_community_name."', `intervention_zip_code` = '".$intervention_zip_code."', `intervention_contract_year` = '".$intervention_contract_year."' WHERE `id` = '".$_POST['intervention_id']."' ";
        $insert=mysql_query($insert_qry);
        if($insert){
            echo $insert_id=$_POST['intervention_id'];
        }else{
            echo '0';
        }
    }
}
if(isset($_POST['delete_intervention_name'])){ 
    if($_POST['intervention_id']>0){
        $delete_qry="DELETE FROM `interventions` WHERE `id`='".$_POST['intervention_id']."'";
        $delete=mysql_query($delete_qry);
        if($delete){
           echo $delete_id=1;
        }else{
            echo '0';
        }
    }else{
       echo $delete_id=1; 
    }
}
if(isset($_POST['partA_save'])){
     if(is_array($_POST['community'])){$community=serialize($_POST['community']);}
     else{$community=$_POST['community'];}
    
    $insert_qry= "INSERT INTO `interventions` (`agency_id`, `user_id`, `fillers_name`, `fillers_email`, `fillers_phoneno`, `PC_fillout`, `community`, `manager_name`, `manager_email`, `manager_phoneno`,part_a_save) VALUES ('".$_POST['agency_id']."', '".$_SESSION['adminlogin']."','".$_POST['fillers_name']."','".$_POST['fillers_email']."','".$_POST['fillers_phoneno']."','".$_POST['PC_fillout']."','".$community."','".$_POST['manager_name']."','".$_POST['manager_email']."','".$_POST['manager_phoneno']."',1) ";
    
    $insert=mysql_query($insert_qry);
    if($insert){
       $insert_id=mysql_insert_id();
    }else{
        $insert_id='0';
    } 
    echo $insert_id;
}
if(isset($_POST['partA_edit'])){
    if(is_array($_POST['community'])){$community=serialize($_POST['community']);}
    else{$community=$_POST['community'];}
    
    $insert_qry= "UPDATE `interventions` SET `user_id` = '".$_SESSION['adminlogin']."', `fillers_name` = '".$_POST['fillers_name']."', `fillers_email` = '".$_POST['fillers_email']."', `fillers_phoneno` = '".$_POST['fillers_phoneno']."', `PC_fillout` = '".$_POST['PC_fillout']."', `community` = '".$community."', `manager_name` = '".$_POST['manager_name']."', `manager_email` = '".$_POST['manager_email']."', `manager_phoneno` = '".$_POST['manager_phoneno']."', `part_a_save` = '1' WHERE `id` = '".$_POST['partA_edit']."' ";
    $insert=mysql_query($insert_qry);
    if($insert){
       $insert_id=$_POST['partA_edit'];
    }else{
        $insert_id= '0';
    }
    echo $insert_id;
}
if(isset($_POST['partB_save'])){
     if(is_array($_POST['IVs'])){$IVs=serialize($_POST['IVs']);}
     else{$IVs=$_POST['IVs'];}
    
    $insert_qry= "INSERT INTO `interventions` (`agency_id`, `addressed_issue`,`approved_state_priority`, `strategy_type`, `IOM_category`, `service_type`, `intervention_model`, `IVs`,part_b_save) VALUES ('".$_POST['agency_id']."', '".$_POST['addressed_issue']."','".$_POST['approved_state_priority']."','".$_POST['strategy_type']."','".$_POST['IOM_category']."','".$_POST['service_type']."','".$_POST['intervention_model']."','".$IVs."',1) ";
    
    $insert=mysql_query($insert_qry);
    if($insert){
       echo $insert_id=mysql_insert_id();
    }else{
        echo '0';
    } 
}
if(isset($_POST['partB_edit'])){
    if(is_array($_POST['IVs'])){$IVs=serialize($_POST['IVs']);}
    else{$IVs=$_POST['IVs'];}   
      
    $insert_qry= "UPDATE `interventions` SET `addressed_issue` = '".str_replace("'", "\'", $_POST['addressed_issue'])."', `approved_state_priority` = '".$_POST['approved_state_priority']."', `strategy_type` = '".$_POST['strategy_type']."', `IOM_category` = '".$_POST['IOM_category']."', `service_type` = '".$_POST['service_type']."',`IVs` = '".$IVs."', `part_b_save` = '1' WHERE `id` = '".$_POST['partB_edit']."' ";
    $insert=mysql_query($insert_qry);
    if($insert){
       echo $insert_id=$_POST['partB_edit'];
    }else{
        echo '0';
    }
}
if(isset($_POST['partD_save'])){
    if(is_array($_POST['settings_locations'])){$settings_locations=serialize($_POST['settings_locations']);}
    else{$settings_locations=$_POST['settings_locations'];}
    $EBP_proff='';
	$target_population=serialize(implode(',',$_POST['target_population']));
    $insert_qry="INSERT INTO `interventions` (`agency_id`, `EBP`, `strategy_model`, `intervention_type`, `sessions`, `frequency`, `cycles`,`target_population`,`numbers_served_reached`, `settings_locations`,part_d_save) VALUES ('".$_POST['agency_id']."', '".$_POST['EBP']."','".$_POST['strategy_model']."','".$_POST['intervention_type']."','".$_POST['sessions']."','".$_POST['frequency']."','".$_POST['cycles']."',$target_population,'".$_POST['numbers_served_reached']."','".$settings_locations."',1) ";
   	$insert=mysql_query($insert_qry);
    if($insert){
       echo $insert_id=mysql_insert_id();
    }else{
        echo '0';
    } 
}
if(isset($_POST['partC_edit'])){
     $intervention_uploads="SELECT * FROM interventions WHERE id='".$_POST['partC_edit']."' ";
     $uploads_data=mysql_query($intervention_uploads);
     $uploads_row=mysql_fetch_assoc($uploads_data);
     
     $arrayfilename=array();
	 
     $arraydates=array();
     if($uploads_row['uploadfilename']!='')  {
        $arrayfilename=unserialize($uploads_row['uploadfilename']);
        $arrayfoldername=unserialize($uploads_row['uploadfoldername']);
        $arraydates=unserialize($uploads_row['upload_dates']);
     } 
     
    if(isset($_SESSION['AttachmentUpload1']) && count($_SESSION['AttachmentUpload1']) > 0){
    foreach($_SESSION['AttachmentUpload1'] as $key => $value){        
        $folderValue[] = $key;
        $fileKey[] = $value;
        $datevalue[] =  date('Y-m-d H:i:s');
    }
	
    $fileName=array();
    $folderNameRandom=array();
    $newdates=array();
    if(count($arrayfilename) > 0){
        $fileName = array_merge($arrayfilename,$fileKey);
        $folderNameRandom = array_merge($arrayfoldername,$folderValue);
        $newdates=array_merge($arraydates,$datevalue);
    }else{
        $fileName = $fileKey;
        $folderNameRandom = $folderValue;
        $newdates=$datevalue;
    }
    
    $uploadfilename = serialize($fileName);    
    $uploadfoldername = serialize($folderNameRandom);
    $uploaddates = serialize($newdates);
    $insert_qry="UPDATE `interventions` SET	uploadfoldername='".$uploadfoldername."' ,uploadfilename='".$uploadfilename."' ,upload_dates='".$uploaddates."', `part_c_save` = '1' WHERE `id` = '".$_POST['partC_edit']."' ";
    
    unset($_SESSION['AttachmentUpload']);
    unset($_SESSION['AttachmentUpload1']);
    }   
    
    $insert=mysql_query($insert_qry);
    if($insert){
       echo $insert_id=$_POST['partC_edit'];
    }else{
        echo '0';
    } 
}
if(isset($_POST['partD_edit'])){
    // if(isset($_POST['settings_locations'])){$settings_locations=serialize($_POST['settings_locations']);}
    // else{$settings_locations=$_POST['settings_locations'];}
      
     $intervention_uploads= "SELECT * FROM interventions WHERE id='".$_POST['partD_edit']."' ";
     $uploads_data=mysql_query($intervention_uploads);
     $uploads_row=mysql_fetch_assoc($uploads_data);
     
     $arrayfilename=array();
     $arraydates=array();
     if($uploads_row['uploadfilename']!='')  {
        $arrayfilename=unserialize($uploads_row['uploadfilename']);
        $arrayfoldername=unserialize($uploads_row['uploadfoldername']);
        $arraydates=unserialize($uploads_row['upload_dates']);
		
     } 
     
     
     $newFilePath="";
    if(isset($_SESSION['AttachmentUpload1']) && count($_SESSION['AttachmentUpload1']) > 0){
    foreach($_SESSION['AttachmentUpload1'] as $key => $value){        
        $folderValue[] = $key;
        $fileKey[] = $value;
        $datevalue[] =  date('Y-m-d H:i:s');
    }
	
    $fileName=array();
    $folderNameRandom=array();
    $newdates=array();
    if(count($arrayfilename) > 0){
        $fileName = array_merge($arrayfilename,$fileKey);
        $folderNameRandom = array_merge($arrayfoldername,$folderValue);
        $newdates=array_merge($arraydates,$datevalue);
    }else{
        $fileName = $fileKey;
        $folderNameRandom = $folderValue;
        $newdates=$datevalue;
    }
    
    $uploadfilename = serialize($fileName);    
    $uploadfoldername = serialize($folderNameRandom);
    $uploaddates = serialize($newdates);
    $ebp_upload=" ,	uploadfoldername='".$uploadfoldername."' ,uploadfilename='".$uploadfilename."' ,upload_dates='".$uploaddates."'";
    
    unset($_SESSION['AttachmentUpload']);
    unset($_SESSION['AttachmentUpload1']);
    }else{
      $ebp_upload='';
    }
    
    $settings_locations=(isset($_POST['settings_locations']) ?serialize($_POST['settings_locations'])  : ''); 
    $EBP =(isset($_POST['EBP']) ? $_POST['EBP'] : ''); 
    $cycles_type =(isset($_POST['cycles_type']) ? $_POST['cycles_type'] : ''); 
    $intervention_type =(isset($_POST['intervention_type']) ? $_POST['intervention_type'] : ''); 
    $target_population =(isset($_POST['target_population'])!='') ?serialize($_POST['target_population'])  : '';  
    $types_of_participants =(isset($_POST['types_of_participants'])!='') ?serialize($_POST['types_of_participants'])  : ''; 
    
    $alternative_participants=(isset($_POST['alternative_participants']) ?serialize($_POST['alternative_participants'])  : '');
    $alternative_activity_types=(isset($_POST['alternative_activity_types'])!='') ?serialize($_POST['alternative_activity_types'])  : '';
    $alternative_activity_types_other =(isset($_POST['alternative_activity_types_other']) ? $_POST['alternative_activity_types_other'] : '');
    
    $site_location=(isset($_POST['site_location']) ?serialize($_POST['site_location'])  : '');  
	
	
	
	$strategy_model = (isset($_POST['strategy_model']) ? $_POST['strategy_model'] : '');
	$strategy_model_other = (isset($_POST['strategy_model']) ? $_POST['strategy_model'] : ''); 
	  
	 $environment_strategy = (isset($_POST['environment_strategy']) ? $_POST['environment_strategy'] : ''); 
	 $policy_related_activities = (isset($_POST['policy_related_activities'])!='') ?serialize($_POST['policy_related_activities'])  : '';  
	 $policy_related_activities_other = (isset($_POST['policy_related_activities_other']) ? $_POST['policy_related_activities_other'] : ''); 
	 $environmental_organizations = (isset($_POST['environmental_organizations'])!='') ?serialize($_POST['environmental_organizations'])  : '';  
	 $environmental_organizations_other = (isset($_POST['environmental_organizations_other']) ? $_POST['environmental_organizations_other'] : '');  
	 $en_alternative_activities = (isset($_POST['en_alternative_activities']) ? $_POST['en_alternative_activities'] : '');  
	 $en_training = (isset($_POST['en_training'])!='') ?serialize($_POST['en_training'])  : '';  
	 $en_training_other = (isset($_POST['en_training_other']) ? $_POST['en_training_other'] : ''); 
	 $intervention_type = (isset($_POST['intervention_type']) ? $_POST['intervention_type'] : '');  
	 $sessions = (isset($_POST['sessions']) ? $_POST['sessions'] : '');   
	 $frequency = (isset($_POST['frequency']) ? $_POST['frequency'] : ''); 
	 $frequency_other = (isset($_POST['frequency_other']) ? $_POST['frequency_other'] : ''); 
	 $time_unit = (isset($_POST['time_unit']) ? $_POST['time_unit'] : ''); 
	 //cycles_type
	 $cycles = (isset($_POST['cycles']) ? $_POST['cycles'] : ''); 
	 $prevention_education_intervention = (isset($_POST['prevention_education_intervention']) ? $_POST['prevention_education_intervention'] : ''); 
	 $prevention_education_intervention_other = (isset($_POST['prevention_education_intervention_other']) ? $_POST['prevention_education_intervention_other'] : ''); 
	 $en_enforcement_efforts = (isset($_POST['en_enforcement_efforts']) ? $_POST['en_enforcement_efforts'] : ''); 
	 $en_target_merchants = (isset($_POST['en_target_merchants']) ? $_POST['en_target_merchants'] : ''); 
	 $en_law_checks = (isset($_POST['en_law_checks']) ? $_POST['en_law_checks'] : ''); 
	 $law_enforcement = (isset($_POST['law_enforcement']) ? $_POST['law_enforcement'] : ''); 
	 $types_of_establishments = (isset($_POST['types_of_establishments']) ? serialize($_POST['types_of_establishments']) : ''); 
	 $number_of_alcohol = (isset($_POST['number_of_alcohol']) ? $_POST['number_of_alcohol'] : ''); 
	 $number_of_retailers = (isset($_POST['number_of_retailers']) ? $_POST['number_of_retailers'] : ''); 
	 $number_of_compliance = (isset($_POST['number_of_compliance']) ? $_POST['number_of_compliance'] : ''); 
	 $en_sobriety_checks = (isset($_POST['en_sobriety_checks']) ? $_POST['en_sobriety_checks'] : ''); 
	 $number_of_law_enforcement = (isset($_POST['number_of_law_enforcement']) ? $_POST['number_of_law_enforcement'] : ''); 
	 $number_of_sobriety = (isset($_POST['number_of_sobriety']) ? $_POST['number_of_sobriety'] : ''); 
	 $en_frequently_sobriety = (isset($_POST['en_frequently_sobriety']) ? $_POST['en_frequently_sobriety'] : ''); 
	 $en_frequently_sobriety_other = (isset($_POST['en_frequently_sobriety_other']) ? $_POST['en_frequently_sobriety_other'] : ''); 
	 $en_shoulder_tap = (isset($_POST['en_shoulder_tap']) ? $_POST['en_shoulder_tap'] : ''); 
	 $en_partner_enforce = (isset($_POST['en_partner_enforce']) ? $_POST['en_partner_enforce'] : ''); 
	 $number_of_law_agencies = (isset($_POST['number_of_law_agencies']) ? $_POST['number_of_law_agencies'] : ''); 
	 $number_of_retail_outlets = (isset($_POST['number_of_retail_outlets']) ? $_POST['number_of_retail_outlets'] : ''); 
	 $number_of_retailers_tab = (isset($_POST['number_of_retailers_tab']) ? $_POST['number_of_retailers_tab'] : ''); 
	 $number_of_shoulder_tab = (isset($_POST['number_of_shoulder_tab']) ? $_POST['number_of_shoulder_tab'] : ''); 
	 $en_shoulder_location = (isset($_POST['en_shoulder_location']) ? $_POST['en_shoulder_location'] : ''); 
	 $en_shoulder_location_other = (isset($_POST['en_shoulder_location_other']) ? $_POST['en_shoulder_location_other'] : ''); 
	 $en_enforcement_activities = (isset($_POST['en_enforcement_activities']) ? $_POST['en_enforcement_activities'] : ''); 
	 $en_enforcement_activities_describe = (isset($_POST['en_enforcement_activities_describe']) ? $_POST['en_enforcement_activities_describe'] : ''); 
	 $en_norming_campaign = (isset($_POST['en_norming_campaign']) ? $_POST['en_norming_campaign'] : ''); 
	 $en_target_audience = (isset($_POST['en_target_audience']) ? serialize($_POST['en_target_audience']) : ''); 
	 $en_target_audience_other = (isset($_POST['en_target_audience_other']) ? $_POST['en_target_audience_other'] : ''); 
	  
	 $en_strategy_plan = (isset($_POST['en_strategy_plan']) ? $_POST['en_strategy_plan'] : ''); 
     $en_strategy_plan_describe = (isset($_POST['en_strategy_plan_describe']) ? $_POST['en_strategy_plan_describe'] : '');
	 $numbers_served_reached = (isset($_POST['numbers_served_reached']) ? $_POST['numbers_served_reached'] : ''); 
		 
 
	if(isset($_POST['partd_strategy_type'])){
        if($_POST['partd_strategy_type']=='Alternative Drug-Free Activities'){
             $insert_qry= "UPDATE `interventions` SET `EBP` = '".$EBP."', `strategy_model` = '".str_replace("'", "\'", $_POST['strategy_model'])."', `strategy_model_other` = '".str_replace("'", "\'", $_POST['strategy_model_other'])."',`socio_ecological` = '".str_replace("'", "\'", $_POST['socio_ecological'])."',`target_population` = '".str_replace("'", "\'", $target_population)."',`target_population_other` = '".str_replace("'", "\'", $_POST['target_population_other'])."',`types_of_participants` ='".str_replace("'", "\'", $types_of_participants)."',`types_of_participants_other` = '".str_replace("'", "\'", $_POST['types_of_participants_other'])."',`alternative_participants` = '".str_replace("'", "\'", $alternative_participants)."',`alternative_activity_types` = '".str_replace("'", "\'", $alternative_activity_types)."',`alternative_activity_types_other` = '".str_replace("'", "\'", $alternative_activity_types_other)."', `intervention_type` = '".$intervention_type."', `sessions` = '".$_POST['sessions']."',`frequency` = '".$_POST['frequency']."',`frequency_other` = '".$_POST['frequency_other']."',`cycles` = '".$_POST['cycles']."',`cycles_type` = '".$cycles_type."',`time_unit` = '".$_POST['time_unit']."',`prevention_education_intervention_other` = '".$_POST['prevention_education_intervention_other']."',`prevention_education_intervention` = '".$_POST['prevention_education_intervention']."',`numbers_served_reached` = '".$_POST['numbers_served_reached']."',`site_location` = '".str_replace("'", "\'", $site_location)."', `part_d_save` = '1' ".$ebp_upload." WHERE `id` = '".$_POST['partD_edit']."' ";
        }
		else if($_POST['partd_strategy_type']=='Environmental')
		{
			//echo $insert_qry= "UPDATE `interventions` SET `EBP` = '".$EBP."', `strategy_model` = '".str_replace("'", "\'", $_POST['strategy_model'])."', `strategy_model_other` = '".$_POST['strategy_model_other']."',`socio_ecological` = '".str_replace("'", "\'", $_POST['socio_ecological'])."',`target_population` = '".str_replace("'", "\'", $target_population)."',`target_population_other` = '".str_replace("'", "\'", $_POST['target_population_other'])."',`types_of_participants` ='".str_replace("'", "\'", $types_of_participants)."',`types_of_participants_other` = '".str_replace("'", "\'", $_POST['types_of_participants_other'])."',`alternative_participants` = '".str_replace("'", "\'", $alternative_participants)."',`alternative_activity_types` = '".str_replace("'", "\'", $alternative_activity_types)."',`alternative_activity_types_other` = '".str_replace("'", "\'", $alternative_activity_types_other)."', `intervention_type` = '".$intervention_type."', `sessions` = '".$_POST['sessions']."',`frequency` = '".$_POST['frequency']."',`frequency_other` = '".$_POST['frequency_other']."',`cycles` = '".$_POST['cycles']."',`cycles_type` = '".$cycles_type."',`time_unit` = '".$_POST['time_unit']."',`prevention_education_intervention_other` = '".$_POST['prevention_education_intervention_other']."',`prevention_education_intervention` = '".$_POST['prevention_education_intervention']."',`numbers_served_reached` = '".$_POST['numbers_served_reached']."',`site_location` = '".$site_location."', `part_d_save` = '1' ".$ebp_upload." WHERE `id` = '".$_POST['partD_edit']."' ";
			 $insert_qry= "UPDATE `interventions` SET `EBP` = '".$EBP."', `strategy_model` = '".str_replace("'", "\'", $_POST['strategy_model'])."', `strategy_model_other` = '".$_POST['strategy_model_other']."', `intervention_type` = '".$intervention_type."', `sessions` = '".$_POST['sessions']."',`frequency` = '".$_POST['frequency']."',`frequency_other` = '".$_POST['frequency_other']."',`cycles` = '".$_POST['cycles']."',`cycles_type` = '".$cycles_type."',`time_unit` = '".$_POST['time_unit']."',`prevention_education_intervention_other` = '".$_POST['prevention_education_intervention_other']."',`prevention_education_intervention` = '".$_POST['prevention_education_intervention']."',`numbers_served_reached` = '".$_POST['numbers_served_reached']."',`settings_locations` = '".$settings_locations."',environment_strategy = '$environment_strategy',policy_related_activities = '$policy_related_activities',policy_related_activities_other ='$policy_related_activities_other',environmental_organizations = '$environmental_organizations',environmental_organizations_other = '$environmental_organizations_other',en_alternative_activities = '$en_alternative_activities',en_training = '$en_training',en_training_other = '$en_training_other',en_enforcement_efforts = '$en_enforcement_efforts',en_target_merchants = '$en_target_merchants',en_law_checks ='$en_law_checks',law_enforcement = '$law_enforcement',types_of_establishments = '$types_of_establishments',number_of_alcohol = '$number_of_alcohol',number_of_retailers = '$number_of_retailers',number_of_compliance = '$number_of_compliance',en_sobriety_checks = '$en_sobriety_checks',number_of_law_enforcement = '$number_of_law_enforcement',number_of_sobriety = '$number_of_sobriety',en_frequently_sobriety = '$en_frequently_sobriety',en_frequently_sobriety_other = '$en_frequently_sobriety_other',en_shoulder_tap = '$en_shoulder_tap',en_partner_enforce = '$en_partner_enforce',number_of_law_agencies = '$number_of_law_agencies',number_of_retail_outlets = '$number_of_retail_outlets',number_of_retailers_tab = '$number_of_retailers_tab',number_of_shoulder_tab = '$number_of_shoulder_tab',en_shoulder_location = '$en_shoulder_location',en_shoulder_location_other = '$en_shoulder_location_other',en_enforcement_activities = '$en_enforcement_activities',en_enforcement_activities_describe = '$en_enforcement_activities_describe',en_norming_campaign = '$en_norming_campaign',en_target_audience = '$en_target_audience',en_target_audience_other = '$en_target_audience_other',en_strategy_plan = '$en_strategy_plan',en_strategy_plan_describe = '$en_strategy_plan_describe', `part_d_save` = '1' ".$ebp_upload." WHERE `id` = '".$_POST['partD_edit']."' ";
		}
		else if($_POST['partd_strategy_type']=='Information Dissemination')
		{
			$target_population =(isset($_POST['target_population'])!='') ?$_POST['target_population']  : '';  
			$types_of_participants =(isset($_POST['types_of_participants'])!='') ? $_POST['types_of_participants'] : '';
			$in_intended_purpose = (isset($_POST['in_intended_purpose']) ? $_POST['in_intended_purpose'] : ''); 
			$in_intended_purpose_other = (isset($_POST['in_intended_purpose_other']) ? $_POST['in_intended_purpose_other'] : ''); 
			$in_comm_effort = (isset($_POST['in_comm_effort']) ? $_POST['in_comm_effort'] : ''); 
			$in_comm_mem = (isset($_POST['in_comm_mem']) ? serialize($_POST['in_comm_mem']) : ''); 
			$in_comm_mem_other = (isset($_POST['in_comm_mem_other']) ? $_POST['in_comm_mem_other'] : '');
			$in_com_group = (isset($_POST['in_com_group']) ? $_POST['in_com_group'] : '');
			$in_social_market = (isset($_POST['in_social_market']) ? $_POST['in_social_market'] : '');
			$in_tele_ad = (isset($_POST['in_tele_ad']) ? $_POST['in_tele_ad'] : '');
			$in_time_tele_ad = (isset($_POST['in_time_tele_ad']) ? $_POST['in_time_tele_ad'] : '');
			$in_week_tele_ad = (isset($_POST['in_week_tele_ad']) ? $_POST['in_week_tele_ad'] : '');
			$in_diff_tele_station = (isset($_POST['in_diff_tele_station']) ? $_POST['in_diff_tele_station'] : '');
			$in_detail_tele_information = (isset($_POST['in_detail_tele_information']) ? $_POST['in_detail_tele_information'] : '');
			$in_radio_ad = (isset($_POST['in_radio_ad']) ? $_POST['in_radio_ad'] : '');
			$in_time_radio_ad = (isset($_POST['in_time_radio_ad']) ? $_POST['in_time_radio_ad'] : '');
			$in_week_radio_ad = (isset($_POST['in_week_radio_ad']) ? $_POST['in_week_radio_ad'] : '');
			$in_diff_radio_station = (isset($_POST['in_diff_radio_station']) ? $_POST['in_diff_radio_station'] : '');
			$in_detail_radio_information = (isset($_POST['in_detail_radio_information']) ? $_POST['in_detail_radio_information'] : '');
			$in_print_ad = (isset($_POST['in_print_ad']) ? $_POST['in_print_ad'] : '');
			$in_create_print_ad = (isset($_POST['in_create_print_ad']) ? $_POST['in_create_print_ad'] : '');
			$in_diff_print_ad = (isset($_POST['in_diff_print_ad']) ? $_POST['in_diff_print_ad'] : '');
			$in_detail_print_ad = (isset($_POST['in_detail_print_ad']) ? $_POST['in_detail_print_ad'] : '');
			$in_spl_event = (isset($_POST['in_spl_event']) ? $_POST['in_spl_event'] : '');
			$in_spl_event_type = (isset($_POST['in_spl_event_type']) ? $_POST['in_spl_event_type'] : '');
			$in_spl_event_tot = (isset($_POST['in_spl_event_tot']) ? $_POST['in_spl_event_tot'] : '');
			$in_promotional_activities = (isset($_POST['in_promotional_activities']) ? $_POST['in_promotional_activities'] : '');
			$in_promotional_activities_type = (isset($_POST['in_promotional_activities_type']) ? $_POST['in_promotional_activities_type'] : '');
			$in_promotional_activities_tot = (isset($_POST['in_promotional_activities_tot']) ? $_POST['in_promotional_activities_tot'] : '');
			$in_com_met = (isset($_POST['in_com_met']) ? $_POST['in_com_met'] : '');
			$in_num_com_met = (isset($_POST['in_num_com_met']) ? $_POST['in_num_com_met'] : '');
			$in_group_com_met = (isset($_POST['in_group_com_met']) ? serialize($_POST['in_group_com_met']) : '');
			$in_letter = (isset($_POST['in_letter']) ? $_POST['in_letter'] : '');
			$in_num_letter = (isset($_POST['in_num_letter']) ? $_POST['in_num_letter'] : '');
			$in_PSA = (isset($_POST['in_PSA']) ? $_POST['in_PSA'] : '');
			$in_time_PSA = (isset($_POST['in_time_PSA']) ? $_POST['in_time_PSA'] : '');
			$in_week_PSA = (isset($_POST['in_week_PSA']) ? $_POST['in_week_PSA'] : '');
			$in_diff_PSA = (isset($_POST['in_diff_PSA']) ? $_POST['in_diff_PSA'] : '');
			$in_detail_PSA = (isset($_POST['in_detail_PSA']) ? $_POST['in_detail_PSA'] : '');
			$in_prevention_poster = (isset($_POST['in_prevention_poster']) ? $_POST['in_prevention_poster'] : '');
			$in_num_prevention_poster = (isset($_POST['in_num_prevention_poster']) ? $_POST['in_num_prevention_poster'] : '');
			$in_prevention_poster_tot = (isset($_POST['in_prevention_poster_tot']) ? $_POST['in_prevention_poster_tot'] : '');
			$in_distribute = (isset($_POST['in_distribute']) ? $_POST['in_distribute'] : '');
			$in_distribute_location = (isset($_POST['in_distribute_location']) ? $_POST['in_distribute_location'] : '');
			$in_distribute_tot = (isset($_POST['in_distribute_tot']) ? $_POST['in_distribute_tot'] : '');
			$in_display_bill = (isset($_POST['in_display_bill']) ? $_POST['in_display_bill'] : '');
			$in_display_bill_loc = (isset($_POST['in_display_bill_loc']) ? $_POST['in_display_bill_loc'] : '');
			$in_week_display_bill = (isset($_POST['in_week_display_bill']) ? $_POST['in_week_display_bill'] : '');
			$in_detail_display_bill = (isset($_POST['in_detail_display_bill']) ? $_POST['in_detail_display_bill'] : '');
			$in_hotline = (isset($_POST['in_hotline']) ? $_POST['in_hotline'] : '');
			$in_resource_center = (isset($_POST['in_resource_center']) ? $_POST['in_resource_center'] : '');
			$in_web_sites = (isset($_POST['in_web_sites']) ? $_POST['in_web_sites'] : '');
			$in_sticker_shock = (isset($_POST['in_sticker_shock']) ? $_POST['in_sticker_shock'] : '');
			$in_sticker_shock_target = (isset($_POST['in_sticker_shock_target']) ? $_POST['in_sticker_shock_target'] : '');
			$in_sticker_shock_outlet = (isset($_POST['in_sticker_shock_outlet']) ? $_POST['in_sticker_shock_outlet'] : '');
			$in_underage_drinking = (isset($_POST['in_underage_drinking']) ? $_POST['in_underage_drinking'] : '');
			$in_underage_drinking_com = (isset($_POST['in_underage_drinking_com']) ? $_POST['in_underage_drinking_com'] : '');
			$in_underage_drinking_win = (isset($_POST['in_underage_drinking_win']) ? $_POST['in_underage_drinking_win'] : '');
			$in_drug_abuse = (isset($_POST['in_drug_abuse']) ? $_POST['in_drug_abuse'] : '');
			$in_drug_abuse_com = (isset($_POST['in_drug_abuse_com']) ? $_POST['in_drug_abuse_com'] : ''); 
			$in_drug_abuse_sticker = (isset($_POST['in_drug_abuse_sticker']) ? $_POST['in_drug_abuse_sticker'] : '');
			$in_com_activity = (isset($_POST['in_com_activity']) ? $_POST['in_com_activity'] : '');
			$in_com_activity_desc = (isset($_POST['in_com_activity_desc']) ? $_POST['in_com_activity_desc'] : '');
			$in_num_of_individual = (isset($_POST['in_num_of_individual']) ? $_POST['in_num_of_individual'] : '');
			
			$partd_strategy_type = (isset($_POST['partd_strategy_type']) ? $_POST['partd_strategy_type'] : '');
			// EBP, strategy_model, strategy_model_other, types_of_participants, types_of_participants_other, target_population, target_population_other, 
			
 			$insert_qry= "UPDATE `interventions` SET `EBP` = '".$EBP."', `strategy_model` = '".str_replace("'", "\'", $_POST['strategy_model'])."', `strategy_model_other` = '".$_POST['strategy_model_other']."',types_of_participants_other = '".str_replace("'", "\'", $_POST['types_of_participants_other'])."',types_of_participants ='".str_replace("'", "\'", $types_of_participants)."',target_population_other = '".str_replace("'", "\'", $_POST['target_population_other'])."',target_population = '".str_replace("'", "\'", $target_population)."',in_intended_purpose = '$in_intended_purpose',in_intended_purpose_other = '$in_intended_purpose_other',in_comm_effort = '$in_comm_effort',in_comm_mem = '$in_comm_mem',in_comm_mem_other = '$in_comm_mem_other',in_com_group = '$in_com_group',in_social_market = '$in_social_market',in_tele_ad = '$in_tele_ad',in_time_tele_ad = '$in_time_tele_ad',in_week_tele_ad = '$in_week_tele_ad',in_diff_tele_station = '$in_diff_tele_station',in_detail_tele_information = '$in_detail_tele_information',in_radio_ad = '$in_radio_ad',in_time_radio_ad = '$in_time_radio_ad',in_week_radio_ad = '$in_week_radio_ad',in_diff_radio_station = '$in_diff_radio_station',in_detail_radio_information = '$in_detail_radio_information',in_print_ad = '$in_print_ad',in_create_print_ad = '$in_create_print_ad',in_diff_print_ad = '$in_diff_print_ad',in_detail_print_ad = '$in_detail_print_ad',in_spl_event = '$in_spl_event',in_spl_event_type = '$in_spl_event_type',in_spl_event_tot = '$in_spl_event_tot',in_promotional_activities = '$in_promotional_activities',in_promotional_activities_type = '$in_promotional_activities_type',in_promotional_activities_tot = '$in_promotional_activities_tot',in_com_met = '$in_com_met',in_num_com_met = '$in_num_com_met',in_group_com_met = '$in_group_com_met',in_letter = '$in_letter',in_num_letter = '$in_num_letter',in_PSA = '$in_PSA',in_time_PSA = '$in_time_PSA',in_week_PSA = '$in_week_PSA',in_diff_PSA = '$in_diff_PSA',in_detail_PSA = '$in_detail_PSA',in_prevention_poster = '$in_prevention_poster',in_num_prevention_poster = '$in_num_prevention_poster',in_prevention_poster_tot = '$in_prevention_poster_tot',in_distribute = '$in_distribute',in_distribute_location = '$in_distribute_location',in_distribute_tot = '$in_distribute_tot',in_display_bill = '$in_display_bill',in_display_bill_loc = '$in_display_bill_loc',in_week_display_bill = '$in_week_display_bill',in_detail_display_bill = '$in_detail_display_bill',in_hotline = '$in_hotline',in_resource_center = '$in_resource_center',in_web_sites = '$in_web_sites',in_sticker_shock = '$in_sticker_shock',in_sticker_shock_target = '$in_sticker_shock_target',in_sticker_shock_outlet = '$in_sticker_shock_outlet',in_underage_drinking = '$in_underage_drinking',in_underage_drinking_com = '$in_underage_drinking_com',in_underage_drinking_win = '$in_underage_drinking_win',in_drug_abuse = '$in_drug_abuse',in_drug_abuse_com = '$in_drug_abuse_com',in_drug_abuse_sticker = '$in_drug_abuse_sticker',in_com_activity = '$in_com_activity',in_com_activity_desc = '$in_com_activity_desc',in_num_of_individual = '$in_num_of_individual',`part_d_save` = '1' ".$ebp_upload." WHERE `id` = '".$_POST['partD_edit']."' ";
		}
		else if($_POST['partd_strategy_type']=='Problem Identification and Referral')
		{
			$target_population = (isset($_POST['target_population'])!='') ?serialize($_POST['target_population'])  : '';
			$types_of_participants = (isset($_POST['types_of_participants'])!='') ?serialize($_POST['types_of_participants'])  : '';
			$pb_iden_activity = (isset($_POST['types_of_participants'])!='') ?serialize($_POST['types_of_participants'])  : '';
			$pb_iden_activity_other = (isset($_POST['pb_iden_activity_other']) ? $_POST['pb_iden_activity_other'] : '');
			$cycles_type = (isset($_POST['cycles_type']) ? $_POST['cycles_type'] : '');
			$cycles = (isset($_POST['cycles']) ? $_POST['cycles'] : '');
			$pb_diff_loc = (isset($_POST['pb_diff_loc']) ? $_POST['pb_diff_loc'] : '');
			$settings_locations=(isset($_POST['settings_locations']) ?serialize($_POST['settings_locations'])  : '');
			$pb_iden_activity_type = (isset($_POST['types_of_participants'])!='') ?serialize($_POST['types_of_participants'])  : '';
			$pb_iden_activity_type_other = (isset($_POST['pb_iden_activity_type_other']) ? $_POST['pb_iden_activity_type_other'] : '');
			
			$partd_strategy_type = (isset($_POST['partd_strategy_type']) ? $_POST['partd_strategy_type'] : '');
			
			$insert_qry= "UPDATE `interventions` SET `EBP` = '".$EBP."', `strategy_model` = '".str_replace("'", "\'", $_POST['strategy_model'])."', `strategy_model_other` = '".$_POST['strategy_model_other']."',types_of_participants_other = '".str_replace("'", "\'", $_POST['types_of_participants_other'])."',types_of_participants ='".str_replace("'", "\'", $types_of_participants)."',target_population_other = '".str_replace("'", "\'", $_POST['target_population_other'])."',target_population = '".str_replace("'", "\'", $target_population)."',`pb_iden_activity_other` = '".str_replace("'", "\'", $pb_iden_activity_other)."',`pb_iden_activity` = '".str_replace("'", "\'", $pb_iden_activity)."',`cycles_type` = '$cycles_type',`cycles` = '$cycles',`pb_diff_loc` = '$pb_diff_loc',`settings_locations` = '".$settings_locations."',`pb_iden_activity_type_other` = '".str_replace("'", "\'", $pb_iden_activity_type_other)."',`pb_iden_activity_type` = '".str_replace("'", "\'", $pb_iden_activity_type)."',`part_d_save` = '1' ".$ebp_upload." WHERE `id` = '".$_POST['partD_edit']."' ";
		
		}
		else if($_POST['partd_strategy_type']=='Community-Based Processes')
		{
			$cp_num_stack_met = (isset($_POST['cp_num_stack_met']) ? $_POST['cp_num_stack_met'] : '');
			$cp_num_stack_train = (isset($_POST['cp_num_stack_train']) ? $_POST['cp_num_stack_train'] : '');
			$cp_num_com_mem = (isset($_POST['cp_num_com_mem']) ? $_POST['cp_num_com_mem'] : '');
			$cp_num_com_org = (isset($_POST['cp_num_com_org']) ? $_POST['cp_num_com_org'] : '');
			$cp_dev_net = (isset($_POST['cp_dev_net']) ? $_POST['cp_dev_net'] : '');
			$cp_reorg_local = (isset($_POST['cp_reorg_local']) ? $_POST['cp_reorg_local'] : '');
			$cp_reallocate_local = (isset($_POST['cp_reallocate_local']) ? $_POST['cp_reallocate_local'] : '');
			$cp_change_way = (isset($_POST['cp_change_way']) ? $_POST['cp_change_way'] : '');
			$cp_other_com = (isset($_POST['cp_other_com']) ? $_POST['cp_other_com'] : '');
			$cp_other_com_detail = (isset($_POST['cp_other_com_detail']) ? $_POST['cp_other_com_detail'] : '');
			$cp_num_of_individual = (isset($_POST['cp_num_of_individual']) ? $_POST['cp_num_of_individual'] : '');
			
			$partd_strategy_type = (isset($_POST['partd_strategy_type']) ? $_POST['partd_strategy_type'] : '');
			
			$insert_qry= "UPDATE `interventions` SET `EBP` = '".$EBP."', `strategy_model` = '".str_replace("'", "\'", $_POST['strategy_model'])."', `strategy_model_other` = '".$_POST['strategy_model_other']."',`cp_num_stack_met` = '$cp_num_stack_met',`cp_num_stack_train` = '$cp_num_stack_train',`cp_num_com_mem` = '$cp_num_com_mem',`cp_num_com_org` = '$cp_num_com_org',`cp_dev_net` = '$cp_dev_net',`cp_reorg_local` = '$cp_reorg_local',`cp_reallocate_local` = '$cp_reallocate_local',`cp_change_way` = '$cp_change_way',`cp_other_com` = '$cp_other_com',`cp_other_com_detail` = '$cp_other_com_detail',`cp_num_of_individual` = '$cp_num_of_individual',`part_d_save` = '1' ".$ebp_upload." WHERE `id` = '".$_POST['partD_edit']."' ";
		
		}
        else{
          $insert_qry= "UPDATE `interventions` SET `EBP` = '".$EBP."', `strategy_model` = '".str_replace("'", "\'", $_POST['strategy_model'])."', `strategy_model_other` = '".$_POST['strategy_model_other']."', `intervention_type` = '".$intervention_type."', `sessions` = '".$_POST['sessions']."',`frequency` = '".$_POST['frequency']."',`frequency_other` = '".$_POST['frequency_other']."',`cycles` = '".$_POST['cycles']."',`cycles_type` = '".$cycles_type."',`time_unit` = '".$_POST['time_unit']."',`types_of_participants_other` = '".str_replace("'", "\'", $_POST['types_of_participants_other'])."',`types_of_participants` ='".str_replace("'", "\'", $types_of_participants)."',`prevention_education_intervention_other` = '".$_POST['prevention_education_intervention_other']."',`prevention_education_intervention` = '".$_POST['prevention_education_intervention']."',`target_population_other` = '".str_replace("'", "\'", $_POST['target_population_other'])."',`target_population` = '".str_replace("'", "\'", $target_population)."',`socio_ecological` = '".str_replace("'", "\'", $_POST['socio_ecological'])."',`numbers_served_reached` = '".$_POST['numbers_served_reached']."', `settings_locations` = '".$settings_locations."', `part_d_save` = '1' ".$ebp_upload." WHERE `id` = '".$_POST['partD_edit']."' ";  
        }
    }else{
      $insert_qry= "UPDATE `interventions` SET `EBP` = '".$EBP."', `strategy_model` = '".str_replace("'", "\'", $_POST['strategy_model'])."', `strategy_model_other` = '".$_POST['strategy_model_other']."', `intervention_type` = '".$intervention_type."', `sessions` = '".$_POST['sessions']."',`frequency` = '".$_POST['frequency']."',`frequency_other` = '".$_POST['frequency_other']."',`cycles` = '".$_POST['cycles']."',`cycles_type` = '".$cycles_type."',`time_unit` = '".$_POST['time_unit']."',`types_of_participants_other` = '".str_replace("'", "\'", $_POST['types_of_participants_other'])."',`types_of_participants` ='".str_replace("'", "\'", $types_of_participants)."',`prevention_education_intervention_other` = '".$_POST['prevention_education_intervention_other']."',`prevention_education_intervention` = '".$_POST['prevention_education_intervention']."',`target_population_other` = '".str_replace("'", "\'", $_POST['target_population_other'])."',`target_population` = '".str_replace("'", "\'", $target_population)."',`socio_ecological` = '".str_replace("'", "\'", $_POST['socio_ecological'])."',`numbers_served_reached` = '".$_POST['numbers_served_reached']."', `settings_locations` = '".$settings_locations."', `part_d_save` = '1' ".$ebp_upload." WHERE `id` = '".$_POST['partD_edit']."' ";  
    }
    
    
    $insert=mysql_query($insert_qry);
    if($insert){
       echo $insert_id=$_POST['partD_edit'];
    }else{
        echo '0';
    } 
}
if(isset($_POST['partE_edit'])){
    $work_bundle             =($_POST['work_bundle']!=''              ? serialize($_POST['work_bundle'])              : $_POST['work_bundle']);
    $description             =($_POST['description']!=''              ? serialize($_POST['description'])              : $_POST['description']);
    $start_date              =($_POST['start_date']!=''               ? serialize($_POST['start_date'])               : $_POST['start_date']);
    $end_date                =($_POST['end_date']!=''                 ? serialize($_POST['end_date'])                 : $_POST['end_date']);
    $like_training           =(isset($_POST['like_training'])         ? $_POST['like_training']                       : ''); 
    $like_training           =($like_training!=''                     ? serialize($like_training)                     : $like_training);
    $about_training          =($_POST['about_training']!=''           ? serialize($_POST['about_training'])           : $_POST['about_training']);
    $responsibilities_parties=($_POST['responsibilities_parties']!='' ? serialize($_POST['responsibilities_parties']) : $_POST['responsibilities_parties']);
    $action_steps            =($_POST['action_steps']!=''             ? serialize($_POST['action_steps'])             : $_POST['action_steps']);
    $activities              =($_POST['activities']!=''               ? serialize($_POST['activities'])               : $_POST['activities']);
    $target_audience         =($_POST['target_audience']!=''          ? serialize($_POST['target_audience'])          : $_POST['target_audience']);
    $other_target_audience   =($_POST['other_target_audience']!=''    ? serialize($_POST['other_target_audience'])    : $_POST['other_target_audience']);
    $end_status              =($_POST['end_status']!=''               ? serialize($_POST['end_status'])               : $_POST['end_status']);
    $ongoing_explain         =($_POST['ongoing_explain']!=''          ? serialize($_POST['ongoing_explain'])          : $_POST['ongoing_explain']);
      
    $insert_qry= "UPDATE `interventions` SET `work_bundle` = '".$work_bundle."', `description` = '".str_replace("'", "\'", $description)."', `start_date` = '".$start_date."', `end_date` = '".$end_date."', `end_status` = '".$end_status."',`ongoing_explain` = '".$ongoing_explain."',`like_training` = '".$like_training."', `about_training` = '".str_replace("'", "\'", $about_training)."', `responsibilities_parties` = '".$responsibilities_parties."',`target_audience` = '".$target_audience."',`other_target_audience` = '".$other_target_audience."',`action_steps` = '".$action_steps."',`activities` = '".str_replace("'", "\'", $activities)."', `part_e_save` = '1' WHERE `id` = '".$_POST['partE_edit']."' ";
    $insert=mysql_query($insert_qry);
    if($insert){
       echo $insert_id=$_POST['partE_edit'];
    }else{
        echo '0';
    }
}
if(isset($_POST['WB_update'])){
    $work_bundle             =($_POST['work_bundle']!=''              ? serialize($_POST['work_bundle'])              : $_POST['work_bundle']);
    $description             =($_POST['description']!=''              ? serialize($_POST['description'])              : $_POST['description']);
    $o_start_date            =($_POST['o_start_date']!=''             ? serialize($_POST['o_start_date'])             : $_POST['o_start_date']);
    $o_end_date              =($_POST['o_end_date']!=''               ? serialize($_POST['o_end_date'])               : $_POST['o_end_date']);
    
    $insert_qry= "UPDATE `interventions` SET `work_bundle` = '".$work_bundle."', `description` = '".str_replace("'", "\'", $description)."', `o_start_date` = '".$o_start_date."', `o_end_date` = '".$o_end_date."' WHERE `id` = '".$_POST['intervention_id']."' ";
    $insert=mysql_query($insert_qry);
    if($insert){
       echo $insert_id=$_POST['intervention_id'];
    }else{
        echo '0';
    } 
}
if(isset($_POST['partD_drug_edit'])){
     /* if(is_array($_POST['settings_locations'])){$settings_locations=serialize($_POST['settings_locations']);}
     else{$settings_locations=$_POST['settings_locations'];} */
      
     $intervention_uploads= "SELECT * FROM interventions WHERE id='".$_POST['partD_drug_edit']."' ";
     $uploads_data=mysql_query($intervention_uploads);
     $uploads_row=mysql_fetch_assoc($uploads_data);
     
     $arrayfilename=array();
     $arraydates=array();
     if($uploads_row['uploadfilename']!='')  {
        $arrayfilename=unserialize($uploads_row['uploadfilename']);
        $arrayfoldername=unserialize($uploads_row['uploadfoldername']);
        $arraydates=unserialize($uploads_row['upload_dates']);
		
     } 
     
     
     $newFilePath="";
    if(isset($_SESSION['AttachmentUpload1']) && count($_SESSION['AttachmentUpload1']) > 0){
    foreach($_SESSION['AttachmentUpload1'] as $key => $value){        
        $folderValue[] = $key;
        $fileKey[] = $value;
        $datevalue[] =  date('Y-m-d H:i:s');
    }
	
    $fileName=array();
    $folderNameRandom=array();
    $newdates=array();
    if(count($arrayfilename) > 0){
        $fileName = array_merge($arrayfilename,$fileKey);
        $folderNameRandom = array_merge($arrayfoldername,$folderValue);
        $newdates=array_merge($arraydates,$datevalue);
    }else{
        $fileName = $fileKey;
        $folderNameRandom = $folderValue;
        $newdates=$datevalue;
    }
    
    $uploadfilename = serialize($fileName);    
    $uploadfoldername = serialize($folderNameRandom);
    $uploaddates = serialize($newdates);
    $ebp_upload=" ,	uploadfoldername='".$uploadfoldername."' ,uploadfilename='".$uploadfilename."' ,upload_dates='".$uploaddates."'";
    
    unset($_SESSION['AttachmentUpload']);
    unset($_SESSION['AttachmentUpload1']);
    }else{
      $ebp_upload='';
    }
     
    $EBP =(isset($_POST['EBP']) ? $_POST['EBP'] : '');
	$strategy_model =(isset($_POST['strategy_model']) ? $_POST['strategy_model'] : '');
	$target_population =($_POST['target_population']!='' ?serialize($_POST['target_population'])  : $_POST['target_population']);
	$types_of_participants =($_POST['types_of_participants']!='' ?serialize($_POST['types_of_participants'])  : $_POST['types_of_participants']);
	$types_of_populations =($_POST['types_of_populations']!='' ?serialize($_POST['types_of_populations'])  : $_POST['types_of_populations']);
	$alternative_participants =($_POST['alternative_participants']!='' ?serialize($_POST['alternative_participants'])  : $_POST['alternative_participants']);
	$intervention_type =(isset($_POST['intervention_type']) ? $_POST['intervention_type'] : '');
	$sessions =(isset($_POST['sessions']) ? $_POST['sessions'] : '');
	$avg_length_session =(isset($_POST['avg_length_session']) ? $_POST['avg_length_session'] : '');
	$frequency =(isset($_POST['frequency']) ? $_POST['frequency'] : '');
    $cycles_type =(isset($_POST['cycles_type']) ? $_POST['cycles_type'] : '');
	$alternative_activity =($_POST['alternative_activity']!='' ?serialize($_POST['alternative_activity'])  : $_POST['alternative_activity']);
	$alternative_activity_types =($_POST['alternative_activity_types']!='' ?serialize($_POST['alternative_activity_types'])  : $_POST['alternative_activity_types']);
	$num_serve =(isset($_POST['num_serve']) ? $_POST['num_serve'] : '');
	$activity_name =(isset($_POST['activity_name']) ? $_POST['activity_name'] : '');
	$activity_types =(isset($_POST['activity_types']) ? $_POST['activity_types'] : '');
	$location_name =(isset($_POST['location_name']) ? $_POST['location_name'] : '');
	$city_name =(isset($_POST['city_name']) ? $_POST['city_name'] : '');
	$street_address =(isset($_POST['street_address']) ? $_POST['street_address'] : '');
	$zip_code =(isset($_POST['zip_code']) ? $_POST['zip_code'] : '');
    
    //$insert_qry= "UPDATE `interventions` SET `EBP` = '".str_replace("'", "\'", $EBP."', `strategy_model` = '".str_replace("'", "\'", $strategy_model)."', `strategy_model_other` = '".$_POST['strategy_model_other']."', `target_population` = '".str_replace("'", "\'", $target_population)."', `types_of_participants_other` = '".str_replace("'", "\'", $_POST['types_of_participants_other'])."', `types_of_participants` ='".str_replace("'", "\'", $types_of_participants)."', `types_of_participants_other` ='".str_replace("'", "\'", $_POST['types_of_participants_other'])."', `types_of_populations` = '".str_replace("'", "\'", $types_of_populations)."', `types_of_populations_other` = '".str_replace("'", "\'", $_POST['types_of_populations_other'])."', `alternative_participants` = '".str_replace("'", "\'", $alternative_participants)."', `intervention_type` = '".str_replace("'", "\'", $intervention_type)."', `sessions` = '".str_replace("'", "\'", $sessions)."', `avg_length_session` = '".str_replace("'", "\'",$avg_length_session)."', `frequency` = '".str_replace("'", "\'", $frequency)."', `frequency_other` = '".str_replace("'", "\'", $_POST['frequency_other'])."', `cycles_type` = '".str_replace("'", "\'",$cycles_type)."', `types_of_participants_other` = '".str_replace("'", "\'", $_POST['types_of_participants_other'])."', `alternative_activity` = '".str_replace("'", "\'", $alternative_activity)."', `alternative_activity_other` = '".str_replace("'", "\'", $_POST['alternative_activity_other'])."', `alternative_activity_types` ='".str_replace("'", "\'", $alternative_activity_types)."', `alternative_activity_types_other` ='".str_replace("'", "\'", $_POST['alternative_activity_types_other'])."', `num_serve` ='".str_replace("'", "\'", $num_serve)."', `activity_name` = '".str_replace("'", "\'", $activity_name)."', `activity_types` = '".str_replace("'", "\'", $activity_types)."', `activity_types_other` = '".str_replace("'", "\'", $_POST['activity_types_other'])."', `location_name` = '".str_replace("'", "\'", $location_name)."', `city_name` = '".str_replace("'", "\'", $city_name)."', `street_address` = '".str_replace("'", "\'", $street_address)."', `zip_code` = '".str_replace("'", "\'", $zip_code)."', `part_d_save` = '1' ".$ebp_upload." WHERE `id` = '".$_POST['partD_drug_edit']."' ";
    
    $insert=mysql_query($insert_qry);
    if($insert){
       echo $insert_id=$_POST['partD_drug_edit'];
    }else{
        echo '0';
    } 
}
 ?>