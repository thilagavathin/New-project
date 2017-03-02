<?php
include_once('config.php');
ob_start(); session_start();

//echo $siteurl; die();
$user_level = array("3");
$fname =isset($_POST["fname"])?$_POST["fname"]:'';
$lname=isset($_POST["lname"])? $_POST["lname"]:'';

$username = isset($_POST["username"])? $_POST["username"]:'';
$phone = isset($_POST["phone"])? $_POST["phone"]:'';
$email = isset($_POST["email"])? $_POST["email"]:'';
$region = isset($_POST["region"])? $_POST["region"]:'';
$agency_name = isset($_POST["agency_name"])? trim($_POST["agency_name"]):'';
$comments = isset($_POST["comments"])? $_POST["comments"]:'';
$approved = "NO";
$error = true;
if($fname<>'' && $email<>'' && $username<>'')
{
    if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email))
    {
        $error = false;
        echo "Invalid email format";
    }
    else
    { 
        $sql = mysql_query("SELECT user_id FROM login_users where username ='".trim($_POST['username'])."' OR email='".trim($_POST['email'])."'");
        if(mysql_num_rows($sql)==0)
        {
            $name=$fname.' '.$lname;
            $result = mysql_query("INSERT INTO login_users (user_level, username, name, email, password, AgencyName, administrator_notes,phone,region, approved) values ('" . serialize($user_level) . "', '" . $username . "', '" . $name . "', '" . $email . "', '', '".$agency_name."', '".$comments."','".$phone."','".$region."' ,'".$approved."' )");
            $user_insert_id=mysql_insert_id();
            if($result){
                $sql_agency=mysql_query("SELECT id FROM agency WHERE name='".$agency_name."' ");
                $user_agency_row=mysql_fetch_row($sql_agency);
                $select_agency_id=$user_agency_row[0];
                $insert_query="INSERT INTO agency_map (user_id,agency_id) VALUES (".$user_insert_id.",".$select_agency_id.")";
                mysql_query($insert_query);
                /* start email area */
                $u_name=$name;
                $info_arr=array();
                $info_arr['to']=$email;
                $info_arr['from']='mbouligny@progroup.us';
                $info_arr['BCC']='';
                $info_arr['CC']='';
                $info_arr['subject']="You're registered with ECCO TTA !";
                $info_arr['username']=$u_name;
                $info_arr['message_title']='';
                $info_arr['message_body']='
<table style="padding:0px;width:100%;position:relative;" class="row">
<tr>
<td style="padding:10px 10px 0px 0px;position:relative; padding-right:0px;" class="wrapper last">
<table style="width:580px;margin:0 auto;" class="twelve columns">
<tr>
<td>
<h1 style="color:#10cfbd;font-size:40px;">Thanks for registering</h1>

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
<p> You are now registered at '.$site_url.' Here are your account details: </p>
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
<p>Name: '.$name.'</p>
<p>Username:'.$username.'</p>
<p>Email:'.$email.'</p>

<p>Your account is waiting for admin approval for '.$site_url.'. Once your account has been approved, We will send User Name and Password. you will be login in '.$site_url.'.</p>

</td>

<td class="expander"></td>
</tr>
</table>
</td>
</tr>
</table>
';
                $admin_level='s:1:"1";';
                $get_email = array();$email_bcc='';
                $sql="SELECT user_id,username,user_level,email FROM login_users WHERE user_level like '%".mysql_real_escape_string($admin_level)."%'";
                $result_mail = mysql_query($sql);
                while($row=mysql_fetch_array($result_mail)) { $get_email[] =$row['email']; }
                $email_bcc = implode(",",$get_email);
                // Admin User Level Email Contents
                $u_name='Administrator';
                $info_arr_admin=array();
                $info_arr_admin['to']='mbouligny@progroup.us';
                $info_arr_admin['from']='mbouligny@progroup.us';
                $info_arr_admin['BCC']= '';  //$email_bcc;
                $info_arr_admin['CC']='';
                $info_arr_admin['subject']="New user has registered with ECCO TTA !";
                $info_arr_admin['username']=$u_name;
                $info_arr_admin['message_title']='';
                $info_arr_admin['message_body']='
<table style="padding:0px;width:100%;position:relative;" class="row">
<tr>
<td style="padding:10px 10px 0px 0px;position:relative; padding-right:0px;" class="wrapper last">
<table style="width:580px;margin:0 auto;" class="twelve columns">
<tr>
<td>
<h1 style="color:#10cfbd;font-size:40px;">New User has registered in Ecco [ecco.ga-sps.org] </h1>

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
<p> New User has registered at '.$site_url.' Here are your account details: </p>
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
<p>Name: '.$name.'</p>
<p>Username:'.$username.'</p>
<p>Email:'.$email.'</p>

</td>

<td class="expander"></td>
</tr>
</table>
</td>
</tr>
</table>
';
				 //print_r($info_arr_admin); echo '<br>';
				 //print_r($info_arr);
				
                 @send_mail_template($info_arr_admin);
                 @send_mail_template($info_arr);
                echo 'success';

            }
        }
        else
        {
            $error = false;
            echo  "Username or Email Id is already exists";
        }
    }
}
else echo "Fill all mandatory fields";
//die;
?>
