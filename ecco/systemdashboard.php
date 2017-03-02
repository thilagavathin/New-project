<?php include_once('templates/header.php');
date_default_timezone_set('America/New_York');
$today = date("l, F j, Y, g:i A");
$agency_in='';
if($_SESSION['userrole']<>1){
$agency_join='';
$sql_agency="SELECT agency_id FROM agency_map WHERE user_id=".$_SESSION['adminlogin'];
$sql_row = mysql_query($sql_agency);
while($row=mysql_fetch_array($sql_row)) {
    $agency_join.=trim($row['agency_id']).',';
}
$agency_in=substr($agency_join, 0, -1);
if($_SESSION['userrole']==3 ) { if($agency_in=='') $user_base_agency=" and agency_id in (0)"; else $user_base_agency=" and agency_id in (".$agency_in.")"; }
else if(($_SESSION['userrole']==2 || $_SESSION['userrole']==4) && $agency_in <>'') $user_base_agency=" and agency_id in (".$agency_in.")";
else $user_base_agency=" and agency_id in (0)";
}
else { $agency_in=''; $user_base_agency=''; }
$agency_in=($agency_in=='')? 0:$agency_in;
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
$report_start=date('Y-m-d H:i:s',mktime(0,0,0,date('m')-2,'5',date('Y')));
$report_end=date('Y-m-d H:i:s',mktime(23,59,59,date('m')+1,'4',date('Y')));
$agency_comment= mysql_query("SELECT COUNT(comment) flag FROM TTA_Report_comment WHERE create_date>='".$report_start."' AND create_date <='".$report_end."' AND status='N' AND normal_status='N' ".$user_base_agency);
$comment=mysql_fetch_row($agency_comment);
$ordering_contract=0;
$pre_month=date('Y-m-d H:i:s',mktime(0,0,0,date('m')-2,date('d')-60,date('Y')));
if($agency_in<>'' || $_SESSION['userrole']<>1)
{
    $sql_order_contract=mysql_query("SELECT contract_num,agency_id FROM tta_regarding_status WHERE agency_id in ($agency_in) and updated_date >='".$pre_month."'");
    while($row=mysql_fetch_array($sql_order_contract))
    {
        $tta_comment=mysql_query("SELECT COUNT(id) FROM tta_regarding_notes where view_status='N' AND contract_num='".$row['contract_num']."' AND agency_id='".$row['agency_id']."'");
        $comment_tta=mysql_fetch_row($tta_comment);

        $tta_comment_user=mysql_query("SELECT comment_count FROM tta_regarding_status WHERE agency_id=".$row['agency_id']." AND contract_num='".$row['contract_num']."' AND user_id=".$_SESSION['adminlogin']);
        $comment_tta_user=mysql_fetch_row($tta_comment_user);
        $comment_tta_user_count=mysql_num_rows($tta_comment_user);
        $rega_count=$comment_tta[0]-$comment_tta_user[0];
        if($rega_count>0) { $ordering_contract=1; break; }
    }
}
else
{
    $sql_order_contract=mysql_query("SELECT contract_num,agency_id FROM tta_regarding_status WHERE updated_date >='".$pre_month."'");
    while($row=mysql_fetch_array($sql_order_contract))
    {
        $tta_comment=mysql_query("SELECT COUNT(id) FROM tta_regarding_notes where view_status='N' AND contract_num='".$row['contract_num']."' AND agency_id='".$row['agency_id']."'");
        $comment_tta=mysql_fetch_row($tta_comment);
        $tta_comment_user=mysql_query("SELECT comment_count FROM tta_regarding_status WHERE agency_id=".$row['agency_id']." AND contract_num='".$row['contract_num']."' AND user_id=".$_SESSION['adminlogin']);
        $comment_tta_user=mysql_fetch_row($tta_comment_user);
        $rega_count=$comment_tta[0]-$comment_tta_user[0];
        if($rega_count>0) { $ordering_contract=1; break; }
    }
}
$community_comment=mysql_query("SELECT COUNT(id) FROM community_comments where view_status='N'");
$community_count=mysql_fetch_row($community_comment);
/*
 * Chat count
 */
$sender_id = $_SESSION['adminlogin'];
$group_chat_count = $chats_count = $chat_count1 = 0;
$group_chat_count = mysql_query("SELECT COUNT(*) FROM group_chat_views WHERE receiver_id=".$sender_id." AND group_id<>'0' AND view_status='N'") or die("Query Error");
if(mysql_num_rows($group_chat_count) > 0){
    while($g_row = mysql_fetch_array($group_chat_count)){
        $g_count = $g_row[0];
    }
}else{
    $g_count = 0;
}
$reg = mysql_query('SELECT COUNT(DISTINCT sender_user_id) FROM chats WHERE receiver_user_id="' . $_SESSION["adminlogin"] . '" AND view_status="N"') or die("Query Error");
$chat_count = 0;
if(mysql_num_rows($reg) > 0){
    while ($reg_row = mysql_fetch_array($reg)) {
        $chat_count = $reg_row["0"];
    }
}
$chats_count = $g_count + $chat_count;
?>
<style>
body { background-color: #fff;}
.footer{ background-color: #f6f6f6;}
@media (min-width:992px) and (max-width:2000px){
	.page-container { padding-left: 5%;}
}
</style>



<section class="mar_t70">
	     		<div class="container">
	     			<div class="row">
	     				<div class="cl-xs-12 col-sm-5 col-sm-offset-1 col-md-4 col-md-offset-2 pad_lr10">
	     					<a href="dashboard.php">
                                <div class="menubox blue_bg ">
                                    <span><i class="custom-icon tech-icon"></i></span>
                                    <p class="text-capitalize mar_t10">Help</p>
                                </div>
                            </a>
	     				</div>
	     				<div class="col-xs-12 col-sm-5 col-md-4">
                            <a href="reportdashboard.php">
                                <div class="menubox blue_bg_lighten-2">
                                    <span><i class="custom-icon report-lg-icon"></i></span>
                                    <p class="text-capitalize mar_t10">reporting</p>
                                </div>
                            </a>
	     				</div>
	     			</div>
	     			<div class="row">
	     				<div class="cl-xs-12 col-sm-5 col-sm-offset-1 col-md-4 col-md-offset-2 pad_lr10">
                            <a href="briefcase.php">
                                <div class="menubox blue_bg_lighten-1">
                                    <span><i class="custom-icon breifcase-icon"></i></span>
                                    <p class="text-capitalize mar_t10">briefcase</p>
                                </div>
                            </a>
	     				</div>
	     				<div class="col-sm-5 col-md-4 col-xs-12">
                            <a href="implementation_planning.php" target="_blank">
                                <div class="menubox blue_bg_lighten-3">
                                    <span><img src="images/reports.png" width="60"></span>
                                    <p class="text-capitalize mar_t10">Plan &amp; Report</p>
                                </div>
                            </a>
	     				</div>
	     			</div>
	     		</div>
</section>
<?php include_once('templates/footer.php'); ?>
