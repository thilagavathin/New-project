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
						  <li class="active">Agency Admin </li>              
						</ol>
					</div>
				 </div>
				 
	     			<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">Agency Admin</h1>
                </div>
            </div>
            
            <div class="row search_user vcenter">
               <div class="col-md-2">
               <?php
                $sql_agency = "SELECT * FROM `agency`";
    			$result_agency = mysql_query($sql_agency) or die(mysql_error());
    			$num_rows_agency = mysql_num_rows($result_agency); 
    			?>
                  <label class="mar0">Total Agencies <span class="text_light_red"><?php echo $num_rows_agency; ?></span></label>
               </div>
               <div class="col-md-7">
                  <div class="form-group mar0">
                     <input type="text" class="form-control user_searchbox" id="search-table" placeholder="Filter by region">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group mar0">
                      <button type="submit" class="button pull-right" data-target="#modalFillIn" data-toggle="modal"><i class="fa fa-plus-circle"></i>Create New Agency</button>
                  </div>
               </div>
            </div>
            <div class="site-table table-responsive" style="overflow-x: visible;">
               <table class="table allUsersList" id="tableWithSearch_agency">
                 <thead>
                   <tr>
                     <th class="left_align">Agency Name</th>
                     <th>Region</th>
                     <th>Manager</th>
                     <th>Phone</th>
                     <th>&nbsp;</th>
                   </tr>
                 </thead>
                 <tbody>
                 	<?php while($row = mysql_fetch_array($result_agency)) { ?>
                    <tr>
                        <td><span><a href="edit_agency.php?aid=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></span></td>
                        <td class="text-center"><?php echo $row['region']; ?></td>
                        <td class="text-center"><?php echo $row['manager_name']; ?></td>
                        <td class="text-center"><?php echo $row['phone']; ?></td>
                        <td>
                            <i class="fa fa-pencil-square-o" onclick="edit_agency(<?php echo $row['id']; ?>)" aria-hidden="true"></i>
                            <i class="fa fa-trash mar_l10" onclick="delete_agency(<?php echo $row['id']; ?>)" aria-hidden="true"></i>
                        </td>
                    </tr>
                   	 <?php } ?>   
                 </tbody>
               </table> 
               
                </div>
          </div>
        <div id="modalFillIn" class="modal comment-box right fade" role="dialog">
            <div class="modal-dialog">
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h3 class="modal-title text_blue">Create Agency</h3>
                </div>
                <div class="modal-body no-shadow">
                  <div class="row" id="comment-form">
                     <div class="col-xs-12 form">
                        <form role="form" autocomplete="off" action="#" method="post">
                          <div class="form-group">
                              <label>Agency Name</label>
                              <input type="text" class="form-control" id="" name="name" placeholder="" value="">
                           </div>
                           <div class="form-group">
                            <label>Region</label>  
                                <select class="form-control" id="" data-init-plugin="cs-select" name="region">
                                  <option value="R-1">R-1</option>
                                  <option value="R-2">R-2</option>
                                  <option value="R-3">R-3</option>
                                  <option value="R-4">R-4</option>
                                  <option value="R-5">R-5</option>
                                  <option value="R-6">R-6</option>
                                  <option value="R-6">ALL</option>
                                  <option value="R-6">NA</option>
                                </select>
                           </div>
                           <div class="form-group">
                              <label>Address</label>
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="street" placeholder="Street" value="">
                              </div>
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="apt_name" placeholder="Apatment Name" value="">
                              </div>
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="city" placeholder="City" value="">
                              </div>
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="state" placeholder="State" value="">
                              </div>
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="zip_code" placeholder="zip_code" value="">
                              </div>
                           </div>
                           <div class="form-group">
                              <label>Phone & Fax</label>
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="phone" placeholder="Phone Number" value="">
                              </div> 
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="fax" placeholder="Fax Number" value="">
                              </div> 
                           </div> 
                           <div class="form-group">
                              <label>Manager Details</label>
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="manager_name" placeholder="Name" value="">
                              </div> 
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="manager_number" placeholder="Phone Number" value="">
                              </div> 
                           </div> 
                           <div class="form-group">
                              <label>Priority</label>
                              <div class="form-group">
                                <input type="text" class="form-control" id="" name="spg_sig_priority" placeholder="" value="">
                              </div> 
                              <div class="form-group">
                                <textarea class="form-control" id="" name="spg_sig_priority_notes" placeholder="Note" ></textarea>
                              </div> 
                           </div> 
                           <div class="form-group">
                              <label>Outcome</label>
                              <div class="form-group">
                                <input type="text" class="form-control" name="spg_sig_outcome" placeholder="" value="">
                              </div> 
                              <div class="form-group">
                                <textarea class="form-control" id="" name="spg_sig_outcome_notes" placeholder="Note" ></textarea>
                              </div> 
                           </div> 
                           <div class="col-xs-12 col-sm-12 text-center form">
                              <button type="button" onclick="ajax_agency_insert();">Submit</button>
                              <button type="button" class="mar_l10 cancel_btn" data-dismiss="modal">Cancel</button>
                           </div>
                        </form>
                     </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
     		</section>
   <script>
	function delete_agency(aid){
		var r = confirm("Press YES to Delete User");
		if (r == true) {
			window.location.href = "delete_agency.php?aid="+aid;
		}
	}
	function edit_agency(aid){
		window.location.href = "edit_agency.php?aid="+aid;
	}
    function ajax_agency_insert()
    {
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
        var spg_sig_priority_notes= $('input[name="spg_sig_priority_notes"]').val();
        var spg_sig_outcome= $('input[name="spg_sig_outcome"]').val();
        var spg_sig_outcome_notes= $('input[name="spg_sig_outcome_notes"]').val();
		if(name == ''){
			alert('Please insert agency name'); return false;
		}
		else if(region == ''){
			alert('Please insert select region'); return false;
		}
		else if(street == ''){
			alert('please insert street'); return false;
		}
		else if(city == ''){
			alert('Please insert city'); return false;
		}
		else if(state == ''){
			alert('Please insert state'); return false;
		}
		else if(zip_code == ''){
			alert('Please insert zip-code'); return false;
		}
		else if(phone == ''){
			alert('Please insert phone number'); return false;
		}
		else if(fax == ''){
			alert('Please insert fax number'); return false;
		}
		else {
        var formData = {name:name,region:region,street:street,apt_name:apt_name,city:city,state:state,zip_code:zip_code,phone:phone,fax:fax,manager_name:manager_name,manager_number:manager_number,spg_sig_priority:spg_sig_priority,spg_sig_priority_notes:spg_sig_priority_notes,spg_sig_outcome:spg_sig_outcome,spg_sig_outcome_notes:spg_sig_outcome_notes};
        $.ajax({
            url : "ajax_agency_insert.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                if(data=='success')	window.location = "agencies.php";
				else alert('Agency not created,Try again');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
		}
    }
	</script>
<?php include_once('templates/footer.php'); ?>