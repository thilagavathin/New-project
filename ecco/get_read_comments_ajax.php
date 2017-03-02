<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$cur_month=date('m');
$cur_day=date('d');
$today=date('Y-m-d');

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
$comment_query=mysql_query("SELECT comment,U.username,create_date,userid,user_image FROM TTA_Report_comment C inner join login_users U on U.user_id=C.userid WHERE agency_id=".$agency_id." and normal_status='Y' order by create_date desc");

$report_note=mysql_query("SELECT report_note,date,fname,lname FROM TTA_Reports_uploads WHERE date>='".$report_start."' AND date <='".$report_end."' and agency=".$agency_id." and report_note<>''");
$notes=mysql_fetch_assoc($report_note);


?>
<h5 class="m-b-20"><?php echo $agencyname[0]; ?></h5>
<div class="quickview-comments comments-wrapper">
    <?php

    while($ls= mysql_fetch_array($comment_query) ) {
        if(trim($ls['user_image'])<>'') {
            $user_img = @unserialize($ls['user_image']);
            $img_val ="assets/profile/".$user_img[0];
        }
        else $img_val ="assets/img/photo.jpg";
        ?>
        <div class="card share comment">
            <div class="circle" data-toggle="tooltip" title="" data-container="body" data-original-title="Label">
            </div>
            <div class="card-header clearfix">
                <div class="user-pic">
                    <img alt="Profile Image" width="33" height="33" src="<?php echo $img_val; ?>">
                </div>
                <h5><?php echo $ls['username']; ?></h5>
                <h6><?php echo date('d M Y h:i A',strtotime($ls['create_date'])); ?>
                    <span class="location semi-bold"><i class="fa fa-map-marker"></i> NYC, New York</span>
                </h6>
            </div>
            <div class="card-description">
                <p><?php echo $ls['comment']; ?></p>
            </div>
        </div>
        <?php
    }
    if($notes['report_note'])
    {
        $img_val ="assets/img/photo.jpg";
        ?>
        <div class="card share comment">
            <div class="circle" data-toggle="tooltip" title="" data-container="body" data-original-title="Label">
            </div>
            <div class="card-header clearfix">
                <div class="user-pic">
                    <img alt="Profile Image" width="33" height="33" src="<?php echo $img_val;?>">
                </div>
                <h5><?php echo $notes['fname'].' '.$notes['lname']; ?></h5>
                <h6><?php echo date('d M Y h:i A',strtotime($notes['date'])); ?>
                    <span class="location semi-bold"><i class="fa fa-map-marker"></i> NYC, New York</span>
                </h6>
            </div>
            <div class="card-description">
                <p><?php echo $notes['report_note']; ?></p>
            </div>
        </div>
        <?php
    }
    ?>
</div>
