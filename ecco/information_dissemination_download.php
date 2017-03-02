<?php 
include_once('config.php');
$id = safe_b64decode($_GET['intervention_id']);
/* echo $id; */
    $intervention_query= "SELECT * FROM interventions WHERE id='".$id."' ";
    $intervention_data=mysql_query($intervention_query);
    $intervention_row=mysql_fetch_assoc($intervention_data);
    if($intervention_row['community']!=''){
       $communities=unserialize($intervention_row['community']); 
       $community_count=count($communities['name']);
    }else{
       $communities=$intervention_row['community']; 
       $community_count=0;
    }
	$in_comm_mem= ($intervention_row['in_comm_mem']!=''?unserialize($intervention_row['in_comm_mem']):array());
	
	$work_bundle             =($intervention_row['work_bundle']!=''              ? unserialize($intervention_row['work_bundle'])              : $intervention_row['work_bundle']);
	$description             =($intervention_row['description']!=''              ? unserialize($intervention_row['description'])              : $intervention_row['description']);
	$start_date              =($intervention_row['start_date']!=''               ? unserialize($intervention_row['start_date'])               : $intervention_row['start_date']);
	$end_date                =($intervention_row['end_date']!=''                 ? unserialize($intervention_row['end_date'])                 : $intervention_row['end_date']);
	$like_training           =($intervention_row['like_training']!=''            ? unserialize(str_replace('-','',$intervention_row['like_training']))            : $intervention_row['like_training']);
	$about_training          =($intervention_row['about_training']!=''           ? unserialize($intervention_row['about_training'])           : $intervention_row['about_training']);
	$responsibilities_parties=($intervention_row['responsibilities_parties']!='' ? unserialize($intervention_row['responsibilities_parties']) : $intervention_row['responsibilities_parties']);
	$action_steps            =($intervention_row['action_steps']!=''             ? unserialize($intervention_row['action_steps'])             : $intervention_row['action_steps']);
	$activities              =($intervention_row['activities']!=''               ? unserialize(str_replace('-','',$intervention_row['activities']))               : $intervention_row['activities']);
	$target_audience         =($intervention_row['target_audience']!=''          ? unserialize($intervention_row['target_audience'])          : $intervention_row['target_audience']);
	$other_target_audience   =($intervention_row['other_target_audience']!=''    ? unserialize($intervention_row['other_target_audience'])    : $intervention_row['other_target_audience']);
	$end_status              =($intervention_row['end_status']!=''    ? unserialize($intervention_row['end_status'])    : $intervention_row['end_status']);
	$ongoing_explain         =($intervention_row['ongoing_explain']!=''    ? unserialize($intervention_row['ongoing_explain'])    : $intervention_row['ongoing_explain']);

	function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
?>
<section>
<div class="container header-block">
<h1 class="top-title"><?php echo $intervention_row['intervention_name']; ?></h1>
<table cellspacing="0">
	<tr class="bg-gray">
		<th style="width: 5%; text-align:left;" class="pad15">S.No</th>
		<th style="width: 30%; text-align:left;" class="pad15">PART A</th>
		<th style="width: 65%;" class="pad15">&nbsp;</th>
	</tr>
	<tr>
		<th style="width: 5%; text-align:left;">&nbsp;</th>
		<th style="width: 30%; text-align:left;" class="blue-txt pad15">Fillers Details</th>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">1</td>
		<td class="pad15" style="width: 30%; text-align:left;">Fillers Name</td>
		<td class="pad15" style="width: 75%;"><?php echo $intervention_row['fillers_name']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">2</td>
		<td class="pad15" style="width: 30%; text-align:left;">Fillers Email</td>
		<td class="pad15" style="width: 75%;"><?php echo $intervention_row['fillers_email']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">3</td>
		<td class="pad15" style="width: 30%; text-align:left;">Fillers Phone No</td>
		<td class="pad15" style="width: 75%;"><?php echo $intervention_row['fillers_phoneno']; ?></td>
	</tr>
	<?php 
	if($community_count>0){
	for($c=0;$c<$community_count;$c++){ 
	$d = $c+1; ?>
	<tr>
		<th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
		<th class="pad15 blue-txt" style="width: 30%; text-align:left;">Community<?php echo $d; ?></th>
		<td class="pad15" style="width: 75%;">&nbsp;</td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">4</td>
		<td class="pad15" style="width: 30%; text-align:left;">Community Name</td>
		<td class="pad15" style="width: 75%;"><?php echo $communities['name'][$c]; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">5</td>
		<td class="pad15" style="width: 30%; text-align:left;">Community Zipcode</td>
		<td class="pad15" style="width: 75%;"><?php echo $communities['zip'][$c]; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">6</td>
		<td class="pad15" style="width: 30%; text-align:left;">Community Date</td>
		<td class="pad15" style="width: 75%;"><?php echo $communities['date'][$c]; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">7</td>
		<td class="pad15" style="width: 30%; text-align:left;">Community Score</td>
		<td class="pad15" style="width: 75%;"><?php echo $communities['score'][$c]; ?></td>
	</tr>
	<?php } } ?>
	<tr>
		<th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
		<th class="pad15 blue-txt" style="width: 30%; text-align:left;">Manager Details</th>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">8</td>
		<td class="pad15" style="width: 30%; text-align:left;">Manager Name</td>
		<td class="pad15" style="width: 75%;"><?php echo $intervention_row['manager_name']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">9</td>
		<td class="pad15" style="width: 30%; text-align:left;">Manager Email</td>
		<td class="pad15" style="width: 75%;"><?php echo $intervention_row['manager_email']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">10</td>
		<td class="pad15" style="width: 30%; text-align:left;">Manager Phone No</td>
		<td class="pad15" style="width: 75%;"><?php echo $intervention_row['manager_phoneno']; ?></td>
	</tr>
	<tr class="bg-gray">
		<th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
		<th class="pad15" style="width: 30%; text-align:left;">PART B</th>
		<th class="pad15" style="width: 65%; text-align:left;">&nbsp;</th>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">1</td>
		<td class="pad15" style="width: 30%; text-align:left;">Problem to be Addressed</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['addressed_issue']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">2</td>
		<td class="pad15" style="width: 30%; text-align:left;">Approved State Priority</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['approved_state_priority']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">3</td>
		<td class="pad15" style="width: 30%; text-align:left;">Strategy Type</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['strategy_type'];?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">4</td>
		<td class="pad15" style="width: 30%; text-align:left;">IOM Category</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['IOM_category']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">5</td>
		<td class="pad15" style="width: 30%; text-align:left;">Service Type</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['service_type']; ?></td>
	</tr>
	<tr>
		<th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
		<th class="pad15 blue-txt" style="width: 30%; text-align:left;">Risk &amp; Protective / Contributing Factors</th>
	</tr>
	<?php 
		$inter_cf_count=1;
		$c=1;
		if($intervention_row['IVs']!=''){ 
			$cfs=unserialize($intervention_row['IVs']);
			$inter_cf_count=count($cfs['cf'] );
		for($iv=0;$iv<$inter_cf_count;$iv++){ ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">6</td>
		<td class="pad15" style="width: 10%; text-align:left;">Risk &amp; Protective<?php echo $c; ?></td>
		<td class="pad15" style="width: 10%; text-align:left;"><?php echo $cfs['context'][$iv] ?></td>
	</tr>
	<?php $b = 1; for($a=0;$a<count($cfs['cf'][$iv]);$a++) { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">&nbsp;</td>
		<td class="pad15" style="width: 10%; text-align:left;">Contributing Factors<?php echo $b; ?></td>
		<td class="pad15" style="width: 10%; text-align:left;"><?php echo $cfs['cf'][$iv][$a] ?></td>
	</tr>
	<?php $b++; } ?>
	<?php $c++;} } ?>
	<tr class="bg-gray">
		<th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
		<th class="pad15" style="width: 30%; text-align:left;">PART C</th>
		<th class="pad15" style="width: 65%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">1</td>
		<td class="pad15" style="width: 30%; text-align:left;">Upload supporting documentation</td>
		<?php 
			$arrayfilename=implode("",unserialize($intervention_row['uploadfilename']));
			$fileCount = count($arrayfilename);
			$file=1;
			for($f=0;$f<$fileCount;$f++){   if(!empty($arrayfilename[$f])&&!empty($arrayfilename[$f])){
		?>
		<td class="pad15" style="width: 30%; text-align:left;"><?php echo $arrayfilename; ?></td>
		<?php } } ?>
	</tr>
	<tr class="bg-gray">
		<th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
		<th class="pad15" style="width: 30%; text-align:left;">PART D</th>
		<th class="pad15" style="width: 65%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">1</td>
		<td class="pad15" style="width: 30%; text-align:left;">Is this an evidence-based program (EBP) ?</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['EBP']; ?></td>
	</tr>
	<?php if($intervention_row['EBP']=='Yes'){ ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">2</td>
		<td class="pad15" style="width: 30%; text-align:left;">Strategy Model</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['strategy_model']; ?></td>
	</tr>
	<?php if($intervention_row['strategy_model']=='Other') { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;"></td>
		<td class="pad15" style="width: 30%; text-align:left;">Other Strategy Model</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['strategy_model_other']; ?></td>
	</tr>
	<?php } } ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">3</td>
		<td class="pad15" style="width: 30%; text-align:left;">Upload supporting documentation</td>
		<?php 
		$arrayfilename=implode("",unserialize($intervention_row['uploadfilename']));
		$fileCount = count($arrayfilename);
		$file=1;
		for($f=0;$f<$fileCount;$f++){   if(!empty($arrayfilename[$f])&&!empty($arrayfilename[$f])){
		?>
		<td class="pad15" style="width: 30%; text-align:left;"><?php echo $arrayfilename; ?></td>
		<?php $file++; }} ?>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">4</td>
		<td class="pad15" style="width: 30%; text-align:left;">What is/are the intended purpose(s) of the communication or information that you will disseminate for this intervention? (Select all that apply)</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['in_intended_purpose']; ?></td>
	</tr>
	<?php if($intervention_row['in_intended_purpose']=="Other") { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;"></td>
		<td class="pad15" style="width: 30%; text-align:left;">Other</td>
		<td class="pad15" style="width: 30%; text-align:left;"><?php echo $intervention_row['in_intended_purpose_other']; ?></td>
	</tr>
	<?php } ?>
	<?php $target_population1 = implode(",",$target_population); ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">5</td>
		<td class="pad15" style="width: 30%; text-align:left;">Does this intervention include communication efforts to raise community awareness of underage alcohol use, marijuana use, or prescription drug misuse problems?</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $target_population1; ?></td>
	</tr>
	<?php if(in_array('Other',$target_population)) { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;"></td>
		<td class="pad15" style="width: 30%; text-align:left;">Other</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['target_population_other']; ?></td>
	</tr>	
	<?php } ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">6</td>
		<td class="pad15" style="width: 30%; text-align:left;">Socio-Ecological targets for this intervention</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['socio_ecological']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">7</td>
		<td class="pad15" style="width: 30%; text-align:left;">Is this a recurring intervention in which the same group of people are served over multiple sessions?</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['intervention_type']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">7a</td>
		<td class="pad15" style="width: 30%; text-align:left;">Average number of sessions per group</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['sessions']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">7b</td>
		<td class="pad15" style="width: 30%; text-align:left;">Select frequency of sessions per group</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['frequency']; ?></td>
	</tr>
	<?php if($intervention_row['frequency']=='Other') { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;"></td>
		<td class="pad15" style="width: 30%; text-align:left;">Other</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['frequency_other']; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">8</td>
		<td class="pad15" style="width: 30%; text-align:left;">What is the average length of each session (in hours) that will be offered?</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['time_unit']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">9</td>
		<td class="pad15" style="width: 30%; text-align:left;">Will this intervention be implemented in a series of cycles?</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['cycles_type']; ?></td>
	</tr>
	<?php if($intervention_row['cycles_type']=='Yes') { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;"></td>
		<td class="pad15" style="width: 30%; text-align:left;">Number of cycles to be implemented this year?</td>
		<td class="pad15" style="width: 30%; text-align:left;"><?php echo $intervention_row['cycles']; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">10</td>
		<td class="pad15" style="width: 30%; text-align:left;">What are the formats of the prevention education intervention you will be implementing?</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['prevention_education_intervention']; ?></td>
	</tr>
	<?php if($intervention_row['prevention_education_intervention']='Other') { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;"></td>
		<td class="pad15" style="width: 30%; text-align:left;">Other</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['prevention_education_intervention']; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">11</td>
		<td class="pad15" style="width: 30%; text-align:left;">Total number to be served or reached by this intervention</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $intervention_row['numbers_served_reached'];?></td>
	</tr>
	<tr>
		<th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
		<th class="pad15 blue-txt" style="width: 30%; text-align:left;">Setting/Location</th>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">12</td>
		<td class="pad15" style="width: 30%; text-align:left;">Setting(s)/Location(s)</td>
	</tr>
		<?php
		$settings_locations=unserialize($intervention_row['settings_locations']);
		$activity_location_count=count($settings_locations);
		$settings_locations_count= ($activity_location_count<1 ? 1 : $activity_location_count);
		for($c=1;$c<=$settings_locations_count;$c++){?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">&nbsp;</td>
		<td class="pad15" style="width: 30%; text-align:left;">Setting/Location<?php echo $c; ?></td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $settings_locations[$c]['setting']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">&nbsp;</td>
		<td class="pad15" style="width: 30%; text-align:left;">Location</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $settings_locations[$c]['location']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">&nbsp;</td>
		<td class="pad15" style="width: 30%; text-align:left;">City</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $settings_locations[$c]['city']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">&nbsp;</td>
		<td class="pad15" style="width: 30%; text-align:left;">Street Address</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $settings_locations[$c]['street_address']; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">&nbsp;</td>
		<td class="pad15" style="width: 30%; text-align:left;">Zip</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $settings_locations[$c]['zip']; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<th class="pad15" style="width: 5%; text-align:left;"></th>
		<th class="pad15" style="width: 30%; text-align:left;">PART E</th>
		<td class="pad15" style="width: 65%; text-align:left;">&nbsp;</td>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
        <th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
	</tr>
	<?php 
		$work_bundle_count=1;
		$w=0;
		if($work_bundle!=''){
		$work_bundle_count=count($work_bundle);
		$s = 1;
		for($w=0;$w<count($work_bundle);$w++){
	?>
	<tr>
		<th class="pad15" style="width: 5%; text-align:left;">&nbsp;</th>
		<th class="pad15 blue-txt" style="width: 30%; text-align:left;">Work Bundle<?php echo $s; ?></th>
		<th class="pad15" style="width: 65%; text-align:left;">&nbsp;</th>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">1</td>
		<td class="pad15" style="width: 30%; text-align:left;">Work Bundle Name</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $work_bundle[$w]; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">2</td>
		<td class="pad15" style="width: 30%; text-align:left;">Work Bundle Description</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $description[$w]; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">3</td>
		<td class="pad15" style="width: 30%; text-align:left;">Responsible Parties</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $responsibilities_parties[$w]; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">4</td>
		<td class="pad15" style="width: 30%; text-align:left;">Target Audience</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $target_audience[$w]; ?></td>
	</tr>
	<?php if($target_audience[$w]!='Others') { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;"></td>
		<td class="pad15" style="width: 30%; text-align:left;">Other Target Audience</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $other_target_audience[$w]; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">5</td>
		<td class="pad15" style="width: 30%; text-align:left;">Is this Work Bundle ongoing?</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $end_status[$w]; ?></td>
	</tr>
	<?php if($end_status[$w]=='Yes'){ ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">5a</td>
		<td class="pad15" style="width: 30%; text-align:left;">Brief explanation</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $ongoing_explain[$w]; ?></td>
	</tr>
	<?php } ?>
	<?php if($end_status[$w]=='No') { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">5b</td>
		<td class="pad15" style="width: 30%; text-align:left;">Projected Start Date</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $start_date[$w]; ?></td>
	</tr>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">5b</td>
		<td class="pad15" style="width: 30%; text-align:left;">Projected End Date</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $end_date[$w]; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">6</td>
		<td class="pad15" style="width: 30%; text-align:left;">Would you like Training or TA on this Work Bundle?</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $like_training[$w]; ?></td>
	</tr>
	<?php if($like_training[$w]=='Yes') { ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;"></td>
		<td class="pad15" style="width: 30%; text-align:left;">Tell us more</td>
		<td class="pad15" style="width: 65%; text-align:left;"><?php echo $about_training[$w]; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">7</td>
		<td class="pad15" style="width: 30%; text-align:left;">Action Steps</td>
	</tr>
	<?php for($a=0;$a<count($activities[$w]);$a++) {?>
	<tr>
		<td class="pad15" style="width: 5%; text-align:left;">&nbsp;</td>
		<td class="pad15" style="width: 30%; text-align:left;"><?php echo $activities[$w][$a];?></td>
	</tr>
	<?php } $s++; } } ?>
</table>
</div>
</section>			



