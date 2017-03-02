<?php include_once('templates/header.php');?>
<link href="assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
<style>
    .file-upload {
        position: relative;
        overflow: hidden;
        margin: 10px; }
    .file-upload input.file-input {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0); }
        input.trigger[type="radio"] {
            display: none !important;
        }
</style>
  		
     		<section >
	     		<div class="container">
				
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li class="active">Settings </li>              
						</ol>
					</div>
				 </div>
				
	     			<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">
                    <div class="row">
                      <span class="col-sm-8">Settings</span>
                    </div>
                  </h1>
                </div>
            </div>
            <div>
            <div class="text-center text-danger" id="message"> </div>
            <div class="site_tabs" id="horizontalTab">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs resp-tabs-list" role="tablist">
                <li role="presentation" class="active"><a href="#profile_settings" aria-controls="home" role="tab" data-toggle="tab">Profile</a></li>
                <li role="presentation"><a href="#account_settings" aria-controls="account_settings" role="tab" data-toggle="tab">Account</a></li>
                <li role="presentation"><a href="#email_settings" aria-controls="email_settings" role="tab" data-toggle="tab">Email</a></li>
				<li role="presentation"><a href="#logo_setting" aria-controls="logo_setting" role="tab" data-toggle="tab">Logo</a></li>
              </ul>

              <!-- Tab panes -->
              <div class="tab-content resp-tabs-container form  col-md-offset-3 col-md-6 col-sm-12 col-xs-12">
                <!-- general settings -->
                <div role="tabpanel"  id="profile_settings">
                <form class="form-horizontal" id="dataprofile" method="post" enctype="multipart/form-data">
                    <div class="row">
                      <h2 class="tab_title">Profile</h2>
                       <div class="col-md-12">
                          <div class="form-group">
                              <label>Display Name</label>
                              <input type="text" placeholder="Display Name" class="form-control form-block input-lg" name="display_name" id="display_name" value="<?php echo $_SESSION['displayname']; ?>">
                          </div>
						  <div class="form-group">
                              <label>Position</label>
                              <input type="text" placeholder="Ex:Lead" class="form-control form-block input-lg" name="position_name" id="position_name" value="<?php echo $position; ?>">
                          </div>
						  <div class="form-group">
                              <label>Agency Name</label>
                              <input type="text" placeholder="Ex:R1 columbia" class="form-control form-block input-lg" name="agency_name" id="agency_name" value="<?php echo $Agencyname; ?>">
                          </div>
						  <div class="form-group">
                              <label>Region</label>
                              <input type="text" placeholder="Ex:R-1" class="form-control form-block input-lg" name="region_name" id="region_name" value="<?php echo $Region; ?>">
                          </div>
						  
                          <div class="form-group">
                              <div class="row">
                                  <div class="col-md-12 col-sm-12">
                                    <label>Profile Image</label>
                                  </div>
                                  <div class="col-md-12 col-sm-12">
                                  <div class="form-group col-sm-6">
                                      <label class="mar_b10 checkbox_normal">
                                        <input type="radio" name="group1" class="user-photo-default trigger" data-rel="user-photo-default" value="usedefault" id="usedefault" <?php echo ($user_img[0]<>'')? 'checked':'';?> />
                                        
                                        <span class="custom-icon radio-icon"></span>Use default avatar
                                      </label>
                                   </div>
                                   <div class="form-group col-sm-6">
                                      <label class="mar_b10 checkbox_normal">
                                        <input type="radio" name="group1" class="user-photo-real trigger" data-rel="user-photo-real" value="useruploadphoto" id="useruploadphoto" <?php echo ($user_img[0]<>'')? 'checked':'';?> />
                                        
                                        <span class="custom-icon radio-icon"></span>Upload new
                                      </label>
                                   </div>
                                   </div>
                                  <div class="col-md-12 col-sm-12">
                                  <div class="user-photo-default upt " style="display:<?php if($user_img[0]=='') {echo 'block';}else{echo 'none';}?> ;">
                                    <div class="col-md-6 col-sm-6">
                                        <?php if($user_img[0]=='')  $img_val ="assets/img/photo.jpg";
                                        else $img_val ="assets/profile/".$user_img[0];
                                        ?>
                                        <img  alt="" src="<?php echo $img_val; ?>" style="width: 100px; height: 100px;"/>
                                    </div>
                                </div>
                                <div class="user-photo-real upt <?php if($user_img[0]<>'') echo 'active';?> " style="display:<?php if($user_img[0]<>'') {echo 'block';}else{echo 'none';}?> ;">
                                    <span class="col-xs-height">
                                        <div class="col-md-6 col-sm-6">
                                            <?php if($user_img[0]=='')  $img_val ="assets/img/photo.jpg";
                                            else $img_val ="assets/profile/".$user_img[0];
                                            ?>
                                            <img id="blah"  alt="" src="<?php echo $img_val; ?>" style="width: 100px; height: 100px;"/>
                                        </div>
                                    </span>
                                    <div class="col-md-6 col-sm-6">
                                        <span class=" btn btn-default file-upload"> <input type="file" name="avatar_img" id="avatar_img" class="file-input">Change photo</span>

                                    </div>
                                </div>
                                  </div>
                              </div>
                          </div>                        
                       </div>
                       <div class="col-xs-12 col-sm-12 text-center form">
                        <button type="submit" class="">Submit</button>
                       </div>                       
                       
                    </div>
                    </form>
                </div>
                <!-- Account_settings -->
                <div role="tabpanel"  id="account_settings">
                    <div class="row">
                    <form class="form-horizontal ga-form no-br m-t-20" role="form" autocomplete="off">
                      <h2 class="tab_title">Account</h2>
                      <div class="col-md-12">
                         <div class="text-center">
                            <strong>Last changed: </strong><?php echo date('M d, Y',strtotime($last_update));?>
                            <hr>
                         </div>
                         <div class="form-group">
                            <label>Current password</label>
                            <input type="password" name="cur_pass" placeholder="Old password" class="form-control">
                          </div>
                          <div class="form-group">
                            <label>New password</label>
                            <input type="password" name="new_pass" placeholder="New password" class="form-control">
                          </div>
                          <div class="form-group">
                            <label>Re-Enter password</label>
                            <input type="password" name="confirm_pass" placeholder="Confirm new password" value="" class="form-control">
                          </div>
                      </div>
                      <div class="col-xs-12 col-sm-12 text-center form">
                        <button class="" type="button" onclick="change_credentials();">Change password</button>
                    </div>
                    </form>
                    </div>
                </div>
                <!-- Email_settings -->
                <div role="tabpanel"  id="email_settings">
                  <div class="row">
                  <form class="form-horizontal ga-form no-br m-t-20" role="form" autocomplete="off">
                    <h2 class="tab_title">Email</h2>
                    <div class="col-sm-12 col-xs-12">
                       <div class="form-group">
                          <label>Email</label>
                          <input type="text" name="u_email" placeholder="Email Id" value="<?php echo $user_email; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12">
                      <label>Email notifications</label>
                      <small>From here you can switch your mail notifications on or off!</small>
                      <label class="mar_b10 checkbox_normal">
                        <input type="checkbox" id="email_status" name="email_status" data-init-plugin="switchery" value="1" checked="checked" />
                        <span class="custom-icon checkbox-icon"></span>
                      </label>
                   </div>
                   <div class="col-sm-12 col-xs-12">
                      <label>Report Upload Notification</label>
                      <small>From here you can switch your mail notifications on or off!</small>
                      <label class="mar_b10 checkbox_normal">
                        <input type="checkbox" id="report_status" name="report_status" data-init-plugin="switchery" value="1" checked="checked" />
                        <span class="custom-icon checkbox-icon"></span>
                      </label>
                   </div>
                    <div class="col-xs-12 col-sm-12 text-center form">
                        <button type="button" onclick="change_email();">Save</button>
                    </div>
                    </form>
                  </div>
                </div>
				<!-- Logo settings -->
                <div role="tabpanel"  id="logo_setting">
                <form class="form-horizontal" id="datalogo" method="post" enctype="multipart/form-data">
                    <div class="row">
                      <h2 class="tab_title">Logo</h2>
                       <div class="col-md-12">
							<div class="form-group">
                              <div class="row">
                                  <div class="col-md-12 col-sm-12">
                                    <label>Logo Image</label>
                                  </div>
							  <?php 
							  $sql=mysql_query("SELECT user_image,user_logo,username,timestamp,email FROM login_users where user_id ='".trim($_SESSION['adminlogin'])."'"); 
								while($row=mysql_fetch_array($sql)) {
								$user_logo = $row["user_logo"];
								if ($user_logo <> '') $user_logo = @unserialize($user_logo); else $user_logo = '';
								}
								if ($user_logo == '') $logo_val = "assets/img/pgroup_full_new.jpg";
								else $logo_val = "assets/logo/" . $user_logo[0];
								  
								  ?>
                                  <div class="col-md-12 col-sm-12">
                                   <div class="form-group col-sm-6">
                                      <label class="mar_b10 checkbox_normal">
                                        <input type="radio" name="group2" class="user-photo-real trigger" data-rel="user-photo-real" value="useruploadphoto1" id="useruploadphoto1" <?php echo ($user_logo[0]<>'')? 'checked':'';?> />
                                        
                                        <span class="custom-icon radio-icon"></span>Upload new
                                      </label>
                                   </div>
                                   </div>
                                  <div class="col-md-12 col-sm-12">
                                <div class="user-photo-real upt <?php if($user_logo[0]<>'') echo 'active';?> " style="display:<?php if($user_logo[0]<>'') {echo 'block';}else{echo 'none';}?> ;">
                                    <span class="col-xs-height">
                                        <div class="col-md-6 col-sm-6">
                                            <?php if($user_logo[0]=='')  $logo_val ="assets/img/pgroup_full_new.jpg";
                                            else $logo_val ="assets/logo/".$user_logo[0];
                                            ?>
                                            <img id="bla"  alt="" src="<?php echo $logo_val; ?>" style="width: 100px; height: 100px;"/>
                                        </div>
                                    </span>
                                    <div class="col-md-6 col-sm-6">
                                        <span class=" btn btn-default file-upload"> <input type="file" name="avatar_logo" id="avatar_logo" class="file-input">Change photo</span>

                                    </div>
                                </div>
                                  </div>
                              </div>
                          </div>                        
                       </div>
                       <div class="col-xs-12 col-sm-12 text-center form">
                        <button type="submit" class="">Submit</button>
                       </div>                       
                       
                    </div>
                    </form>
                </div>
                
              </div>
              
            </div>
            </div>
	     		</div>
     		</section>
 

<?php include_once('templates/footer.php');?>
<script src="assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>
<script>
    $('.trigger').click(function() {
        $('.upt').hide();
        $('.' + $(this).data('rel')).show();
    });
    $("#avatar_img").change(function(){
        readURL(this);
    });
	$("#avatar_logo").change(function(){
        readlogoURL(this);
    });
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
	function readlogoURL(input) {

        if (input.files && input.files[0]) {
            var reader1 = new FileReader();

            reader1.onload = function (e) {
                $('#bla').attr('src', e.target.result);
            }

            reader1.readAsDataURL(input.files[0]);
        }
    }

    $("form#dataprofile").submit(function(){
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "update_settings.php",
            type: 'POST',
            data: formData,
            async: false,
            success: function (data) {
                if(data=='success'){sweetAlert("Success...", "Updated successfully", "success");window.location.reload();}
                else{sweetAlert("Sorry...", "Please do again", "error");}
            },
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
    });
	
	$("form#datalogo").submit(function(){
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "update_logo.php",
            type: 'POST',
            data: formData,
            async: false,
            success: function (data) {
                if(data=='success'){sweetAlert("Success...", "Updated successfully", "success");window.location.reload();}
                else{sweetAlert("Sorry...", "Please do again", "error");}
            },
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
    });
    function change_credentials()
    {
        var cur_pass= $('input[name="cur_pass"]').val();
        var new_pass= $('input[name="new_pass"]').val();
        var confirm_pass= $('input[name="confirm_pass"]').val();
        if(new_pass.length < 4) {
            $('#message').html('New Password must have FOUR letters !!');
            alert('New Password must have FOUR letters !!');
            return false;
        }
        if(new_pass===confirm_pass)
        {
            var formData = {cur_pass:cur_pass,new_pass:new_pass};
            $.ajax({
                url : "update_password.php",
                type: "POST",
                data : formData,
                success: function(data, textStatus, jqXHR)
                {
                    if(data=='success') {
                        $('input[name="cur_pass"]').val('');
                        $('input[name="new_pass"]').val('');
                        $('input[name="confirm_pass"]').val('');
                        $('#message').html('Password has updated Successfully !!!');
                        alert('Password has updated Successfully !!!');
                    }
                    else if(data=='invalid')
                    {
                        $('#message').html('Current Password does not match !!');
                        alert('Current Password does not match !!');
                    } 
                    else alert('Due to internet problem not reachable database ,Try again');
                }
            });
        }
        else {
            $('#message').html('New Password does not match the Re-Enter Password');
            alert('New Password does not match the Re-Enter Password');
        }
    }
    function change_email()
    {
        var u_email= $('input[name="u_email"]').val();
        var isChecked =$('#email_status:checked').val()?true:false;
        var formData = {u_email:u_email,isChecked:isChecked};
        $.ajax({
            url : "update_email.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                if(data=='success') {
                    $('input[name="u_email"]').val(u_email);
                    $('#message').html('Email Id has updated Successfully !!!');
                    alert('Email Id has updated Successfully !!!');
                }
                else if(data=='invalid'){
                    $('#message').html('Current email does not match !!');
                    alert('Current email does not match !!');
                } 
                else alert('Due to internet problem not reachable database ,Try again');
            }
        });
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
    
  </body>
</html>
