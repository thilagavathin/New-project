<?php include_once('templates/header.php');
if($_SESSION['userrole']==3) {
    header('Location:dashboard.php'); die;
}
?>
     		<section >
			<?php
			$sql_tta = "SELECT `TTA_Forms`.`contract_num`,`agency`.`id`, `agency`.`user_updated`, `agency`.`name`, `agency`.`street`, `agency`.`apt`, `agency`.`city`, `agency`.`state`, `agency`.`zip`, `agency`.`phone`, `agency`.`fax`, `agency`.`manager_name`, `agency`.`alt_num`, `agency`.`SPG_SIG_priority`, `agency`.`SPG_SIG_priority_notes`, `agency`.`SPG_SIG_outcome`, `agency`.`SPG_SIG_outcome_notes`, `agency`.`date_updated`, `TTA_Forms`.`agency_id`, `TTA_Forms`.`contract_num`, `TTA_Forms`.`updated_date`, `TTA_Forms`.`user_updated`, `TTA_Forms`.`status`, `TTA_Forms`.`TTA_inquiry_type`, `TTA_Forms`.`TTA_inquiry_notes`, `TTA_Forms`.`SPF_steps_assessment`,
            `TTA_Forms`.`SPF_align_capacity`, `TTA_Forms`.`SPF_align_planning`, `TTA_Forms`.`SPF_align_implment`,
            `TTA_Forms`.`SPF_align_evaluation`, `TTA_Forms`.`SPF_align_competency`, `TTA_Forms`.`SPF_align_sustainability`,
            `TTA_Forms`.`SPF_align_notes`, `TTA_Forms`.`SPF_steps_capacity`, `TTA_Forms`.`SPF_steps_planning`,
            `TTA_Forms`.`SPF_steps_implment`, `TTA_Forms`.`SPF_steps_evaluation`, `TTA_Forms`.`SPF_steps_notes`,
            `TTA_Forms`.`SPF_align_assessment`, `TTA_Forms`.`TTA_problem_addressed`, `TTA_Forms`.`TTA_problem_addressed_notes`,
            `TTA_Forms`.`TTA_desc`, `TTA_Forms`.`TTA_desc_notes`, `TTA_Forms`.`TTA_outcome`, `TTA_Forms`.`TTA_outcome_notes`,
            `TTA_Forms`.`TTA_Referral`, `TTA_Forms`.`TTA_Contact_Phone`, `TTA_Forms`.`TTA_Email`, `TTA_Forms`.`timeframe`,
            `TTA_Forms`.`timeframe_notes`, `TTA_Forms`.`assigned_staff`, `TTA_Forms`.`prelim_result`, `TTA_Forms`.`TTA_service_scheduled`,
            `TTA_Forms`.`supporting_docs`, `TTA_Forms`.`TTA_service_provider`, `TTA_Forms`.`results`,`TTA_Forms`.`assignedUser`
            FROM `agency`
            INNER JOIN `TTA_Forms`
            ON `TTA_Forms`.`agency_id` = `agency`.`id`
            WHERE `contract_num` = '".$_GET['contract_num']."'";
			$result_tta = mysql_query($sql_tta) or die(mysql_error());
			$row_tta = mysql_fetch_array($result_tta);		
			$phoneFormat = preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($row_tta['phone'])), 2);
			$altNumFormat = preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($row_tta['alt_num'])), 2);
            $faxFormat = preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($row_tta['fax'])), 2);
			?>
	     		<div class="container">
	     			 <div class="row text-center">
                <div class="col-md-8 col-sm-10 col-sm-offset-1  col-md-offset-2 col-xs-12">
                    <div class="form_msgbox">
                        <h1 class="text_blue msg_title"><i class="fa fa-thumbs-o-up"></i> <span class="fb_300">Created Successfully!</span></h1>
                        <p><button class="button" onclick="createnewform()"><i class="fa fa-plus-circle"></i>Create Another TTA Form</button></p>
                        <p><button class="button form_btn" onclick="dashboard()">Show All Forms</button></p>
                    </div>
                </div>
             </div>
          </div>
     		</section>
	<script>
	function createnewform(){
	window.location.href = "createagencyform.php"
	}
	function dashboard(){
	window.location.href = "dashboard.php"
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
<?php include_once('templates/footer.php'); ?>    
  </body>
</html>
