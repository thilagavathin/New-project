<?php include_once('templates/header.php');
if($_SESSION['userrole']==3 || $_SESSION['userrole']==2) {
    header('Location:dashboard.php'); die;
}
?>
     		<section >
	     		<div class="container">
				
				<div class="row">
				<?php
            		$sql_userlevels = "SELECT * FROM `login_levels` WHERE `id` = '".$_GET['level']."'";
            		$result_userlevels = mysql_query($sql_userlevels) or die(mysql_error());
            		$row_level = mysql_fetch_array($result_userlevels);
            		?>
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li><a href="userlevels.php">User Levels</a></li>
						  <li class="active"><?php echo $row_level['level_name']; ?> </li>              
						</ol>
					</div>
				 </div>
				
	     			<div class="row">
                    
                <div class="col-sm-12">
                  <h1 class="page-title"><?php echo $row_level['level_name']; ?> <small class="text_black fb_300">(Level Control)</small></h1>
                </div>
            </div>
            <form role="form" autocomplete="off" action="update_level.php" method="post">
            <div class="row form">
                <div class="form-group col-sm-4">
                  <label>Name</label>
                  <input type="text" class="form-control" name="level_name" placeholder="Name" value="<?php echo $row_level['level_name']; ?>">
               </div>
               <div class="form-group col-sm-4">
                  <label>Level</label>
                  <input type="text" class="form-control" name="level_level" placeholder="1" readonly value="<?php echo $row_level['level_level']; ?>">
               </div>
               <div class="form-group col-sm-4">
                  <label>Redirect</label>
                  <input type="text" class="form-control" name="redirect" placeholder="eg: www.youtube.com" value="<?php echo $row_level['redirect']; ?>">
                  <p class="mar_t5"><small class="fb_500 mar_t5"><span class="text_blue">When logging in,this user will be redirected to the URL you specify. Leave blank to redirect to the referring page.</span></small></p>
               </div>
            </div>
            <div class="row form">
                <div class="form-group col-sm-4">
                  <label class="ele_bl">Welcome Email</label>
                  <label class="mar_b10 checkbox_normal">
                    <input type="checkbox" name="welcome_email" <?php if($row_level['welcome_email'] == '1'){ ?>checked<?php } ?> id="checkbox8">
                    <span class="custom-icon checkbox-icon"></span>Send welcome email when users join this level
                  </label>
                  <p class=""><small class="fb_500 mar_t5"><span class="text_blue">When logging in,this user will be redirected to the URL you specify. Leave blank to redirect to the referring page.</span></small></p>
               </div>
               <div class="form-group col-sm-4">
                  <label class="ele_bl">Disable</label>
                  <label class="mar_b10 checkbox_normal">
                    <input type="checkbox" <?php if($row_level['level_disabled'] == '1'){ ?>checked<?php } ?> id="checkbox9" name="disable">
                    <span class="custom-icon checkbox-icon"></span>Prevent this level from accessing any secure content
                  </label>
               </div>
               <div class="form-group col-sm-4">
                  <label class="ele_bl">Delete</label>
                  <label class="mar_b10 checkbox_normal">
                    <input type="checkbox" name="delete" id="checkbox10">
                    <span class="custom-icon checkbox-icon"></span>Remove this level from the database disabled
                  </label>
               </div>
               <input type="hidden" name="level_id" value="<?php echo $_GET['level']; ?>">
            </div>
            <div class="col-xs-12 col-sm-12 text-center mar_b20">
              <button class="button" type="submit">Update</button>
           </div>
           </form>
            <?php
			$sql_user = "SELECT `login_users`.* ,(SELECT `timestamp` FROM `login_timestamps` WHERE `login_timestamps`.`user_id` = `login_users`.`user_id` ORDER BY `login_timestamps`.`id` DESC LIMIT 1)  AS last_login FROM  `login_users` WHERE `user_level` LIKE '".'%"'.$row_level['level_level'].'"%'."'";
			$result_user = mysql_query($sql_user) or die(mysql_error());
			?>
            <div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title"><?php echo $row_level['level_name']; ?><small class="text_black fb_300"> Existing User</small></h1>
                </div>
            </div>
            <div>
              
              <div class="site-table table-responsive mar_t30" style="overflow-x: visible;">
                 <table class="table" id="edit_user_level">
                   <thead>
                     <tr>
                       <th>Username</th>
                       <th>Role</th>
                       <th>Name</th>
                       <th>Email</th>
                       <th>Registered Date</th>
                       <th>Login</th>
                       <th>&nbsp;</th>
                     </tr>
                   </thead>
                   <tbody>
                   <?php while($row_user = mysql_fetch_array($result_user)){ ?>
                      <tr>
                          <td>
                             <a href="userprofile.php?uid=<?php echo $row_user['user_id']; ?>" class="usernameRole"><img src="new/images/chat-noimage.png" class="user_profileicon" alt="user profile image" width="30">
                             <span><?php echo $row_user['username']; ?></span></a>
                          </td>
                          <?php if($row_level['level_level'] == '1'){ ?>
                          <td class="text-center"><span class="badge">A</span></td>
                          <?php }elseif($row_level['level_level'] == '2'){ ?>
                          <td class="text-center"><span class="badge">S</span></td>
                          <?php }elseif($row_level['level_level'] == '3'){ ?>
                          <td class="text-center"><span class="badge">U</span></td>
                          <?php } ?>
                          <td><?php echo $row_user['name']; ?></td>
                          <td class="text-center"><?php echo $row_user['email']; ?></td>
                          <td class="text-center"><?php if(isset($row_user['timestamp'])){ echo date("M d, Y \a\\t h:i A", strtotime($row_user['timestamp'])); } ?></td>
                          <td class="text-center"><?php if(isset($row_user['last_login'])){ echo date("M d, Y \a\\t h:i A", strtotime($row_user['last_login'])); } ?></td>
                          <td><i class="fa fa-pencil-square-o" aria-hidden="true" onclick="userprofile(<?php echo $row_user['user_id']; ?>)"></i> 
                              <i class="fa fa-trash mar_l10" aria-hidden="true" onclick="delete_user(<?php echo $row_user['user_id']; ?>)"></i></td>
                      </tr>
                  	<?php } ?>
                   </tbody>
                 </table> 
                 
              </div>
            </div>
	     		</div>
     		</section>


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
    	<script>
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
 <?php include_once('templates/footer.php'); ?>  