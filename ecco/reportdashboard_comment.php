<?php
include_once('header.php');
$agency_user_id = $agency_have_comment_id = $all_agency_id = "";
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
if($comment_count < 0){
    $comment_count = 0;
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
        if(($_SESSION['userrole']==3 || $_SESSION['userrole']==2 ||$_SESSION['userrole']==4 ) && $sort_query=='') $sort_query= ' AND agency_id in (0)';

        $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query."  group by agency_id order by created");
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

        $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."'  ".$sort_query."  group by agency_id order by updated DESC, created");
        @$report_count=mysql_num_rows($report_dash);

        $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."'  ".$sort_query."  group by agency_id order by created ");
        @$report_count_page=mysql_num_rows($report_dash_c);
    }
    elseif($_REQUEST['sort']=='recent')
    {
        $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,max(updated) updated ,max(report_id) res,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by res desc");
        @$report_count=mysql_num_rows($report_dash);

        $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by created ");
        @$report_count_page=mysql_num_rows($report_dash_c);
    }
    else
    {
        $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by created");
        @$report_count=mysql_num_rows($report_dash);

        $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by created ");
        @$report_count_page=mysql_num_rows($report_dash_c);
    }
}
else
{
    $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by updated DESC, created");
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

function Differences ($Arg1, $Arg2){
    $Arg1 = explode (',', $Arg1);
    $Arg2 = explode (',', $Arg2);

    $Difference_1 = array_diff($Arg1, $Arg2);
    $Difference_2 = array_diff($Arg2, $Arg1);
    $Diff = array_merge($Difference_1, $Difference_2);
    $Difference = implode(',', $Diff);
    return $Difference;
}
$count_of_com1 = 0;
if($report_count>0){
 $v = true;
     while($ls= mysql_fetch_array($report_dash) ) {
         $all_agency_id .= $ls['agency_id'];
         $all_agency_id .= ",";

         $sql_reg=mysql_query("SELECT comment_count FROM tta_reports_comments_status WHERE agency_id='".$ls['agency_id']."' AND user_id='".$user_id."' ");
         $row_reg=mysql_fetch_row($sql_reg);

         $agency_comment_c= mysql_query("SELECT COUNT(comment) flag FROM TTA_Report_comment C inner join login_users U on U.user_id=C.userid WHERE  agency_id=".$ls['agency_id']);
         $ag_com_c=mysql_fetch_row($agency_comment_c);

         $count_of_com=$ag_com_c[0]-$row_reg[0];
         if($count_of_com > 0){
             $agency_have_comment_id .= $ls['agency_id'];
             $agency_have_comment_id .= ",";
         }
         $count_of_com1 += $count_of_com;
     }
}
$remaining_agency_id = Differences($all_agency_id,$agency_have_comment_id);
$all_agency_sorted_by_comment = $agency_have_comment_id.$remaining_agency_id;

$all_agency_sorted_by_comment = explode(',', $all_agency_sorted_by_comment);
$listLen = count($all_agency_sorted_by_comment);
$chunkSize = 9;

for($offset=0; $offset<$listLen; $offset+=$chunkSize) {
    $subList = array_slice($all_agency_sorted_by_comment, $offset, $chunkSize);
    $subListAll[] = $subList;
}
$current_page = $x  = 0;
if(isset($_REQUEST['page'])){
    $current_page = $_REQUEST['page'];
}else{
    $current_page = 1;
}
$s_page = $current_page-1;
echo $s_page;
?>
<style>
    .pagination{width:100%; height:auto;}
    .pag_num{margin:0px; padding:0px;}
    .pag_num li{list-style:none; display: inline; margin-bottom:5px;}
    .pag_num li a{width: auto; height:30px; display:inline-block; padding:4px 8px; background:white; font-size:13px; text-decoration:none; text-align:center;  color:#3987ca;  border:1px solid #e1e1e1; margin-right:2px;}
    .pag_num li a:hover{background:#3987ca; color:white; border: 1px solid #2e7bbc;}
    .pag_num li:first-child a{width:60px;}
    .pag_num li:last-child a{width:60px;}
    .pag_num li .active_Pagination{ background:#3987ca; color:white; border: 1px solid #2e7bbc; }
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('images/loading.gif') 50% 50% no-repeat rgb(249,249,249);
        background-size: 50px 50px;
    }
    .loading {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('images/loader.gif') 50% 50% no-repeat rgba(249,249,249,0.5);
        background-size: 50px 50px;
    }
</style>
<!-- START PAGE CONTENT WRAPPER -->
<div class="page-content-wrapper">
    <!-- START PAGE CONTENT -->
    <div class="content sm-gutter">
        <!-- START CONTAINER FLUID -->
        <div class="container-fluid">
            <!-- START BREADCRUMB -->
            <ul class="breadcrumb">
                <li><a href="reportdashboard_comment.php" class="active">Report Dashboard</a></li>

            </ul>
            <?php
            if(isset($_GET['message']))
            {
                if($_GET['message']=='true') {
                    ?>
                    <div style="color:red; text-align: center;">Upload report successfully completed !!!</div>
                    <?php
                }
            }

            ?>
            <!-- END BREADCRUMB -->



            <!-- START ROW -->
            <div class="row">
                <!-- Mini Boxes -->
                <div class="col-md-5">
                    <div class="row statics">
                        <div class="col-md-4">
                            <div class="panel a-r-1" data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="Agency Updated">
                                <a href="?sort=agency" > <span class="label">Agency Updated</span>
                                    <h1 class="tk-count"><?php echo $agency_count[0]; ?></h1></a>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="panel a-r-2" data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="No of Control Flags">
                                <a href="?sort=control"> <span class="label">Control flags</span>
                                    <h1 class="tk-count"><?php echo $flag_count; ?></h1>

                                </a>
                                <div class="avg-count">
                                    <small>System Average</small>
                                    <span class="count"><?php echo round($avg_control_count,2); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="panel a-r-3" data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="No of <?php echo ($_SESSION['userrole']==3) ? 'Unread Comments':'Comments'; ?>">
                                <a href="?sort=comments" ><span class="label re-com"><?php echo ($_SESSION['userrole']==3) ? 'Unread Comments':'Comments'; ?></span>
                                    <h1 class="tk-count"><?php echo $comment_count; ?></h1></a>

                            </div>
                        </div>

                    </div>
                    <!-- / Row -->
                </div>
                <!-- End Mini Boxes -->
                <!-- Quick Links -->
                <div class="col-md-3 nav-quicklinks">
                    <!-- START WIDGET -->
                    <div class="panel a-r-1">
                        <div class="panel-heading">
                            <div class="panel-title">
                                Quick Links
                            </div>
                        </div>
                        <div class="panel-body">
                            <ul class="no-style t-black">
                                <li><a href="?sort=recent#recent" name="recent"><i class="fa fa-angle-right m-r-10"></i>Most Recent Updates</a></li>
                                <li><a href="tta_enquiry.php" target="_blank"><i class="fa fa-angle-right m-r-10"></i>Create TTA</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- END WIDGET -->
                </div>
                <!-- End Quick Links -->
                <div class="col-md-4 hidden-sm hidden-xs">
                    <!-- START WIDGET -->
                    <div class="panel w-weather-2 no-border no-margin">
                        <div class="panel-heading">
                            <div class="panel-title">
                                Weather in Georgia, USA
                            </div>
                            <div class="panel-controls">
                                <ul>
                                    <li class="">
                                        <div class="dropdown">
                                            <a data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                                                <i class="portlet-icon portlet-icon-settings"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right" role="menu">
                                                <li><a href="#">AAPL</a>
                                                </li>
                                                <li><a href="#">YHOO</a>
                                                </li>
                                                <li><a href="#">GOOG</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <a data-toggle="refresh" class="portlet-refresh text-black" href="#"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="p-l-5">
                                <div class="row">
                                    <div class="col-md-12 col-xlg-6">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <h4 class="no-margin"><?php echo  date('l'); ?></h4>
                                                <p class="small hint-text"><?php echo date ('d M Y'); ?></p>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="text-right">
                                                    <p class="small hint-text no-margin">Currently</p>
                                                    <?php
                                                    $city="NewYork";
                                                    $country="US"; //Two digit country code
                                                    $url="http://api.openweathermap.org/data/2.5/weather?q=".$city.",".$country."&units=metric&cnt=1&lang=en&APPID=4ef67044fc88cce533b324f5f4823c49";
                                                    $json=@file_get_contents($url);
                                                    $data=@json_decode($json,true);
                                                    $fahrenheit=round($data['main']['temp']*9/5+32);
                                                    ?>
                                                    <h4 class="text-danger bold no-margin"><?php echo $fahrenheit;?>°
                                                        <span class="small">/ <?php echo round($data['main']['temp']); ?>F</span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET -->
                </div>
            </div>
            <!-- END ROW -->



            <!-- SEARCH -->
            <div class="row">

                <!-- Start Col -->
                <div class="col-md-12">

                    <!-- Start Row -->
                    <div class="row">
                        <!-- Search TTA Requests -->
                        <div class="col-md-12" style="padding:0 15px;">
                            <div class="well search-find-reports b-rad-n clearfix">
                                <div id="recent"></div>

                                <div class="row">
                                    <div class="col-md-12"><div class="fs-18 text-white bold m-b-20">Sort & Search Reports</div> <hr></div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="text-white">By Region</label>
                                            <select name="select_region" id="select_region"  class="form-control cs-skin-slide m-r-10"   onChange="selectedchange();">
                                                <option value=""> Select </option>
                                                <option <?php if($select_region=='R-1') { echo 'selected'; }  ?> value="R-1">R-1</option>
                                                <option <?php if($select_region=='R-2') { echo 'selected'; }  ?> value="R-2">R-2</option>
                                                <option <?php if($select_region=='R-3') { echo 'selected'; }  ?> value="R-3">R-3</option>
                                                <option <?php if($select_region=='R-4') { echo 'selected'; }  ?> value="R-4">R-4</option>
                                                <option <?php if($select_region=='R-5') { echo 'selected'; }  ?> value="R-5">R-5</option>
                                                <option <?php if($select_region=='R-6') { echo 'selected'; }  ?> value="R-6">R-6</option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if($_SESSION['userrole']<>3) {?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="text-white">By Agency</label>
                                                <select name="agency_id" id="agency_id" class=" form-control " onChange="selectedchange();">
                                                    <option value="">Select an Agency</option>
                                                    <?php
                                                    while($row1=mysql_fetch_array($agency_list)) { ?>
                                                        <option value="<?php echo $row1['id']; ?>" <?php if($row1['id']==$agency_post) { echo 'selected'; }?>><?php echo $row1['name']; ?></option>
                                                    <?php }   ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php }
                                    else
                                    {
                                        ?>
                                        <input type="hidden" name="agency_id" id="agency_id" value="">
                                        <?php
                                    }
                                    ?>
                                    <form name="frmsearch" action="reportdashboard_comment.php" method="get" >
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="text-white">By Keyword</label>
                                                <input type="text" name="txtsearch" <?php if(isset($_REQUEST['txtsearch'])) { ?>value="<?php echo $_REQUEST['txtsearch']; ?>" <?php } ?> class="form-control" placeholder="" >
                                            </div>
                                        </div>

                                        <div class="col-md-2" style="margin-top:25px; margin-bottom:15px"><input class="btn btn-success btn-block btn-search " type="submit" value="SEARCH" ></div>
                                    </form>
                                </div>


                            </div>
                        </div>
                        <!-- End Search TTA Requests -->
                    </div>
                    <!-- End Row -->

                </div><!-- End Col -->


                <div class="col-md-12">

                    <!-- Start Search Result -->
                    <div class="search-result">
                        <div class="row">
                            <div class="sortable">
                                <?php
                                if($report_count>0) {
                                    $v = true;
                                    $count_of_com1 = $count_of_com_all = 0;
                                    mysql_data_seek($report_dash, 0);
                                    for ($q = 0; $q < count($subListAll[$s_page]); $q++) {
                                        if ($subListAll[$s_page][$q] != "0") {
                                            $agency_id_sorted = $subListAll[$s_page][$q];
                                            $get_query = mysql_query("SELECT report_id,updated FROM TTA_Reports_imports WHERE created>='" . $report_start . "' AND created <='" . $report_end . "' AND agency_id=$agency_id_sorted group by report_id order by updated desc limit 0,3");
                                            $reportID = '';
                                            $report_dash_query= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' AND A.id=".$agency_id_sorted." ".$sort_query." ".$user_base_agency." group by agency_id order by updated ASC, created");
                                            if(mysql_num_rows($report_dash_query) > 0){
                                                while ($l = mysql_fetch_array($report_dash_query)) {
                                                    $new_report_id = $l['report_id'];
                                                    $new_created = $l['created'];
                                                }
                                            }

                                            while ($list = mysql_fetch_array($get_query)) {
                                                $reportID .= $list['report_id'] . ',';
                                            }
                                            $reportId_list = rtrim($reportID, ',');

                                            $a = mysql_query("SELECT name from agency WHERE id=".$agency_id_sorted);
                                            while($wer = mysql_fetch_array($a)){
                                                $new_name = $wer['name'];
                                            }

                                            $agency_flag = mysql_query("SELECT COUNT(control_flag) flag FROM TTA_Reports_imports WHERE created>='" . $report_start . "' AND created <='" . $report_end . "' AND control_flag='1' AND agency_id=$agency_id_sorted group by report_id order by report_id desc");

                                            $control_flag = mysql_fetch_row($agency_flag);

                                            $sql_reg = mysql_query("SELECT comment_count FROM tta_reports_comments_status WHERE agency_id=$agency_id_sorted AND user_id='" . $user_id . "' ");
                                            $row_reg = mysql_fetch_row($sql_reg);

                                            $agency_comment_c = mysql_query("SELECT COUNT(comment) flag FROM TTA_Report_comment C inner join login_users U on U.user_id=C.userid WHERE  agency_id=$agency_id_sorted");
                                            $ag_com_c = mysql_fetch_row($agency_comment_c);

                                            $count_of_com = $ag_com_c[0] - $row_reg[0];
                                            $count_of_com_all += $count_of_com;

                                            if (!empty($reportId_list)) {
                                                $get_file = mysql_query("SELECT uploadfoldername,uploadfilename,uploaduser,date FROM TTA_Reports_uploads WHERE id in (" . $reportId_list . ") order by id DESC");
                                                $i = 0;
                                                $download = '';
                                                while ($f_list = mysql_fetch_array($get_file)) {
                                                    if ($i == 0) $last_update = date('d M Y', strtotime($f_list['date']));
                                                    $foldername = unserialize($f_list['uploadfoldername']);
                                                    $filename = unserialize($f_list['uploadfilename']);
                                                    $i++;
                                                    if ($f_list['uploaduser'] == 'Help') {

                                                        $download .= '<a data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="' . $filename[0] . '" href="http://ecco.ga-sps.org/assets/uploader/php-traditional-server/files/' . $f_list['uploadfoldername'] . '/' . $filename[0] . '" target="_blank">Report' . $i . '</a> ' . ',';
                                                    } else $download .= '<a data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="' . $filename[0] . '" href="http://ecco.ga-sps.org/assets/uploader/php-traditional-server/files/' . $foldername[0] . '/' . $filename[0] . '" target="_blank">Report' . $i . '</a> ' . ',';
                                                }
                                                $get_agency_query = "SELECT user_id FROM agency_map WHERE agency_id = $agency_id_sorted";
                                                $get_agency = mysql_query($get_agency_query);
                                                $agency_user_id = "";
                                                if (mysql_num_rows($get_agency) > 0) {
                                                    while ($row = mysql_fetch_array($get_agency)) {
                                                        $agency_user_id .= $row['user_id'];
                                                        $agency_user_id .= ",";
                                                    }
                                                }
                                                $agency_user_id = rtrim($agency_user_id, ",");
                                                $new_user_level = array("4");
                                                $assigned_sql = mysql_query("SELECT name,email FROM login_users WHERE username <> 'admin' AND user_level <> '" . serialize($new_user_level) . "' AND  user_id IN (" . $agency_user_id . ") LIMIT 0,1");
                                                $assigned_user = mysql_fetch_row($assigned_sql);
                                                if ($assigned_user[0] == '') {
                                                    $assigned_user[0] = 'admin';
                                                    $assigned_user[1] = 'admin@admin.com';
                                                }
                                                ?>
                                                <div class="col-md-4 score">
                                                    <div class="panel panel_adj panel_adj_bc briefcase rd-h">
                                                        <div class="panel-heading agency-name" data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="<?php echo $new_name;?>"><a href="#"><span class="r-db" style="margin-right:10px;"><i class="fa fa-list-ul"></i></span><?php echo $new_name;?></a>
                                                            <div data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="<?php echo $assigned_user[1]; ?>"> <div class="assigned-to"><small><i class="fa fa-mail-forward"></i> Assigned to </small><a href="#"><?php echo $assigned_user[0]; ?></a></div></div>
                                                        </div>

                                                        <div class="panel-body rd-l-p">
                                                            <dl class="dl-horizontal agency-details briefcase r-dash-b">
                                                                <dt><i class="fa fa-refresh"></i> Last Update:</dt>
                                                                <dd><?php echo $last_update; ?></dd>
                                                                <dt><i class="fa fa-flag"></i> Control Flag:</dt>
                                                                <dd><?php echo $control_flag[0];?></dd>
                                                                <dt><img src="assets/images/see_reports.png" alt="" /> See Reports:</dt>
                                                                <dd>
                                                                    <?php if($download<>'') echo rtrim($download,','); ?>
                                                                </dd>
                                                            </dl>
                                                            <div class="col-md-12 col-sm-12 col-xs-12 r-db-foot <?php
                                                            if($_SESSION['userrole']==3) { echo "unread-adj"; }
                                                            ?>">

                                                                <div class="make-comments-button"><a class="btn btn-sm btn-block r-db-c" href="javascript:void(0);" onclick="usercomment('<?php echo $agency_id_sorted; ?>','<?php echo $new_report_id; ?>','all');" data-target="#usercomments-quickview" data-toggle="modal"><span class="badge badge-success" id="<?php echo $agency_id_sorted.'_count'; ?>"><?php
                                                                            if($count_of_com > 0 || $count_of_com == 0){
                                                                                echo $count_of_com;
                                                                            }elseif($count_of_com < 0){
                                                                                echo "0";
                                                                            }
                                                                            ?></span><?php echo ($_SESSION['userrole']==3) ? 'Unread Comments':'Comments'; ?> </a></div>
                                                                <a href="javascript:void(0);" onclick="updatereport('<?php echo $agency_id_sorted; ?>','<?php echo $new_created;?>');" data-target="#uploadreportform" data-toggle="modal" class="r-db-ur briefcase-upload">Upload Report</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    ?>
                                    <div class="col-md-4" style="color:red;">
                                        No Record
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div><!-- Start Search Result -->
                </div><!-- End Col -->
            </div>
            <!-- End SEARCH -->
            <div class="pagination">
                <ul class="pag_num">
                    <?php
                    $total_pages = ceil($report_count_page / $num_rec_per_page);
                    $previous = $cPage - 1;
                    $next = $cPage + 1;
                    ?>
                    <li><a href='reportdashboard_comment.php?page=1<?php echo $page_url; ?>'><<</a></li>
                    <?php

                    if($cPage != 1)
                        echo "<li><a href='reportdashboard_comment.php?page=".$previous.$page_url."'>".'<'."</a></li> ";

                    for ($i=1; $i<=$total_pages; $i++) {
                        get_availPage($i,$cPage,$page_url);
                    };
                    if($cPage != $total_pages)
                        echo "<li><a href='reportdashboard_comment.php?page=".$next.$page_url."'>".'>'."</a></li> ";

                    echo "<li><a href='reportdashboard_comment.php?page=".$total_pages.$page_url."'>".'>>'."</a></li> ";

                    function get_availPage($i,$cPage,$page_url){
                        if($i < ($cPage + 10) && $i >= $cPage){ ?>
                            <li><a href='reportdashboard_comment.php?page=<?php echo $i.$page_url; ?>' <?php if($cPage == $i){ ?>class="active_Pagination"<?php } ?> ><?php echo $i; ?></a></li>
                        <?php }
                    } ?>
                </ul>
            </div>


        </div>
        <!-- END CONTAINER FLUID -->

    </div>
    <!-- END PAGE CONTENT -->


    <!-- START COPYRIGHT -->
    <!-- START CONTAINER FLUID -->
     <div class="container-fluid container-fixed-lg footer">
        <div class="col-md-12 col-sm-12 col-xs-12 copyright sm-text-center">
            <p class="col-md-4 col-sm-12 col-xs-12 small no-margin">
                <span class="hint-text">Copyright © <?php echo date('Y'); ?> </span>
                <span class="font-montserrat">Prospectus Group, LLC.</span>
                <span class="hint-text">All rights reserved. </span>
            </p>
            <p class="col-md-4 col-sm-12 col-xs-12 text-center foot_logo">
                <img src="assets/img/pgroup_full_new.png" width="250" alt="Powered by Progroup">
            </p>
            <p class="col-md-4 col-sm-12 col-xs-12 small no-margin m-tc">
                <span class="sm-block"><a href="#" class="m-l-10 m-r-10">Terms of use</a> <span class="muted">&#8226;</span> <a href="#" class="m-l-10">Privacy Policy</a></span>
            </p>
            <div class="clearfix"></div>
        </div>
    </div>
    <!-- END COPYRIGHT -->
</div>
<!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTAINER -->


<div class="modal fade" id="uploadreportform" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content-wrapper">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                </button>
                <div class="container-xs-height full-height">
                    <div class="row-xs-height">
                        <div class="modal-body col-xs-height col-middle">
                            <h5 class="text-primary m-b-20">Update Report</h5>
                            <div id="loadingimg" class="loader" style="display: none;"></div>
                            <form id="myform" class="form-horizontal ga-form" role="form" autocomplete="off" method="POST" action="update_report.php" enctype="multipart/form-data">
                                <div class="well">
                                    <div class="clearfix">
                                        <div class="pull-left"><input type="file" name="exampleInputFile" id="exampleInputFile"></div>
                                        <div class="pull-right"><input class="btn btn-lg btn-primary" type="submit" onClick="loading();" name="sub" value="UPLOAD"></div>
                                    </div>

                                </div>
                                <div class="help-text m-t-5 m-b-20">
                                    <b>Note:</b> <span class="m-t-20">Upload xls or xlsx file only.</span>
                                </div>
                                <input type="hidden" name="up_agency" value="" >
                                <input type="hidden" name="up_createdate" value="" >
                            </form>



                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- END MODAL -->

<!-- MODAL COMMENTS -->
<div class="modal fade modal-bottom-full slide-right" id="usercomments-quickview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content-wrapper">
            <div class="modal-content ">
                <button type="button" onclick="winclose();" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                </button>
                <div class="container-xs-height full-height">
                    <div class="row-xs-height">
                        <div class="modal-body col-middle">
                            <h5 class="text-primary m-b-20">User Comments</h5>
                            <div id="loading_comments" class="loading" style="display: none;"></div>
                            <hr>
                            <div class="m-b-20">
                                <button class="btn btn-primary" data-toggle="collapse" data-target="#user-addnewcommentform" aria-expanded="false" aria-controls="collapseExample"><b><i class="fs-14 pg-plus"></i> Add new comment</b></button>

                                <div class="collapse" id="user-addnewcommentform">
                                    <form class="m-t-20" action="#" method="post" name="usercommentsfrm" >
                                        <textarea id="user_comments" name="user_comments" rows="5" placeholder="write your comment here" class="form-control"></textarea>
                                        <div class="m-t-20 extraoptions">
                                            <label>Send Email Notification</label>
                                            <p>(Select User Type)</p>
                                            <div class="checkbox check-primary">
                                                <input type="checkbox" name="com_admin" id="com_admin" value="1">
                                                <label for="com_admin">Admin</label>
                                                <input type="checkbox" id="com_user" name="com_user" value="2">
                                                <label for="com_user">User</label>
                                                <input type="checkbox" id="com_other" name="com_other" value="3" onclick="return enable_cbtest();">
                                                <label for="com_other" id="com_other_label">Other</label>
                                            </div>
                                            <div class="m-t-20">
                                                <input type="text" name="com_subject" id="com_subject" required="" placeholder="Subject" class="form-control">
                                            </div>
                                            <div class="m-t-20">
                                                <input type="text" required="" name="com_email" id="com_email" placeholder="Email" class="form-control" disabled="disabled">
                                            </div>

                                        </div>

                                        <div class="m-t-10 text-right">  <input type="button" onclick="add_user_comments();" class="btn btn-success" name="com_comments" value="Submit">
                                            <input type="hidden" id="com_agency" name="com_agency" value="" >
                                            <input type="hidden" id="com_report" name="com_report" value="" >
                                            <input type="hidden" id="com_type" name="com_type" value="" >

                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div id="comment_status"></div>
                            <div id="htmlcomment"> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- END MODAL -->
<script>
    function updatereport(agency,cdate)
    {
        $('input[name="up_agency"]').val(agency);
        $('input[name="up_createdate"]').val(cdate);

    }
    function usercomment(agency,report,mode)
    {
        $('input[name="com_agency"]').val(agency);
        $('input[name="com_report"]').val(report);
        $('input[name="com_type"]').val(mode);
        $('#comment_status').html('');
        $("#htmlcomment").html('');
        var formData = {agency:agency,report:report};
        $('#'+agency+'_count').html('0');
        var page;
        page="get_user_comments_ajax.php"
        $.ajax({
            url : page,
            type: "POST",
            data : formData,
            beforeSend: function() {
                loading_comments();
            },
            success: function(data, textStatus, jqXHR)
            {
                document.getElementById("loading_comments").style.display = 'none';
                $("#htmlcomment").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    function readcomment(agency,report,mode)
    {
        $('input[name="com_agency"]').val(agency);
        $('input[name="com_report"]').val(report);
        $('input[name="com_type"]').val(mode);
        $('#comment_status').html('');
        $("#htmlcomment").html('');
        var formData = {agency:agency,report:report};
        $.ajax({
            url : "get_read_comments_ajax.php",
            type: "POST",
            data : formData,
            beforeSend: function() {
                loading_comments();
            },
            success: function(data, textStatus, jqXHR)
            {
                document.getElementById("loading_comments").style.display = 'none';
                $("#htmlcomment").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    function selectedchange()
    {
        var region=$( "#select_region" ).val();
        var agency=$( "#agency_id" ).val();
        window.location = "reportdashboard_comment.php?region="+region+"&agency="+agency;
    }
    function loading()
    {
        document.getElementById("loadingimg").style.display = 'block';
    }
    function loading_comments()
    {
        document.getElementById("loading_comments").style.display = 'block';
    }
    function add_user_comments()
    {
        var agency= $('#com_agency').val();
        var report=$('#com_report').val();
        var mode=$('#com_type').val();
        var user_comments=$('#user_comments').val();
        if(  $("input[type='checkbox']#com_admin").is(':checked') )
        {
            var com_admin = "1";
        }else{
            var com_admin = "";
        }
        if(  $("input[type='checkbox']#com_user").is(':checked') )
        {
            var com_user = "2";
        }else{
            var com_user = "";
        }
        if(  $("input[type='checkbox']#com_other").is(':checked') )
        {
            var com_other = "3";
        }else{
            var com_other = "";
        }
        var com_email = $('#com_email').val();
        var com_subject = $('#com_subject').val();
        $('#comment_status').html('');
        $("#htmlcomment").html('');
        var formData = {com_agency:agency,com_report:report,user_comments:user_comments,com_user:com_user,com_admin:com_admin,com_other:com_other,com_email:com_email,com_subject:com_subject};
        $.ajax({
            url : "insert_user_comments.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                $('#user_comments').val('');
                $('#com_subject').val('');
                $('#com_email').val('');
                if(  $("input[type='checkbox']#com_admin").is(':checked') ){
                    $("input[type='checkbox']#com_admin").prop('checked', false);
                }
                if(  $("input[type='checkbox']#com_other").is(':checked') ){
                    $("input[type='checkbox']#com_other").prop('checked', false);
                }
                $("input[type='checkbox']#com_user").prop('checked', true);
                $('#comment_status').html('User comments updated successfully!!!');
                $("#htmlcomment").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    function winclose()
    {
        window.location = "reportdashboard_comment.php";
    }
    function myTrim(x) {
        return x.replace(/^\s+|\s+$/gm,'');
    }
    function enable_cbtest(){
        if(  $("input[type='checkbox']#com_other").is(':checked') ){
            $("input[type='text']#com_email").prop("disabled", false);
        }else{
            $("input[type='text']#com_email").prop("disabled", true);
        }
    }
</script>

<?php include_once('report_footer.php'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.sortable').each(function () {
            var $this = $(this);
            $this.append($this.find('.score').get().sort(function (a, b) {
                return $(a).data('index') - $(b).data('index');
            }));
        });
    });
</script>
