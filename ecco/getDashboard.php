<?php
function getDashboardUnread(){
    $rega_count_total = 0;
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
    if($_SESSION['userrole']<>1){
        $agency_join='';
        $sql_agency="SELECT agency_id FROM agency_map WHERE user_id=".$_SESSION['adminlogin'];
        $sql_row = mysql_query($sql_agency);
        while($row=mysql_fetch_array($sql_row)) {
            $agency_join.=trim($row['agency_id']).',';
        }
        $agency_in=substr($agency_join, 0, -1);
        if($_SESSION['userrole']==3 ) { if($agency_in=='') $where_f=" and agency_id in (0)"; else $where_f=" and agency_id in (".$agency_in.")"; }
        else if(($_SESSION['userrole']==2 || $_SESSION['userrole']==4) && $agency_in <>'') $where_f=" and agency_id in (".$agency_in.")";
        else $where_f=" and assignedUser='".$_SESSION['adminlogin1']."'";
    }
    else {$where_f=''; $agency_in='';}
    $agency_in=($agency_in=='')? 0:$agency_in;
    $finish_qry = "SELECT id from TTA_Forms where status='finished' AND delete_flag='N' ".$where_f;
    $result = mysql_query($finish_qry);
    $finish_count = mysql_num_rows($result);

    $pending_qry = "SELECT id from TTA_Forms where status='pending' AND delete_flag='N' ".$where_f;
    $pending_result = mysql_query($pending_qry);
    $pending_count = mysql_num_rows($pending_result);


    $start_qry = "SELECT id from TTA_Forms where status='Started' AND delete_flag='N' ".$where_f;
    $start_result = mysql_query($start_qry);
    $start_count = mysql_num_rows($start_result);
    $page_url='';
    if(!isset($_GET['sid']))
    {
        $_GET['sid']=isset($_SESSION['sid'])? $_SESSION['sid']:'';
        $_GET['uid']=isset($_SESSION['uid'])? $_SESSION['uid']:'';
        $_GET['sort']=isset($_SESSION['sort'])? $_SESSION['sort']:'';
        if($_SESSION['userrole']<>3) {
            $_GET['reg']=isset($_SESSION['reg'])? $_SESSION['reg']:'';
            $_GET['agn']=isset($_SESSION['agn'])? $_SESSION['agn']:'';
        }
        else
        {
            $_GET['reg']=''; $_GET['agn']='';
        }
    }
    if(isset($_REQUEST['dsearch']))
    {
        if($_REQUEST['dsearch']<>'' && !isset($_GET['page'])) { $_GET['uid']='';$_GET['sid']='';$_GET['sort']='';$_GET['reg']='';$_GET['agn']=''; }
    }

    if(isset($_GET['sid']) ) {

        $_SESSION['sid']=$_GET['sid'];
        $_SESSION['uid']=$_GET['uid'];
        $_SESSION['sort']=$_GET['sort'];
        if($_SESSION['userrole']<>3) {
            $_SESSION['reg'] = $_GET['reg'];
            $_SESSION['agn'] = $_GET['agn'];
        }
        else { $_GET['reg']=''; $_GET['agn']=''; }

        if(isset($_GET['rid'])) $rid='&rid=1'; else $rid='';
        if(!isset($_REQUEST['dsearch'])) $_REQUEST['dsearch']='';
        if(isset($_GET['page'])) $page='&page='.$_GET['page']; else $page='';
        if($_SESSION['userrole']==3) $page_url='&sid='.$_GET['sid'].'&uid='.$_GET['uid'].'&sort='.$_GET['sort'].'&dsearch='.$_REQUEST['dsearch'].$rid;
        else $page_url='&sid='.$_GET['sid'].'&uid='.$_GET['uid'].'&sort='.$_GET['sort'].'&reg='.$_GET['reg'].'&agn='.$_GET['agn'].'&dsearch='.$_REQUEST['dsearch'].$rid;
    }
    elseif(isset($_REQUEST['dsearch']))
    {
        $_GET['sid']=isset($_GET['sid'])? $_GET['sid']:'';
        $_GET['uid']=isset($_GET['uid'])? $_GET['uid']:'';
        $_GET['sort']=isset($_GET['sort'])? $_GET['sort']:'';
        $_GET['reg']=isset($_GET['reg'])? $_GET['reg']:'';
        $_GET['agn']=isset($_GET['agn'])? $_GET['agn']:'';
        if(isset($_GET['page'])) $page='&page='.$_GET['page']; else $page='';
        if($_SESSION['userrole']==3) $page_url='&sid='.$_GET['sid'].'&uid='.$_GET['uid'].'&sort='.$_GET['sort'].'&dsearch='.$_REQUEST['dsearch'];
        else $page_url='&sid='.$_GET['sid'].'&uid='.$_GET['uid'].'&sort='.$_GET['sort'].'&reg='.$_GET['reg'].'&agn='.$_GET['agn'].'&dsearch='.$_REQUEST['dsearch'];
    }
    elseif(isset($_GET['rid']))
    {
        $_REQUEST['dsearch']=(isset($_REQUEST['dsearch']))? $_REQUEST['dsearch']:'';
        $_GET['sid']=isset($_GET['sid'])? $_GET['sid']:'';
        $_GET['uid']=isset($_GET['uid'])? $_GET['uid']:'';
        $_GET['sort']=isset($_GET['sort'])? $_GET['sort']:'';
        $_GET['reg']=isset($_GET['reg'])? $_GET['reg']:'';
        $_GET['agn']=isset($_GET['agn'])? $_GET['agn']:'';
        if(isset($_GET['page'])) $page='&page='.$_GET['page']; else $page='';
        if($_SESSION['userrole']==3) $page_url='&sid='.$_GET['sid'].'&uid='.$_GET['uid'].'&sort='.$_GET['sort'].'&dsearch='.$_REQUEST['dsearch'].'&rid=1';
        else $page_url='&sid='.$_GET['sid'].'&uid='.$_GET['uid'].'&sort='.$_GET['sort'].'&reg='.$_GET['reg'].'&agn='.$_GET['agn'].'&dsearch='.$_REQUEST['dsearch'].'&rid=1';
    }
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
            $rega_count_total = $rega_count_total + $rega_count;
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
            $rega_count_total = $rega_count_total + $rega_count;
        }
        $ordering_contract=substr($ordering_contract, 0, -1);
        if($ordering_contract<>'') $order_by_bell=' CASE WHEN contract_num in ('.$ordering_contract.') THEN 1 ELSE 0 END DESC, ';
    }
    //
    if($_SESSION['userrole']==3)
    {
        $agency_where=" agency_id in (".$agency_in.") AND ";
        if(isset($_REQUEST['dsearch'])) { if($_REQUEST['dsearch']<>'') $where_like = "  (tta.ManagerName LIKE '%".trim($_REQUEST['dsearch'])."%' OR tta.AgencyName LIKE '%".trim($_REQUEST['dsearch'])."%' OR tta.AgencyContactNumber LIKE '%".trim($_REQUEST['dsearch'])."%' OR tta.contract_num LIKE '%".trim($_REQUEST['dsearch'])."%') AND "; }
        if(isset($_GET['rid'])) $order_by=' updated_date desc, ';
        if(!isset($_GET['sid'])) { $_GET['sid']=''; $_GET['sort']=''; $_GET['uid']=''; $_GET['reg']=''; $_GET['agn']=''; }
        if($_GET['sid']<>'' && $_GET['sort']<>'') $filter_where = "  tta.status='".$_GET['sid']."' and  ".$merge."='".$_GET['sort']."' AND ";
        elseif($_GET['sid']<>'') $filter_where = " tta.status='".$_GET['sid']."' AND ";
        elseif($_GET['sort']<>'') $filter_where = $merge."='".$_GET['sort']."' AND ";
        else $filter_where ='';
    }
    else
    {
        if($_SESSION['userrole']==2 || $_SESSION['userrole']==4) $agency_where=" agency_id in (".$agency_in.") AND ";
        else $agency_where='';
        if(isset($_REQUEST['dsearch'])) { if($_REQUEST['dsearch']<>'') $where_like = "  (tta.ManagerName LIKE '%".trim($_REQUEST['dsearch'])."%' OR tta.AgencyName LIKE '%".trim($_REQUEST['dsearch'])."%' OR tta.AgencyContactNumber LIKE '%".trim($_REQUEST['dsearch'])."%' OR tta.contract_num LIKE '%".trim($_REQUEST['dsearch'])."%') AND "; }
        if(isset($_GET['rid'])) $order_by=' updated_date desc, ';
        if(!isset($_GET['sid'])) { $_GET['sid']=''; $_GET['sort']=''; $_GET['uid']=''; $_GET['reg']=''; $_GET['agn']=''; }
        if($_GET['sid']<>'' && $_GET['sort']<>'' && $_GET['uid']<>'' && $_GET['reg']<>'' && $_GET['agn']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and tta.status='".$_GET['sid']."' and  ".$merge."='".$_GET['sort']."' and region='".$_GET['reg']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sid']<>'' && $_GET['sort']<>'' && $_GET['uid']<>'' && $_GET['reg']<>'' ) $filter_where = " tta.assignedUser='".$_GET['uid']."' and tta.status='".$_GET['sid']."' and  ".$merge."='".$_GET['sort']."' and region='".$_GET['reg']."'  AND ";
        elseif($_GET['sid']<>'' && $_GET['sort']<>'' && $_GET['uid']<>'' && $_GET['agn']<>'' ) $filter_where = " tta.assignedUser='".$_GET['uid']."' and tta.status='".$_GET['sid']."' and  ".$merge."='".$_GET['sort']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sid']<>'' && $_GET['sort']<>'' && $_GET['reg']<>'' && $_GET['agn']<>'') $filter_where = " tta.status='".$_GET['sid']."' and  ".$merge."='".$_GET['sort']."' and region='".$_GET['reg']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sid']<>'' && $_GET['uid']<>'' && $_GET['reg']<>'' && $_GET['agn']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and tta.status='".$_GET['sid']."' and region='".$_GET['reg']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sort']<>'' && $_GET['uid']<>'' && $_GET['reg']<>'' && $_GET['agn']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and  ".$merge."='".$_GET['sort']."' and region='".$_GET['reg']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sid']<>'' && $_GET['sort']<>'' && $_GET['uid']<>'' ) $filter_where = " tta.assignedUser='".$_GET['uid']."' and tta.status='".$_GET['sid']."' and  ".$merge."='".$_GET['sort']."' AND ";
        elseif($_GET['sid']<>'' && $_GET['sort']<>'' && $_GET['agn']<>'') $filter_where = " tta.status='".$_GET['sid']."' and  ".$merge."='".$_GET['sort']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sid']<>'' && $_GET['reg']<>'' && $_GET['agn']<>'') $filter_where = " tta.status='".$_GET['sid']."' and region='".$_GET['reg']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['uid']<>'' && $_GET['reg']<>'' && $_GET['agn']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and region='".$_GET['reg']."' AND agency_id=".$_GET['agn']." AND ";
        elseif( $_GET['sort']<>'' && $_GET['reg']<>'' && $_GET['agn']<>'') $filter_where = $merge."='".$_GET['sort']."' and region='".$_GET['reg']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sort']<>'' && $_GET['uid']<>'' && $_GET['agn']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and  ".$merge."='".$_GET['sort']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sort']<>'' && $_GET['uid']<>'' && $_GET['reg']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and  ".$merge."='".$_GET['sort']."' and region='".$_GET['reg']."' AND ";
        elseif($_GET['sid']<>'' && $_GET['uid']<>'' && $_GET['agn']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and tta.status='".$_GET['sid']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sid']<>'' && $_GET['sort']<>'' && $_GET['reg']<>'') $filter_where = " tta.status='".$_GET['sid']."' and  ".$merge."='".$_GET['sort']."' and region='".$_GET['reg']."'  AND ";

        elseif($_GET['sid']<>'' && $_GET['sort']<>'') $filter_where = " tta.status='".$_GET['sid']."' and  ".$merge."='".$_GET['sort']."' AND ";
        elseif($_GET['sid']<>'' && $_GET['agn']<>'') $filter_where = " tta.status='".$_GET['sid']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sid']<>'' && $_GET['reg']<>'' ) $filter_where = " tta.status='".$_GET['sid']."' and region='".$_GET['reg']."' AND ";
        elseif($_GET['sid']<>'' && $_GET['uid']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and tta.status='".$_GET['sid']."' AND ";
        elseif($_GET['sort']<>'' && $_GET['uid']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and  ".$merge."='".$_GET['sort']."' AND ";
        elseif($_GET['sort']<>'' && $_GET['reg']<>'') $filter_where = $merge."='".$_GET['sort']."' and region='".$_GET['reg']."' AND ";
        elseif($_GET['sort']<>'' && $_GET['agn']<>'') $filter_where = $merge."='".$_GET['sort']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['uid']<>'' && $_GET['reg']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' and region='".$_GET['reg']."' AND ";
        elseif($_GET['uid']<>'' && $_GET['agn']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['reg']<>'' && $_GET['agn']<>'') $filter_where = " region='".$_GET['reg']."' AND agency_id=".$_GET['agn']." AND ";
        elseif($_GET['sid']<>'') $filter_where = " tta.status='".$_GET['sid']."' AND ";
        elseif($_GET['sort']<>'') $filter_where = $merge."='".$_GET['sort']."' AND ";
        elseif($_GET['reg']<>'') $filter_where = " region='".$_GET['reg']."' AND ";
        elseif($_GET['agn']<>'') $filter_where = " agency_id=".$_GET['agn']." AND ";
        elseif($_GET['uid']<>'') $filter_where = " tta.assignedUser='".$_GET['uid']."' AND ";
        else $filter_where='';
    }
    $where=$agency_where.$where_like.$filter_where;
    if(isset($_GET['bell'])) $order_by='';
    $num_rec_per_page=5;
    if (isset($_REQUEST["page"])) { $cPage  = $_REQUEST["page"]; } else { $cPage=1; }
    $start_from = ($cPage-1) * $num_rec_per_page;

        $start_from = 1; $num_rec_per_page = 10000000000000000;
   // }
    $dash_sql="SELECT tta.id,tta.contract_num as contract_num,tta.agency_id as agency ,tta.created_date as created_date,tta.status
						  as status,tta.created_date as created_date,tta.updated_date as updated_date,tta.assignedUser,tta.contract_num,
						  tta.status,tta.TTA_inquiry_type,tta.TTA_inquiry_notes,tta.TTA_desc,tta.TTA_outcome_notes,tta.TTA_Referral,tta.TTA_Contact_Phone,
						  tta.timeframe,tta.assigned_staff,tta.prelim_result,tta.regarding,tta.regarding_notes,tta.TTA_Email as email,
						  tta.timeframe_w,tta.resources,tta.service_frame_start,tta.service_frame_end,tta.estimate_q1,tta.estimate_q2,tta.estimate_q3,tta.estimate_q4,tta.estimate_total,tta.training_date,tta.push_notification,tta.push_notify_email,tta.push_notify_comments,modality,modality_other FROM TTA_Forms tta left join agency A on A.id=tta.agency_id WHERE ".$where." tta.delete_flag='N' order by ".$order_by.$order_by_bell." tta.id Desc LIMIT $start_from, $num_rec_per_page";
    $result_mail = mysql_query($dash_sql) or die(mysql_error());
    $num_rows = mysql_num_rows($result_mail);
    $tt=0;
    if(mysql_num_rows($result_mail) > 0){
        while($row=mysql_fetch_array($result_mail)) {
            $tt++;
            $agency_result1 = "select ag.name as agency_name,ag.manager_name as mname,ag.street as street,ag.city as city,ag.zip as zip,ag.phone as phone,ag.state as state from agency as ag where id='".$row['agency']."'";
            $result_agency = mysql_query($agency_result1);
            $agency_result = mysql_fetch_array($result_agency);
            $agency_name = $agency_result['agency_name'];
            $help_query="SELECT uploadfoldername,uploadfilename,filepath FROM help WHERE contract_num='".$row['contract_num']."'";
            $help_upload = mysql_query($help_query);
            $upload_help = mysql_fetch_array($help_upload);
            $agency_name = $agency_result['agency_name'];

            $uploadfoldername = (is_array(unserialize($upload_help['uploadfoldername']))) ?  unserialize($upload_help['uploadfoldername']) : array();
            $uploadfilename = (is_array(unserialize($upload_help['uploadfilename']))) ? unserialize($upload_help['uploadfilename']) : array();
            $upload_url =($row['assignedUser'])	? 'ecco.ga-sps.org' : 'ga-sps.org';
            if($row['status']=='finished'){ $bg_color='req-finished';}
            else if($row['status']=='started'){ $bg_color='req-started';}
            else{ $bg_color='req-pending';}

            $tta_comment=mysql_query("SELECT COUNT(id) FROM tta_regarding_notes where view_status='N' AND contract_num='".$row['contract_num']."' AND agency_id='".$row['agency']."'");
            $comment_tta=mysql_fetch_row($tta_comment);
            $tta_comment_user=mysql_query("SELECT comment_count FROM tta_regarding_status WHERE agency_id=".$row['agency']." AND contract_num='".$row['contract_num']."' AND user_id=".$_SESSION['adminlogin']);
            $comment_tta_user=mysql_fetch_row($tta_comment_user);
            $z = 1;
            if($comment_tta[0]<>0 && $comment_tta_user[0]<$comment_tta[0]) {
                $rega_count = $comment_tta[0] - $comment_tta_user[0];
                if ($rega_count > 0) {
                    $rega_count_total = $rega_count_total + 1;
                }
            }
        }
    }
    return $rega_count_total;
}
