
<?php include_once('templates/header.php');
if($_SESSION['userrole']==3 || $_SESSION['userrole']==2) {
    header('Location:dashboard.php'); die;
}

$sql_user = "SELECT * FROM `login_users` WHERE `user_id` = '".$_REQUEST['uid']."'";
$result_user = mysql_query($sql_user) or die(mysql_error());
$row = mysql_fetch_array($result_user);
if($row['user_image']<>'') $user_img=@unserialize($row['user_image']);
else $user_img='';
if($user_img=='')  $img_val ="assets/img/photo.jpg";
else $img_val ="assets/profile/".$user_img[0];
$sql_tab = "SELECT DISTINCT `section` FROM `login_profile_fields` WHERE `public` = '1'";
$result_tab = mysql_query($sql_tab) or die(mysql_error());
?>	
<style>
.mar_b10.checkbox_normal > input {
  display: block;
}
.active.resp-tab-item.resp-tab-active {
  height: auto;
}
.link_btn {
  background: #bdbdbd none repeat scroll 0 0;
  border-color: transparent;
  border-radius: 3px !important;
  color: #fff;
  font-size: 18px;
  margin-top: 15px;
  outline: 0 none;
  padding: 10px 20px;
  width: 100px;
  z-index: 4;
   cursor: pointer;
}
.link_btn:hover{
  color: #fff;  
}
.menu_list .active {
    height: auto;
}
</style>	
     		<section >
	     		<div class="container">
                <div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li><a href="users.php">Users</a></li>              
						  <li class="active">Users Profile</li>              
						</ol>
					</div>
				 </div>
	     			<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">
                    <div class="row">
                      <span class="col-sm-8">users profile</span>
                      <span class="col-sm-4 text_blue text-right proflilname_icon"><img src="<?php echo $img_val; ?>"> <?php echo $row['name']; ?></span>
                    </div>
                  </h1>
                </div>
            </div>
            <div>
            <div class="site_tabs" id="horizontalTab">
                <form action="update_profile.php" method="post">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs resp-tabs-list" role="tablist">
                <li role="presentation" class="active"><a href="#general_settings" aria-controls="home" role="tab" data-toggle="tab">General</a></li>
                <?php 
				$section = [];
				while($row_tab = mysql_fetch_array($result_tab)){ 
				$section[] = $row_tab['section'];
				?>
                <li role="presentation"><a href="#<?php echo str_replace(' ', '', $row_tab['section']); ?>" aria-controls="ageny_name" role="tab" data-toggle="tab"><?php echo $row_tab['section']; ?></a></li> 
                <?php } ?>
                
                <li role="presentation"><a href="#accesslogs" aria-controls="accesslogs" role="tab" data-toggle="tab">Access Logs</a></li>
              </ul>

              <!-- Tab panes -->
              <div class="tab-content resp-tabs-container form">
                <!-- general settings -->
                <div role="tabpanel"  id="general_settings">
                    <div class="row">
                      <h2 class="tab_title">General Settings</h2>
                       <div class="col-md-4">
                          <div class="form-group">
                              <label>Name</label>
                              <input type="text" name="user[name]" value="<?php echo $row['name']; ?>" class="form-control">
                          </div>
                          <div class="form-group">
                              <label>User Name</label>
                              <input type="text" placeholder="" name="user[username]" value="<?php echo $row['username']; ?>" class="form-control">
                          </div>
                          <div class="form-group">
                              <label>Password</label>
                              <input type="password" placeholder="" name="user[password]" value="" class="form-control">
                          </div>
                          <div class="form-group">
                              <label>Password Again</label>
                              <input type="password" placeholder="" name="user[cpassword]" value="" class="form-control">
                          </div>
                       </div>
                       <div class="col-md-4">
                          <div class="form-group">
                              <label>Phone Number</label>
                              <input type="text" value="<?php echo $row['phone']; ?>" name="user[phone]" id="user[phone]" placeholder="(+123) 456-7890" class="form-control" >
                          </div>
                          <div class="form-group">
                              <label>Email</label>
                              <input type="text" placeholder="" name="user[email]" value="<?php echo $row['email']; ?>" class="form-control">
                          </div>
                          <div class="form-group">
                              <label>Region</label>
                              <input type="text" readonly placeholder="" name="" value="<?php echo $row['region']; ?>" class="form-control">
                          </div>
                          <div class="form-group">
                              <label>Position</label>
                              <input type="text" placeholder="" name="user[position]" value="<?php echo $row['position']; ?>" class="form-control">
                          </div>                          
                       </div>
                       <div class="col-md-4">
                          <div class="form-group">
                              <label>Agency Name</label>  
                              <input type="text" readonly placeholder="" name="" value="<?php echo $row['AgencyName']; ?>" class="form-control">
                          </div>
                          <?php if($_SESSION['userrole']==1) { ?>
                          <div class="form-group">
                              <p><label>Approved</label></p>
                              <label class="mar_b10 checkbox_normal">
                                <input type="radio" value="YES" name="user_approved" <?php echo ($row['approved']=='YES')? 'checked':''; ?> > YES
                                
                              </label>
                              <label class="checkbox_normal mar_l20 mar_b10">
                                <input type="radio" value="NO" name="user_approved" <?php echo ($row['approved']=='NO')? 'checked':''; ?> > NO
                                
                              </label>
                          </div>
                          <?php $user_level = unserialize($row['user_level']); ?>
                          <div class="form-group">
                              <label>User Levels</label>
                              <select class="full-width form-control"  data-init-plugin="select2"
                                     name="user[user_level][]">
                                <option
                                    value="1" <?php if (in_array("1", $user_level)) { ?> selected <?php } ?> >
                                    Admin
                                </option>
                                <option
                                    value="2" <?php if (in_array("2", $user_level)) { ?> selected <?php } ?> >
                                    Special Users
                                </option>
                                <option
                                    value="3" <?php if (in_array("3", $user_level)) { ?> selected <?php } ?> >
                                    Users
                                </option>
                                <option
                                    value="4" <?php if (in_array("4", $user_level)) { ?> selected <?php } ?> >
                                    Middle Admin
                                </option>
                            </select>
                          </div>
                          <?php }?>
                          <div class="form-group ">
                            <p><label>&nbsp;</label></p>
                            <label class="mar0 checkbox_normal">
                              <input type="checkbox" id="checkResUser" name="user[restricted]" <?php if($row['restricted'] == '1'){ ?> checked <?php } ?> >
                              <span class="custom-icon checkbox-icon"></span>Restrict user
                            </label>
                            <label class="mar0 pull-right checkbox_normal">
                               <input type="checkbox" id="checkDelUser" name="user[delete]">
                              <span class="custom-icon checkbox-icon"></span>Delete user?(can not be undone)
                            </label>
                            <span class="clearfix"></span>
                          </div>
                          
                       </div>
                    </div>
                </div>
                <?php foreach($section as $val){ ?>
                <!-- agency name -->
                <div role="tabpanel"  id="<?php echo str_replace(' ', '', $val); ?>">
                    <div class="row">
                      <h2 class="tab_title"><?php echo $val; ?></h2>
                      <?php
						$sql_data = "SELECT * FROM `login_profile_fields` WHERE `section` = '".$val."'";
						$result_data = mysql_query($sql_data) or die(mysql_error());
						while($row_data = mysql_fetch_array($result_data)){ 
							$sql_value = "SELECT * FROM `login_profiles` WHERE `pfield_id` = '".$row_data['id']."' AND `user_id` = '".$_REQUEST['uid']."'";
							$result_value = mysql_query($sql_value) or die(mysql_error());
							$row_value = mysql_fetch_array($result_value); ?>
                      <div class="col-md-12">
                         <div class="form-group">
                            <label><?php echo $row_data['label']; ?></label>
                            <?php if($row_data['type'] == "checkbox"){ ?>
								<input type="hidden" name="profile[<?php echo $row_data['id']; ?>]" value="0">
								<input type="<?php echo $row_data['type']; ?>" name="profile[<?php echo $row_data['id']; ?>]" <?php if($row_value['profile_value'] == '1'){ ?>checked<?php } ?> >
							<?php }else{ ?>
							<input type="<?php echo $row_data['type']; ?>" name="profile[<?php echo $row_data['id']; ?>]" value="<?php if(isset($row_value['profile_value'])){ echo $row_value['profile_value']; } ?>" class="form-control">
							<?php } ?>
                            </div>
                      </div>
                      <?php }?>
                    </div>
                </div>
                <?php }?>
               
                <!-- accesslogs -->
                <div role="tabpanel"  id="accesslogs">
                  <div class="row">
                    <h2 class="tab_title">Access Logs</h2>
                    <div class="col-sm-6 col-sm-offset-3">
                       <div class="site-table table-responsive">
                         <table class="table">
                           <thead>
                             <tr>
                               <th>LAST LOGIN</th>
                               <th>LOCATION</th>
                             </tr>
                           </thead>
                           <tbody class="text-center">
                           <?php
							$sql_lastlogin = "SELECT * FROM `login_timestamps` WHERE `user_id` = '".$_REQUEST['uid']."' ORDER BY `login_timestamps`.`id` DESC LIMIT 2";
							$result_lastlogin = mysql_query($sql_lastlogin) or die(mysql_error());
							while($row_lastlogin = mysql_fetch_array($result_lastlogin)){
							?>
                              <tr>
                                  <td><?php if(isset($row_lastlogin['timestamp'])){ echo date("M d, Y \a\\t h:i A", strtotime($row_lastlogin['timestamp'])); } ?></td>
                                  <td><?php echo $row_lastlogin['ip']; ?></td>
                              </tr>
                            <?php } ?>	
                           </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 text-center form">
                <input type="hidden" value="<?php echo $_REQUEST['uid']; ?>" name="user_id">
                <button type="submit">Update</button>
                <a href="users.php" class="mar_l10 link_btn">Back</a>
             </div>
             </form>
            </div>
            </div>
	     		</div>
     		</section>
  
  </body>
</html>
<?php include_once('templates/footer.php');
if(isset($_SESSION['update'])){

?>
<script>
alert("Profile Updated Successfully");
</script>
   
    
<?php unset($_SESSION['update']);
}
?>
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
  $(document).ready(function () {
    $('#horizontalTab').easyResponsiveTabs({
      type: 'default', //Types: default, vertical, accordion           
      width: 'auto', //auto or any width like 600px
      fit: true,   // 100% fit in a container
      closed: 'accordion', // Start closed if in accordion view
      activate: function(event) { // Callback function if tab is switched
        var $tab = $(this);
        var $info = $('#tabInfo');
        var $name = $('span', $info);
        $name.text($tab.text());
        $info.show();
      }
    });
  });
  </script>