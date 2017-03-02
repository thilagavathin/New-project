<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Georgia Strategic Prevention System - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="pages/css/pages-icons.css" rel="stylesheet" type="text/css">
    <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript">
    window.onload = function()
    {
      // fix for windows 8
      if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
        document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
    }
    </script>
  </head>
  <body class="fixed-header full-bg">
    <!-- START PAGE-CONTAINER -->
      

      
    <div class="login-wrapper ">
        
        

      <!-- START Login Container-->
      <div class="login-container">
          
        <!-- Logo --> 
        <div class="logo-wrapper">
            <div class="text-center padding-30">
                <img src="assets/img/ecco-logo-md.png" alt="logo">
                <h5 class="m-t-30 bold text-white">Georgia Training and Technical Assistance  Tracker</h5>
                
            </div>  
        </div>
        <!-- End Logo -->
            
        <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sx-10">
                
          <h6 class="p-t-10 text-white text-uppercase fs-16">Sign into your account</h6>
        
          <!-- START Login Form -->
          <form id="form-login" class="p-t-15" role="form" method="post" action="chk_login.php">
            <!-- START Form Control-->
            <div class="form-group form-group-default">
              <label>Login</label>
              <div class="controls">
                <input type="text" name="username" id="username" placeholder="User Name" class="form-control" required>
              </div>
            </div>
            <!-- END Form Control-->
            <!-- START Form Control-->
            <div class="form-group form-group-default">
              <label>Password</label>
              <div class="controls">
                <input type="password" class="form-control" name="password" id="password" placeholder="Credentials" required>
              </div>
            </div>
            <!-- START Form Control-->
			<?php if(isset($_GET['id']))  { $Err='block'; } else { $Err='none'; } ?>
			 <label for="checkbox1" class="text-white" style="color:red !important;display:<?php echo $Err; ?>">
               <?php if($_GET['id']==1) echo 'Invalid User Name / Password'; elseif($_GET['id']==2) echo 'Login Details not approved'; ?>
             </label>
            <div class="row">
              <div class="col-md-6 no-padding">
                <div class="checkbox check-success">
                  <input type="checkbox" value="1" id="checkbox1">
                  <label for="checkbox1" class="text-white">Keep Me Signed in</label>
                </div>
              </div>

            </div>
            <!-- END Form Control-->
            <button class="btn btn-lg btn-success btn-block btn-cons m-t-10" type="submit">Sign in</button>
              
            <div class="text-white text-center m-t-20 m-b-20">New to ECCO? <a href="#" class="m-l-10 fs-16 bold text-white"> Sign up Now!</a></div>
              
          </form>
          <!--END Login Form-->
                
        </div>

        </div>
        </div>
      </div>
      <!-- END Login Container-->
    </div>
    <!-- END PAGE CONTAINER -->
    <!-- BEGIN VENDOR JS -->
    <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-bez/jquery.bez.min.js"></script>
    <script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
    <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script type="text/javascript" src="assets/plugins/bootstrap-select2/select2.min.js"></script>
    <script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
    <script src="assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <!-- END VENDOR JS -->
    <!-- BEGIN CORE TEMPLATE JS -->
    <script src="pages/js/pages.min.js"></script>
    <!-- END CORE TEMPLATE JS -->
    <!-- BEGIN PAGE LEVEL JS -->
    <script src="assets/js/scripts.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL JS -->
    <script>
    $(function()
    {
      $('#form-login').validate()
    })
    </script>
  </body>
</html>