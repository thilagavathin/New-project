<?php include_once('templates/header.php');
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
						  <li><a href="agencies.php">Agency Admin</a></li>
						  <li class="active">Edit agency </li>              
						</ol>
					</div>
				 </div>
				 
	     			<div class="row">
                <div class="col-sm-12">
                <?php
				$sql_agency = "SELECT * FROM `agency` WHERE `id` = '".$_REQUEST['aid']."'";
				$result_agency = mysql_query($sql_agency) or die(mysql_error());
				$row = mysql_fetch_array($result_agency);
				?>
                  <h1 class="page-title">edit agency</h1>
                  <h2 class="text-center mar_tb30">Agency Name <span class="text_blue"><?php if($row['name'] != ""){ echo $row['name']; } ?></span></h2>
                </div>
            </div>
            <form role="form" autocomplete="off" action="#" method="POST">
            <div class="row form">
               <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" placeholder="" value="<?php if($row['name'] != ""){ echo $row['name']; } ?>">
                 </div>
                 <div class="form-group">
                    <label>Region</label>
                    <select class="form-control" data-init-plugin="cs-select" name="region">
                          <option value="">Select Region</option>
                          <option value="R-1" <?php if($row['region'] == "R-1"){ ?>selected<?php } ?> >R-1</option>
                          <option value="R-2" <?php if($row['region'] == "R-2"){ ?>selected<?php } ?> >R-2</option>
                          <option value="R-3" <?php if($row['region'] == "R-3"){ ?>selected<?php } ?> >R-3</option>
                          <option value="R-4" <?php if($row['region'] == "R-4"){ ?>selected<?php } ?> >R-4</option>
                          <option value="R-5" <?php if($row['region'] == "R-5"){ ?>selected<?php } ?> >R-5</option>
                          <option value="R-6" <?php if($row['region'] == "R-6"){ ?>selected<?php } ?> >R-6</option>
                    </select>
                 </div>
                 <div class="form-group">
                    <label>Address</label>
                    <div class="row" >
                      <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="Street" name="street" value="<?php if($row['street'] != ""){ echo $row['street']; } ?>"></span>
                      <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="Apatment Name" name="apt_name" value="<?php if($row['apt'] != ""){ echo $row['apt']; } ?>"></span>
                      <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="City" value="<?php if($row['city'] != ""){ echo $row['city']; } ?>" name="city"></span>
                      <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="State" value="<?php if($row['state'] != ""){ echo $row['state']; } ?>" name="state"></span>
                      <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="Zip code" value="<?php if($row['zip'] != ""){ echo $row['zip']; } ?>" name="zip_code"></span>
                    </div>
                 </div>
                 
               </div>
               <div class="col-md-4 col-sm-4 col-xs-12">
                   <div class="form-group">
                      <label>Phone &amp; Fax</label>
                      <div class="row">
                        <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="Phone Number" name="phone" value="<?php if($row['phone'] != ""){ echo $row['phone']; } ?>"></span>
                        <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="Fax Number" name="fax" value="<?php if($row['fax'] != ""){ echo $row['fax']; } ?>"></span>
                      </div>
                   </div>
                   <div class="form-group">
                        <label>Manager Details</label>
                        <div class="row">
                          <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="Name" name="manager_name" value="<?php if($row['manager_name'] != ""){ echo $row['manager_name']; } ?>"></span>
                          <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="Phone Number" name="manager_number" value="<?php if($row['alt_num'] != ""){ echo $row['alt_num']; } ?>"></span>
                        </div>
                   </div>
               </div>
               <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>Priority</label>
                        <div class="row">
                          <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="" name="spg_sig_priority" value="<?php if($row['SPG_SIG_priority'] != ""){ echo $row['SPG_SIG_priority']; } ?>"></span>
                          <span class="col-sm-12 form-group"><textarea class="form-control" name="spg_sig_priority_notes" id="spg_sig_priority_notes" placeholder="Note"><?php if($row['SPG_SIG_priority_notes'] != ""){ echo $row['SPG_SIG_priority_notes']; } ?></textarea></span>
                        </div>
                     </div>
                     <div class="form-group">
                        <label>Outcome</label>
                        <div class="row">
                          <span class="col-sm-12 form-group"><input type="text" class="form-control" placeholder="" name="spg_sig_outcome" value="<?php if($row['SPG_SIG_outcome'] != ""){ echo $row['SPG_SIG_outcome']; } ?>"></span>
                          <span class="col-sm-12 form-group"><textarea class="form-control" name="spg_sig_outcome_notes" id="spg_sig_outcome_notes" placeholder="Note"><?php if($row['SPG_SIG_outcome_notes'] != ""){ echo $row['SPG_SIG_outcome_notes']; } ?></textarea></span>
                        </div>
                     </div>
                     <input type="hidden" name="agency_id" value="<?php echo $_REQUEST['aid']; ?>">
               </div>
               <div class="col-xs-12 col-sm-12 text-center mar_b20">
                  <button type="button" onClick="ajax_agency_update();">Save</button>
                   <button id="cancel" class="mar_l10 cancel_btn" >Cancel</button> 
               </div>
            </div>
            </form>
           </div>
	     		</div>
     		</section>
<?php include_once('templates/footer.php'); ?>
   
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
    <script type="text/javascript">
      
      $('.comment-box .modal-content').slimScroll({
        position: 'right',
        height:'100%',
        railVisible: true,
        alwaysVisible: true
    });
    </script>
    <script>
    function ajax_agency_update()
    {
        var agency_id= $('input[name="agency_id"]').val();
        var name= $('input[name="name"]').val();
       var region= $('select[name="region"]').val();
        var street= $('input[name="street"]').val();
        var apt_name= $('input[name="apt_name"]').val();
        var city= $('input[name="city"]').val();
        var state= $('input[name="state"]').val();
        var zip_code= $('input[name="zip_code"]').val();
        var phone= $('input[name="phone"]').val();
        var fax= $('input[name="fax"]').val();
        var manager_name= $('input[name="manager_name"]').val();
        var manager_number= $('input[name="manager_number"]').val();
        var spg_sig_priority= $('input[name="spg_sig_priority"]').val();
        var spg_sig_priority_notes= document.getElementById("spg_sig_priority_notes").value;
        var spg_sig_outcome= $('input[name="spg_sig_outcome"]').val();
        var spg_sig_outcome_notes= document.getElementById("spg_sig_outcome_notes").value;
        var formData = {agency_id:agency_id,name:name,region:region,street:street,apt_name:apt_name,city:city,state:state,zip_code:zip_code,phone:phone,fax:fax,manager_name:manager_name,manager_number:manager_number,spg_sig_priority:spg_sig_priority,spg_sig_priority_notes:spg_sig_priority_notes,spg_sig_outcome:spg_sig_outcome,spg_sig_outcome_notes:spg_sig_outcome_notes};
        $.ajax({
            url : "ajax_agency_update.php",
            type: "POST",
            data : formData,
            
            success: function(data, textStatus, jqXHR)
            {
               if(data=='success') window.location = "agencies.php";
                else alert('Due to internet problem not reachable database ,Try again');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
</script>
<script type="text/javascript">
    document.getElementById("cancel").onclick = function () {
        location.href = "http://localhost:8080/dev_ecco/agencies.php";
    };
</script>
