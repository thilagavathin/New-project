<?php ob_start(); session_start(); include_once('config.php');
      if(!isset($_SESSION['adminlogin'])) {
		  header('Location:login.php'); die;
	  }
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Georgia Strategic Prevention System</title>
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
    <link href="assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
    <link href="assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" media="screen">
    <link href="assets/plugins/dropzone/css/dropzone.css" rel="stylesheet" type="text/css" />
    <link href="pages/css/pages-icons.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/simple-line-icons/simple-line-icons.css" rel="stylesheet" type="text/css" media="screen" />
    <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="pages/css/styles.css" rel="stylesheet" type="text/css" />

    <link href="assets/plugins/upload/css/fineuploader-gallery.css" rel="stylesheet">


    <link class="" href="pages/css/themes/simples.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript">
    window.onload = function()
    {
      // fix for windows 8
      if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
        document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
    }
    </script>
  </head>
  <body class="fixed-header">
    <!-- BEGIN SIDEBPANEL-->
    <nav class="page-sidebar" data-pages="sidebar">


      <!-- START SIDEBAR MENU -->
      <div class="sidebar-menu">
        <!-- BEGIN SIDEBAR MENU ITEMS-->
        <ul class="menu-items">
          <li class="m-t-15">
            <a href="dashboard.php">
              <span class="title">Dashboard</span>
            </a>
            <span class="icon-thumbnail"><i class="pg-home"></i></span>
          </li>
          <li class="">
            <a href="javascript:;"><span class="title">Menu</span>
            <span class="arrow"></span></a>
            <span class="icon-thumbnail"><i class="pg-menu_lv"></i></span>
            <ul class="sub-menu">
              <li>
                <a href="javascript:;">Home</a>
              </li>

			  <li>
                <a href="javascript:;">Admin</a>
              </li>
              <li style="display:none;">
                <a href="javascript:;"><span class="title">Level 2</span>
                <span class="arrow"></span></a>
                <ul class="sub-menu">
                  <li>
                    <a href="javascript:;">Sub Menu</a>

                  </li>
                  <li>
                    <a href="ujavascript:;">Sub Menu</a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
        <div class="clearfix"></div>


      </div>
      <!-- END SIDEBAR MENU -->
    </nav>
    <!-- END SIDEBAR -->
    <!-- END SIDEBPANEL-->
    <!-- START PAGE-CONTAINER -->
    <div class="page-container">
      <!-- START HEADER -->
      <div class="header ">
        <!-- START MOBILE CONTROLS -->
        <!-- LEFT SIDE -->
        <div class="pull-left full-height visible-sm visible-xs">
          <!-- START ACTION BAR -->
          <div class="sm-action-bar">
            <a href="#" class="btn-link toggle-sidebar" data-toggle="sidebar">
              <span class="icon-set menu-hambuger"></span>
            </a>
          </div>
          <!-- END ACTION BAR -->
        </div>
        <!-- RIGHT SIDE -->
        <div class="pull-right full-height visible-sm visible-xs">
          <!-- START ACTION BAR -->
          <div class="sm-action-bar">
            <a href="#" class="btn-link" data-toggle="quickview" data-toggle-element="#quickview">
              <i class="fs-14 sl-user"></i>
            </a>
          </div>
          <!-- END ACTION BAR -->
        </div>
        <!-- END MOBILE CONTROLS -->
        <div class=" pull-left sm-table">
          <div class="header-inner">

		   <div class="brand inline">
			<img src="assets/img/gaspa-logo.png" alt="logo"  title="Georgia Strategic Prevention System"  width="163" height="34">
			</div>






            </div>
        </div>
		<?php
		 $sql="SELECT * FROM login_users where user_id ='".trim($_SESSION['adminlogin'])."'";
	     $result_mail = mysql_query($sql) or die(mysql_error());
         $num_rows = mysql_num_rows($result_mail);
         while($row=mysql_fetch_array($result_mail)) {
		    $user_name = $row['username'];
			$user_email = $row['email'];
		  }
		 ?>
        <div class="pull-right visible-lg visible-md  ">
          <!-- START User Info-->
          <div class="m-t-10 m-l-20">
            <div class="pull-left p-r-10 p-t-10 fs-16 font-heading">
              <span class="semi-bold"><?php echo $user_name; ?></span></div>
            <div class="dropdown pull-right">
              <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="thumbnail-wrapper d32 circular inline m-t-5">
                <?php if($user_email=="kryslokk@gmail.com") {
					 $img_val ="image/Krystal.jpg";
					} else if($user_email=="mbouligny@progroup.us") {
						 $img_val ="image/marcus.jpg";
					}
					 else {
						 $img_val ="assets/img/photo.jpg";
					 }
					?>

				<img src="<?php echo $img_val; ?>" alt="" data-src="<?php echo $img_val; ?>" data-src-retina="<?php echo $img_val; ?>" width="32" height="32">

            </span>
              </button>
              <ul class="dropdown-menu profile-dropdown" role="menu">
                <li><a href="#"><i class="sl-settings"></i> Settings</a>
                </li>
                </li>
                <li class="bg-master-lighter">
                  <a href="logout.php" class="clearfix">
                    <span class="pull-left">Logout</span>
                    <span class="pull-right"><i class="sl-logout"></i></span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <!-- END User Info-->
        </div>

      </div>
      <!-- END HEADER -->
      <!-- START PAGE CONTENT WRAPPER -->
      <div class="page-content-wrapper">



        <!-- START PAGE CONTENT -->
        <div class="content">
          <!-- START CONTAINER FLUID -->
          <div class="container-fluid container-fixed-lg">

            <!-- START BREADCRUMB -->
            <ul class="breadcrumb">
              <li>
                <a href="dashboard.php">Dashboard</a>
              </li>
              <li>
                <a href="#">File Upload</a>
              </li>
            </ul>
            <!-- END BREADCRUMB -->

            <div id="rootwizard" class="m-t-50">
            <h5 class="m-l-30 m-b-20 bold text-blue-dark">File Upload</h5>
			
			<?php
			if(isset($_POST['submit'])){
				$allowedExts = array("doc", "docx", "pdf", "gif", "jpeg", "jpg", "png");
				$extension = end(explode(".", $_FILES["file"]["name"]));
				if (($_FILES["file"]["type"] == "application/pdf") || ($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "application/msword") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") && ($_FILES["file"]["size"] < 2000000) && in_array($extension, $allowedExts)){
					if ($_FILES["file"]["error"] > 0){
						echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
					}else{
						if (file_exists("upload/" . $_FILES["file"]["name"])){
							echo $_FILES["file"]["name"] . " already exists. ";
						}else{
							move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . $_FILES["file"]["name"]);
							echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
							$sql = "INSERT INTO documents (document_name, document_title) VALUES ('content/resources/".$_FILES["file"]["name"]."', '".$_POST['title']."')";
							$result = mysql_query($sql);
						}
					}
				}else{
					echo "Invalid file";
				}
			}
			?> 
            <form action="" method="post" enctype="multipart/form-data">
				<label>Title:</label><input type="text" name="title" ><br />
				Select image to upload:
				<input type="file" name="file" id="file">
				<input type="submit" value="Upload Image" name="submit">
			</form>
			
            </div>
          </div>
          <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->


        <!-- START COPYRIGHT -->
        <!-- START CONTAINER FLUID -->
        <div class="container-fluid container-fixed-lg footer">
          <div class="copyright sm-text-center">
            <p class="small no-margin pull-left sm-pull-reset">
              <span class="hint-text">Copyright © 2015 </span>
              <span class="font-montserrat">Georgia Strategic Prevention System</span>.
              <span class="hint-text">All rights reserved. </span>

            </p>
            <p class="small no-margin pull-right sm-pull-reset">
                <span class="sm-block"><a href="#" class="m-l-10 m-r-10">Terms of use</a> <span class="muted">&#8226;</span> <a href="#" class="m-l-10">Privacy Policy</a></span>
            </p>
            <div class="clearfix"></div>
          </div>
        </div>
        <!-- END COPYRIGHT -->
      </div>
      <!-- END PAGE CONTENT WRAPPER -->
    </div>
    <!-- END PAGE CONTAINER -->

    <!--START QUICKVIEW -->
    <div id="quickview" class="quickview-wrapper" data-pages="quickview">

            <ul class="no-style m-t-40 padding-30">
                <li><a href="#"><i class="sl-settings"></i> Settings</a>
                </li>
                </li>
                <li><a href="#"><i class="sl-question"></i> Help</a>
                </li>
                <li class="m-t-20">
                  <a href="#" class="clearfix">
                    <span class="pull-left">Logout</span>
                    <span class="pull-right"><i class="sl-logout"></i></span>
                  </a>
                </li>
              </ul>
      <a class="btn btn-default quickview-toggle" data-toggle-element="#quickview" data-toggle="quickview"><i class="pg-close"></i></a>

    </div>
    <!-- END QUICKVIEW-->
	<div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-success">Successfully Completed</h4>
      </div>
      <div class="modal-body">
        <div class="text-center m-t-30">
              <p>Thank you</p>
        </div>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->








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
    <script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
    <script src="assets/plugins/boostrap-form-wizard/js/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>


    <!-- END VENDOR JS -->
    <!-- BEGIN CORE TEMPLATE JS -->
    <script src="pages/js/pages.min.js"></script>
    <!-- END CORE TEMPLATE JS -->
    <!-- BEGIN PAGE LEVEL JS -->
    <script src="assets/js/form_wizard.js" type="text/javascript"></script>
    <script src="assets/js/form_elements.js" type="text/javascript"></script>
    <script src="assets/js/scripts.js" type="text/javascript"></script>
    <script src="assets/plugins/upload/all.fine-uploader.min.js"></script>
    <script src="assets/plugins/upload/upload-gallery.js"></script>



    <!-- END PAGE LEVEL JS -->
	<script>
	function btn_formsubmit() {
		if(document.getElementById('agency_id').value=='') {
			alert("Please Choose Agency");
			return false;
		}
		else {
		document.getElementById("ttaform").submit();
	}
	}

	$("button[name='next']").click(function() { if(document.getElementById('agency_id').value=='') { alert("Please Choose Agency"); window.location='tta_enquiry.php'; } });
	</script>

  </body>
</html>
