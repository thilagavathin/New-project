<?php
ob_start(); session_start(); include_once('config.php');
error_reporting(0);

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
    $agency_post='';
	$select_region='';

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
        $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by updated DESC, created");
        @$report_count=mysql_num_rows($report_dash);

        $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by created ");
        @$report_count_page=mysql_num_rows($report_dash_c);

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
if($count_of_com1!=0){
echo "<span class='notification'>".$count_of_com1."</span>";
}else{
	echo "<span class='empty-notification'></span>";
}
