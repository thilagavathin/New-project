<?php
session_start();
if(isset($_SESSION['adminlogin'])){
header('Location:logout.php'); die;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Georgia Strategic Prevention System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="pages/css/pages-icons.css" rel="stylesheet" type="text/css">
    <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css" />
    <link class="main-stylesheet" href="assets/css/style.css" rel="stylesheet" type="text/css" />
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

<!-- START Forgot password Container -->
<div class="forgot-password">

    <!-- Logo -->
    <div class="logo-wrapper">
        <div class="text-center padding-30">
            <img src="assets/img/ecco-new1.png" alt="logo" style="width: 20%;">
            <h5 class="m-t-30 bold text-white">Training and Technical Assistance  Tracker</h5>

        </div>
    </div>
    <!-- End Logo -->

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sx-10">

                <h4 class="p-t-10 text-white">Enter your registered email ID</h4>
                <div id="answers" class=""></div>

                <!-- START Login Form -->
                <form id="forgetform" class="p-t-15" role="form" method="post" action="" >
                    <!-- START Form Control-->
                    <div class="form-group form-group-default">
                        <label>Email ID</label>
                        <div class="controls">
                            <input type="text" id="email" name="email" placeholder="hello@ecco.com" class="form-control" required>
                        </div>
                    </div>
                    <!-- END Form Control-->


                    <input type="submit" name="submit" value="Submit" class="btn btn-lg btn-success btn-block btn-cons m-t-10">

                    <div class="text-white text-center m-t-20 m-b-20 backtologin"><a href="login.php" >back to login</a></a></div>

                </form>
                <!--END Login Form-->

            </div>

        </div>
    </div>


</div>
<!-- END Forgot password Container-->

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
        $('#forgetform').validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: "forgetpassword_action.php",
                    type: "POST",
                    data: 'email='+$("#email").val(),
                    success: function(response) {
                        $('#answers').html(response);
                        $('#answers').addClass("form-group form-group-default has-error");
                        $("#email").val('');
                    }
                });

            }
        });


    });
</script>
</body>
</html>
