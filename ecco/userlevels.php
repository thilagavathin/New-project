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
						  <li class="active">User Levels </li>              
						</ol>
					</div>
				 </div>
				
	     			<div class="row">
                        <div class="col-sm-12">
                              <h1 class="page-title">User Levels</h1>
                             </div>
                    </div>
            <div class="row search_user vcenter">
               <div class="col-md-2">
               <?php
                $sql_userlevels = "SELECT * FROM `login_levels` ORDER BY `login_levels`.`id` ASC";
				$result_userlevels = mysql_query($sql_userlevels) or die(mysql_error());
				$num_rows_userlevels = mysql_num_rows($result_userlevels); 
				?>
                  <label class="mar0">Total Agencies <span class="text_light_red"><?php echo $num_rows_userlevels; ?></span></label>
               </div>
               <div class="col-md-7">
                  <div class="form-group mar0">
                     <input type="text" id="search-table" class="form-control" placeholder="Filter by region">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group mar0">
                      <button type="submit" class="button pull-right" data-toggle="modal" data-target="#create_userlevel"><i class="fa fa-plus-circle"></i>Create New Level</button>
                  </div>
               </div>
            </div>
            <div class="site-table table-responsive" style="overflow-x: visible;">
               <table class="table allUsersList" id="tableWithSearch">
                 <thead>
                   <tr>
                     <th class="left_align">Name</th>
                     <th>Level</th>
                     <th>Active Users</th>
                     <th>Redirect</th>
                   </tr>
                 </thead>
                 <tbody>
                 <?php 
    				while($row = mysql_fetch_array($result_userlevels)){ 
    				$sql_count = "SELECT COUNT(*) as levelcount FROM `login_users` WHERE `user_level` LIKE '".'%"'.$row['level_level'].'"%'."'";
    				$result_count = mysql_query($sql_count) or die(mysql_error());
    				$row_count = mysql_fetch_array($result_count);
    				?>
                    <tr>
                        <td><span><a href="edit_level.php?level=<?php echo $row['id']; ?>" ><?php echo $row['level_name']; ?></a></span></td>
                        <td class="text-center"><span class="badge1  <?php if($row['level_level'] == '1'){ ?>badge badge_red<?php }elseif($row['level_level'] == '2'){ ?>badge badge_blue<?php }else{ ?>badge_grey<?php } ?>" data-original-title="Admin" data-toggle="tooltip"><?php echo $row['level_level']; ?></span></td>
                        <td class="text-center"><?php echo $row_count['levelcount']; ?></td>
                        <td class="text-center"><?php echo $row['redirect']; ?></td>
                    </tr>
                    <?php } ?>
                 </tbody>
               </table> 
               
                </div>
          </div>
     		</section>
        <!-- start create new user level popup window -->
                  <div id="create_userlevel" class="modal comment-box right fade" role="dialog">
                    <div class="modal-dialog">
                      <!-- Modal content-->
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h3 class="modal-title text_blue">Create New Level</h3>
                        </div>
                        <div class="modal-body no-shadow">
                          <div class="row" id="comment-form">
                             <div class="col-xs-12 form">
                                <form role="form" autocomplete="off" action="insert_levels.php" method="post">
                                  <div class="form-group">
                                      <label>Name</label>
                                      <input type="text" class="form-control" name="name" placeholder="Full Name">
                                   </div>
                                   <div class="form-group">
                                      <label>Redirect</label>
                                      <input type="text" class="form-control" name="redirect" placeholder="eg: https://www.youtube.com">
                                      <p class="mar_t5"><small class="fb_500 mar_t5"><span class="text_blue">When logging in,this user will be redirected to the URL you specify. Leave blank to redirect to the referring page.</span></small></p>
                                   </div> 
                                   <div class="col-xs-12 col-sm-12 text-center form">
                                      <button type="submit">Submit</button>
                                      <button type="button" class="mar_l10 cancel_btn" data-dismiss="modal">Cancel</button>
                                   </div>
                                </form>
                             </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- end Create new user level -->
<?php include_once('templates/footer.php'); ?>
