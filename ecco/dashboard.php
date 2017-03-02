<?php 
include 'templates/header.php';
include_once('config.php');
include('dashboardUpdate.php');
?>
<style>
.qq-upload-button {
  background: #284fa3 none repeat scroll 0 0 !important;
  border: medium none !important;
  border-radius: 3px !important;
  color: #fff !important;
  cursor: pointer;
}
.TTA_Inquiry_Notes {
    font-weight: 500;
}
</style>
<?php
function resources($id)
{
    $res_level=unserialize($id);

	foreach($res_level as $value){
		if($value!='Select'){
			$res_level1[]=$value;
		}
	}
    if(is_array($res_level)) $resource=implode(',',$res_level);
    else $resource='0';
    if($resource=='') $resource=0;

    $resource = str_replace('"Select,"','""',$resource);
    $query_res= "SELECT document_name FROM documents WHERE id in (".$resource.")";

    $sql=mysql_query($query_res);
    if( mysql_num_rows($sql)>0)
    {
        $return='';
        while($row_resource=mysql_fetch_array($sql)) {
            $document_link=$row_resource['document_name'];
            $document_arr=explode('/',$document_link);
            $count_no=count($document_arr)-1;
            $document_det=explode('.',$document_arr[$count_no]);

            $return .= '<a target="_blank" href="'.$site_url.'/'.$document_link.'"><small class="label label-default"><i class="fa fa-times"></i>'.$document_det[0].' ('.$document_det[1].')</small></a><br>';
        }
    }
    else $return='';
    return $return;
}
    //Agency Mapping Query
    $user_agency=array();
    $sql_agency="SELECT agency_id FROM agency_map WHERE user_id=".$_SESSION['adminlogin'];
    $sql_agency_row = mysql_query($sql_agency);
    while($agency_row=mysql_fetch_array($sql_agency_row)) {
    $user_agency[]=$agency_row['agency_id'];
    }
    
    $user_agency=array_unique($user_agency);
    if(count($user_agency)!=0){
       $user_agency_ids=implode(',',$user_agency); 
    }else{
        $user_agency_ids='0';
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
                $tta_forms_id = $_POST["tta_forms_id"];
                $finish_qry = "SELECT * from TTA_Forms JOIN agency on agency.id=TTA_Forms.agency_id where agency.name !='' AND status='finished' AND delete_flag='N' ".$where_f;
        if(!empty($tta_form_id) && $_GET["recent_comment"]){
                    $finish_qry = "SELECT * from TTA_Forms JOIN agency on agency.id=TTA_Forms.agency_id where agency.name !='' AND  status='finished' AND id in (".$tta_form_id.") AND delete_flag='N' ".$where_f;
                }
                $result = mysql_query($finish_qry);
                $finish_count = mysql_num_rows($result);

                $pending_qry = "SELECT * from TTA_Forms JOIN agency on agency.id=TTA_Forms.agency_id where agency.name !='' AND status='pending' AND delete_flag='N' ".$where_f;
        if(!empty($tta_form_id) && $_GET["recent_comment"]){
            $pending_qry = "SELECT * from TTA_Forms JOIN agency on agency.id=TTA_Forms.agency_id where agency.name !='' AND status='pending' AND id in (".$tta_form_id.") AND delete_flag='N' ".$where_f;
        }
                $pending_result = mysql_query($pending_qry);
                $pending_count = mysql_num_rows($pending_result);


                $start_qry = "SELECT * from TTA_Forms JOIN agency on agency.id=TTA_Forms.agency_id where agency.name !='' AND status='Started' AND delete_flag='N' ".$where_f;
        if(!empty($tta_form_id) && $_GET["recent_comment"]){
            $start_qry = "SELECT * from TTA_Forms JOIN agency on agency.id=TTA_Forms.agency_id where agency.name !='' AND status='Started' AND id in (".$tta_form_id.") AND delete_flag='N' ".$where_f;
        }
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

$where = "";
if(isset($_GET['searchValues']) && $_GET['searchValues'] != ""){
    $searchValues = $_GET['searchValues'];
    $searchFields = $_GET['searchFields'];
    if($searchFields == "users"){
        $searchFields="tta.assignedUser";
    }
    if($searchFields == "agency_name"){
        $searchFields="tta.agency_id";
    }
    $where.= $searchFields ." = '".$searchValues."'";
    $where.= " AND ";
}
if(isset($_GET['agency_name']) && $_GET['agency_name'] != "") {
    $agency_name = $_GET['agency_name'];
    $where .= "  (tta.ManagerName LIKE '%".trim($agency_name)."%' OR tta.AgencyName LIKE '%".trim($agency_name)."%' OR tta.AgencyContactNumber LIKE '%".trim($agency_name)."%' OR tta.contract_num LIKE '%".trim($agency_name)."%') AND ";
}
if($_SESSION['userrole']!=1){
   $where .= " tta.agency_id IN(".$agency_in.") AND "; 
}

// Pagination 
$tta_form_results="SELECT tta.id from TTA_Forms tta left join agency A on A.id=tta.agency_id  WHERE ".$where."  tta.delete_flag='N' AND A.name !='' ";
$tta_results = mysql_query($tta_form_results) or die(mysql_error());

$tta_form_counts = mysql_num_rows($tta_results);

$per_page=5;
if (isset($_REQUEST["page"])) { $cPage  = $_REQUEST["page"]; } else { $cPage=1; }
$start_from = ($cPage-1) * $per_page;
// Agency list


// Agency frowdown list
 if($_SESSION['userrole']==1) $sql="SELECT distinct(name),id FROM agency order by name asc";
	else if($_SESSION['userrole']==3) $sql="SELECT distinct(name),id FROM agency WHERE id in (".$agency_in.") order by name asc";
	else $sql="SELECT distinct(name),id FROM agency WHERE id in (".$agency_in.") GROUP BY name order by name asc";
	$agency_list = mysql_query($sql);
$limit = "LIMIT $start_from, $per_page";


$ttaRecords="SELECT tta.id,tta.contract_num as contract_num,tta.agency_id as agency_id ,A.name as agency_name, tta.created_date as created_date,tta.status
	  as status,tta.created_date as created_date,tta.updated_date as updated_date,tta.assignedUser,tta.contract_num,tta.TTA_inquiry_type,tta.TTA_inquiry_notes,tta.TTA_desc,tta.TTA_outcome_notes,tta.TTA_Referral,tta.TTA_Contact_Phone,
	  tta.timeframe,tta.assigned_staff,tta.prelim_result,tta.regarding as regarding,tta.regarding_notes,tta.TTA_Email as email,
	  tta.timeframe_w,tta.resources as resources,tta.service_frame_start,tta.service_frame_end,tta.estimate_q1,tta.estimate_q2,tta.estimate_q3,
	  tta.estimate_q4,tta.estimate_total,tta.training_date,tta.push_notification as push_notification,tta.push_notify_email,tta.push_notify_comments,modality,
	  modality_other from TTA_Forms tta left join agency A on A.id=tta.agency_id WHERE ".$where." tta.delete_flag='N' AND A.name !=''
	  order by ".$order_by.$order_by_bell." tta.id Desc ".$limit;
      
$ttaRecordsList = mysql_query($ttaRecords) or die(mysql_error());


?>
<link href="assets/css/components.css" rel="stylesheet" type="text/css">

  <!-- Fixed navbar -->

    <!-- Begin page content -->
     		<section >
	     		<div class="container">
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li class="active">Help Dashboard </li>              
						</ol>
					</div>
				 </div>
	     			<div class="row">
                        <div class="col-sm-12">
                            <h1 class="page-title">Help Dashboard<span class="info-badge text-lowercase" onclick="info_taggle()" data-toggle = "tooltip" data-placement = "right" title="Click to view Info">i</span></h1>
                            <div class="col-md-12 info_taggle" style="display: none;">
                                  <div class="custom-blockquote mar_b20">
                                    <p class="mar0">The Help Dashboard provides tools for providers, managers, evaluators, and TTA staff to request help and monitor the help process. Each of the rows below is for one TTA service record, for one TTA request.</p>
                                  </div>
                              </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-sm-4 col-xs-12">

                            <div class="db-box text-center">
                                <p><i class="fa fa-minus-circle fa-2x pull-right"></i></p>
                                <span class="clearfix"></span>
                                <a href="dashboard.php?searchFields=status&searchValues=pending" >
                                <p class="db-value text_orange"><?php echo $pending_count; ?></p>
                                <p class="value-title">pending</p>
                                </a>
                            </div>

                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12">
                            <div class="db-box text-center">
                                <p><i class="fa fa-hourglass-start fa-2x pull-right"></i></p>
                                <span class="clearfix"></span>
                                <a href="dashboard.php?searchFields=status&searchValues=started" >
                                <p class="db-value text_green"><?php echo $start_count; ?></p>
                                <p class="value-title">started</p>
                                    </a>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12">
                            <div class="db-box text-center">
                                <p><i class="fa fa-list fa-2x pull-right"></i></p>
                                <span class="clearfix"></span>
                                <a href="dashboard.php?searchFields=status&searchValues=finished" >
                                <p class="db-value text_light_red"><?php echo $finish_count; ?></p>
                                <p class="value-title">finished</p>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="db-box pad_lr20">
                                <p class="box-title">quick links</p>
                                <div class="quick-links">   
                                    <p><a href="tta_enquiry.php"><i class="fa fa-file-text"></i> Create a Request</a></p>
                                    <p><a href="dashboard.php?rid=1"><i class="fa fa-refresh"></i> Recent Updates</a></p>
                                    <p><a href="dashboard.php?bell=1"><i class="fa fa-bell"></i> TTA Comments</a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="db-box pad_lr20">
                                <p class="box-title">weather in georgia, usa</p>
                                <div class="col-sm-6 col-xs-6 pad0">
                                    <p class="text_blue ft_18 mar0"><?php echo  date('l'); ?></p>
                                    <p class="text_red ft_11"><?php echo date ('d M Y'); ?></p>
                                </div>
                                <div class="col-sm-6 col-xs-6 text-right pad0 fb_500">
                                    <p class="mar0 ft_15">Currently</p>
									 <?php
										$city="NewYork";
										$country="US"; //Two digit country code
										$url="http://api.openweathermap.org/data/2.5/weather?q=".$city.",".$country."&units=metric&cnt=1&lang=en&APPID=4ef67044fc88cce533b324f5f4823c49";
										$json=@file_get_contents($url);
										$data=@json_decode($json,true);
										$fahrenheit=@round($data['main']['temp']*9/5+32);
									?>
                                    <p class="weather_info"><span class="text_red"><?php echo $fahrenheit;?>° </span> / <span class="text_blue"><?php echo @round($data['main']['temp']); ?>F</span> </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 mar_t20">
                            <h1 class="page-title">Find Requests</h1>
                        </div>
                        <!-- find request -->
                        <div class="col-sm-12">
                            <div class = "input-group mar_t10 dashboard-search">
                                <input type = "text" name="search_name" id="search_name" class = "form-control" <?php if(isset($_GET['agency_name'])) { echo 'value="'.$_GET['agency_name'].'"';}?> >
                                <span class = "input-group-btn">
                                    <button id="search_button" class = "btn btn-default search_button" type = "button">Search</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- filter form -->
                    <div class="row mar_t20 filter ">
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
                                <label class="ft_17">Status</label>
                                <select class="form-control status" id="status" >
                                  <option value="">All</option>
                                  <option value="finished" <?php if(isset($_GET['searchValues'])) { if($_GET['searchValues']=='finished') { echo "selected"; } }?>>Finished</option>
                                  <option value="started" <?php if(isset($_GET['searchValues'])) { if($_GET['searchValues']=='started') { echo "selected"; } }?>>Started</option>
                                  <option value="pending" <?php if(isset($_GET['searchValues'])) { if($_GET['searchValues']=='pending') { echo "selected"; } }?>>Pending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
                                <label class="ft_17">User</label>
                                <?php 
								$login_qry = "SELECT username from login_users";
								$login_result = mysql_query($login_qry);
								?>
								<select class="form-control users" id="users">
									<option value="">All</option>
									<?php if($_SESSION['userrole']<>3) { ?>
									<?php while($login_row = mysql_fetch_array($login_result)) { $uname = $login_row['username']; ?>
									<option value="<?php echo $login_row['username']; ?>" <?php if(isset($_GET['searchValues'])) { if($_GET['searchValues']==$uname) { echo "selected"; } }?>><?php echo $login_row['username']; ?></option>
									<?php } } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
                                <label class="ft_17">Regarding</label>
                                <select class="form-control regarding" id="sort">
                                  <option value="">All</option>
                                  <option value="Implementation" <?php if(isset($_GET['searchValues'])) { if($_GET['searchValues']=='Implementation') { echo "selected"; } }?> >Implementation</option>
                                  <option value="Capacity" <?php if(isset($_GET['searchValues'])) { if($_GET['searchValues']=='Capacity') { echo "selected"; } }?> >Capacity</option>
                                  <option value="Evaluation" <?php if(isset($_GET['searchValues'])) { if($_GET['searchValues']=='Evaluation') { echo "selected"; } }?> >Evaluation</option>
                                  <option value="Technology" <?php if(isset($_GET['searchValues'])) { if($_GET['searchValues']=='Technology') { echo "selected"; } }?> >Technology</option>
                                  <option value="Other" <?php if(isset($_GET['searchValues'])) { if($_GET['searchValues']=='Other') { echo "selected"; } }?> >Other</option>
                                </select>
                            </div>
                        </div>
                        <?php  $select_region=isset($_GET['searchValues'])? $_GET['searchValues']:'';
                                $select_agn=isset($_GET['searchValues'])? $_GET['searchValues']:'';
                                if($_SESSION['userrole']<>3) { ?>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
							 
                                <label class="ft_17">Region</label>
                                <select class="form-control region" id="region">
                                  <option value="">All</option>
                                  <option <?php if($select_region=='R-1') { echo 'selected'; }  ?> value="R-1">R-1</option>
                                  <option <?php if($select_region=='R-2') { echo 'selected'; }  ?> value="R-2">R-2</option>
                                  <option <?php if($select_region=='R-3') { echo 'selected'; }  ?> value="R-3">R-3</option>
                                  <option <?php if($select_region=='R-4') { echo 'selected'; }  ?> value="R-4">R-4</option>
                                  <option <?php if($select_region=='R-5') { echo 'selected'; }  ?> value="R-5">R-5</option>
                                  <option <?php if($select_region=='R-6') { echo 'selected'; }  ?> value="R-6">R-6</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12 ">
                            <div class="form-group">
                                <label class="ft_17">Agency</label>
                                <select class="form-control agency_name" id="agency">
                                  <option value="">All</option>
                                  <?php
                                   while($row1=mysql_fetch_array($agency_list)) { ?>
                                  <option value="<?php echo $row1['id']; ?>" <?php echo ($select_agn==$row1['id'])? 'selected':''; ?> ><?php echo $row1['name']; ?></option>
                                  <?php }   ?>
                                </select>
                            </div>
                        </div>
						<?php } ?>
                    </div>
			<div class="panel-group ip_dash" id="accordion_ta1">
              <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title" id="load_graphs">
                      <div class="col-sm-12">
                          <i class="fa fa-angle-down down_arrow1" aria-hidden="true"></i><a data-toggle="collapse" data-parent="#accordion_ta1" href="#collapse_dd3" class="form-title">TTA Data Dashboard</a>
                      </div>
                    </h4>
                  </div>
                  <div id="collapse_dd3" class="panel-collapse collapse">
                    <div class="panel-body pad_l0 pad_r0">
                    <div class="row">
                        <div class="col-md-12 site_tabs">
                       <ul class="nav nav-tabs">
                          <li class="active"><a data-toggle="tab" href="#graphs">Graphs</a></li>
                          <li><a data-toggle="tab" href="#reports">Reports</a></li>
                          <li><a data-toggle="tab" href="#intervention">Interventions</a></li>
                          <li><a data-toggle="tab" href="#demographics">Demographics</a></li>
                          <li><a data-toggle="tab" href="#schedule">Schedule</a></li>
                          <li><a data-toggle="tab" href="#coalitions">Coalitions</a></li>
                          <li><a data-toggle="tab" href="#workgroup">Work Group</a></li>
                        </ul>

                        <div class="tab-content">
                          <div id="graphs" class="tab-pane fade in active">
                            <div class="row">
                               <div class="col-md-4 col-sm-12 col-xs-12">
                                    <div class="chart-container border pad10">
                                      <div class="chart has-fixed-height" id="connect_column"></div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12">
                                    <div class="chart-container border pad10">
                                      <div class="chart has-fixed-height" id="line_bar"></div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12">
                                 <div class="chart-container border pad10">
                                    <div class="chart has-fixed-height " id="basic_donut"></div>
                                  </div>
                                </div>
                            </div>
                          </div>
                          <div id="reports" class="tab-pane fade">
                            <h3>Reports</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                          </div>
                          <div id="intervention" class="tab-pane fade">
                            <h3>Interventions</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                          </div>
                          <div id="demographics" class="tab-pane fade">
                            <h3>Demographics</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                          </div>
                          <div id="schedule" class="tab-pane fade">
                            <h3>Schedule</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                          </div>
                          <div id="coalitions" class="tab-pane fade">
                            <h3>Coalitions</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                          </div>
                          <div id="workgroup" class="tab-pane fade">
                            <h3>Work Group</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                          </div>
                        </div>
                    </div>
                    </div>
                    </div>
                  </div>
                </div>
				<div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <div class="col-sm-12">
                          <i class="fa fa-angle-down down_arrow1" aria-hidden="true"></i><a data-toggle="collapse" data-parent="#accordion_ta1" href="#collapse_ta2" class="form-title">View, Sort and Search TA Items</a>
                      </div>
                    </h4>
                  </div>
				  <div id="collapse_ta2" class="panel-collapse collapse in">
					<div class="panel-body pad_l0 pad_r0">
                    <div class="col-md-12 pad_l0 pad_r0">
                    <div class="info_taggle_cmd" style="display: none;">
                      <div class="custom-blockquote mar_b20">
                        <p class="mar0">Use the comments (bell) feature to view and make comments. From here you can view and update an existing TA service record. Listed below are all the TTA service records that you have access to. You can view and add comments to your TTA service record here in "Comments". If the bell is solid, you have unread "Comments". Click the bell to see your "Comments".</p>
                      </div>
                  </div>
                    <!-- agency list -->
					
                    <div class="row mar0 list-title">
                        <div class="">
                            <div class="col-md-4 col-sm-3 col-xs-6 text-left">
                                <p>R-Agency</p>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-4 text-center">
                                <p>Comments<span class="info-badge1 form_date_alone" onclick="info_taggle_cmd()" data-toggle = "tooltip" data-placement = "right" title="Click to view Info">i</span></p>
                            </div>
                            <div class="col-md-2 col-sm-2 hidden-xs text-center">
                                <p>Created</p>
                            </div>
                            <div class="col-md-2 col-sm-2 hidden-xs text-center">
                                <p>Updated</p>
                            </div>
                            <div class="col-md-1 col-sm-2 hidden-xs text-center">
                                <p>Status</p>
                            </div>
                            <div class="col-md-1 col-sm-1 col-xs-2 text-center">
                                <p>Edit</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mar0 ">
                        <div class="panel-group" id="accordion">
                        <div class="panel ">
						<?php 				  
								 while($tta_details=mysql_fetch_assoc($ttaRecordsList)) {
                                     $agency_result1 = "select ag.name as agency_name,ag.manager_name as mname,ag.street as street,ag.city as city,ag.zip as zip,ag.phone as phone,ag.state as state from agency as ag where id='".$tta_details['agency_id']."'";
                                     $result_agency = mysql_query($agency_result1);
                                     $agency_resultrecord = mysql_fetch_array($result_agency);
                                     $tta_comment=mysql_query("SELECT COUNT(id) FROM tta_regarding_notes where view_status='N' AND contract_num='".$tta_details['contract_num']."' AND agency_id='".$tta_details['agency_id']."' ");
                                        $comment_tta=mysql_fetch_row($tta_comment);
                                        $tta_comment_user=mysql_query("SELECT comment_count FROM tta_regarding_status WHERE agency_id=".$tta_details['agency_id']." AND contract_num='".$tta_details['contract_num']."' AND user_id=".$_SESSION['adminlogin']);
                                        $comment_tta_user=mysql_fetch_row($tta_comment_user);
                                        $rega_count=$comment_tta[0]-$comment_tta_user[0];

								?>
                          <div class="panel-heading">
                            <h4 class="panel-title">
                                <div class="col-md-4 col-sm-3 col-xs-6 text-left agency-name" data-toggle='tooltip' data-placement="top" title="<?php echo $tta_details['agency_name']; ?>">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse033_<?php echo $tta_details['contract_num']; ?>" class=""><i class="fa fa-search tta_search" aria-hidden="true"></i></a>
                                    <a  data-toggle="collapse" data-parent="#accordion" href="#collapse033_<?php echo $tta_details['contract_num']; ?>" class="" ><?php echo $tta_details['agency_name']; ?></a>
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-4 text-center" >
                                <?php
                                if(($_SESSION['userrole']==1)||($_SESSION['userrole']==4)){
                                    $onclick="regarding_comments('".$tta_details['contract_num']."','".$tta_details['agency_id']."');";
                                    $data_target="#usercomments-quickview";
                                }else{
                                    $onclick="regarding_comments('".$tta_details['contract_num']."','".$tta_details['agency_id']."');";
                                    $data_target="#usercomments-quickview";
                                }
                                if($rega_count!=0){ ?>
                                <a class="solid" data-target="<?php echo $data_target; ?>" data-toggle="modal" onclick="<?php echo $onclick; ?>">
                                <i class="fa fa-bell <?php echo $tta_details['contract_num']; ?>_belll comment-icon"></i><span class="<?php echo $tta_details['contract_num']; ?>_bell"> <?php echo $rega_count; ?> </span></a>
                                <?php }else{ ?>
                                 <a class="solid" data-target="<?php echo $data_target; ?>" data-toggle="modal" onclick="<?php echo $onclick; ?>">
                                <i class="fa fa-bell-o comment-icon"></i></a>   
                                <?php } ?>
                                </div>
                                <div class="col-md-2 col-sm-2 hidden-xs text-center">
                                    <p><?php echo $created_date=date('m-d-Y h:i A',strtotime($tta_details['created_date'])); ?></p>
                                </div>
                                <div class="col-md-2 col-sm-2 hidden-xs text-center">
                                    <p><?php echo $updated_date=date('m-d-Y h:i A',strtotime($tta_details['updated_date']));?></p>
                                </div>
                                <div class="col-md-1 col-sm-2 hidden-xs text-center">
								<?php
								if($tta_details['status']=='' || $tta_details['status']=='pending'){
									echo "<span class='label label-warning'>Pending</span>";
								}else if($tta_details['status']=='finished'){
									echo "<span class='label label-danger'>Finished</span>";
								}else if($tta_details['status']=='started'){
									echo "<span class='label label-success'>Started</span>";
								} ?>
                                    
                                </div>
                                <div class="col-md-1 col-sm-1 col-xs-2 text-center">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#tta_<?php echo $tta_details['contract_num']; ?>"><i class="fa fa-pencil-square-o edit-icon" aria-hidden="true"></i></a>
                                </div>
                            </h4>
                          </div>


						       <!-- start comment box popup window -->
                          <div id="usercomments-quickview" class="modal comment-box right fade"  role="dialog">
                            <div class="modal-dialog">
                              <!-- Modal content-->
                              <div class="modal-content">
                                <div class="modal-header grey_bg">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h3 class="modal-title text_blue">Comments and Question</h3>
								  <div id="loading_comments" class="loading" style="display: none;"></div>
                                </div>
                                <div class="modal-body grey_bg">
                                  <button class="comment-button" data-toggle="collapse" data-target="#comment-form">Write Your Comment Here</button>
                                  <div class="row comment-form collapse" id="comment-form">
                                     <div class="col-xs-12 form">
                                        <form>
                                           <div class="form-group">
                                              <textarea rows="2"id="regarding_notes" name="regarding_notes" class="form-control" placeholder="Write your comment here"></textarea>
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
                                                  <label class="checkbox-inline">
                                                      <input type="checkbox" id="com_other" name="com_other" value="3" onclick="return enable_cbtest();">
                                                      <span class="custom-icon checkbox-icon"></span>Other
                                                  </label>
                                              </div>
                                              <div class="form-group">
                                                  <input type="text" class="form-control" placeholder="Subject" name="com_subject" id="com_subject" required >
                                              </div>
                                              <div class="form-group">
                                                  <input type="text" class="form-control" required name="com_email" id="com_email"  placeholder="Email">
                                              </div>
                                           </div>
                                           <!-- inner toggle ends-->
                                           <div class="form-group mar_b0">
                                              <button type="button" class="button pull-right" onclick="add_comments();" >Submit</button>
                                              <span class="clearfix"></span>
											  <input type="hidden" id="com_agency" name="com_agency" value="" >
												<input type="hidden" id="com_contract" name="com_contract" value="" >
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
						  
						 

                            <div id="collapse033_<?php echo $tta_details['contract_num']; ?>" class="panel-collapse collapse">
                            <div class="panel-body grey_bg contract_details">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 details-box">
                                    <ul>
                                        <li><span>Contract Number</span><span class="text_blue text-uppercase"><?php echo $tta_details['contract_num']; ?></span></li>
                                        <li><span>Results</span><span class="text_red"><?php echo $tta_details['prelim_result']; ?></span></li>
                                        <li><span>TTA Inquiry Type</span><span><?php echo $tta_details['TTA_inquiry_type']; ?></span></li>
                                        <li><span>TTA Inquiry Notes</span><?php if($tta_details['TTA_inquiry_notes']!=''){ ?><span class="TTA_Inquiry_Notes"><?php echo $tta_details['TTA_inquiry_notes']; ?></span> <?php }?></li>
                                    </ul>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 details-box">
                                    <ul class="location_details">
                                        <li class="user_icon"><?php echo $agency_resultrecord['mname']; ?></li>
                                        <li class="mobile_icon"><?php echo $agency_resultrecord['phone']; ?></li>
                                        <li class="location_icon"><?php echo $agency_resultrecord['street'], $agency_resultrecord['city'],   $agency_resultrecord['state'],   $agency_resultrecord['zip']; ?></li>
                                    </ul>
                                </div>
                                <?php
                                $help_query="SELECT uploadfoldername,uploadfilename,filepath FROM help WHERE contract_num='".$tta_details['contract_num']."'";
								
                                $help_upload = mysql_query($help_query);
                                $upload_help = mysql_fetch_array($help_upload);
                                $arrayfoldername=unserialize($upload_help['uploadfoldername']); 
                                $arrayfilename=unserialize($upload_help['uploadfilename']);
                                $fileCount = count($arrayfilename); ?>
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 details-box">
                                    <div class="download-files">
                                        <h4 class="pad0 mar_t0">Attachments</h4>
                                        <ul class="attachment_list">
                                        <?php for($i=0;$i<$fileCount;$i++){   if(!empty($arrayfilename[$i])&&!empty($arrayfilename[$i])){?>
                                            <li><a href="<?php echo $site_url; ?>/assets/uploader/php-traditional-server/files/<?php echo $arrayfoldername[$i]; ?>/<?php echo $arrayfilename[$i]; ?>" ><?php echo $arrayfilename[$i]; ?></a></li>
                                        <?php }}?>
                                        
                                        
                                        
                                         </ul>



                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 details-box  no-border">
                                    <div class="download-files">
                                        <h4 class="pad0 mar_t0">Resources</h4>

                                        <ul class="attachment_list">
                                            <?php echo resources($tta_details['resources']);?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                            
                        <?php 
                        $disableed_class='';
                        if($tta_details['status']=='' || $tta_details['status']=='pending'){
                        if(($_SESSION['userrole']==1)||($_SESSION['userrole']==4)||(in_array($tta_details['agency_id'],$user_agency))){?>
                        
                        <?php }}
                        else if($tta_details['status']=='finished'){
                        if(($_SESSION['userrole']==1)||($_SESSION['userrole']==4)){
                        ?>
                        <?php 
                        }
                        else{ $disableed_class='edit_disable';}
                        }
                        else if($tta_details['status']=='started'){
                        if(($_SESSION['userrole']==1)||($_SESSION['userrole']==4) ){ }
                        else{ $disableed_class='edit_disable';}
                        ?>
                        
                        <?php }?>

						 <!-- Panel -->
                        <div id="tta_<?php echo $tta_details['contract_num']; ?>" class="panel-collapse collapse <?php echo $disableed_class; ?>">
						<form action="" method="post" enctype="multipart/form-data" name="edit_tta_form" id="edit_tta_form" role="form">
                            <div class="panel-body">
                                <!-- inner accordian form -->
                                <div class="panel-group" id="accordion1">
                                  <div class="panel">
                                    <div class="panel-heading">
                                      <h4 class="panel-title">
                                        <div class="col-sm-12">
                                            <i class="fa fa-angle-down down_arrow1" aria-hidden="true"></i><a data-toggle="collapse" data-parent="#accordion1" href="#collapse11_<?php echo $tta_details['contract_num']; ?>" class="form-title">TA Logistics</a>
                                        </div>
                                      </h4>
                                    </div>
                                    <div id="collapse11_<?php echo $tta_details['contract_num']; ?>" class="panel-collapse collapse">
                                        <div class="panel-body">
											<div class="col-xs-12 col-sm-6 col-md-6">
											  <div class="form-group">
												<label>Agency</label>
												<select class="form-control" name="agency_id"><option>Select an Agency</option>
												<?php

                                                        if($_SESSION['userrole']==1) $sql="SELECT distinct(name),id FROM agency order by name asc";
                                                        else if($_SESSION['userrole']==3) $sql="SELECT distinct(name),id FROM agency WHERE id in (".$agency_in.") order by name asc LIMIT 0,1";
                                                        else $sql="SELECT distinct(name),id FROM agency WHERE id in (".$agency_in.") GROUP BY name order by name asc";
                                                        $result_mail1 = mysql_query($sql);


													while($agency_row=mysql_fetch_array($result_mail1)) { ?>
														<option value="<?php echo $agency_row['id']; ?>" <?php if($agency_row['id']==$tta_details['agency_id']) { echo 'selected'; } else { }?>><?php echo $agency_row['name']; ?></option>
													<?php }   ?></select>
											  </div>
											  <div class="form-group">
												<label>Requester's Name</label>
												<input type="text" name="requester_name" value="<?php echo isset($_POST['requester_name'])? $_POST['requester_name'] : $tta_details['TTA_Referral']; ?>" class="form-control">
											  </div>
											</div>
											<div class="col-xs-12 col-sm-6 col-md-6">
											  <div class="form-group">
												<label>Contact Phone</label>
												<input type="text" name="contact_number" class="form-control" value="<?php echo isset($_POST['contact_number'])? $_POST['contact_number'] : $tta_details['TTA_Contact_Phone']; ?>">
											  </div>
											  <div class="form-group">
												<label>Contact Email</label>
												<input type="text" name="email" class="form-control" value="<?php echo isset($_POST['email'])? $_POST['email'] : $tta_details['email']; ?>">
											  </div>
											</div>

										</div>
                                    </div>








                                  </div>
                                  <div class="panel">
                                    <div class="panel-heading">
                                      <h4 class="panel-title">
                                        <div class="col-sm-12">
                                            <i class="fa fa-angle-down down_arrow1" aria-hidden="true"></i>
                                            <a data-toggle="collapse" data-parent="#accordion1" href="#collapse21_<?php echo $tta_details['contract_num']; ?>" class="form-title">
                                                TA Request
                                            </a>
                                        </div>
                                      </h4>
                                    </div>
                                    <div id="collapse21_<?php echo $tta_details['contract_num']; ?>" class="panel-collapse collapse">
										<div class="panel-body">
											<?php include('tta_request.php'); ?>
										</div>
                                    </div>
                                  </div>
                                  <div class="panel ">
                                    <div class="panel-heading">
                                      <h4 class="panel-title">
                                        <div class="col-sm-12">
                                            <i class="fa fa-angle-down down_arrow1" aria-hidden="true"></i>
                                            <a data-toggle="collapse" data-parent="#accordion1" href="#collapse31_<?php echo $tta_details['contract_num']; ?>" class="form-title">
                                                TA Results
                                            </a>
                                        </div>
                                      </h4>
                                    </div>
                                    <div id="collapse31_<?php echo $tta_details['contract_num']; ?>" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <?php if($tta_details['service_frame_start']=='0000-00-00') $display_service_f=date('m/d/Y'); else $display_service_f=date("m/d/Y",strtotime($tta_details['service_frame_start']));
                                            if($tta_details['service_frame_end']=='0000-00-00') $display_service_e=date('m/d/Y'); else $display_service_e=date("m/d/Y",strtotime($tta_details['service_frame_end']));
                                            ?>
                                            <div class="form">
                                                <div class="col-xs-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label>Service Time Frame</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" placeholder="Starting Date" id="service_frame_start_<?php echo $tta_details['contract_num']; ?>" name="service_frame_start"  value="<?php echo $display_service_f; ?>"/>
                                                            <span class="input-group-addon no-border"><i aria-hidden="true" class="fa fa-calendar"></i></span>
                                                            <span class="input-group-addon no-border no_bg">To</span>
                                                            <input type="text" class="form-control" placeholder="Ending Date" id="service_frame_end_<?php echo $tta_details['contract_num']; ?>" name="service_frame_end"  value="<?php echo $display_service_e; ?>" />
                                                            <span class="input-group-addon no-border"><i aria-hidden="true" class="fa fa-calendar"></i></span>
                                                        </div>
                                                    </div>
                                                    <?php if($tta_details['training_date']=='0000-00-00') $display_training=date('m/d/Y'); else $display_training=date("m/d/Y",strtotime($tta_details['training_date'])); ?>
                                                    <div class="form-group">
                                                        <label>Training or TA Date</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control datepic"  id="training_date_<?php echo $tta_details['contract_num']; ?>" name="training_date" value="<?php echo $display_training; ?>">
                                                            <span class="input-group-addon no-border"><i aria-hidden="true" class="fa fa-calendar"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-6">
                                                    <div class="form-group estimate-time-box col-sm-12 pad0">
                                                        <label class="col-sm-12 pad0">Estimated Time <br> <small class="text_grey fb_300">Q1+Q2+Q3+Q4=Total Hours</small></label>
                                                    <span class="col-sm-2 pad_l0">
                                                       <div class="form-group">
                                                           <label>OCT NOV DEC</label>
                                                           <div class="">
                                                               <input type="text" placeholder="Hours" onblur="calc_estimate_time('<?php echo $tta_details['contract_num']; ?>');" class="form-control esttime_<?php echo $tta_details['contract_num']; ?> allow_number_only"  id="estimate_q1_<?php echo $tta_details['contract_num']; ?>" name="estimate_q1" value="<?php echo $tta_details['estimate_q1']; ?>">
                                                           </div>
                                                       </div>
                                                    </span>
                                                    <span class="col-sm-2">
                                                       <div class="form-group">
                                                           <label>JAN FEB MAR</label>
                                                           <div class="">
                                                               <input type="text" placeholder="Hours" onblur="calc_estimate_time('<?php echo $tta_details['contract_num']; ?>');" class="form-control esttime_<?php echo $tta_details['contract_num']; ?> allow_number_only"  id="estimate_q2_<?php echo $tta_details['contract_num']; ?>" name="estimate_q2" value="<?php echo $tta_details['estimate_q2']; ?>">
                                                           </div>
                                                       </div>
                                                    </span>
                                                    <span class="col-sm-2">
                                                       <div class="form-group">
                                                           <label>APR MAY JUN</label>
                                                           <div class="">
                                                               <input type="text" placeholder="Hours" onblur="calc_estimate_time('<?php echo $tta_details['contract_num']; ?>');" class="form-control esttime_<?php echo $tta_details['contract_num']; ?> allow_number_only"  id="estimate_q3_<?php echo $tta_details['contract_num']; ?>" name="estimate_q3" value="<?php echo $tta_details['estimate_q3']; ?>">
                                                           </div>
                                                       </div>
                                                    </span>
                                                    <span class="col-sm-2">
                                                       <div class="form-group">
                                                           <label>JUL AUG SEP</label>
                                                           <div class="">
                                                               <input type="text" placeholder="Hours" onblur="calc_estimate_time('<?php echo $tta_details['contract_num']; ?>');" class="form-control esttime_<?php echo $tta_details['contract_num']; ?> allow_number_only"  id="estimate_q4_<?php echo $tta_details['contract_num']; ?>" name="estimate_q4" value="<?php echo $tta_details['estimate_q4']; ?>">
                                                           </div>
                                                       </div>
                                                    </span>
                                                    <span class="col-sm-2">
                                                       <div class="form-group">
                                                           <label>Total Hours</label>
                                                           <div class="">
                                                               <input type="text" readonly="readonly" placeholder="Hours"  class="form-control"  id="estimate_total_<?php echo $tta_details['contract_num']; ?>" name="estimate_total" value="<?php echo $tta_details['estimate_total']; ?>">
                                                           </div>
                                                       </div>
                                                    </span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Results</label>
                                                        <textarea class="form-control"  placeholder="Write something here" id="prelim_result_<?php echo $tta_details['contract_num']; ?>" name="prelim_result"><?php echo $tta_details['prelim_result']; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                  </div>

                                    <div class="row mar_tb40">
                                        <div class="col-md-offset-3 col-md-6 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label>Description Of Service Requested</label>
                                                <textarea class="form-control" id="TTA_desc_<?php echo $tta_details['contract_num']; ?>" name="TTA_desc" placeholder="Write something here"><?php
                                                    if(!empty($tta_details['TTA_desc'])){echo $tta_details['TTA_desc'];}
                                                    if(!empty($tta_details['regarding_notes'])){echo $tta_details['regarding_notes'];}
                                                    ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Push Notification</label>
                                                <div class="">
                                                    <?php
                                                    $push_notification_array = explode(",",$tta_details['push_notification']);
                                                    ?>
                                                    <ul>
                                                        <li>
                                                            <label class="mar0">
                                                                <input type="checkbox" name="push_notification[]" value="User" <?php if(in_array("User",$push_notification_array)){ ?>checked="checked"<?php } ?>  id="push_notification1_<?php echo $tta_details['contract_num']; ?>" class="push_notification_<?php echo $tta_details['contract_num']; ?>">
                                                                <span class="custom-icon checkbox-icon"></span>Agency User(s)
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label class="mar0">
                                                                <input type="checkbox" name="push_notification[]" value="Submiter" <?php if(in_array("Submiter",$push_notification_array)){ ?>checked="checked"<?php } ?> id="push_notification2_<?php echo $tta_details['contract_num']; ?>" class="push_notification_<?php echo $tta_details['contract_num']; ?>">
                                                                <span class="custom-icon checkbox-icon"></span>Submitter
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label class="mar0">
                                                                <input type="checkbox" name="push_notification[]" value="Admin" <?php if(in_array("Admin",$push_notification_array)){ ?>checked="checked"<?php } ?> id="push_notification3_<?php echo $tta_details['contract_num']; ?>" class="push_notification_<?php echo $tta_details['contract_num']; ?>">
                                                                <span class="custom-icon checkbox-icon"></span>Site Administrator
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label class="mar0">
                                                                <input type="checkbox" name="push_notification[]" value="Others" <?php if(in_array("Others",$push_notification_array)){ ?>checked="checked"<?php } ?> id="push_notification4_<?php echo $tta_details['contract_num']; ?>" class="push_notification_<?php echo $tta_details['contract_num']; ?>">
                                                                <span class="custom-icon checkbox-icon"></span>Other
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Send Updates To Email</label>

                             <input type="text" class="form-control" name="push_notify_email" placeholder="Email address"  value="<?php echo $tta_details['push_notify_email']; ?>">
                                                <textarea class="form-control mar_t10" name="push_notify_comments" placeholder="Write something here"><?php echo $tta_details['push_notify_comments']; ?></textarea>
                                            </div>
                                            <?php 
                                            if($tta_details['status']=='' || $tta_details['status']=='pending'){
                                            if(($_SESSION['userrole']==1)||($_SESSION['userrole']==4)||(in_array($tta_details['agency_id'],$user_agency))){?>
                                            <div class="col-xs-12 col-sm-12 text-center form">
                                                <button type="submit" name="ttaEditEnquiry">Save</button>
                                                <button class="mar_l10 cancel_btn">Clear</button>
                                            </div>
                                            <?php }}
                                            else if($tta_details['status']=='finished'){
                                            if(($_SESSION['userrole']==1)||($_SESSION['userrole']==4)){
                                            ?>
                                            <div class="col-xs-12 col-sm-12 text-center form">
                                                <button type="submit" name="ttaEditEnquiry">Save</button>
                                                <button class="mar_l10 cancel_btn">Clear</button>
                                            </div>
                                            <?php }}//}
                                            else if($tta_details['status']=='started'){
                                            if(($_SESSION['userrole']==1)||($_SESSION['userrole']==4) ){
                                            ?>
                                            <div class="col-xs-12 col-sm-12 text-center form">
                                                <button type="submit" name="ttaEditEnquiry">Save</button>
                                                <button class="mar_l10 cancel_btn">Clear</button>
                                            </div>
                                            <?php }}else{?>
                                            <div class="col-xs-12 col-sm-12 text-center form">
                                                <button type="submit" name="ttaEditEnquiry">Save</button>
                                                <button class="mar_l10 cancel_btn">Clear</button>
                                            </div>    
                                            <?php }?>
                                        </div>
                                    </div>
								</div>
                            </div>
							</form>
                          </div>
						  
						   <?php  } ?> 
                        </div>
                        
                       
                        </div>
                    </div>
					</div>
					</div>
					</div>
					</div>
					</div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <ul class="pagination">
							<?php
                            if($tta_form_counts >5){
                                $total_pages = ceil($tta_form_counts / $per_page);
                            }
                            else{
                                $total_pages =1;
                            }
                            if($cPage>1){
                                $previous = $cPage - 1;
                            }
                            else{
                                $previous = $cPage;
                            }
                            if($total_pages==1){
                                $next = $cPage;
                            }else{
                                $next = $cPage + 1;
                            }

							?>
                             <li><a href="dashboard.php?page=<?php echo $previous; ?>&searchFields=<?php echo $_GET['searchFields']; ?>&searchValues=<?php echo $_GET['searchValues']; ?>">&#171;</a></li>
              
							 <?php for($i=1;$i<=$total_pages;$i++){
								if($i < ($cPage + 10) && $i >= $cPage){
								 ?>
								 <li><a href="dashboard.php?page=<?php echo $i; ?>&searchFields=<?php echo $_GET['searchFields']; ?>&searchValues=<?php echo $_GET['searchValues']; ?>"><?php echo $i; ?></a></li>
								 <?php
								} 
							 }?>
                              
                              <li><a href="dashboard.php?page=<?php echo $next; ?>&searchFields=<?php echo $_GET['searchFields']; ?>&searchValues=<?php echo $_GET['searchValues']; ?>">&#187;</a></li>
                            </ul>       
                        </div>
                    </div>
	     		</div>
     		</section>
<?php include 'templates/footer.php'; ?>
<link rel="stylesheet" href="pages/css/fine-uploader-new.min.css" type="text/css" />
<script type="text/javascript" src="assets/js/all.fine-uploader.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/visualization/echarts/echarts.js"></script>
<script type="text/javascript" src="assets/js/core/app.js"></script>
<script type="text/javascript" src="assets/js/charts/echarts/pies_donuts.js"></script>

<script type="text/javascript">
$("#load_graphs").click(function(){
        $.getScript("assets/js/core/app.js");
      }); 
	  
    $(document).ready(function(){
        $(".edit_disable input,.edit_disable textarea,.edit_disable select").attr('disabled','disabled');
        
        $("[data-tooltip = 'tooltip']").tooltip(); 
        $('#accordion .panel-collapse').on('show.bs.collapse', function () {
            $(this).siblings('.panel-heading').addClass('active');
          });
        $('#accordion .panel-collapse').on('hide.bs.collapse', function () {
            $(this).siblings('.panel-heading').removeClass('active');
          });
		$('#search_button').click(function(){
			var search_name = $('#search_name').val();
            window.location = "dashboard.php?agency_name="+search_name+"&searchFields=<?php echo $_GET['searchFields']; ?>&searchValues=<?php echo  $_GET['searchValues']; ?>";
		});
		$('#edit_tta_enquiry').click(function(){
			var tta_form_postdata = $('#edit_tta_form').serialize();
			$.ajax({
				type: "POST",
				url: 'search.php',
				data : {tta_form_postdata:tta_form_postdata},
				success: function(response) {
					console.log("aaaa  "+response);
					$("#response").html(response);
					alert(response);
				}
			});
		});
		$('.status, .users, .regarding, .region, .agency_name').change(function(){
			var searchClassName = $(this).attr('class');

			var searchFields = searchClassName.split(" ").pop();

			var searchValues = $(this).val();
            window.location = "dashboard.php?searchFields="+searchFields+"&searchValues=" + searchValues;
		});
    });
    function editform(id) {
        if($('#'+id+'').css('display') == 'none') {
            document.getElementById(id).style.display = 'block';
            document.getElementById(id+"_main").style.display = 'block';
            document.getElementById(id+"_sub").style.display = 'none';
        }
        else
        {
            document.getElementById(id).style.display = 'none';
            document.getElementById(id+"_main").style.display = 'block';
            document.getElementById(id+"_sub").style.display = 'block';
        }
    }
    function updown(id) {
        if($('#'+id+'').css('display') == 'none') {
            var myString = id;
            var arr = myString.split('_');
            document.getElementById(id).style.display = 'block';
            document.getElementById(arr[0]+"_sub").style.display = 'block';
            document.getElementById(arr[0]).style.display = 'none';
        } else {
            document.getElementById(id).style.display = 'none';
        }
    }

    function add_comments()
    {
        var agency= $('#com_agency').val();
        var contract=$('#com_contract').val();

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

        var regarding_notes=$('#regarding_notes').val();
        $('#htmlcomment').html('');$('#comment_status').html('');
        var formData = {contract:contract,agency:agency,regarding_notes:regarding_notes, com_user:com_user,com_admin:com_admin,com_other:com_other,com_email:com_email,com_subject:com_subject};
        $.ajax({
            url : "insert_regarding_notes.php",
            type: "POST",
            data : formData,
            beforeSend: function() {
                $("#loading_comments").show();
            },
            success: function(data, textStatus, jqXHR)
            {
			console.log(data);
                $('#com_subject').val('');
                $('#com_email').val('');
                if(  $("input[type='checkbox']#com_admin").is(':checked') ){
                    $("input[type='checkbox']#com_admin").prop('checked', false);
                }
                if(  $("input[type='checkbox']#com_other").is(':checked') ){
                    $("input[type='checkbox']#com_other").prop('checked', false);
                }
                $("input[type='checkbox']#com_user").prop('checked', true);
                document.getElementById("loading_comments").style.display = 'none';
                regarding_comments(contract,agency);
                var result=myTrim(data);
                if(result=='failure') $('#comment_status').html('Comments not updated, Please try again! ');
                else $('#comment_status').html('Comments updated successfully!!!');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    function myTrim(x) {
        return x.replace(/^\s+|\s+$/gm,'');
    }
    function regarding_comments(contract,agency)
    {

        $('input[name="com_agency"]').val(agency);
        $('input[name="com_contract"]').val(contract);

        $('#comment_status').html('');$('#regarding_notes').val('');
        if(agency=='')
        {
            alert("Please Choose Agency"); window.location='tta_enquiry.php'; return false;
        }
        $('.'+contract+'_bell').hide();
        $('i.'+contract+'_belll').removeClass("fa-bell");
        $('i.'+contract+'_belll').addClass("fa-bell-o");

        var formData = {contract:contract,agency:agency};
        $.ajax({
            url : "get_regarding_notes.php",
            type: "POST",
            data : formData,
            beforeSend: function(){
                $("#htmlcomment").html("");
                $("#loading_comments").show();
            },
            complete: function(){
                $("#loading_comments").hide();
            },
            success: function(data, textStatus, jqXHR)
            {
                $("#htmlcomment").html(data);
                var formData = "";
                $.ajax({
                url: "getDashboardNew_new.php",
                type: "POST",
                data: formData,
                cache: false,
                success: function (html) {
                    var $success = $.trim(html);
					$("span.tta_count").html($success);
                }
            });


            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    function calc_estimate_time(id)
    {
        var sum = 0;
        $('.esttime_'+id).each(function(){
            if(this.value != "")
            {
                sum += parseFloat(this.value);
            }
            else
            {
                $(this).val("0");
            }
        });
        $("#estimate_total_"+id).val(sum);
    }
    </script>
    <script>
        $(document).ready(function(){
          $('.resources_select').select2();
        });
    </script>
    <?php
$ttaRecords="SELECT tta.id,tta.contract_num as contract_num,tta.agency_id as agency_id ,A.name as agency_name, tta.created_date as created_date,tta.status
	  as status,tta.created_date as created_date,tta.updated_date as updated_date,tta.assignedUser,tta.contract_num,tta.TTA_inquiry_type,tta.TTA_inquiry_notes,tta.TTA_desc,tta.TTA_outcome_notes,tta.TTA_Referral,tta.TTA_Contact_Phone,
	  tta.timeframe,tta.assigned_staff,tta.prelim_result,tta.regarding as regarding,tta.regarding_notes,tta.TTA_Email as email,
	  tta.timeframe_w,tta.resources as resources,tta.service_frame_start,tta.service_frame_end,tta.estimate_q1,tta.estimate_q2,tta.estimate_q3,
	  tta.estimate_q4,tta.estimate_total,tta.training_date,tta.push_notification as push_notification,tta.push_notify_email,tta.push_notify_comments,modality,
	  modality_other from TTA_Forms tta left join agency A on A.id=tta.agency_id WHERE ".$where." tta.delete_flag='N' AND A.name !=''
	  order by ".$order_by.$order_by_bell." tta.id Desc ".$limit;
      
$ttaRecordsList = mysql_query($ttaRecords) or die(mysql_error()); 
while($tta_details=mysql_fetch_assoc($ttaRecordsList)) {?>
    <script>
var manualUploader = new qq.FineUploader({
    element: document.getElementById('fine-uploader-manual-trigger_<?php echo $tta_details['contract_num']; ?>'),
    template: 'qq-template-manual-trigger_<?php echo $tta_details['contract_num']; ?>',
    request: {
        endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
    },
    deleteFile: {
        enabled: true,
        endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
    },
    chunking: {
        enabled: true,
        concurrent: {
            enabled: true
        },
        success: {
            endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php?done"
        }
    },
    resume: {
        enabled: true
    },
    retry: {
        enableAuto: true,
        showButton: true
    },
    autoUpload: true,
    debug: true
});

</script>
<?php } ?>  
<script>
qq(document.getElementById("trigger-upload")).attach("click", function() {
        manualUploader.uploadStoredFiles();
    });
    function info_taggle_cmd(){
        $('.info_taggle_cmd').toggle("slow");
      }
</script>
  </body>
</html>
