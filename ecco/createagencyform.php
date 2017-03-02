<?php
include_once('templates/header.php');
if($_SESSION['userrole']==3 || $_SESSION['userrole']==2) {
    header('Location:dashboard.php'); die;
}
?>
     		<section >
	     		<div class="container">
				
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li class="active">Ecco Account </li>              
						</ol>
					</div>
				 </div>
				
					<?php
					$agency_id = "";
					$assignedUser = "";
					if(isset($_GET['contract_num'])){
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
					$agency_id = $row_tta['agency_id'];
					$assignedUser = $row_tta['assignedUser'];
					}
					?>
					<form role="form" autocomplete="off" action="insert_agencyform.php" method="post">
	     			<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">Ecco Account <small class="text_black fb_300">(Level Control)</small></h1>
                  <h3 class="text-center text_blue mar_tb30">Please Enter a contract number while Assigning an <span class="text_black">Agency</span> and <span class="text_black">User Name </span></span></h3>
                </div>
            </div>
            <div class="row form">
               <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="form-group">
                    <label>Contract Number</label>
                    <div class="row">
					<?php if(isset($_GET['contract_num'])){ ?>
                      <span class="col-sm-4 col-xs-12 form-group"><input type="text" class="form-control " placeholder="Name" id="exampleInputAmount" name="contract_numGET" placeholder="" readonly value="<?php echo $_GET['contract_num'] ?>"></span>
					<?php }else{ ?>
					  <span class="col-sm-4 col-xs-12 form-group"><input type="text" class="form-control " name="contract_num" id="exampleInputAmount" style="text-transform:uppercase" placeholder="" required></span>
                      <span class="col-sm-8 col-xs-12 form-group"><input type="text" class="form-control " id="exampleInputAmount" name="contract_num_rand" placeholder="" readonly value="<?php echo rand(); ?>"></span>
					<?php } ?>
                    </div>
                 </div>
               </div>
			   <?php
					if($_SESSION['userrole']==2){
					$where_login_users=" WHERE user_level NOT LIKE '%\"1\"%'";    
					}else{
					$where_login_users='';
					}                        
					$sql_name = "SELECT username FROM login_users ".$where_login_users;
					$result_name = mysql_query($sql_name) or die(mysql_error()); 
				?>
               <div class="col-md-4 col-sm-4 col-xs-12">
                   <div class="form-group">
                      <label>Choose User Name</label>
                      <select class="form-control" name="username" data-init-plugin="select2" required onchange="check_user_role(this.value);">
						<option value=""> Select User Name </option>
							<?php while($row_name = mysql_fetch_array($result_name)) {
							if($row_name['username']<>'admin')
							{
							?>
							<option value="<?php echo $row_name['username']; ?>" <?php if($assignedUser == $row_name['username']){ ?>selected<?php } ?> ><?php echo $row_name['username']; ?></option>
							<?php
							}
							?>
							<?php } ?>
					  </select>
                   </div>
               </div>
			   <?php 
					$sql_agency = "SELECT id, user_updated, name, street, apt, city, state, zip, phone, fax, manager_name, alt_num, SPG_SIG_priority, SPG_SIG_priority_notes, SPG_SIG_outcome, SPG_SIG_outcome_notes, date_updated FROM agency";
					$result_agency = mysql_query($sql_agency) or die(mysql_error()); 
			   ?>
               <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="form-group" id="demo">
                    <label>Choose Agency</label>
                    <select class="form-control" name="agency_id" data-init-plugin="select2" required>
					<?php while($row_agency = mysql_fetch_array($result_agency)) { ?>
						<option value="<?php echo $row_agency['id']; ?>" <?php if($agency_id == $row_agency['id']){ ?>selected<?php } ?> ><?php echo $row_agency['name']; ?></option>
						<?php } ?>
					</select>
                 </div>
               </div>
               <div class="col-xs-12 col-sm-12 text-center mar_b20">
			   <?php if(isset($_GET['contract_num'])){ ?>
                  <button type="submit">Create</button>
			   <?php }else{ ?>
				  <button type="submit">Create</button>
			   <?php } ?>
                  <button class="mar_l10 cancel_btn">Clear</button>
               </div>
            </div>
			</form>
           </div>
     		</section> 
	<script>
    function check_user_role(name)
    {
        $.ajax({url: "select_agency.php?id="+name, success: function(result){
            $("#demo").html(result);
        }});
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
    <!-- slim scroll for attachment panels -->
 
<?php include_once('templates/footer.php'); ?>
  </body>
</html>
