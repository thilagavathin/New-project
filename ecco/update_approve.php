<?php
ob_start(); session_start(); include_once('config.php');
$approve_u=(isset($_POST['case']))? $_POST['case']:'';
if($approve_u<>'')
{
    foreach($approve_u as $ls)
    {
        $password = substr(md5(rand().rand()), 0, 7);
        $sql_update="UPDATE login_users SET approved='YES',password='".md5($password)."' WHERE user_id=".$ls." ";
		$result= mysql_query($sql_update);
        if($result==1)
        {
            $get_info=mysql_query("SELECT email,name,username FROM login_users WHERE user_id=".$ls." ");
            $get_info_ar=mysql_fetch_row($get_info);
            $info_arr=array();
            $info_arr['to']=$get_info_ar[0];
            $info_arr['from']='ceoden@ingowhiz.com';
            $info_arr['BCC']='';
            $info_arr['CC']='';
            $info_arr['subject']='Your account has been Appoved in ECCO ';
            $info_arr['username']=$get_info_ar[1];
            $info_arr['message_title']='';
            $message_uplaod='';
            $font_family="'Helvetica','Arial'";
            $info_arr['message_body']='
<table class="row">
<tr>
<td class="wrapper last">
<table class="twelve columns">
<tr>
<td>
<h4 style="color:#6d5cae;">Hello, '.$get_info_ar[1].'</h4>
<p> You account has been approved in <a href="http://ecco.ga-sps.org" target="_blank"> ecco.ga-sps.org </a> </p>
</td>
<td class="expander"></td>
</tr>
</table>
</td>
</tr>
</table>

<table style="padding:0px;width:100%;position:relative;" class="row callout">
<tr>
<td style="padding:10px 20px 0px 0px;position:relative; padding-right:0px;" class="wrapper last">
<table style="width:580px;margin:0 auto;" class="twelve columns">
<tr>
<td style="background:#fafafa;border-color:#CCC;padding:10px; border: 1px solid #d9d9d9;" class="panel">
<p> </p>
<p>Name: '.$get_info_ar[1].'</p>
<p>Email: '.$get_info_ar[0].'</p>
<p>Username:'.$get_info_ar[2].'</p>
<p>Password: '.$password.'</p><br>

</td>

<td class="expander"></td>
</tr>
</table>
</td>
</tr>
</table>
';
            send_mail_template($info_arr);
        }
    }
    if($result==1) header('Location:user_approval.php?msg=success');
    else header('Location:user_approval.php');
    die;
}
else
{
    header('Location:user_approval.php');
    die;
}
?>