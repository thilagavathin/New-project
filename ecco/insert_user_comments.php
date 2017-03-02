<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
if(isset( $_SESSION['adminlogin'])){
    $current_user = $_SESSION['adminlogin'];
}
date_default_timezone_set('America/New_York');
$current_date = date('Y-m-d H:i:s');

$agency_id= $_POST['com_agency'];
$report_id= $_POST['com_report'];
$user_comments=mysql_real_escape_string($_POST['user_comments']);
$user_id=$_SESSION['adminlogin'];
$username=$_SESSION['displayname'];

$com_subject = $_POST["com_subject"];
$com_user = $_POST["com_user"];
$com_admin = $_POST["com_admin"];
$com_email = $_POST["com_email"];
$com_other = $_POST["com_other"];

$a_name = "";

//User Details
$current_user_userimage = $current_user_name = $current_user_username  = ""; $img_val = $site_url."/";
$current_users = mysql_query("SELECT user_id, user_level, username, name, user_image, region FROM login_users WHERE user_id=".$_SESSION['adminlogin']." AND approved = 'YES'") or die("Query 3 Error");
while($row=mysql_fetch_array($current_users)) {
    $current_user_username =trim($row['username']);
    $current_user_name =trim($row['name']);
    $current_user_userimage = trim($row['user_image']);
}

if($current_user_userimage <> '') $current_user_userimage=@unserialize($current_user_userimage); else $current_user_userimage='';
if($current_user_userimage=='')  $img_val .="assets/img/photo.jpg";
else $img_val .="assets/profile/".$current_user_userimage[0];

//Agency
$a = mysql_query("SELECT name FROM agency WHERE id='".$agency_id."'") or die("Query Error");
while($row=mysql_fetch_array($a)){
    $a_name = $row["name"];
}

$info_arr_admin=array();
$info_arr_admin['from']='mbouligny@progroup.us';
$info_arr_admin['BCC']='';
$info_arr_admin['CC']='';
$info_arr_admin['subject']='Comment from ECCO';
$info_arr_admin['message_title']='';

$info_arr_admin['message_body']='
<table>
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
  	 			<td style="text-align:center; ">
  	 				<img src="'.$img_val.'" width="130" style="border:2px solid #d4dced; border-radius:50%; width:130px; height:130px; ">
  	 				<p style="font-family: \'Roboto\'; font-weight:500; font-size:18px; margin-bottom:0;">'.$current_user_name.'</p>
  	 			</td>
  	 			<td style="padding-top:0; padding-left:20px;">
  	 				<p style="font-family: \'Roboto\'; margin-top:0; font-size:18px; font-weight:500;">New Comment from <a href="" style="color:#284fa3; font-weight:500; text-decoration:none;">'.$username.'</a></p>
  	 				<h2 style="font-family: \'Roboto\'; font-weight:500;">'.$a_name.'</h2>
  	 				<p style="font-family: \'Roboto\'; font-weight:300; color:#284fa3; font-size:18px;">
                       Made a comment that was added to your report node on '.date("M d, Y").', at '.date("h:i a").'.
                    </p>
  	 				<p style="font-family: \'Roboto\';  font-weight:300; text-align: right; text-decoration: underline;  font-size:14px; margin-bottom:0;"><a href="'.$site_url.'"><i>Log into Ecco to see your comments</i></p>
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
    $info_arr_admin['to']=$email_bcc;
    $info_arr_admin['username']=$u_name;
    @send_mail_template($info_arr_admin);
}
if($com_user == 2){
    $agency_user_id = array();
    $get_agency_query = "SELECT user_id FROM agency_map WHERE agency_id = '".$agency_id."'";
    $get_agency = mysql_query($get_agency_query);
    if( count(mysql_num_rows($get_agency))){
        while($row=mysql_fetch_array($get_agency)) {
            $agency_user_id[] =$row['user_id'];
        }
        $agency_user_id = implode(",",$agency_user_id);
    }
    $sql="SELECT user_id,username,user_level,email FROM login_users WHERE user_id IN (". $agency_user_id.")";
    $result_mail = mysql_query($sql);
    $count = mysql_num_rows($result_mail);
    if( $count > 0){
        while($row=mysql_fetch_array($result_mail)) {
            $get_email =$row['email'];
            $u_name =$row['username'];
        
        // Admin User Level Email Contents
		
        $info_arr_admin['to']=$get_email;
        $info_arr_admin['username']=$u_name;
        @send_mail_template($info_arr_admin);
        }
    }   
}

if($com_other == 3){
    $info_arr_admin['to']=$com_email;
    $info_arr_admin['username']=$u_name;
    @send_mail_template($info_arr_admin);
}

$insert_query="INSERT INTO TTA_Report_comment (username,userid,agency_id,report_id,comment,create_date) VALUES ('".$username."',".$user_id.",".$agency_id.",".$report_id.",'".$user_comments."','".$current_date."')";
$result =mysql_query($insert_query);

if($_SESSION['userrole']==3) mysql_query("UPDATE TTA_Report_comment SET normal_status='Y' WHERE id=".mysql_insert_id()."");

if($_SESSION['userrole']==3) $comment_query=mysql_query("SELECT comment,U.username,create_date,userid,user_image FROM TTA_Report_comment C inner join login_users U on U.user_id=C.userid WHERE agency_id=".$agency_id." AND status='N' and normal_status='Y' order by create_date desc");
else $comment_query=mysql_query("SELECT comment,U.username,create_date,userid,user_image FROM TTA_Report_comment C inner join login_users U on U.user_id=C.userid WHERE agency_id=".$agency_id." order by create_date desc ");
$agency_name=mysql_query("SELECT name FROM agency WHERE id=".$agency_id);
$agencyname=mysql_fetch_row($agency_name);
?>
    <h5 class="m-b-20"><?php echo $agencyname[0]; ?></h5>
	<div class="modal-footer no-border">		
                                <ul>
<?php
while($ls= mysql_fetch_array($comment_query) ) {
    if(trim($ls['user_image'])<>'') {
        $user_img = @unserialize($ls['user_image']);
        $img_val ="assets/profile/".$user_img[0];
    }
    else $img_val ="assets/img/photo.jpg";
    ?>
     <!-- left comment area -->
                                   <li class="row left-comment text-left">
                                      <div class="col-md-2 col-sm-2">
                                          <img src="<?php echo $img_val;?>" alt="profile icon" class="profile-image">
                                      </div>
                                      <div class="col-md-10 col-sm-10 pad_l0">
                                          <div class="comment-info">
                                             <p><span class="chat-username"><?php echo  $ls['username']; ?></span><small class="chat-datelocation"><i><?php echo date('d M Y h:i A',strtotime($ls['create_date'])); ?> NYC, New York</i></small></p>
                                             <span class="clearfix"></span>
                                             <p class="chat-content mar_tb10"><?php echo $ls['comment']; ?>
                                             </p>
                                          </div>
                                      </div>
                                   </li>
<?php
}
?>
                                  </ul>
                                </div>
