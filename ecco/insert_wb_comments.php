<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
date_default_timezone_set('America/New_York');
$current_date = date('Y-m-d H:i:s');
$agency_id= $_POST['agency'];
$strategy = $_POST['strategy'];
$node = $_POST['node'];
$node_name = $_POST['node_name'];
$wb_comments=mysql_real_escape_string($_POST['wb_comments']);
$user_id=$_SESSION['adminlogin'];
$username=$_SESSION['displayname'];

$com_subject = ($_POST['com_subject']!='' ? $_POST['com_subject'] : "Comments From WorkBundle");
$com_user = $_POST["com_user"];
$com_admin = $_POST["com_admin"];
$com_email = $_POST["com_email"];
$com_other = $_POST["com_other"];

$a = mysql_query("SELECT name FROM agency WHERE id='".$agency_id."'") or die("Query Error");
while($row=mysql_fetch_array($a)){
    $a_name = $row["name"];
}

$bundle_sql ="SELECT * FROM interventions WHERE id=".$strategy ;
$bundle_details = mysql_query($bundle_sql);
$bundle_row = mysql_fetch_assoc($bundle_details);
$strategy_name=$bundle_row['intervention_name'];

$info_arr_admin=array();
$info_arr_admin['from']='mbouligny@progroup.us';
$info_arr_admin['BCC']='';
$info_arr_admin['CC']='';
$info_arr_admin['subject']=$com_subject;
$info_arr_admin['message_title']='';


$info_arr_admin['message_body']='
<table style="width: 100%;">
<tr>
  	<!-- subject details -->
  	 <td style="padding: 20px;">
  	 	<h2 style="color:#284fa3; font-family: \'Roboto\'; line-height:20px;"><i>Subject</i></h2>
  	 	<p style="font-family: \'Roboto\'; font-weight:300; font-size:18px;">'.$com_subject.' </p>
  	 </td>
  	 <!-- end subject details -->
  </tr>
  <tr>
  	<!-- Comment details -->
  	 <td bgcolor="#e9e9e9" style="padding:30px 20px;  border-top:1px solid #c2c2c2; border-bottom:1px solid #c2c2c2;">
  	 	<table style="width:100%; ">
  	 		<tr>
  	 			<td style="padding-top:0; padding-left:20px;">
  	 				<p style="font-family: \'Roboto\'; margin-top:0; font-size:18px; font-weight:500;">New Comment from <a href="" style="color:#284fa3; font-weight:500; text-decoration:none;">'.$username.'</a></p>
  	 				<h2 style="font-family: \'Roboto\'; font-weight:500;">'.$a_name.' - '.$strategy_name.' - '.$node_name.'</h2>
  	 				<p style="font-family: \'Roboto\'; font-weight:300; color:#284fa3; font-size:18px;">'.$wb_comments.'</p>
  	 				<p style="font-family: \'Roboto\';  font-weight:300; text-align: right; text-decoration: underline;  font-size:14px; margin-bottom:0;"><a href="'.$site_url.'"><i>Log into Ecco to see your comments</i></a></p>
  	 			</td>
  	 		</tr>
  	 	</table>
  	 </td>
  	 <!-- end Comment details -->
  </tr>
</table>
';

if( $com_admin == 1 ){
    $admin_level='s:1:"1";';
    $get_email = array();$email_bcc='';
    $sql="SELECT user_id,username,user_level,email FROM login_users WHERE user_level like '%".mysql_real_escape_string($admin_level)."%'";
    $result_mail = mysql_query($sql);
    while($row=mysql_fetch_array($result_mail)) { $get_email[] =$row['email']; }
    $email_bcc = implode(",",$get_email);
    // Admin User Level Email Contents
    $u_name='Administrator';
	$email_bcc="vanitha.m@vividinfotech.com";
    $info_arr_admin['to']=$email_bcc;
    $info_arr_admin['username']=$u_name;
}

if($com_user == 2){
    $agency_user_id = array();
    $get_agency_query = "SELECT user_id FROM agency_map WHERE agency_id = '".$agency_id."'";
    $get_agency = mysql_query($get_agency_query);
    if( count(mysql_num_rows($get_agency))){
        while($row=mysql_fetch_array($get_agency)) {
            $agency_user_id =$row['user_id'];
        }
    }
    $sql="SELECT user_id,username,user_level,email FROM login_users WHERE user_id IN ('". implode(',',$agency_user_id)."')";
    $result_mail = mysql_query($sql);
    $count = mysql_num_rows($result_mail);
    if( $count > 0){
        while($row=mysql_fetch_array($result_mail)) {
            $get_email =$row['email'];
            $u_name =$row['username'];
        }
        // Admin User Level Email Contents
        $info_arr_admin['to']=$get_email;
        $info_arr_admin['username']=$u_name;
    }
}

if($com_other == 3){
    $info_arr_admin['to']=$com_email;
    $info_arr_admin['username']=$u_name;
}

$insert_query="INSERT INTO wb_comments (username,user_id,agency_id,intervention_id,node_id,node_name,comments,created_date) VALUES ('".$username."',".$user_id.",".$agency_id.",'".$strategy."','".$node."','".$node_name."','".$wb_comments."','".$current_date."')";
$result =mysql_query($insert_query);
if($result==1) echo 'success'; else echo 'failure';
?>
