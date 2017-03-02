<?php
session_start();
if(isset($_SESSION['adminlogin'])){
header('Location:logout.php'); die;
}

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
.error {
color: red;
}
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
                        <div class="col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12 form-area">
                         <div class="homeform-content">
                         <!-- logo goes here-->
                            <div class="text-center">
                                 <img src="assets/img/ecco-new1.png" alt="ecco logo" width="170" class=" logo mar0 text-center">
                            </div>
                            <h2><div style="color: red;" id="answers" class=""></div></h2>
                         <!-- form area -->
                            <form class="form-horizontal login-form" id="form-register" method="post" role="form" action="">
                              <h2><span>STEP 1 :</span> Fill in your information</h2>  
                              <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" name="fname" class="form-control" placeholder="First Name" id="fname" required="" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" name="lname" id="lname" placeholder="Last Name" required="" class="form-control">
                                    </div>
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" name="myusername" id="myusername" placeholder="Username" class="form-control" required="" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" name="phone" id="phone" placeholder="(+123) 456-7890" class="form-control" required="" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input type="email" id="email" name="email" placeholder="Email - Login information will be sent to your email" class="form-control" required="" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                              </div>
                              <h2><span>STEP 2 :</span> Select your agency</h2>  
                              <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="input-group">
                                       
                                        <select onchange="fetch_select(this.value);" name="region" id="region" class="form-control" data-placeholder="Select your Region" aria-required="true" aria-invalid="true">
                                            <option value="">-- Select Region --</option>
                                            <option value="R-1">R1</option>
                                            <option value="R-2">R2</option>
                                            <option value="R-3">R3</option>
                                            <option value="R-4">R4</option>
                                            <option value="R-5">R5</option>
                                            <option value="R-6">R6</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <select class="form-control" id="agency_name" name="agency_name"  aria-required="true" aria-invalid="true">
                                        </select>
                                    </div>
                                </div>
                              </div>
                              <h2><span>STEP 3 :</span> Notes of Administrator</h2>  
                              <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <textarea name="comments" class="form-control" id="comments" placeholder="Write your notes here" aria-invalid="false"></textarea>
                                    </div>
                                </div>
                              </div>
                              <div class="form-group mar_t30">
                                <div class="col-sm-6 col-xs-8">
                                    <p><input checked type="checkbox" id="agreeterms" value="1"  name="agreeterms" />
                                     <label for="agreeterms" class="text-white">I agree to the <a href="#">Terms</a> and <a href="#">Privacy</a>.</label></p>
                                    <p>Already register member?<a href="login.php" class="small forget-pwd">Login</a></p>
                                </div>
                                <div class="col-sm-6 col-xs-4">
                                    <button class="login_btn pull-right" type="submit">Create a new account</button>
                                </div>
                              </div>
                            </form>
                        <!-- form ends -->
                            
                         </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
            <!-- header ends -->
            
   
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
<script type="text/javascript">
    $(function() {

        $('#form-register').validate({
            rules: {
                agreeterms: {
                    required: true
                }
            },
            submitHandler: function(form) {
                var fname = $("#fname").val();
                var lname = $("#lname").val();
                var myusername = $("#myusername").val();
                var phone = $("#phone").val();
                var email = $("#email").val();
                var region = $("#region").val();
                var agency_name = $("#agency_name").val();
                var comments = $("#comments").val();

                var formData = {fname:fname,lname:lname,username:myusername,phone:phone,email:email,region:region,agency_name:agency_name,comments:comments};
                $.ajax({
                    url: "signup_action.php",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        var result=myTrim(response);
                        if(result=='success') 
                        {
                          window.location.href='login.php?msg=success';  
                        }
                        
                        else {
                            $('#answers').html(response);
                            $('#answers').addClass("form-group form-group-default has-error");
                        }

                    }
                });

            }
        });

    });

    function fetch_select(val)
    {
        $.ajax({
            type: 'post',
            url: 'fetch_data.php',
            data: {
                get_option:val
            },
            success: function (response) {
                document.getElementById("agency_name").innerHTML=response;
            }
        });
    }
    function myTrim(x) {
        return x.replace(/^\s+|\s+$/gm,'');
    }
</script>

</body>
</html>
