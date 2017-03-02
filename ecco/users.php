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
						  <li class="active">Users </li>              
						</ol>
					</div>
				 </div>
	     			<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">users</h1>
                </div>
            </div>
            <div>
            <div class="site_tabs">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Users List</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Email functions</a></li>
              </ul>

              <!-- Tab panes -->
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="home">
                    <?php
                    $sql_users = "SELECT `login_users`.* ,(SELECT `timestamp` FROM `login_timestamps` WHERE `login_timestamps`.`user_id` = `login_users`.`user_id` ORDER BY `login_timestamps`.`id` DESC LIMIT 1)  AS last_login FROM  `login_users`";
    				$result_users = mysql_query($sql_users) or die(mysql_error());
    				$num_rows_users = mysql_num_rows($result_users); 
    				?>
                    <div class="row search_user vcenter">
                       <div class="col-md-2">
                          <label class="mar0">Total Users <span class="text_light_red"><?php echo $num_rows_users; ?></span></label>
                       </div>
                       <div class="col-md-7">
                          <div class="form-group mar0">
                             <input type="text" id="search-table" class="form-control user_searchbox" placeholder="Search">
                          </div>
                       </div>
                       <div class="col-md-3">
                          <div class="form-group mar0">
                              <button data-target="#createNewLevel1" data-toggle="modal" class="button pull-right"><i class="fa fa-plus-circle" ></i>Create New User</button>
                          </div>
                       </div>
                    </div>
                    <div class="site-table table-responsive" style="overflow-x: visible;">
                       <table id="tableWithSearch_usertable" class="table user_table">
                         <thead>
                           <tr>
                             <th>Username</th>
                             <th>Role</th>
                             <th>Name</th>
                             <th>Email</th>
                             <th style="width: 15%;">Registered Date</th>
                             <?php if($_SESSION['userrole']==1) { ?>
                             <th style="width: 10%;">Approved</th> 
                             <?php } ?>
                             <th style="width: 15%;">Login</th>
                             <th style="width: 5%;">&nbsp;</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php while($row = mysql_fetch_array($result_users)) { 
						  $user_level = unserialize($row['user_level']);?>
                            <tr>
                                <td>
                                    <?php if(in_array("1", $user_level) && $_SESSION['userrole']==2 ){ ?>
                                    <a class="usernameRole"><img src="images/chat-noimage.png" class="user_profileicon" alt="user profile image" width="30">
                                        <span><?php echo $row['username']; ?></span>
                                    </a>
                                </td>
                                <?php }else{?>
                                    <a href="userprofile.php?uid=<?php echo $row['user_id']; ?>" class="usernameRole">
                                    <img class="user_profileicon" src="images/chat-noimage.png" width="30">
                                    <span><?php echo $row['username']; ?></span>
                                    </a>
                                </td>
								<?php }?>
                        
                                <?php if(in_array("1", $user_level)){ ?>
                                    <td class="text-center"><span class="badge">A</span></td>
                                <?php }else{?> <td></td> <?php } ?>
                                <td><?php echo $row['name']; ?></td>
                                <td class="text-center"><?php echo $row['email']; ?></td>
                                <td class="text-center"><?php if(isset($row['timestamp'])){ echo date("M d, Y \a\\t h:i A", strtotime($row['timestamp'])); } ?></td>
                                <td class="text-center"><label class="label label-success"><?php if($_SESSION['userrole']==1) {  echo $row['approved']; } ?></label></td>
                                <td class="text-center"><?php if(isset($row['last_login'])){ echo date("M d, Y \a\\t h:i A", strtotime($row['last_login'])); } ?></td>
                                
                    	       
                                <?php
                                if(in_array("1", $user_level) && $_SESSION['userrole']==2 ){ }
                                else{ ?>
                                <td>
                                    <i class="fa fa-pencil-square-o" onclick="userprofile(<?php echo $row['user_id']; ?>)" aria-hidden="true"></i> 
                                    <i class="fa fa-trash mar_l1" aria-hidden="true" data-target="#rowDelCnfm" data-toggle="modal" onclick="delete_user(<?php echo $row['user_id']; ?>)"></i>
                                </td>
                                <?php }?>
                            </tr>
                            <?php } ?>
                         </tbody>
                       </table> 
                       
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="profile">
                    <div class="email-notification mar_t20">
                      <div class="row">
                      <form action="sendemail.php" method="post">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label>To</label>
                              <select class="form-control" id="multi" multiple name="user_level[]">
                                    <option value="1">Admin</option>
                                    <option value="2">Special Users</option>
                                    <option value="3">Users</option>
                              </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label>Subject</label>
                              <input type="text" class="form-control" name="subject" placeholder="Write subject here" value="">
                              <small class="text_grey">Select the user groups that will receive your email.(This will send as BCC)</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label>Message</label>
                              <textarea class="form-control" name="message" rows="4" id="" placeholder="Write something here"></textarea>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 text-center form">
                          <button type="submit">Send</button>
                          <button class="mar_l10 cancel_btn">Clear</button>
                        </div>
                        </form>
                      </div>
                    </div>
                </div>
              </div>
              
            </div>
            </div>
	     		</div>
     		</section>
            <div id="createNewLevel1" class="modal comment-box right fade" role="dialog">
                    <div class="modal-dialog">
                      <!-- Modal content-->
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h3 class="modal-title text_blue">Create New User</h3>
                        </div>
                        <div class="modal-body no-shadow">
                          <div class="row" id="comment-form">
                             <div class="col-xs-12 form">
                                <form class="form-horizontal ga-form" role="form" autocomplete="off" action="insert_user.php" method="post">
                                  <div class="form-group">
                                    <div class="col-sm-8">
                                      <input type="text" placeholder="Full Name" name="name" value="" class="form-control">
                                    </div>
                                  </div>  
                                  
                                   <div class="form-group">
                                    <div class="col-sm-8">
                                      <input type="text" placeholder="User Name" name="username" value="" class="form-control">
                                    </div>
                                  </div>  
                                  
                                   <div class="form-group">
                                    <div class="col-sm-8">
                                      <input type="text" placeholder="Email Address" name="email" value="" class="form-control">
                                    </div>
                                  </div>  
                                  
                                  <div class="form-group">
                                  <div class="col-sm-8">
                                    <b>Note:</b> <span>A random password will be generated and emailed to the user.</span>  
                                  </div>
                                  </div>
                                                  
                                  <div class="row">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-8 m-t-40 m-b-40">
                                        <button type="submit" class="btn btn-primary">Add User</button>
                                        <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                                    </div>
                                  </div>
                                </form>
                             </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>


   
    
<?php include_once('templates/footer.php'); ?>
    <script>
    <?php if($_REQUEST['error']){?>
       alert('Username Already exist.Please change the Username') ;
       window.location.href = "users.php";
    <?php } ?>
	function delete_user(uid){
		var r = confirm("Press YES to Delete User");
		if (r == true) {
			window.location.href = "delete_user.php?uid="+uid;
		}
	}
	function userprofile(uid){
		window.location.href = "userprofile.php?uid="+uid;
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