<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
function get_user_image_ajax($userid)
{
    $result_mail=mysql_query("SELECT user_image FROM login_users where user_id =".$userid);
    $user_photo = mysql_fetch_assoc($result_mail);
    if($user_photo['user_image']<>'') {
        $user_img = @unserialize($user_photo['user_image']);
        $return =$user_img[0];
    }
    else $return='';
    return $return;
}
$cur_month=date('m');
$cur_day=date('d');
$today=date('Y-m-d');
$user_id=$_SESSION['adminlogin'];
if($cur_day < 5)
{
    $report_start=date('Y-m-d H:i:s',mktime(0,0,0,date('m')-1,'5',date('Y')));
    $report_end=date('Y-m-d H:i:s',mktime(23,59,59,date('m'),'4',date('Y')));
}
else
{
    $report_start=date('Y-m-d H:i:s',mktime(0,0,0,date('m'),'5',date('Y')));
    $report_end=date('Y-m-d H:i:s',mktime(23,59,59,date('m')+1,'4',date('Y')));
}
$agency_id= $_POST['agency'];
$report_id= $_POST['report'];
$agency_name=mysql_query("SELECT name FROM agency WHERE id=".$agency_id);
$agencyname=mysql_fetch_row($agency_name);

if($_SESSION['userrole']==3) mysql_query("UPDATE TTA_Report_comment SET normal_status='Y' WHERE agency_id=".$agency_id."");

if($_SESSION['userrole']==3) $comment_query=mysql_query("SELECT comment,U.username,create_date,userid,user_image FROM TTA_Report_comment C inner join login_users U on U.user_id=C.userid WHERE agency_id=".$agency_id." and status='N' and normal_status='Y' order by create_date desc");
else $comment_query=mysql_query("SELECT comment,U.username,create_date,userid,user_image FROM TTA_Report_comment C inner join login_users U on U.user_id=C.userid WHERE agency_id=".$agency_id."  order by create_date desc ");

$report_note=mysql_query("SELECT report_note,date,fname,lname FROM TTA_Reports_uploads WHERE date>='".$report_start."' AND date <='".$report_end."' and agency=".$agency_id." and report_note<>''");
$notes=mysql_fetch_assoc($report_note);
$comment_query_c=mysql_query("SELECT id FROM TTA_Report_comment C inner join login_users U on U.user_id=C.userid WHERE agency_id=".$agency_id." ");
$num_rows=0;
$num_rows = mysql_num_rows($comment_query_c);

$sql_reg=mysql_query("SELECT id FROM tta_reports_comments_status WHERE agency_id='".$agency_id."' AND user_id='".$user_id."' ");
$row_reg=mysql_fetch_row($sql_reg);
if($row_reg[0]) mysql_query("UPDATE tta_reports_comments_status SET comment_count=".$num_rows." WHERE agency_id=".$agency_id." AND user_id='".$user_id."' ");
else mysql_query("INSERT INTO tta_reports_comments_status (agency_id,user_id,comment_count) VALUES (".$agency_id.",".$user_id.",".$num_rows.")");

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
    if($notes['report_note'])
    {
        $img_val ="assets/img/photo.jpg";
        ?>
        <?php
    }
    
    ?>
                                  </ul>
                                </div>
