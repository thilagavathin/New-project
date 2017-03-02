<?php
include_once('config.php');
error_reporting(0);
ob_start();
    $username = isset($_POST["email"])? $_POST["email"]:'';
    if($username==''){
        echo "Email Id Is Required";
        die;
    }
    $password = substr(md5(rand().rand()), 0, 6);

    $sql = mysql_query("SELECT user_id,username,name,email,user_level FROM login_users WHERE email = '".$username."' AND user_id<>1 ");
if(mysql_num_rows($sql)>0)
{
    $row=mysql_fetch_array($sql);
    $name = $row["name"];
    $username = $row["username"];
    $email = $row["email"];
    $user_level = $row["user_level"];
    $update_query = mysql_query("UPDATE login_users SET password='".md5($password)."' WHERE user_id='".$row["user_id"]."'") ;
    if($update_query){
        $msg = "Password will be generated and sent to your email.";
        /* Start mail area */
        $u_name=$name;
        $info_arr=array();
        $info_arr['to']=$email;
        $info_arr['from']=	'ceoden <ceoden@ingowhiz.com> ';
        $info_arr['BCC']='';
        $info_arr['CC']='';
        $info_arr['subject']="Request for Forgot Password with ECCO TTA !";
        $info_arr['username']=$u_name;
        $info_arr['message_title']='';
        $info_arr['message_body']='
<table style="padding:0px;width:100%;position:relative;" class="row">
<tr>
<td style="padding:10px 10px 0px 0px;position:relative; padding-right:0px;" class="wrapper last">
<table style="width:580px;margin:0 auto;" class="twelve columns">
<tr>
<td>
<h1 style="color:#10cfbd;font-size:40px;">Forgot Password</h1>

</td>
<td class="expander"></td>
</tr>
</table>
</td>
</tr>
</table>

<table class="row">
<tr>
<td class="wrapper last">
<table class="twelve columns">
<tr>
<td>
<h4 style="color:#6d5cae;">Hello, '.$u_name.'</h4>
<p> Your new Password is <b>'.$password.'</b>. Click here to <a href="http://ecco.ga-sps.org/login.php" target="_blank">login</a> with your account at http://ecco.ga-sps.org</p>
</td>
<td class="expander"></td>
</tr>
</table>
</td>
</tr>
</table>
';
        @send_mail_template($info_arr);
    echo $msg;
    }
    else echo "Please check your Email Id";
}
else echo "You are not a user";
die;
?>
