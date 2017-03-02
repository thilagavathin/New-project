<?php
ob_start(); session_start(); include_once('config.php');
error_reporting(0); $rega_count_total = 0; $rega_count_all=0;
if(!isset($_SESSION['adminlogin'])) {
    header('Location:login.php'); die;
}
if(isset($_GET['did'])) {
    if($_SESSION['userrole']<>3)
    {
        $delete_tta_forms = "UPDATE TTA_Forms SET delete_flag='Y' where id ='".$_GET['did']."'";
        mysql_query($delete_tta_forms);
        header('Location:dashboard.php'); die;
    }
    else {header('Location:dashboard.php'); die; }
}
$_SESSION['AttachmentUpload'] = array();
function resources($id)
{
    $res_level=unserialize($id);
    if(is_array($res_level)) $resource=implode(',',$res_level);
    else $resource='0';
    if($resource=='') $resource=0;
    $sql=mysql_query("SELECT document_name FROM documents WHERE id in (".$resource.")");
    if( mysql_num_rows($sql)>0)
    {
        $return='';
        while($row_resource=mysql_fetch_array($sql)) {
            $document_link=$row_resource['document_name'];
            $document_arr=explode('/',$document_link);
            $count_no=count($document_arr)-1;
            $document_det=explode('.',$document_arr[$count_no]);
            $return.='<a target="_blank" href="http://ga-sps.org/'.$document_link.'">'.$document_det[0].' ('.$document_det[1].')</a><br>';
        }
    }
    else $return='';
    return $return;
}
$agency_list=mysql_query("SELECT id,name FROM agency order by name ");
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
$login_qry = "SELECT username from login_users";
$login_result = mysql_query($login_qry);

$z = 1; $y = 10000000000;
$filter_where='';$where='';$order_by='';$agency_where='';$where_like='';$merge='tta.regarding';
//Bell Filter
$ordering_contract='';$order_by_bell='';
$pre_month=date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d')-20,date('Y')));
if($agency_in<>'')
{
    $sql_order_contract=mysql_query("SELECT contract_num,agency_id FROM tta_regarding_status WHERE agency_id in ($agency_in) and updated_date >='".$pre_month."'");
    while($row=mysql_fetch_array($sql_order_contract))
    {
        $tta_comment=mysql_query("SELECT COUNT(id) FROM tta_regarding_notes where view_status='N' AND contract_num='".$row['contract_num']."' AND agency_id='".$row['agency_id']."'");
        $comment_tta=mysql_fetch_row($tta_comment);
        $tta_comment_user=mysql_query("SELECT comment_count FROM tta_regarding_status WHERE agency_id=".$row['agency_id']." AND contract_num='".$row['contract_num']."' AND user_id=".$_SESSION['adminlogin']);
        $comment_tta_user=mysql_fetch_row($tta_comment_user);
        $rega_count=$comment_tta[0]-$comment_tta_user[0];
        if($rega_count>0) $ordering_contract.="'".$row['contract_num']."',";
        $rega_count_all = $rega_count_all + $rega_count;
    }
    $ordering_contract=substr($ordering_contract, 0, -1);
    if($ordering_contract<>'') $order_by_bell=' CASE WHEN contract_num in ('.$ordering_contract.') THEN 1 ELSE 0 END DESC, ';
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
        if($rega_count>0) $ordering_contract.="'".$row['contract_num']."',";
        $rega_count_all = $rega_count_all + $rega_count;
    }
    $ordering_contract=substr($ordering_contract, 0, -1);
    if($ordering_contract<>'') $order_by_bell=' CASE WHEN contract_num in ('.$ordering_contract.') THEN 1 ELSE 0 END DESC, ';
}
//
if($_SESSION['userrole']==3)
{
    $agency_where=" agency_id in (".$agency_in.") AND ";
    if(!isset($_GET['sid'])) { $_GET['sid']=''; $_GET['sort']=''; $_GET['uid']=''; $_GET['reg']=''; $_GET['agn']=''; }
        $filter_where ='';
}
else
{
    if($_SESSION['userrole']==2 || $_SESSION['userrole']==4) $agency_where=" agency_id in (".$agency_in.") AND ";
    else $agency_where='';
    if(!isset($_GET['sid'])) { $_GET['sid']=''; $_GET['sort']=''; $_GET['uid']=''; $_GET['reg']=''; $_GET['agn']=''; }
 $filter_where='';
}
$where=$agency_where.$where_like.$filter_where;
if(isset($_GET['bell'])) $order_by='';
$num_rec_per_page = 10000;
//}
if (isset($_REQUEST["page"])) { $cPage  = $_REQUEST["page"]; } else { $cPage=1; }
$start_from = ($cPage-1) * $num_rec_per_page;

$dash_sql="SELECT tta.id,tta.contract_num as contract_num,tta.agency_id as agency ,tta.created_date as created_date,tta.status
						  as status,tta.created_date as created_date,tta.updated_date as updated_date,tta.assignedUser,tta.contract_num,
						  tta.status,tta.TTA_inquiry_type,tta.TTA_inquiry_notes,tta.TTA_desc,tta.TTA_outcome_notes,tta.TTA_Referral,tta.TTA_Contact_Phone,
						  tta.timeframe,tta.assigned_staff,tta.prelim_result,tta.regarding,tta.regarding_notes,tta.TTA_Email as email,
						  tta.timeframe_w,tta.resources,tta.service_frame_start,tta.service_frame_end,tta.estimate_q1,tta.estimate_q2,tta.estimate_q3,tta.estimate_q4,tta.estimate_total,tta.training_date,tta.push_notification,tta.push_notify_email,tta.push_notify_comments,modality,modality_other FROM TTA_Forms tta left join agency A on A.id=tta.agency_id WHERE ".$where." tta.delete_flag='N' order by ".$order_by.$order_by_bell." tta.id Desc LIMIT $start_from, $num_rec_per_page";

$result_mail = mysql_query($dash_sql) or die(mysql_error());

$dash_sql111="SELECT tta.id FROM TTA_Forms tta left join agency A on A.id=tta.agency_id  WHERE ".$where."  tta.delete_flag='N' ";
$result_mail111 = mysql_query($dash_sql111) or die(mysql_error());
$num_rows111 = mysql_num_rows($result_mail111);

$num_rows = mysql_num_rows($result_mail);
$tt=0; $acc_count = 0;
while($row=mysql_fetch_array($result_mail)) {
    $tt++;
    $agency_result1 = "select ag.name as agency_name,ag.manager_name as mname,ag.street as street,ag.city as city,ag.zip as zip,ag.phone as phone,ag.state as state from agency as ag where id='" . $row['agency'] . "'";
    $result_agency = mysql_query($agency_result1);
    $agency_result = mysql_fetch_array($result_agency);
    $agency_name = $agency_result['agency_name'];
    $help_query = "SELECT uploadfoldername,uploadfilename,filepath FROM help WHERE contract_num='" . $row['contract_num'] . "'";
    $help_upload = mysql_query($help_query);
    $upload_help = mysql_fetch_array($help_upload);
    $agency_name = $agency_result['agency_name'];

    $uploadfoldername = (is_array(unserialize($upload_help['uploadfoldername']))) ? unserialize($upload_help['uploadfoldername']) : array();
    $uploadfilename = (is_array(unserialize($upload_help['uploadfilename']))) ? unserialize($upload_help['uploadfilename']) : array();
    $upload_url = ($row['assignedUser']) ? 'dev-ecco.ga-sps.org' : 'ga-sps.org';
    if ($row['status'] == 'finished') {
        $bg_color = 'req-finished';
    } else if ($row['status'] == 'started') {
        $bg_color = 'req-started';
    } else {
        $bg_color = 'req-pending';
    }

    $tta_comment = mysql_query("SELECT COUNT(id) FROM tta_regarding_notes where view_status='N' AND contract_num='" . $row['contract_num'] . "' AND agency_id='" . $row['agency'] . "'");
    $comment_tta = mysql_fetch_row($tta_comment);
    $tta_comment_user = mysql_query("SELECT comment_count FROM tta_regarding_status WHERE agency_id=" . $row['agency'] . " AND contract_num='" . $row['contract_num'] . "' AND user_id=" . $_SESSION['adminlogin']);
    $comment_tta_user = mysql_fetch_row($tta_comment_user);

    if ($comment_tta[0] <> 0 && $comment_tta_user[0] < $comment_tta[0]) {
        $rega_count = $comment_tta[0] - $comment_tta_user[0];
        if ($rega_count > 0) {
            $rega_count_total = $rega_count_total + 1;
        }
    } else {
    }
}
if ($rega_count_total > 0) {
    echo "<span class='not'><i class='fa fa-bell'></i><span class='num_not'>" . $rega_count_total . "</span></span>";
} else {
    echo "<span class='not'><i class='fa fa-bell-o'></i></span>";
}
?>
