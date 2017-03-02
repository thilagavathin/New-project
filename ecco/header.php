<?php ob_start(); session_start(); include_once('config.php');
error_reporting(0);
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in storage
header('Location:logout.php'); die;
}
if(!isset($_SESSION['adminlogin'])) {
    header('Location:login.php'); die;
}
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
$group_chat_count123 = $chats_count123 = 0;
$group_chat_count123 = mysql_query("SELECT COUNT(*) FROM group_chat_views WHERE receiver_id=".$sender_id." AND group_id<>'0' AND view_status='N'") or die("Query Error");
if(mysql_num_rows($group_chat_count123) > 0){
    while($g_row = mysql_fetch_array($group_chat_count123)){
        $g_count123 = $g_row[0];
    }
}else{
    $g_count123 = 0;
}
$reg = mysql_query('SELECT COUNT(DISTINCT sender_user_id) FROM chats WHERE receiver_user_id="' . $_SESSION["adminlogin"] . '" AND view_status="N"') or die("Query Error");
$chat_count123 = 0;
if(mysql_num_rows($reg) > 0){
    while ($reg_row = mysql_fetch_array($reg)) {
        $chat_count123 = $reg_row["0"];
    }
}

$chats_count123 = $g_count123 + $chat_count123;
function getReportDashboardUnread(){
    $user_id=$_SESSION['adminlogin'];
//Agency List
    $agency_list=mysql_query("SELECT id,name FROM agency order by name ");
    if($_SESSION['userrole']<>1){

        $get_user_agen_q="SELECT agency_id FROM agency_map WHERE user_id=".$_SESSION['adminlogin'];
        $get_user_agen = mysql_query($get_user_agen_q);
        $agency_join='';$agency_in='';
        while($row=mysql_fetch_array($get_user_agen)) {
            $agency_join.=trim($row['agency_id']).',';
        }
        $agency_in=substr($agency_join, 0, -1);
        if($_SESSION['userrole']==3 ){
            if($agency_in=='') $user_base_agency=" and agency_id in (0)"; else $user_base_agency=" and agency_id in (".$agency_in.")";
        }
        else if(($_SESSION['userrole']==2 || $_SESSION['userrole']==4) && $agency_in <>''){ $user_base_agency=" and agency_id in (".$agency_in.")";}
        else $user_base_agency=" and agency_id in (0)";
    }
    else { $agency_in=''; $user_base_agency=''; }


// Report Query
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
    $report_start=date('Y-m-d H:i:s',mktime(0,0,0,date('m')-4,date('d'),date('Y')));
    $report_end=date('Y-m-d H:i:s',mktime(23,59,59,date('m'),date('d'),date('Y')));

    $set_flag_q=mysql_query("SELECT id,projected_start_date,projected_end_date,actual_start_date,actual_end_date FROM TTA_Reports_imports WHERE created>='".$report_start."' and created <='".$report_end."' and control_flag ='N' ".$user_base_agency."");
    if(mysql_num_rows($set_flag_q)>0)
    {
        while($get_rs=mysql_fetch_array($set_flag_q)) {
            if($today>=$get_rs['projected_start_date'])
            {
                // Actual Date Empty
                if($get_rs['actual_start_date']=='0000-00-00') $set_flag=1;
                elseif($get_rs['projected_start_date'] < $get_rs['actual_start_date']) $set_flag=1;
                elseif($get_rs['projected_start_date'] >= $get_rs['actual_start_date'] && $get_rs['projected_end_date'] < $get_rs['actual_end_date']) $set_flag=1;
                else $set_flag='0';
                $insert_q="UPDATE TTA_Reports_imports SET control_flag='".$set_flag."' WHERE id=".$get_rs['id'];
                $result = mysql_query($insert_q);
            }
        }
    }
    if(isset($_REQUEST['agency'])) $agency_post=$_REQUEST['agency'];
    else $agency_post='';
    if(isset($_REQUEST['region'])) $select_region=$_REQUEST['region']; else $select_region='';

// Count Details Agnecy
    $agency_c_q=mysql_query("SELECT COUNT(DISTINCT(agency_id)) agency FROM TTA_Reports_imports WHERE created>='".$report_start."' and created <='".$report_end."' ".$user_base_agency."");
    $agency_count=mysql_fetch_row($agency_c_q);
// count Flag
    $flag_c_q=mysql_query("SELECT COUNT(DISTINCT(control_flag)) flag FROM TTA_Reports_imports WHERE created>='".$report_start."' and created <='".$report_end."' and control_flag='1' ".$user_base_agency."  group by agency_id order by report_id desc");
    $flag_count=mysql_num_rows($flag_c_q);

// count Comment
    if($_SESSION['userrole']==3)
    {
        $comment_c_q=mysql_query("SELECT count(id) FROM TTA_Report_comment WHERE create_date>='".$report_start."' and create_date <='".$report_end."' and status='N' and normal_status='N' ".$user_base_agency." group by agency_id");
        $comment_count=mysql_num_rows($comment_c_q);
    }
    else
    {
        $comment_c_q=mysql_query("SELECT count(id) FROM TTA_Report_comment WHERE create_date>='".$report_start."' and create_date <='".$report_end."' and status='N' ".$user_base_agency." group by agency_id");
        $comment_count=mysql_num_rows($comment_c_q);
    }
// Report Query
    $num_rec_per_page=9;
    if (isset($_REQUEST["page"])) { $cPage  = $_REQUEST["page"]; } else { $cPage=1; }
    $start_from = ($cPage-1) * $num_rec_per_page;
    $page_url='';$all_region='';
    if($agency_post<>'' || isset($_GET['sort']) || $select_region<>'')
    {

        if(isset($_GET['region'])) $region='&region='.$_GET['region']; else $region='';
        if(isset($_GET['agency'])) $agency='&agency='.$_GET['agency']; else $agency='';
        if(isset($_GET['sort'])) $sort='&sort='.$_GET['sort']; else $sort='';
        $page_url=$region.$agency.$sort;
    }
// Sorting Process
    if($agency_post<>'' && $select_region<>'')
    {
        $get_region=mysql_query("SELECT id FROM agency WHERE region='".$select_region."'");
        $regions=array();
        while($reg=mysql_fetch_array($get_region))
        {
            $regions[]=$reg['id'];
        }
        if(in_array($agency_post,$regions)) $sort_query=' AND agency_id ='.$agency_post;
        else $sort_query=' AND agency_id =0';

    }
    elseif($select_region<>'')
    {
        $get_region=mysql_query("SELECT id FROM agency WHERE region='".$select_region."'");
        $regions='';
        while($reg=mysql_fetch_array($get_region))
        {
            $regions.=$reg['id'].',';
        }
        $all_region=rtrim($regions,',');
        $sort_query=' AND agency_id in ('.$all_region.')';
    }
    elseif($agency_post<>'')
    {
        $sort_query=' AND agency_id='.$agency_post;
    }
    else $sort_query='';
// Search Keywords
    if(isset($_REQUEST['txtsearch'])) {

        $sort_query=" AND name like '%".$_REQUEST['txtsearch']."%'";
    }
    if(isset($_REQUEST['sort'])) {
        if($_REQUEST['sort']=='control')
        {
            $sort_query='';
            $flag_query= mysql_query("SELECT agency_id,COUNT(control_flag) flag FROM TTA_Reports_imports WHERE created>='".$report_start."' AND created <='".$report_end."' AND control_flag='1' ".$user_base_agency." group by agency_id  ");
            $control_flag='';
            while($cf=mysql_fetch_array($flag_query))
            {
                $control_flag.=$cf['agency_id'].',';
            }
            $control_flag=rtrim($control_flag,',');
            if($control_flag<>'') $sort_query= ' AND agency_id in ('.$control_flag.')';
            if(($_SESSION['userrole']==3 || $_SESSION['userrole']==2 || $_SESSION['userrole']==4) && $sort_query=='') $sort_query= ' AND agency_id in (0)';

            $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query."  group by agency_id order by created LIMIT $start_from, $num_rec_per_page");
            @$report_count=mysql_num_rows($report_dash);
            $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query."  group by agency_id order by created ");
            @$report_count_page=mysql_num_rows($report_dash_c);
        }
        elseif($_REQUEST['sort']=='comments')
        {
            $sort_query='';
            if($_SESSION['userrole']==3) $agency_comment= mysql_query("SELECT agency_id, COUNT(comment) flag FROM TTA_Report_comment WHERE create_date>='".$report_start."' AND create_date <='".$report_end."' AND status='N' AND normal_status='N' ".$user_base_agency." group by agency_id ");
            else $agency_comment= mysql_query("SELECT agency_id, COUNT(comment) flag FROM TTA_Report_comment WHERE create_date>='".$report_start."' AND create_date <='".$report_end."' AND status='N' ".$user_base_agency." group by agency_id ");
            $control_flag='';
            while($cf=mysql_fetch_array($agency_comment))
            {
                $control_flag.=$cf['agency_id'].',';
            }
            $control_flag=rtrim($control_flag,',');
            if($control_flag<>'') $sort_query= ' AND agency_id  in ('.$control_flag.')';
            if(($_SESSION['userrole']==3 || $_SESSION['userrole']==2 || $_SESSION['userrole']==4) && $sort_query=='') $sort_query= ' AND agency_id in (0)';

            $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."'  ".$sort_query."  group by agency_id order by created LIMIT $start_from, $num_rec_per_page");
            @$report_count=mysql_num_rows($report_dash);

            $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."'  ".$sort_query."  group by agency_id order by created ");
            @$report_count_page=mysql_num_rows($report_dash_c);
        }
        elseif($_REQUEST['sort']=='recent')
        {
            $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,max(updated) updated ,max(report_id) res,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by res desc LIMIT $start_from, $num_rec_per_page");
            @$report_count=mysql_num_rows($report_dash);

            $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by created ");
            @$report_count_page=mysql_num_rows($report_dash_c);
        }
        else
        {
            $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by created LIMIT $start_from, $num_rec_per_page");
            @$report_count=mysql_num_rows($report_dash);

            $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by created ");
            @$report_count_page=mysql_num_rows($report_dash_c);
        }
    }
    else
    {
        $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by updated DESC, created LIMIT $start_from, $num_rec_per_page");
        @$report_count=mysql_num_rows($report_dash);

        $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by created ");
        @$report_count_page=mysql_num_rows($report_dash_c);
    }
// Get Control Flag total count
    $total_contrl=0;
    $avg_contl_sql=mysql_query("SELECT R.id,agency_id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' group by agency_id ");
    while($ls= mysql_fetch_array($avg_contl_sql) )
    {
        $avg_count_sql= mysql_query("SELECT COUNT(control_flag) flag FROM TTA_Reports_imports WHERE created>='".$report_start."' AND created <='".$report_end."' AND control_flag='1' AND agency_id='".$ls['agency_id']."'  group by report_id order by report_id desc LIMIT 0,1");
        $get_avg_contrl=mysql_fetch_row($avg_count_sql);
        $total_contrl+=$get_avg_contrl[0];
    }
// all agency count
    $agency_all_sql=mysql_query("SELECT COUNT(DISTINCT(agency_id)) agency FROM TTA_Reports_imports WHERE created>='".$report_start."' and created <='".$report_end."'");
    $agency_all_count=mysql_fetch_row($agency_all_sql);
    $avg_control_count=$total_contrl/$agency_all_count[0];
    $count_of_com1 = 0;
    if($report_count>0)
    {
        while($ls= mysql_fetch_array($report_dash) ) {
            $get_query=mysql_query("SELECT report_id,updated FROM TTA_Reports_imports WHERE created>='".$report_start."' AND created <='".$report_end."' AND agency_id=".$ls['agency_id']." group by report_id order by updated desc limit 0,3");
            $reportID='';
            while($list= mysql_fetch_array($get_query) )
            {
                $reportID.=$list['report_id'].',';
            }
            $reportId_list=rtrim($reportID,',');
            $agency_flag= mysql_query("SELECT COUNT(control_flag) flag FROM TTA_Reports_imports WHERE created>='".$report_start."' AND created <='".$report_end."' AND control_flag='1' AND agency_id='".$ls['agency_id']."'  group by report_id order by report_id desc");

            $control_flag=mysql_fetch_row($agency_flag);

            $sql_reg=mysql_query("SELECT comment_count FROM tta_reports_comments_status WHERE agency_id='".$ls['agency_id']."' AND user_id='".$user_id."' ");
            $row_reg=mysql_fetch_row($sql_reg);

            $agency_comment_c= mysql_query("SELECT COUNT(comment) flag FROM TTA_Report_comment C inner join login_users U on U.user_id=C.userid WHERE  agency_id=".$ls['agency_id']);
            $ag_com_c=mysql_fetch_row($agency_comment_c);

            $count_of_com=$ag_com_c[0]-$row_reg[0];
            if($count_of_com > 0){
                $count_of_com1 += 1;
            }

        }
    }

    return $count_of_com1;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Georgia Strategic Prevention System - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
    <link href="assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" media="screen">
    <link href="assets/plugins/dropzone/css/dropzone.css" rel="stylesheet" type="text/css" />
    <link href="pages/css/pages-icons.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/simple-line-icons/simple-line-icons.css" rel="stylesheet" type="text/css" media="screen" />
    <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="pages/css/styles.css" rel="stylesheet" type="text/css" />
    <link href="assets/uploader/css/fine-uploader-new.min.css" rel="stylesheet">

    <script type="text/javascript">
        window.onload = function()
        {
            // fix for windows 8
            if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
                document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
        }

    <!-- checkbox onclick event stored -->
        function getcheckbox(id) {
            $("p").append(id,",");
            var gval = $("#pid").html();
            document.getElementById("staff").value =gval;
        }

    </script>
</head>
<body class="fixed-header has-drawer">
<!-- BEGIN SIDEBPANEL-->
<div id="drawerExample" class="drawer col-xs-10 col-sm-5 col-md-3 fold" aria-labelledby="drawerExample">
            
            <div class="drawer-contents">
<ul class="drawer-nav icon_adj_sec">
            <li class="m-t-15">
                <a href="systemdashboard.php"><span>Home</span></a>
                <span class="menu_main_sec">
                <a href="systemdashboard.php"><span class="icon-thumbnail"><i class="pg-home"></i></span></a>
                </span>
            </li>
            <li>
                <a href="dashboard.php"> <span>TTA Tracker</span></a>
                <span class="menu_main_sec">
                <a href="dashboard.php"><span class="icon-thumbnails"><img src="pages/img/icons/icon-assistance.png" width="20"></span></a>
                <a href="dashboard.php?recent_comment=1" id="dashboard_new_count">
                 <?php if($dashboard_recent_comment > 0) {
                        echo "<span class='not'><i class='fa fa-bell'></i><span class='num_not'>".$dashboard_recent_comment."</span></span>";
                    }else{
                        echo "<span class='not'><i class='fa fa-bell-o'></i></span>";
                    } ?>
                </a>
                </span>
            </li>
            <li>
                <a href="reportdashboard.php"><span>Reports</span></a>
                <span class="menu_main_sec">
                <a href="reportdashboard.php"><span class="icon-thumbnails"><img src="pages/img/icons/icon-reports.png" width="19"></span></a>
                <a href="reportdashboard_comment.php" id="get_rd_count">
                    <?php if($report_count123<>0) {
                        echo "<span class='not'><i class='fa fa-bell'></i><span class='num_not'>".$report_count123."</span></span>";
                    } else{
                        echo "<span class='not'><i class='fa fa-bell-o'></i></span>";
                    }
                    ?>
                </a>
                </span>
            </li>
            <li>
                <a href="messages_test.php"><span>Messages</span></a>
                <span class="menu_main_sec">
                <a href="messages_test.php"><span class="icon-thumbnail"><i class="fa fa-comments"></i></span></a>
                <a href="messages_test.php" id="msg_chat_count">
                    <?php if($chats_count123 <> 0) { 
					echo "<span class='not'><i class='fa fa-bell'></i><span class='num_not' id=''>".$chats_count123."</span></span>"; }else{
                        echo "<span class='not'><i class='fa fa-bell-o'></i></span>";
                    }  ?>
                </a>
                </span> 
            </li>
            <li>
                <a href="briefcase.php"><span>Briefcase</span></a>
                  <span class="menu_main_sec">
                <a href="briefcase.php"><span class="icon-thumbnail"><i class="fa fa-briefcase "></i></span></a>
            </li>
            <li>
                <a href="javascript:;"><span class="admin_adj">Quick Links </span> <span class="arrow arr1" onClick="show_section()"></span></a>
                <span class="menu_main_sec">
                <a href="#"><span class="icon-thumbnail"><i class="fa fa-link"></i></span></a>
                </span>
                <ul class="sub-menu sum1">
                    <li><a href="http://ga-sps.org/resources" target="_blank">Resources <span class="icon-thumbnail"><i class="fa fa-book"></i></span> </a></li>
                    <li><a href="http://ga-sps.org/training" target="_blank">Training Center  <span class="icon-thumbnail"><i class="fa fa-users"></i></span> </a></li>
					<li><a href="help.php" target="_blank">Get Help <span class="icon-thumbnail"><i class="fa fa-book"></i></span> </a></li>
                    <li><a href="http://ga-sps.org/calendar" target="_blank">Calender <span class="icon-thumbnail"><i class="fa fa-calendar"></i></span> </a></li>
                </ul>
            </li>
            <?php if($_SESSION['userrole']==1)
            {
            ?>
            <li>
                <a href="javascript:;"><span class="admin_adj">Admin </span> <span class="arrow arr2" onClick="show_section1()"></span></a>
                <span class="menu_main_sec">
                <a href="#"><span class="icon-thumbnail"><i class="pg-menu_lv"></i></span></a>
                </span>
                <ul class="sub-menu sum2">
                    <li><a href="users.php">User <span class="icon-thumbnail"><i class="fa fa-users"></i></span> </a></li>
                    <li><a href="agencies.php">Agency <span class="icon-thumbnail"><i class="fa fa-file-o"></i></span> </a></li>
                    <li><a href="userlevels.php">Levels <span class="icon-thumbnail"><i class="fa fa-signal"></i></span> </a></li>
                    <li><a href="createagencyform.php">Create Form <span class="icon-thumbnail"><i class="fa  fa-plus-square-o"></i></span></a> </li>
                    <li><a href="user_approval.php">User Approval <span class="icon-thumbnail"><i class="fa  fa-user"></i></span></a> </li>
                    <li><a href="assignments.php">Show Assignments <span class="icon-thumbnail"><i class="fa  fa-user"></i></span></a> </li>
                </ul>
            </li>
            <?php }

            ?>
        <li><a href="#drawerExample" data-toggle="drawer" href="#drawerExample" aria-foldedopen="false" aria-controls="drawerExample" class="hand_icon" title="Click Here to View Menu"><i class="fa fa-hand-o-up"></i></a></li>
        </ul>
                   
                
                <p class="header_logo"><img src="assets/img/pgroup_new.jpg" width="50" alt="alt="Powered by Progroup""></p>
        </div>
        </div>
<!-- END SIDEBAR -->
<!-- END SIDEBPANEL-->
<?php
$sql="SELECT user_image,username,timestamp,email FROM login_users where user_id ='".trim($_SESSION['adminlogin'])."'";
$result_mail = mysql_query($sql) or die(mysql_error());
$num_rows = mysql_num_rows($result_mail);
while($row=mysql_fetch_array($result_mail)) {
    $user_name = $row['username'];
    $last_update=$row['timestamp'];
    $user_email=$row['email'];
    if($row['user_image']<>'') $user_img=@unserialize($row['user_image']);
    else $user_img='';
}
if($user_img=='')  $img_val ="assets/img/photo.jpg";
else $img_val ="assets/profile/".$user_img[0];
?>
<!-- START PAGE-CONTAINER -->
<div class="page-container" id="drawer-page">
    <!-- START HEADER -->
    <div class="header ">
        <!-- START MOBILE CONTROLS -->
        <!-- LEFT SIDE -->
        <div class="pull-left full-height visible-sm visible-xs">
            <!-- START ACTION BAR -->
            <div class="sm-action-bar">
                <a href="#drawerExample" data-toggle="drawer" href="#drawerExample" aria-foldedopen="false" aria-controls="drawerExample" class="hand_icon">
            <span class="icon-set menu-hambuger"></span>
          </a>
            </div>
            <!-- END ACTION BAR -->
        </div>
        <!-- RIGHT SIDE -->
        <div class="pull-right full-height visible-sm visible-xs">
            <!-- START ACTION BAR -->
            <div class="sm-action-bar">
                <a href="#" class="btn-link" data-toggle="quickview" data-toggle-element="#quickview">

                     <span class="thumbnail-wrapper d32 circular inline m-t-5">
                        <img src="<?php echo $img_val; ?>" alt="" data-src="<?php echo $img_val; ?>" width="32" height="32">
                    </span>
                </a>
            </div>
            <!-- END ACTION BAR -->
        </div>
        <!-- END MOBILE CONTROLS -->
        <div class=" pull-left sm-table">
            <div class="header-inner">

                <div class="brand inline">
                    <img src="assets/img/ecco-new1.png" alt="logo"  title="Georgia Strategic Prevention System"  width="150">
                </div>


            </div>
        </div>

        <div class="pull-right visible-lg visible-md">
            <!-- START User Info-->
            <div class="m-t-10 m-l-20">
                <div class="pull-left p-r-10 p-t-10 fs-16 font-heading">
                    <span class="semi-bold"><?php echo $_SESSION['displayname']; ?></span></div>
                <div class="dropdown pull-right">
                    <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="thumbnail-wrapper d32 circular inline m-t-5">

                    <img src="<?php echo $img_val; ?>" alt="" data-src="<?php echo $img_val; ?>" width="32" height="32">
            </span>
                    </button>
                    <ul class="dropdown-menu profile-dropdown" role="menu">
                        <li><a href="settings.php"><i class="sl-settings"></i> Settings</a>
                        </li>
                        </li>
                        <li><a href="#"><i class="sl-question"></i> Help</a>
                        </li>
                        <li class="bg-master-lighter">
                            <a href="logout.php" class="clearfix">
                                <span class="pull-left">Logout</span>
                                <span class="pull-right"><i class="sl-logout"></i></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- END User Info-->
        </div>

        <div class="pull-right" style="display:none;">
            <div class="header-inner">
                <div class="b-grey b-r p-l-30 p-r-20 m-r-15">
                    <a href="javascript:;" id="notification-center" class="sl-globe">
                        <span class="bubble">3</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- END HEADER -->
