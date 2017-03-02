<?php
session_start();
if(isset($_SESSION['adminlogin'])){
header('Location:logout.php'); die;
}
$username = isset($_COOKIE['username'])? $_COOKIE['username']:'';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ecco</title>

    <!-- Bootstrap Core CSS -->
    <link href="new/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="new/css/full.css" rel="stylesheet">

    <!-- font awesome icons -->
    <link rel="stylesheet" href="new/css/font-awesome.min.css">

    <style type="text/css">
        body,html{ height: 100%; }
    </style>


</head>
<body>
    <!-- Full Page Image Background Carousel Header -->
        <section class="banner">
            <div id="myCarousel" class="carousel slide">
                <!-- Wrapper for Slides -->
                <div class="carousel-inner">
                    <div class="item active">
                        <div class="fill" style="background-image:url('new/images/banner1.jpg');"></div>
                    </div>
                    <div class="item">
                        <div class="fill" style="background-image:url('new/images/banner3.jpg');"></div>
                    </div>
                    <div class="item">
                        <div class="fill" style="background-image:url('new/images/banner4.jpg');"></div>
                    </div>
                    <div class="item">
                        <div class="fill" style="background-image:url('new/images/banner5.jpg');"></div>
                    </div>
                </div>
            </div>
            <section class="home-form">
                <div class="container">
                    <div class="row mar0">
                        <div class="col-md-4 col-md-offset-8 col-sm-6 col-sm-offset-6 col-xs-12 form-area">
                         <div class="homeform-content">
                         <!-- logo goes here-->
                            <div class="text-center">
                                 <img src="new/images/logo.png" alt="ecco logo" width="270" class=" logo text-center">
                            </div>
                            
                         <!-- form area -->
                            <form class="form-horizontal login-form" id="form-login">
                              <div class="form-group mar_b40">
                                <div class="col-sm-12">
                                  <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user fa-2x"></i></span>
                                    <input type="text" class="form-control" placeholder="Username" name="username" id="username" value="<?php echo $username; ?>">
                                  </div>
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-sm-12">
                                  <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key fa-2x"></i></span>
                                    <input type="password"  class="form-control" placeholder="Password" id="password" name="password">
                                  </div>
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-sm-8 col-xs-8">
                                    <label class="mar_t5 text_blue">
                                      <input type="checkbox" id="checkbox" value="option1">
                                      <span class="custom-icon checkbox-icon"></span>Keep me signed in
                                    </label>
                                </div>
                                <div class="col-sm-4 col-xs-4">
                                    <button class="login_btn pull-right" id="login">Login</button>
                                </div>
                              </div>
                            </form>
                        <!-- form ends -->
                            <div class="join-now">
                                <h1>New to ECCO?</h1>
                                <a href="signup.php" class="text-capitalize text_blue">join now</a>
                                <p class="text-capitalize mar_t10">georgia training and technical assistance tracker</p>
                                <div class="row text-center">
                                    <figure class="col-sm-12 col-xs-6">
                                        <img src="new/images/pgroup_full_new.png" class="mar_t10" width="180" alt="gaspa logo" >
                                    </figure>
                                </div>
                            </div>
                         </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
            <!-- header ends -->
            
        
    <!-- jQuery -->
    <script src="new/js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="new/js/bootstrap.min.js"></script>
    <!-- Script to Activate the Carousel -->
    <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="new/js/sweet-alert/dist/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="new/js/sweet-alert/dist/sweetalert.css">
    <script>
    $('.carousel').carousel({
        interval: 5000, //changes the speed
        pause: "false"
    })
    </script>
    <script type="text/javascript">
        $( "input" ).focus(function() {
            $( this ).prev(".input-group-addon").addClass("input-focus");
        }).blur(function(){
            $( this ).prev(".input-group-addon").removeClass("input-focus");
        });
    </script>
    <script type="text/javascript">
    $(document).ready(function(){
        $( "#login" ).click(function() {
            var username = $("#username").val();
            var password = $("#password").val();
            if($("#checkbox").is(':checked')){
               var signedin=1;
            }else{
                var signedin=0;
            }
            if(username != "" && password != ""){
                $.ajax({
                    url: "login_action.php",
                    type: "POST",
                    data: 'username='+$("#username").val()+'&password='+$("#password").val()+'&signedin='+signedin,

                    success: function(response) {
                        var $success = $.trim(response);
                        if($success=='success'){
                            window.location.href='systemdashboard.php';
                        } 
                        else
                        {
                            sweetAlert("Oops...", $success, "error");

                        }
                    }
                });
                return false;
            }else{
                sweetAlert("Oops...", "Required Fields Are Missing", "error");
                return false;
            }
        });
    });
</script>
</body>
</html>
