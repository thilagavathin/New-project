<?php include 'templates/header.php'; 
include_once('config.php');
$agency_user_id = "";
$user_id=$_SESSION['adminlogin'];

//Agency Mapping Query
    $user_agency=array();
    $sql_agency="SELECT agency_id FROM agency_map WHERE user_id=".$_SESSION['adminlogin'];
    $sql_agency_row = mysql_query($sql_agency);
    while($agency_row=mysql_fetch_array($sql_agency_row)) {
    $user_agency[]=$agency_row['agency_id'];
    }
    
    $sql_tta=mysql_query("SELECT agency FROM TTA_Reports_uploads WHERE userid='".$_SESSION['adminlogin']."'");
    while($tta_row=mysql_fetch_array($sql_tta)) {
    $user_agency[]=$tta_row['agency'];
    }
    
    $user_agency=array_unique($user_agency);
    if(count($user_agency)!=0){
       $user_agency_ids=implode(',',$user_agency); 
    }else{
        $user_agency_ids='0';
    }
    $where_cmd_oder='';
    if($_SESSION['userrole']==3 || $_SESSION['userrole']==2){$where_cmd_oder.=" WHERE id IN(".$user_agency_ids.")";}
    $order_by_comment='';
    $order_by_agency_id='';
    $array_counts=array();
    $sql_agency=mysql_query("SELECT * FROM agency ".$where_cmd_oder);
    while($agency_order=mysql_fetch_array($sql_agency)) {
    
    $comment_status_query= "SELECT comment_count FROM tta_reports_comments_status WHERE agency_id='".$agency_order['id']."' AND user_id='".$user_id."' ";
    $tta_comment=mysql_query($comment_status_query);
    $comment_tta=mysql_fetch_row($tta_comment); 
    $comment_query="SELECT COUNT(comment) flag FROM TTA_Report_comment WHERE  agency_id='".$agency_order['id']."' ";           
    $tta_comment_user= mysql_query($comment_query);
    $comment_tta_user=mysql_fetch_row($tta_comment_user);
    
    $comment_tta_user[0].",".$comment_tta[0].'<br/>';
    $count_of_com=$comment_tta_user[0]-$comment_tta[0];
    
    if($count_of_com>0){
        $array_counts[$agency_order['id']]=$count_of_com;
    }
    
    }
    arsort($array_counts);
    $ii=1;
    foreach ($array_counts as $key => $val) {
        $order_by_agency_id .=" WHEN ".$key." THEN ".$ii."";
        
        if(count($array_counts)==$ii){
        }
        $ii++;
    }
    
    if($order_by_agency_id<>'') $order_by_comment='CASE A.id '.$order_by_agency_id.' ELSE '.(count($array_counts)+1).'  END , ';


if($_SESSION['userrole']==1){
    $get_user_agen_q="SELECT id FROM agency ";
    $get_user_agen = mysql_query($get_user_agen_q);
    $user_agency_ids='';
    while($row=mysql_fetch_array($get_user_agen)) {
        if($row['id']){
          $user_agency_ids.=trim($row['id']).',';  
        }
        
    }
    $user_agency_ids=substr($user_agency_ids, 0, -1);
}

//Agency List
$agency_list=mysql_query("SELECT id,name FROM agency order by name ");
if($_SESSION['userrole']<>1){
    $get_user_agen_q="SELECT agency_id FROM agency_map WHERE user_id=".$_SESSION['adminlogin'];
    $get_user_agen = mysql_query($get_user_agen_q);
    $agency_join='';$agency_in='';
    while($row=mysql_fetch_array($get_user_agen)) {
        $agency_join.=trim($row['agency_id']).',';
    }
    
    $sql_tta=mysql_query("SELECT agency FROM TTA_Reports_uploads WHERE userid='".$_SESSION['adminlogin']."'");
    while($tta_row=mysql_fetch_array($sql_tta)) {
    $agency_join.=trim($tta_row['agency']).',';
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
$agency_report_query= "SELECT COUNT(DISTINCT(agency_id)) agency FROM TTA_Reports_imports WHERE created>='".$report_start."' and created <='".$report_end."' ".$user_base_agency;
$agency_c_q=mysql_query($agency_report_query);
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
$num_rec_per_page=6;
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
    if(in_array($agency_post,$regions)) $sort_query=' AND (A.id ='.$agency_post.' AND A.id IN('.$user_agency_ids.'))';
    else $sort_query=' AND A.id IN('.$user_agency_ids.')';

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
    $sort_query=' AND (A.id in ('.$all_region.') AND A.id IN('.$user_agency_ids.'))';
}
elseif($agency_post<>'')
{
    $sort_query=' AND (A.id='.$agency_post.' AND A.id IN('.$user_agency_ids.'))';
}
else $sort_query='AND A.id IN('.$user_agency_ids.')';
// Search Keywords
if(isset($_REQUEST['txtsearch'])) {

    $sort_query=" AND name like '%".$_REQUEST['txtsearch']."%' AND A.id IN(".$user_agency_ids.")";
}
if(isset($_REQUEST['sort'])) {

    if($_REQUEST['sort']=='control')
    {
        $sort_query='AND A.id IN('.$user_agency_ids.')';
        $flag_query= mysql_query("SELECT agency_id,COUNT(control_flag) flag FROM TTA_Reports_imports WHERE created>='".$report_start."' AND created <='".$report_end."' AND control_flag='1' ".$user_base_agency." group by agency_id  ");
        $control_flag='';
        while($cf=mysql_fetch_array($flag_query))
        {
            $control_flag.=$cf['agency_id'].',';
        }
        $control_flag=rtrim($control_flag,',');
        if($control_flag<>'') $sort_query= ' AND (agency_id in ('.$control_flag.') AND R.agency_id IN('.$user_agency_ids.'))';
        if(($_SESSION['userrole']==3 || $_SESSION['userrole']==2 ||$_SESSION['userrole']==4 ) && $sort_query=='') $sort_query= ' AND R.agency_id IN('.$user_agency_ids.')';

        $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query."  group by agency_id order by created LIMIT $start_from, $num_rec_per_page");
        @$report_count=mysql_num_rows($report_dash);
        $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query."  group by agency_id order by created ");
        @$report_count_page=mysql_num_rows($report_dash_c);
    }
    elseif($_REQUEST['sort']=='comments')
    {
        $sort_query=' AND R.agency_id IN('.$user_agency_ids.')';
        if($_SESSION['userrole']==3) $agency_comment= mysql_query("SELECT agency_id, COUNT(comment) flag FROM TTA_Report_comment WHERE create_date>='".$report_start."' AND create_date <='".$report_end."' AND status='N' AND normal_status='N' ".$user_base_agency." group by agency_id ");
        else $agency_comment= mysql_query("SELECT agency_id, COUNT(comment) flag FROM TTA_Report_comment WHERE create_date>='".$report_start."' AND create_date <='".$report_end."' AND status='N' ".$user_base_agency." group by agency_id ");
        $control_flag='';
        while($cf=mysql_fetch_array($agency_comment))
        {
            $control_flag.=$cf['agency_id'].',';
        }
        $control_flag=rtrim($control_flag,',');
        if($control_flag<>'') $sort_query= ' AND (agency_id  in ('.$control_flag.') AND A.id IN('.$user_agency_ids.'))';
        if(($_SESSION['userrole']==3 || $_SESSION['userrole']==2 || $_SESSION['userrole']==4) && $sort_query=='') $sort_query= ' AND A.id IN('.$user_agency_ids.')';

        $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."'  ".$sort_query."  group by agency_id order by updated DESC, created LIMIT $start_from, $num_rec_per_page");
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

        if(isset($_GET["recent_comments"])){
            $report_dash= mysql_query("SELECT R.id,agency_id,report_id,created,updated,name FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id LIMIT $start_from, $num_rec_per_page");
            @$report_count=mysql_num_rows($report_dash);

            $report_dash_c= mysql_query("SELECT R.id FROM TTA_Reports_imports R inner join agency A on R.agency_id=A.id  WHERE created>='".$report_start."' and created <='".$report_end."' ".$sort_query." ".$user_base_agency." group by agency_id order by created ");
            @$report_count_page=mysql_num_rows($report_dash_c);

        }
    }
}
else
{
  $ttaImportQuery = "SELECT R.id,A.id as agency_id,report_id,created,updated,name FROM agency A
						LEFT join  TTA_Reports_imports R  on R.agency_id=A.id  
						WHERE name!='' ".$sort_query."
						group by A.id order by ".$order_by_comment." updated DESC, created 
						LIMIT $start_from, $num_rec_per_page";

    $report_dash= mysql_query($ttaImportQuery);
    @$report_count=mysql_num_rows($report_dash);

    $report_dash_c= mysql_query("SELECT R.id FROM agency A LEFT join TTA_Reports_imports R on R.agency_id=A.id  WHERE name!='' ".$sort_query." group by A.id order by created ");
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

?>

<style>
body {
    padding-right: 0 !important;
}
</style>
<section >
	     		<div class="container">
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li class="active">Report Dashboard </li>              
						</ol>
					</div>
				 </div>
				 
	     			<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">report dashboard <span class="info-badge text-lowercase" onclick="info_taggle()" data-toggle = "tooltip" data-placement = "right" title="Click to view Info">i</span></h1>
                  <div class="col-md-12 info_taggle" style="display: none;">
                      <div class="custom-blockquote mar_b20">
                        <p class="mar0">The Report Dashboard is where providers can upload the Implementation Reports (IPs) on a monthly basis. This page also provides a location where managers, evaluators, and TTA staff can view and provide feedback to each submitted report. Please only upload the approved Implementation Excel Sheets (no other types of reports are uploaded to this page). Control Flags indicate that there is an instance of schedule variation in the report, between the projected start / projected end and the actual start and actual end dates.</p>
                      </div>
                  </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 col-sm-4 col-xs-12">
                    <div class="db-box text-center">
                        <span class="clearfix"></span>
                        <p class="db-value text_orange"><?php echo $agency_count[0]; ?></p>
                        <p class="mar_b20 pad_tb3">&nbsp;</p>
                        <p class="value-title">agency updated</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-12">
                    <div class="db-box text-center">
                        <span class="clearfix"></span>
                        <p class="db-value text_green"><?php echo $flag_count; ?></p>
                        <p class="ribbon_green text-uppercase mar_b20">system average <?php echo round($avg_control_count,2); ?></p>
                        <p class="value-title">control flags</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-12">
                    <div class="db-box text-center">
                        <span class="clearfix"></span>
                        <p class="db-value text_light_red"><?php echo $comment_count; ?></p>
                        <p class="mar_b20 pad_tb3">&nbsp;</p>
                        <p class="value-title">comments</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="db-box pad_lr20">
                        <p class="box-title">quick links</p>
                        <div class="quick-links">
                            <p><a href="?sort=recent#recent" name="recent"><i class="fa fa-refresh"></i> Most Recent Updates</a></p>
                            <p><a href="tta_enquiry.php" target="_blank"><i class="fa fa-file"></i> Create TTA </a></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="db-box pad_lr20">
                        <p class="box-title">weather in georgia, usa</p>
                        <div class="col-sm-6 col-xs-6 pad0">
                            <p class="text_blue ft_14 mar0"><?php echo  date('l'); ?></p>
                            <p class="text_red ft_18 fb_300"><?php echo date ('d M Y'); ?></p>
                        </div>
						  <?php
                                                    $city="NewYork";
                                                    $country="US"; //Two digit country code
                                                    $url="http://api.openweathermap.org/data/2.5/weather?q=".$city.",".$country."&units=metric&cnt=1&lang=en&APPID=4ef67044fc88cce533b324f5f4823c49";
                                                    $json=@file_get_contents($url);
                                                    $data=@json_decode($json,true);
                                                    $fahrenheit=round($data['main']['temp']*9/5+32);
                                                    ?>
                        <div class="col-sm-6 col-xs-6 text-right pad0">
                            <p class="mar0 ft_14">Currently</p>
                            <p class="weather_info"><span class="text_red"><?php echo $fahrenheit;?> &deg;</span> / <span class="text_blue"> <?php echo round($data['main']['temp']); ?>F</span> </p>
                        </div>
                    </div>
                </div>
            </div>
             <!-- filter form -->
            <div class="row mar_t10 filter form">
              <div class="col-sm-12 mar_b10">
                <h1 class="page-title">Sort &amp; Search Reports</h1>
              </div>
              <div class="col-md-2 col-sm-2 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">By Region</label>
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
              <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">By Agency</label>
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
			<form name="frmsearch" action="reportdashboard.php" method="post" >
              <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">By Keyword</label>
                      <input type="text" class="form-control"  name="txtsearch" <?php if(isset($_REQUEST['txtsearch'])) { ?>value="<?php echo $_REQUEST['txtsearch']; ?>" <?php } ?> placeholder="" >
                  </div>
              </div>
              <div class="col-md-2 col-sm-2 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">&nbsp;</label>
                      <button class="wid100 mar_t0" type="submit" >Search</button>
                  </div>
              </div>
			 </form>
            </div>
             <!-- search list items -->
         
				 <div class="row">
				  <?php
                                $count_of_com1 = 0;
                                
                                if($report_count>0)
                                {
                                    $v = true;
                                    $created_date=date('Y-m-d H:i:s');
                                    while($ls= mysql_fetch_array($report_dash) ) {
                                        $control_flag[0]='--';
                                        $last_update='--';
                                        $download=array();
                                        if($ls['report_id']!=''){
                                        $created_date=$ls['created'];
										$getImportsQuery = "SELECT report_id,updated FROM TTA_Reports_imports WHERE created>='".$report_start."' AND created <='".$report_end."' AND agency_id=".$ls['agency_id']." group by report_id order by updated desc limit 0,3";
                                        $get_query=mysql_query($getImportsQuery);
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
                                        $count_of_com1 += $count_of_com;
                                        }
										if(isset($_GET["recent_comment"])){
										  
                                            $x=1;
                                            if($count_of_com > 0){
                                                if($ls['report_id']!=''){
                                                $get_file=mysql_query("SELECT uploadfoldername,uploadfilename,uploaduser,date FROM TTA_Reports_uploads WHERE id in (".$reportId_list.") ");
                                                $i=0;$download=array();
                                                while($f_list= mysql_fetch_array($get_file) )
                                                {
                                                    if($i==0)  $last_update=date('d M Y',strtotime($f_list['date']));
                                                    $foldername= unserialize($f_list['uploadfoldername']);
                                                    $filename= unserialize($f_list['uploadfilename']);
                                                    $i++;
                                                    if($f_list['uploaduser']=='Help')
                                                    {

                                                        $download[]='<li><a data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="'.$filename[0].'" href="'.$site_url.'/assets/uploader/php-traditional-server/files/'.$f_list['uploadfoldername'].'/'.$filename[0].'" target="_blank">Report'.$i.'</a></li> ';
                                                    }
                                                    else $download[]='<li><a data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="'.$filename[0].'" href="'.$site_url.'/assets/uploader/php-traditional-server/files/'.$foldername[0].'/'.$filename[0].'" target="_blank">Report'.$i.'</a> </li>';
                                                }
                                                $get_agency_query = "SELECT user_id FROM agency_map WHERE agency_id = '".$ls['agency_id']."'";
                                                $get_agency = mysql_query($get_agency_query);
                                                $agency_user_id = "";
                                                if( mysql_num_rows($get_agency) > 0){
                                                    while($row=mysql_fetch_array($get_agency)) {
                                                        $agency_user_id .=$row['user_id'];
                                                        $agency_user_id .= ",";
                                                    }
                                                }
                                                $agency_user_id = rtrim($agency_user_id, ",");
                                                $new_user_level = array("4");
                                                $assigned_sql=mysql_query("SELECT name,email FROM login_users WHERE username <> 'admin' AND user_level <> '" . serialize($new_user_level) . "' AND  user_id IN (". $agency_user_id.") LIMIT 0,1");
                                                $assigned_user=mysql_fetch_row($assigned_sql);
                                                if($assigned_user[0]==''){ $assigned_user[0]='admin'; $assigned_user[1]='admin@admin.com'; }
											
								                }
												?>
					<div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="assign-box">
                      <div class="assign-title">
                         <small class="pull-right"><i class="text_darkgrey fb_300">Assigned to</i> <span class="text_black fb_500 " data-toggle = "tooltip" data-placement = "top" title="<?php echo $assigned_user[1]; ?>"><?php echo $assigned_user[0]; ?></span></small>
                         <span class="clearfix"></span>
                         <h5 class="text_blue fb_500" data-toggle = "tooltip" data-placement = "top" title="<?php echo $ls['name']; ?>"><?php echo $ls['name']; ?></h5>
                      </div>
                      <div class="report_info">
                        <ul>
                          <li class="update_icon"><span>Last Update</span><span>><?php echo $last_update; ?></span></li>
                          <li class="flag_icon"><span>Control Flag</span><span class="text_light_red"><?php echo $control_flag[0];?></span></li>
                          <li class="item_icon"><span>See Reports</span>
                            <span>
                                <ul>
								<?php
									foreach($download as $downloadValues){
										echo $downloadValues;
									}?>
                                  
                                </ul>
                            </span>
                          </li>
                        </ul>
                      </div>
                      <div class="upload-item text-center mar_tb10">
                         <button class="comment_button" id="commentsReports" data-target="#commentsReport" data-toggle="modal" onclick="usercomment('<?php echo $ls['agency_id']; ?>','<?php echo $ls['report_id']; ?>','all');">
							<i class="fa fa-bell"></i> <span id="<?php echo $ls['agency_id'].'_count'; ?>">Comments <b>(</b>
							<?php
							if($count_of_com > 0 || $count_of_com == 0){
								echo $count_of_com;
							}elseif($count_of_com < 0){
								echo "0";
							}else {echo ($_SESSION['userrole']==3) ? 'Unread Comments':'Comments'; }?> 
							<b>)</b></span>
						 </button>
                         <button class="upload_button" data-target="#uploadreportform" data-toggle="modal" class="r-db-ur briefcase-upload" onclick="updatereport('<?php echo $ls['agency_id']; ?>','<?php echo $created_date;?>');"><i class="fa fa-upload"></i> <span>Upload Report</span></button>
						
                      </div>
                    </div>
					</div>
					
					   <?php    			
					    }
						}
							else{
				                if($ls['report_id']!=''){
								$reportQuery = "SELECT uploadfoldername,uploadfilename,uploaduser,date FROM TTA_Reports_uploads WHERE id in (" . $reportId_list . ") order by id desc ";
							    $get_file = mysql_query($reportQuery);
								$i = 0;
								$download = array();
								while ($f_list = mysql_fetch_array($get_file)) {
									if ($i == 0) $last_update = date('d M Y', strtotime($f_list['date']));
									$foldername = unserialize($f_list['uploadfoldername']);
									$filename = unserialize($f_list['uploadfilename']);
									$i++;
									if ($f_list['uploaduser'] == 'Help') {

										$download[]= '<li><a data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="' . $filename[0] . '" href="'.$site_url.'/assets/uploader/php-traditional-server/files/' . $f_list['uploadfoldername'] . '/' . $filename[0] . '" target="_blank">Report' . $i . '</a></li> ';
									} else {
										$f_url = 'assets/uploader/php-traditional-server/files/' . $foldername[0] . '/' . $filename[0] ;
										$handle = @fopen($f_url,'r');
										if($handle === false) {
											$download[]= '<li><a data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="' . $filename[0] . '" href="'.$site_url.'/assets/uploader/php-traditional-server/files/' . $foldername[0] . '/' . $filename[0] . '" target="_blank">Report' . $i . '</a> </li>' ;
										}else{
											$download[]= '<li><a data-toggle="tooltip" title="" data-placement="top" data-container="body" data-original-title="' . $filename[0] . '" href="'.$site_url.'/assets/uploader/php-traditional-server/files/' . $foldername[0] . '/' . $filename[0] . '" target="_blank">Report' . $i . '</a></li> ' ;
										}
									}
								}
                                }
								$get_agency_query = "SELECT user_id FROM agency_map WHERE agency_id = '" . $ls['agency_id'] . "'";
								$get_agency = mysql_query($get_agency_query);
								$agency_user_id = ""; 
								if (mysql_num_rows($get_agency) > 0) {
									while ($row = mysql_fetch_array($get_agency)) {
										$agency_user_id .=$row['user_id'];
										$agency_user_id .=",";
									}
								}
								$agency_user_id = rtrim($agency_user_id, ",");
								$new_user_level = array("4");
								$assigned_sql=mysql_query("SELECT name,email FROM login_users WHERE username <> 'admin' AND user_level <> '" . serialize($new_user_level) . "' AND  user_id IN (". $agency_user_id.") LIMIT 0,1");
								$assigned_user = mysql_fetch_row($assigned_sql);
								if ($assigned_user[0] == '') {
									$assigned_user[0] = 'admin';
									$assigned_user[1] = 'admin@admin.com';
								}
								?>
				<div class="col-md-4 col-sm-6 col-xs-12">
                  <div class="assign-box">
                      <div class="assign-title">
                         <small class="pull-right"><i class="text_darkgrey fb_300">Assigned to</i> <span class="text_black fb_500 " data-toggle = "tooltip" data-placement = "top" title="<?php echo $assigned_user[1]; ?>"><?php echo $assigned_user[0]; ?></span></small>
                         <span class="clearfix"></span>
                         <h5 class="text_blue fb_500" data-toggle = "tooltip" data-placement = "top" title="<?php echo $ls['name']; ?>"><?php echo $ls['name']; ?></h5>
                      </div>
                      <div class="report_info">
                        <ul>
                          <li class="update_icon"><span>Last Update</span><span><?php echo $last_update; ?></span></li>
                          <li class="flag_icon"><span>Control Flag</span><span class="text_light_red"><?php echo $control_flag[0];?></span></li>
                          <li class="item_icon"><span>See Reports</span>
                            <span>
							
                                <ul>
								<?php 
								foreach($download as $downloadValues){
								echo $downloadValues;
								}
								?>
                                </ul>
                            </span>
                          </li>
                        </ul>
                      </div>
                      <div class="upload-item text-center mar_tb10">
                         <button  class="comment_button" id="commentsReports" data-target="#commentsReport" data-toggle="modal" onclick="usercomment('<?php echo $ls['agency_id']; ?>','<?php echo $ls['report_id']; ?>','all');">
							<i class="fa fa-bell"></i> <span id="<?php echo $ls['agency_id'].'_count'; ?>">Comments <b>(</b>
							<?php
							if($count_of_com > 0 || $count_of_com == 0){
								echo $count_of_com;
							}elseif($count_of_com < 0){
								echo "0";
							}else {echo ($_SESSION['userrole']==3) ? 'Unread Comments':'Comments'; }?> 
							<b>)</b></span>
						 </button>
						 <button class="upload_button" data-target="#uploadreportform" data-toggle="modal" class="r-db-ur briefcase-upload" onclick="updatereport('<?php echo $ls['agency_id']; ?>','<?php echo $created_date;?>');"><i class="fa fa-upload"></i> <span>Upload Report</span></button>
                      </div>
                    </div>
					</div>

                                            <?php
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
              <!-- pagination -->
              <div class="row">
                  <div class="col-md-12 col-xs-12">
                      <ul class="pagination">
					  <?php
						$total_pages = ceil($report_count_page / $num_rec_per_page);
						$previous = $cPage - 1;
						$next = $cPage + 1;
						?>
						<li><a href='reportdashboard.php?page=1<?php echo $page_url; ?>'><<</a></li>
						<?php

						if($cPage != 1)
							echo "<li><a href='reportdashboard.php?page=".$previous.$page_url."'>".'<'."</a></li> ";

						for ($i=1; $i<=$total_pages; $i++) {
							get_availPage($i,$cPage,$page_url);
						};
						if($cPage != $total_pages)
							echo "<li><a href='reportdashboard.php?page=".$next.$page_url."'>".'>'."</a></li> ";

						echo "<li><a href='reportdashboard.php?page=".$total_pages.$page_url."'>".'>>'."</a></li> ";

						function get_availPage($i,$cPage,$page_url){
							if($i < ($cPage + 10) && $i >= $cPage){ ?>
								<li><a href='reportdashboard.php?page=<?php echo $i.$page_url; ?>' <?php if($cPage == $i){ ?>class="active_Pagination"<?php } ?> ><?php echo $i; ?></a></li>
							<?php }
						} ?>
                        
                      </ul>       
                  </div>
              </div>
	     </div>
     		</section>		
			
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
   <!-- start comment box popup window -->
                          <div id="commentsReport" class="modal comment-box right fade" role="dialog">
                            <div class="modal-dialog">
                              <!-- Modal content-->
                              <div class="modal-content">
                                <div class="modal-header grey_bg">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h3 class="modal-title text_blue">User Comments</h3>
								  <div id="loading_comments" class="loading" style="display: none;"></div>
                                </div>
                                <div class="modal-body grey_bg">
                                  <button class="comment-button" data-toggle="collapse" data-target="#comment-form"> Add new comment</button>
                                  <div class="row comment-form collapse" id="comment-form">
                                     <div class="col-xs-12 form">
                                        <form>
                                           <div class="form-group">
                                              <textarea rows="2" id="user_comments" name="user_comments" class="form-control" placeholder="Write your comment here"></textarea>
                                           </div>
                                           <label class="button" data-toggle="collapse" data-target="#comment-emailform">Push to Email <i class="fa fa-caret-down text_white"></i></label>
                                           <!-- innter toggle form -->
                                           <div class="collapse" id="comment-emailform">
                                              <div class="form-group">
                                                  <label class="checkbox-inline pad_l0">
                                                      <input type="checkbox" name="com_admin" id="com_admin" value="1">
                                                      <span class="custom-icon checkbox-icon"></span>Admin
                                                  </label>
                                                  <label class="checkbox-inline">
                                                      <input type="checkbox" id="com_user" name="com_user" value="2" checked="checked">
                                                      <span class="custom-icon checkbox-icon"></span>User
                                                  </label>
                                                  <label class="checkbox-inline" >
                                                      <input type="checkbox" id="com_other" name="com_other" value="3" onclick="return enable_cbtest();">
                                                      <span class="custom-icon checkbox-icon"></span>Other
                                                  </label>
                                              </div>
                                              <div class="form-group">
                                                  <input type="text" class="form-control" placeholder="Subject" name="com_subject" id="com_subject" required >
                                              </div>
                                              <div class="form-group">
                                                  <input type="text" class="form-control" required name="com_email"  disabled="disabled" id="com_email"  placeholder="Email">
                                              </div>
                                           </div>
                                           <!-- inner toggle ends-->
                                           <div class="form-group mar_b0">
                                              <button type="button" class="button pull-right" name="com_comments" onclick="add_user_comments();" >Submit</button>
                                              <span class="clearfix"></span>
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
<!-- END MODAL -->
<?php include 'templates/footer.php'; ?>

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
                $('#'+agency+'_count').empty();
                $('#'+agency+'_count').html('Comments <b>(</b> 0 <b>)</b>');
                var formData = "";
                $.ajax({
                url: "getReportDashboardCountNew.php",
                type: "POST",
                data: formData,
                cache: false,
                success: function (html) {
                    var $success = $.trim(html);
					$("span.report_count").html($success);
                }
            });
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
        window.location = "reportdashboard.php?region="+region+"&agency="+agency;
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
        window.location = "reportdashboard.php";
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
<script type="text/javascript">
    $(document).ready(function() {
        $('.sortable').each(function () {
            var $this = $(this);
            $this.append($this.find('.score').get().sort(function (a, b) {
                return $(a).data('index') - $(b).data('index');
            }));
        });
        
       var maxHeight = 0;
        $(".report_info ul li ul").each(function(){
           if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
        });
        $(".report_info ul li ul").height(maxHeight); 
        
    });
</script>
