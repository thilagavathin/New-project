<?php include 'templates/header.php'; 
$id = safe_b64decode($_GET['intervention_id']);

 function get_strategy_type($id)
 {
	$sql = "select wb_id from work_bundle where wb_name = '$id'";
	$rs = mysql_query($sql);
	$row = mysql_fetch_array($rs);
	return $row[0];
 }
 
 function get_strategy_items($id)
 {
	$sql = "select wb_id,wb_name from work_bundle where wb_id='$id'";
	return mysql_query($sql);
 }
 
$bundle_sql ="SELECT * FROM interventions WHERE id=".$id ;
$bundle_details = mysql_query($bundle_sql);
$bundle_row = mysql_fetch_assoc($bundle_details);
$user_id = $bundle_row['user_id'];
//echo $user_id; 
$user_sql =mysql_query("SELECT * FROM login_users where user_id=".$user_id);
$user_details = mysql_fetch_assoc($user_sql);
$user_name = $user_details['username'];

echo '<input type="hidden" value="'.$bundle_row['agency_id'].'" id="agency_id" />';

$work_bundle             =($bundle_row['work_bundle']!=''              ? unserialize($bundle_row['work_bundle'])    
: $bundle_row['work_bundle']);
$description             =($bundle_row['description']!=''              ? unserialize($bundle_row['description'])    
: $bundle_row['description']);
$start_date              =($bundle_row['start_date']!=''               ? unserialize($bundle_row['start_date'])     
: $bundle_row['start_date']);
$end_date                =($bundle_row['end_date']!=''                 ? unserialize($bundle_row['end_date'])      
: $bundle_row['end_date']);
$o_start_date            =($bundle_row['o_start_date']!=''             ? unserialize($bundle_row['o_start_date'])     
: $bundle_row['o_start_date']);
$o_end_date              =($bundle_row['o_end_date']!=''               ? unserialize($bundle_row['o_end_date'])      
: $bundle_row['o_end_date']);
$like_training           =($bundle_row['like_training']!=''            ? unserialize($bundle_row['like_training']) 
: $bundle_row['like_training']);
$about_training          =($bundle_row['about_training']!=''           ? unserialize($bundle_row['']) 
: $bundle_row['about_training']);
$responsibilities_parties=($bundle_row['responsibilities_parties']!='' ? unserialize($bundle_row['responsibilities_parties']) 
: $bundle_row['responsibilities_parties']);
$action_steps            =($bundle_row['action_steps']!=''             ? unserialize($bundle_row['action_steps'])  
: $bundle_row['action_steps']);
$activities              =($bundle_row['activities']!=''               ? unserialize($bundle_row['activities']): $bundle_row['activities']);
$activities_checks       =($bundle_row['activities_checks']!=''        ? unserialize($bundle_row['activities_checks']): $bundle_row['activities_checks']);
$target_audience         =($bundle_row['target_audience']!=''          ? unserialize($bundle_row['target_audience'])          : $bundle_row['target_audience']);
$other_target_audience   =($bundle_row['other_target_audience']!=''    ? unserialize($bundle_row['other_target_audience'])    : $bundle_row['other_target_audience']);
$end_status              =($bundle_row['end_status']!=''    ? unserialize($bundle_row['end_status'])    : $bundle_row['end_status']);


$filter_work_bundle=array_unique($work_bundle);


$min_date = min(array_map('strtotime', array_filter($start_date))); 
$start_date_filters=array_map('strtotime', array_filter($start_date));
sort($start_date_filters,SORT_NUMERIC);
//$start_date_filters=array_unique($start_date_filters, SORT_REGULAR);
//print_r($start_date_filters);
$fill_temp_date=$min_date;
for($im=1;$im<=5;$im++)  {  
  $fil_months[]=$fill_temp_date;  
  $fill_temp_date = strtotime(date('Y-m-d',$fill_temp_date).'+1 month');
} 
//print_r($filter_work_bundle);
?>

<style>
.workbundle .filter-box button {
  font-weight: 400;
  padding: 5px 0;
  width:100%;
}
select.disable_select {
    background: #eeeeee none repeat scroll 0 0;
    cursor: no-drop;
}
</style>     		
             <section>
          <div class="container">
		  <div class="row">
			<div class="col-md-12">
			<?php $intervention_name=$bundle_row['intervention_name']; ?>
				<ol class="breadcrumb">
				  <li><a href="systemdashboard.php">Dashboard</a></li>
				  <li><a href="implementation_planning.php">Implementation planning Dashboard</a></li>              
				  <li class="active"><?php echo $intervention_name.' - '. $bundle_row['intervention_community_name']; ?> </li>              
				</ol>
			</div>
				 </div>
             <div class="row workbundle">
                <div class="col-md-12">
                  <h1 class="page-title filter-box"><?php echo $intervention_name.' - '. $bundle_row['intervention_community_name'] ; ?> </h1> <!-- page title -->
                </div>
               </div>
               <div class="row workbundle filter-box form">
               <form name="frmsearch" action="worknode_dashboard.php?intervention_id=<?php echo $_GET['intervention_id'] ?>" method="post">
                  <div class="col-md-3" id="WB_Filtername_cover">
                      <div class="form-group">
                         <label>Filter by Name</label>
                         <select class="form-control" id="filter_name" name="filter_name">
                            <option value="">All</option>
                            <?php for($w=0;$w<count($filter_work_bundle);$w++){?>
                            <option <?php if(trim($filter_work_bundle[$w])==trim($_POST['filter_name'])){ echo 'selected="selected"';} ?> value="<?php echo trim($filter_work_bundle[$w]); ?>"><?php echo $filter_work_bundle[$w]; ?></option>
                            <?php }?>
                         </select>
                         <input type="hidden" value="<?php echo trim($work_bundle[$w]); ?>" id="filter_wbname" />
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="form-group">
                         <label>Filter by Month</label>
                         <select class="form-control" name="date">
                             <option value="">All Bundles</option> 
                             <?php
                             $check_filter_date=array();
                             if(isset($start_date_filters)){ foreach($start_date_filters as $start_date_filter) {
                             if(!in_array(date('m/Y',$start_date_filter),$check_filter_date)){ 
                             $check_filter_date[] =date('m/Y',$start_date_filter); 
                             ?>
                             <option value="<?php echo date('m/Y',$start_date_filter) ?>" <?php if($_POST['date']==date('m/Y',$start_date_filter)){ echo 'selected="selected"';} ?>><?php echo date('F Y',$start_date_filter) ?></option> 
                             <?php }}}?>                                         
                         </select>
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="form-group">
                         <label>Filter by Date</label>
                         <div class="input-group date form_date" data-date-format="mm/dd/yyyy" >
                            <input class="form-control" type="text" id="filter_date" name="filter_date" value="<?php echo $_POST['filter_date']; ?>" >
        					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>&nbsp;</label>
                        <button class="search_btn">Search</button>
                     </div>
                  </div>
                </form>
            </div>
            <div class="res_wb">

            <div class="row wb_head text-center mar0">
                <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
                   <label>WB. Name</label>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                   <label>WB. Description</label>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                   <label>S.Date<br />E.date</label>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                   <label class="act_title">Edit/Save</label>
                </div>
                <div class="col-lg-1 hidden-md hidden-sm hidden-xs">
                   <label>CMTS</label>
                </div>
                <div class="col-lg-1 hidden-md hidden-sm hidden-xs">
                   <label>Time</label>
                </div>
                <div class="col-lg-1 hidden-md hidden-sm hidden-xs">
                   <label>Demo</label>
                </div>
                <div class="col-lg-1 hidden-md hidden-sm hidden-xs">
                   <label>Notes</label>
                </div>
                <div class="col-lg-1 hidden-md hidden-sm hidden-xs">
                   <label>Upload</label>
                </div>
              </div>
              <form method="post" id="WBform">
              <div class="row mar0 workbundle-list">              
                <?php 
				$work_bundle_count=1;
				$work_bundle_count=count($work_bundle);
                $bundles_count=0;
				for($w=0;$w<count($work_bundle);$w++){
				    $prjt_start_date= date('m/Y',strtotime($start_date[$w]));
                    $prjt_end_date=  date('m/Y',strtotime($end_date[$w]));
                
                //Commets Count calculation
                
                $wb_comment=mysql_query("SELECT COUNT(id) FROM wb_comments where view_status='N' AND intervention_id='".$id."' AND node_id='".$w."' AND agency_id='".$bundle_row['agency_id']."'");
                $comment_wb=mysql_fetch_row($wb_comment);
                $wb_comment_user=mysql_query("SELECT comment_count FROM wb_comments_status WHERE agency_id=".$bundle_row['agency_id']." AND intervention_id='".$id."' AND node_id='".$w."' AND user_id=".$_SESSION['adminlogin']);
                $comment_wb_user=mysql_fetch_row($wb_comment_user);
                $rega_count=$comment_wb[0]-$comment_wb_user[0];               
                
                                    
                if(($_POST['date']!='') || ($_POST['filter_name']!='') || ($_POST['filter_date']!='')){                     
                if(($prjt_start_date==$_POST['date']) || ($prjt_end_date==$_POST['date']) || (trim($work_bundle[$w])==trim($_POST['filter_name']))|| ((strtotime($start_date[$w])<=strtotime($_POST['filter_date']))&& (strtotime($end_date[$w])>=strtotime($_POST['filter_date'])))) {
				$bundles_count +=1;   
				
				?>
              
                 <div class="text-center WB_list<?php echo $w;?> disabled_input" onclick="addActiveClass(<?php echo $w;?>)">
                    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
                       <div class="form-group">
                           <span data-toggle = "tooltip" data-placement = "top" title="Work Bundle Name">
                           <select class="form-control disable_select" name="work_bundle[]" id="work_bundle">
                               <?php
								$strategy = get_strategy_type($bundle_row['strategy_type']);
								$strategy_items = get_strategy_items($strategy);
								while($items=mysql_fetch_array($strategy_items))
								{ ?>
									<option <?php if($work_bundle[$w]==$items[1]){echo 'selected="selected"';} ?> value="<?php echo $items[1]?>"><?php echo $items[1]?></option>
								<?php } ?>
						   </select>
                           </span>
                       </div>
                       <p class="text-left"><a data-toggle="collapse" href="#collapse<?php echo $w;?>"><i class="fa fa-angle-down mar_r10"></i> Details</a></p> <!-- page title -->
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                       <div class="form-group">
                           <span data-toggle = "tooltip" data-placement = "top" title="Work Bundle Description"><textarea class="form-control" name="description[]" id="description"><?php echo $description[$w]; ?></textarea></span>
                       </div>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-2 pad0 col-xs-2">
                       <div class="form-group">
                           <span class="form_date_alone" data-toggle = "tooltip" data-placement = "right" title="Actual Start Date"><input class="form-control date form_date_alone mar_b10" readonly="" placeholder="Start" type="text" name="o_start_date[]" id="o_start_date<?php echo $w;?>" value="<?php echo $o_start_date[$w];  ?>" data-date-format="mm/dd/yyyy"></span>
                           <span class="form_date_alone" data-toggle = "tooltip" data-placement = "right" title="Actual End Date" ><input class="form-control date form_date_alone" readonly=""  placeholder="End" type="text" name="o_end_date[]" id="o_end_date<?php echo $w;?>" value="<?php echo $o_end_date[$w];  ?>" data-date-format="mm/dd/yyyy"></span>
     			            
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                        
                        <span class="btn btn-default red_bg text_white no-border sh_ebtn editWBbutton editWBbutton<?php echo $w;?>" onclick="edit_open(<?php echo $w;?>)" data-toggle = "tooltip" data-placement = "bottom" title="Edit"><i class="fa fa-edit ft_18" ></i></span>
                        <span class="btn btn-default green_bg text_white no-border saveWBbutton saveWBbutton<?php echo $w;?>" onclick="save_wb(<?php echo $w;?>)" data-toggle = "tooltip" data-placement = "bottom" title="Save" style="display: none;"><i class="fa fa-save ft_18"></i></span> 
                     
                        <div class="dropdown" style="display: none;">
                             <span class="btn btn-default red_bg text_white no-border dropdown-toggle" data-toggle="dropdown"><i class="fa fa-ellipsis-h ft_18"></i></span>
                             <ul class="dropdown-menu">
                                <li><a href="#" class="editMbutton editMbutton<?php echo $w;?>" onclick="edit_open(<?php echo $w;?>)"><i class="fa fa-edit ft_18"></i> Edit</a></li>
                                <li><a href="#" class="saveMbutton saveMbutton<?php echo $w;?>" onclick="save_wb(<?php echo $w;?>)" style="display: none;"><i class="fa fa-save ft_18"></i> Save</a></li>
                                <li><a href="#"><i class="fa fa-bell-o ft_18"></i> CMTS</a></li>
                                <li><a href="#"><i class="fa fa-clock-o ft_18"></i> Time</a></li>
                                <li><a href="#"><i class="fa fa-users ft_18"></i>Demo</a></li>
                                <li><a href="#"><i class="fa fa-sticky-note ft_18"></i>Notes</a></li>
                                <li><a href="#"><i class="fa fa-upload ft_18"></i>Upload</a></li>
                              </ul>
                          </div>
                        
                                            
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs" >
                       <span class="btn btn-default blue_bg text_white no-border" data-toggle = "tooltip" data-placement = "bottom" title="Comments"><span class="solid" data-target="#usercomments-quickview" data-toggle="modal" onclick="regarding_comments('<?php echo $id; ?>','<?php echo $w; ?>','<?php echo $work_bundle[$w]; ?>');">
                            <?php if($rega_count!=0){ ?>
                            <i class="fa fa-bell ft_18 <?php echo $w; ?>_belll "></i><span class="<?php echo $w; ?>_bell"> <?php echo $rega_count; ?> </span>                            
                            <?php }else{ ?>
                            <i class="fa fa-bell-o ft_18 "></i>  
                            <?php } ?>
                       </span></span>
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs">
                       <span class="btn btn-default blue_bg text_white no-border" data-toggle = "tooltip" data-placement = "bottom" title="Time"><i class="fa fa-clock-o ft_18 "></i></span>
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs" data-toggle = "tooltip" data-placement = "bottom" title="Demo">
                       <span class="btn btn-default blue_bg text_white no-border"><i class="fa fa-users ft_18"></i></span>
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs" data-toggle = "tooltip" data-placement = "bottom" title="Notes">
                      <span class="btn btn-default blue_bg text_white no-border"> <i class="fa fa-sticky-note ft_18"></i></span>
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs" data-toggle = "tooltip" data-placement = "bottom" title="Upload">
                       <span class="btn btn-default blue_bg text_white no-border"><i class="fa fa-upload ft_18"></i></span>
                    </div>
                    
                    <!-- details box -->
                      <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                          <div id="collapse<?php echo $w;?>" class="panel-collapse collapse">
                           <div class="row pad_lr30">
                              <div class="col-md-3 col-sm-6 col-xs-3">
                                 <div class="workbundle_detailsbox">
                                    <p>Target Audience</p>
                                    <label name="bundle_audience" id="bundle_audience"><?php echo ($target_audience[$w]=='Others'?$other_target_audience[$w]:$target_audience[$w]); ?></label>
                                 </div>
                              </div>
                              <div class="col-md-3 col-sm-6 col-xs-3">
                                 <div class="workbundle_detailsbox">
                                    <p>Responsible Party</p>
                                    <label name="bundle_parties" id="bundle_parties"><?php echo $responsibilities_parties[$w]; ?></label>
                                 </div>
                              </div>
                              <div class="col-md-3 col-sm-6 col-xs-6">
                                 <div class="workbundle_detailsbox">
                                    <p>Projected Start</p>
                                    <label name="bundle_start_date" id="bundle_start_date">
                                    <?php if($end_status[$w]=='No'){echo $start_date[$w];}else{echo 'Ongoing';} ?>
                                    </label>
                                    <span class="time-iconbg hidden-xs hidden-sm"></span>
                                    <span class="time-icon hidden-xs hidden-sm"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                 </div>
                              </div>
                              <div class="col-md-3 col-sm-6 col-xs-3">
                                 <div class="workbundle_detailsbox">
                                    <p>Projected End</p>
                                    <label name="bundle_end_date" id="bundle_end_date">
                                    <?php if($end_status[$w]=='No'){echo $end_date[$w];}else{echo 'Ongoing';} ?>
                                    </label>
                                 </div>
                              </div>
                          </div>
                         <div class="col-md-12">
                            <div class="panel-group">
                              <div class="panel panel-default">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#workbundle_list" href="#collapse1<?php echo $w;?>" class="activities"><i class="fa fa-angle-down" aria-hidden="true"></i><span>Action Steps</span></a>
									<a href="javascript:void(0);" data-toggle="modal" data-target="#addpost_modal" onclick="open_add(<?php echo $w;?>)"><i class="fa fa-plus-circle" aria-hidden="true"></i><span>Add</span></a>
                                    <a href="javascript:void(0);" onclick="edit_activities(<?php echo $w;?>)"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Edit</span></a>
                                    <a href="javascript:void(0);" onclick="click_edit()" id="save_activities<?php echo $w;?>"><i class="fa fa-plus-circle" aria-hidden="true"></i><span>Save</span></a>
                                    <a href=""><i class="fa fa-bell" aria-hidden="true"></i><span>Comments</span></a>
                                  </h4>
                                  <a href="" class="close_panel"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
                                </div>
                                <div id="collapse1<?php echo $w;?>" class="panel-collapse collapse">
                                  <div class="panel-body">
                                  
                                     <ul class="activity_list activity_list<?php echo $w;?>">
                                        <?php for($a=0;$a<count($activities[$w]);$a++) {
									    if($activities[$w][$a]!=''){?> 
                                        <li class="col-md-3 col-xs-3">
                                           <div class="blue_bg pad10 form-inline">
                                                <div class="checkbox">
                                                    <label class="cus_cb">
                                                        <input type="checkbox" class="hidden activity_inputcheckbox<?php echo $a;?>" <?php if($activities_checks[$w][$a]=='on'){ echo 'checked=""';} ?> name="activities_checks[<?php echo $w;?>][<?php echo $a;?>]" />
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="form-group mar_l10">
                                                    <input type="text" class="form-control activity_inputtype<?php echo $a;?>" name="activities[<?php echo $w;?>][]" value="<?php echo $activities[$w][$a]; ?>" />
                                                </div>
                                           </div>
                                        </li>
                                        <?php }} ?>
									</ul>
                                    
                                  </div>
                                </div>
                              </div>
                            </div>
                         </div>
                         </div>
                        </div>
                      </div>
                 </div>
                 
                 <?php } //If search available
                    else{   ?>
                        <input type="hidden" value="<?php echo $work_bundle[$w]; ?>" name="work_bundle[]"/> 
                        <input type="hidden" value="<?php echo $description[$w]; ?>" name="description[]"/>  
                        <input type="hidden" value="<?php echo $o_start_date[$w]; ?>" name="o_start_date[]"/> 
                        <input type="hidden" value="<?php echo $o_end_date[$w]; ?>" name="o_end_date[]"/> 
                    <?php 
                    } }else{
    				  $bundles_count +=1;  
                    ?>
                 <div class="text-center WB_list<?php echo $w;?> disabled_input" onclick="addActiveClass(<?php echo $w;?>)" >
                    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
                       <div class="form-group">
                           <span data-toggle = "tooltip" data-placement = "top" title="Work Bundle Name">
                           <select class="form-control disable_select" name="work_bundle[]" id="work_bundle">
                            <?php
									$strategy = get_strategy_type($bundle_row['strategy_type']);
									$strategy_items = get_strategy_items($strategy);
									while($items=mysql_fetch_array($strategy_items))
									{ ?>
										<option <?php if($work_bundle[$w]==$items[1]){echo 'selected="selected"';} ?> value="<?php echo $items[1]?>"><?php echo $items[1]?></option>
									<?php } ?>
                           </select>
                           </span>
                       </div>
                       <p class="text-left"><a data-toggle="collapse" href="#collapse<?php echo $w;?>"><i class="fa fa-angle-down mar_r10"></i> Details</a></p> <!-- page title -->
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                       <div class="form-group">
                           <span data-toggle = "tooltip" data-placement = "top" title="Work Bundle Description"><textarea class="form-control" name="description[]" id="description"><?php echo $description[$w]; ?></textarea></span>
                       </div>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-2 pad0 col-xs-2">
                       <div class="form-group">
                           <span class="form_date_alone" data-toggle = "tooltip" data-placement = "right" title="Actual Start Date"><input class="form-control date form_date_alone mar_b10" readonly="" placeholder="Start" type="text" name="o_start_date[]" id="o_start_date<?php echo $w;?>" value="<?php echo $o_start_date[$w];  ?>" data-date-format="mm/dd/yyyy"></span>
                           <span class="form_date_alone" data-toggle = "tooltip" data-placement = "right" title="Actual End Date" ><input class="form-control date form_date_alone" readonly=""  placeholder="End" type="text" name="o_end_date[]" id="o_end_date<?php echo $w;?>" value="<?php echo $o_end_date[$w];  ?>" data-date-format="mm/dd/yyyy"></span>
     			            
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                       <span class="btn btn-default red_bg text_white no-border sh_ebtn editWBbutton editWBbutton<?php echo $w;?>" onclick="edit_open(<?php echo $w;?>)" data-toggle = "tooltip" data-placement = "bottom" title="Edit"><i class="fa fa-edit ft_18" ></i></span>
                       <span class="btn btn-default green_bg text_white no-border saveWBbutton saveWBbutton<?php echo $w;?>" onclick="save_wb(<?php echo $w;?>)" data-toggle = "tooltip" data-placement = "bottom" title="Save" style="display: none;"><i class="fa fa-save ft_18"></i></span> 
                        <div class="dropdown" style="display: none;">
                             <span class="btn btn-default red_bg text_white no-border dropdown-toggle" data-toggle="dropdown"><i class="fa fa-ellipsis-h ft_18"></i></span>
                             <ul class="dropdown-menu">
                                <li><a href="#" class="editMbutton editMbutton<?php echo $w;?>" onclick="edit_open(<?php echo $w;?>)"><i class="fa fa-edit ft_18"></i> Edit</a></li>
                                <li><a href="#" class="saveMbutton saveMbutton<?php echo $w;?>" onclick="save_wb(<?php echo $w;?>)" style="display: none;"><i class="fa fa-save ft_18"></i> Save</a></li>
                                <li><a href="#"><i class="fa fa-bell-o ft_18"></i> CMTS</a></li>
                                <li><a href="#"><i class="fa fa-clock-o ft_18"></i> Time</a></li>
                                <li><a href="#"><i class="fa fa-users ft_18"></i>Demo</a></li>
                                <li><a href="#"><i class="fa fa-sticky-note ft_18"></i>Notes</a></li>
                                <li><a href="#"><i class="fa fa-upload ft_18"></i>Upload</a></li>
                              </ul>
                          </div>
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs" >
                       <span class="btn btn-default blue_bg text_white no-border" data-toggle = "tooltip" data-placement = "bottom" title="Comments"><span class="solid" data-target="#usercomments-quickview" data-toggle="modal" onclick="regarding_comments('<?php echo $id; ?>','<?php echo $w; ?>','<?php echo $work_bundle[$w]; ?>');">
                            <?php if($rega_count!=0){ ?>
                            <i class="fa fa-bell ft_18 <?php echo $w; ?>_belll "></i><span class="<?php echo $w; ?>_bell"> <?php echo $rega_count; ?> </span>                            
                            <?php }else{ ?>
                            <i class="fa fa-bell-o ft_18 "></i>  
                            <?php } ?> 
                       </span></span>
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs">
                       <span class="btn btn-default blue_bg text_white no-border" data-toggle = "tooltip" data-placement = "bottom" title="Time"><i class="fa fa-clock-o ft_18 "></i></span>
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs" data-toggle = "tooltip" data-placement = "bottom" title="Demo">
                       <span class="btn btn-default blue_bg text_white no-border"><i class="fa fa-users ft_18"></i></span>
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs" data-toggle = "tooltip" data-placement = "bottom" title="Notes">
                      <span class="btn btn-default blue_bg text_white no-border"> <i class="fa fa-sticky-note ft_18"></i></span>
                    </div>
                    <div class="col-lg-1 hidden-md hidden-sm hidden-xs" data-toggle = "tooltip" data-placement = "bottom" title="Upload">
                       <span class="btn btn-default blue_bg text_white no-border"><i class="fa fa-upload ft_18"></i></span>
                    </div>
                    
                    <!-- details box -->
                      <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                          <div id="collapse<?php echo $w;?>" class="panel-collapse collapse">
                           <div class="row pad_lr30">
                              <div class="col-md-3 col-sm-6 col-xs-3">
                                 <div class="workbundle_detailsbox">
                                    <p>Target Audience</p>
                                    <label name="bundle_audience" id="bundle_audience"><?php echo ($target_audience[$w]=='Others'?$other_target_audience[$w]:$target_audience[$w]); ?></label>
                                 </div>
                              </div>
                              <div class="col-md-3 col-sm-6 col-xs-3">
                                 <div class="workbundle_detailsbox">
                                    <p>Responsible Party</p>
                                    <label name="bundle_parties" id="bundle_parties"><?php echo $responsibilities_parties[$w]; ?></label>
                                 </div>
                              </div>
                              <div class="col-md-3 col-sm-6 col-xs-3">
                                 <div class="workbundle_detailsbox">
                                    <p>Projected Start</p>
                                    <label name="bundle_start_date" id="bundle_start_date">
                                    <?php if($end_status[$w]=='No'){echo $start_date[$w];}else{echo 'Ongoing';} ?>
                                    </label>
                                    <span class="time-iconbg hidden-xs hidden-sm"></span>
                                    <span class="time-icon hidden-xs hidden-sm"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                 </div>
                              </div>
                              <div class="col-md-3 col-sm-6 col-xs-3">
                                 <div class="workbundle_detailsbox">
                                    <p>Projected End</p>
                                    <label name="bundle_end_date" id="bundle_end_date">
                                    <?php if($end_status[$w]=='No'){echo $end_date[$w];}else{echo 'Ongoing';} ?>
                                    </label>
                                 </div>
                              </div>
                          </div>
                         <div class="col-md-12">
                            <div class="panel-group">
                              <div class="panel panel-default">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#workbundle_list" href="#collapse1<?php echo $w;?>" class="activities"><i class="fa fa-angle-down" aria-hidden="true"></i><span>Action Steps</span></a>
									<a href="javascript:void(0);" data-toggle="modal" data-target="#addpost_modal" onclick="open_add(<?php echo $w;?>)"><i class="fa fa-plus-circle" aria-hidden="true"></i><span>Add</span></a>
                                    <a href="javascript:void(0);" onclick="edit_activities(<?php echo $w;?>)"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Edit</span></a>
                                    <a href="javascript:void(0);" onclick="click_edit()" id="save_activities<?php echo $w;?>"><i class="fa fa-plus-circle" aria-hidden="true"></i><span>Save</span></a>
                                    <a href=""><i class="fa fa-bell" aria-hidden="true"></i><span>Comments</span></a>
                                  </h4>
                                  <a href="" class="close_panel"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
                                </div>
                                <div id="collapse1<?php echo $w;?>" class="panel-collapse collapse">
                                  <div class="panel-body">
                                  
                                     <ul class="activity_list activity_list<?php echo $w;?>">
									    <?php for($a=0;$a<count($activities[$w]);$a++) {
									    if($activities[$w][$a]!=''){?>
                                        <li class="col-md-3 col-xs-3" >
                                           <div class="blue_bg pad10 form-inline">
                                                <div class="checkbox">
                                                    <label class="cus_cb">
                                                        <input type="checkbox" class="hidden activity_inputcheckbox<?php echo $a;?>" <?php if($activities_checks[$w][$a]=='on'){ echo 'checked=""';} ?> name="activities_checks[<?php echo $w;?>][<?php echo $a;?>]" />
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="form-group mar_l10">
                                                    <input type="text" name="activities[<?php echo $w;?>][]" class="form-control activity_inputtype<?php echo $a;?>" value="<?php echo $activities[$w][$a]; ?>" />
                                                </div>
                                           </div>
                                        </li>
                                        <?php }} ?>
									</ul>
                                   
                                  </div>
                                </div>
                              </div>
                            </div>
                         </div>
                         </div>
                        </div>
                      </div>
                 </div>                
                 <?php } }  if($bundles_count==0){echo '<div class="text-center"> No bundles found.</div>';}  ?>
            </div>
            </form>
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
                              <textarea rows="2" id="wb_comments" name="wb_comments" class="form-control" placeholder="Write your comment here"></textarea>
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
							  <input type="hidden" id="com_strategy" name="com_strategy" value="" >
							  <input type="hidden" id="com_node" name="com_node" value="" >
                              <input type="hidden" id="com_node_name" name="com_node_name" value="" >
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
		  
		  <!-- Activities add button -->
	<div id="addpost_modal" class="modal right fade" role="dialog">
		<div class="modal-dialog">
		  <!-- Modal content-->
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
			  <h3 class="modal-title text_blue">Action Steps</h3>
			</div>
			<div class="modal-body no-shadow">
			  <div class="row" id="comment-form">
				 <form>
					<div class="col-md-12 col-sm-12 col-xl-12">
						<div class="form-group">
						  <label>Add Action Steps</label>
						  <input type="text" class="form-control" name="activity" id="activity">
					   </div>
				   </div>
				   <div class="col-xs-12 col-sm-12 text-center form"> 
                        <input type="hidden" id="wbbundle_id" />
					  <button type="button" onclick="add_activities()">Add</button>
					  <button type="button" class="mar_l10 cancel_btn" data-dismiss="modal">Clear</button>
				   </div>
				</form>
			  </div>
			</div>
		  </div>
		</div>
		</div>
            
            
            <!-- page content ends -->
          </div>
        </section>
<?php include 'templates/footer.php'; ?>   
<link href="new/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" media="screen">
<script src="new/js/bootstrap-datetimepicker.js" type="text/javascript"></script> 
<script type="text/javascript">
$(document).ready(function(){
    $('.disabled_input input,.disabled_input select,.disabled_input textarea,.disabled_input button').prop('readonly',true);
    $('.disabled_input .date.form_date_alone,.disabled_input select,.activity_list input').prop('disabled',true);   
    $('#accordion .panel-collapse').on('show.bs.collapse', function () {
        $(this).siblings('.panel-heading').addClass('active');
      });
    $('#accordion .panel-collapse').on('hide.bs.collapse', function () {
        $(this).siblings('.panel-heading').removeClass('active');
      });
});
</script>
<!-- slim scroll for attachment panels -->
<script type="text/javascript">
 
  var s_wsize = $(window).width();

  if(s_wsize < 768) {
     $('.workbundle-list').slimScroll({
        width:'767px',
        height:'auto',
        position: 'right',
        railVisible: true,
        alwaysVisible: true
    });
  } 
  
  var win_size = $(window).width();
  if(win_size >= 768){
    
    var wb_offset = $(".wb_head").offset().top;
    
  $(window).scroll(function(e){
    e.preventDefault();
    var scrolltop = $(window).scrollTop();
   
    if(scrolltop > wb_offset - 80 ){
        $(".wb_head").addClass("wbhead_fix");
        
    }else if(scrolltop < wb_offset - 80){
        $(".wb_head").removeClass("wbhead_fix");
    }
    
  });
  }
  
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".workbundle-list .dropdown").hide();
    $(".workbundle-list .sh_ebtn").show()
    var win_size = $(window).width();
    if(win_size < 768){
      $(".wb_head").addClass("wb_headfix");
      $(".workbundle-list > div").addClass("wb_listfix");
      $(".wb_heade").removeClass("wbhead_fix");
    }
    if(win_size < 1198){
      $(".act_title").empty();
      $(".workbundle-list .sh_ebtn").hide();
      $(".workbundle-list .dropdown").show();
    }
  });
</script>
<script type="text/javascript">

function addActiveClass(no){
   $('.disabled_input').removeClass('activate') ; 
   $('.WB_list'+no).addClass('activate') ;
}
function filter_by(){
 var filter_date=$('#filter_date').val();
 var filter_name=$('#filter_name').val();
 var curl = '<?php echo $site_url; ?>/worknode_dashboard.php?intervention_id=<?php echo $_GET['intervention_id'] ?>';
 if(filter_date!=''){
  curl += "&filter_date=" + filter_date;
 }
 if(filter_name!=''){
  curl += "&filter_name=" + filter_name;
 }
  window.location.href=curl;
 }
 $(document).ready(function() {
$('.form_date').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0,
         
    });   
$('.date.form_date_alone').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0,
         
    });
}); 
function edit_open(list){
    var win_size = $(window).width();
    $('.WB_list'+list+'.disabled_input input,.WB_list'+list+'.disabled_input select,.WB_list'+list+'.disabled_input textarea,.WB_list'+list+'.disabled_input button').prop('readonly',false);
    $('.date.form_date_alone').prop('readonly',true);
    if(win_size < 1198){
        $('.editMbutton'+list).hide();
        $('.saveMbutton'+list).show(); 
    }else{
        $('.editWBbutton'+list).hide();
        $('.saveWBbutton'+list).show();   
    }    
    $('.disabled_input .date.form_date_alone,.disabled_input select').prop('disabled',false);
    $('.WB_list'+list+'.disabled_input select').removeClass('disable_select');
}
function save_wb(list){    
    var WBform = $('#WBform').serialize();
    var win_size = $(window).width();
    var start_date=$('#o_start_date'+list).val();
    var end_date=$('#o_end_date'+list).val();
    
    $.ajax({
        url: "intervention_save.php",
        type: "POST",
        data: WBform+'&intervention_id='+<?php echo safe_b64decode($_REQUEST['intervention_id']); ?>+'&WB_update=1',
        success: function(data) {  
            if(data==0){sweetAlert("Oops...", "WorkBundle not saved, Please try again", "error");}
            else{       
                sweetAlert("Success...", "'WorkBundle' informations are saved successfully", "success");
                $('.disabled_input input,.disabled_input select,.disabled_input textarea,.disabled_input button').prop('readonly',true); 
                if(win_size < 1198){
                    $('.editMbutton').show();
                    $('.saveMbutton').hide(); 
                }else{
                    $('.saveWBbutton').hide();
                    $('.editWBbutton').show();   
                } 
                call_WB_names(); 
                $('.disabled_input .date.form_date_alone,.disabled_input select').prop('disabled',true);    
                $('.disabled_input select').addClass('disable_select') ;         
                }
            }
    });
    
}
function call_WB_names(){
    $.ajax({
        url: "get_wb_names.php",
        type: "POST",
        data: '&wb_name='+1+'&intervention_id='+<?php echo safe_b64decode($_REQUEST['intervention_id']); ?>,
        success: function(data) {
            $('#WB_Filtername_cover').html(data);
        }
    });
}
function add_comments()
{
    var agency= $('#agency_id').val();
    var strategy=$('#com_strategy').val();
    var node=$('#com_node').val();
    var node_name=$('#com_node_name').val();

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

    var wb_comments=$('#wb_comments').val();
    $('#htmlcomment').html('');$('#comment_status').html('');
    var formData = {strategy:strategy,node:node,node_name:node_name,agency:agency,wb_comments:wb_comments, com_user:com_user,com_admin:com_admin,com_other:com_other,com_email:com_email,com_subject:com_subject};
    $.ajax({
        url : "insert_wb_comments.php",
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
            regarding_comments(strategy,node,node_name);
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
function regarding_comments(strategy,node,node_name)
{
    var agency= $('#agency_id').val();
    $('input[name="com_strategy"]').val(strategy);
    $('input[name="com_node"]').val(node);
    $('input[name="com_node_name"]').val(node_name);

    $('#comment_status').html('');$('#wb_comments').val('');
    $('.'+node+'_bell').hide();
    $('i.'+node+'_belll').removeClass("fa-bell");
    $('i.'+node+'_belll').addClass("fa-bell-o");

    var formData = {strategy:strategy,node:node,agency:agency};
    $.ajax({
        url : "get_wb_comments.php",
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
        }        
   });
}
function edit_activities(bundle){
    $('#collapse1'+bundle).addClass('in');
    $('.activity_list'+bundle+' input').attr('readonly',false);
    $('.activity_list'+bundle+' input').attr('disabled',false);
    $('#save_activities'+bundle).attr('onclick','save_activities('+bundle+')');
}
function click_edit(){
    sweetAlert("Oops...", "Please click the edit button & save the changes ", "error");
}
function save_activities(bundle){    
    
    var inputs = $('.activity_list'+bundle+' input[type=text]');
    var checkbox = $('.activity_list'+bundle+' input[type=checkbox]');
    //alert(inputs.length);
    var checkbox_values = [];
    var checkbox_checked = [];
    var input_values = [];
    for (var i=0; i<inputs.length; i++) {
        if ($('.activity_list'+bundle+' .activity_inputcheckbox'+i).is(':checked')) {
            
            checkbox_values.push($('.activity_list'+bundle+' .activity_inputcheckbox'+i).val());
            checkbox_checked.push(i);
        }
       
        input_values.push($('.activity_list'+bundle+' .activity_inputtype'+i).val());
    }
    //alert(checkbox_checked)  ; 
     
    $.ajax({
        url:"activities.php",
        type:"POST",
        data :'&activities='+input_values+'&activities_checks='+checkbox_values+'&checkbox_checked='+checkbox_checked+'&bundle='+bundle+'&update_activities=1'+'&intervention_id='+<?php echo safe_b64decode($_REQUEST['intervention_id']); ?>,
        success:function(data){
            if(data==1){
                sweetAlert("Success...", "Activities are Updated successfully", "success");
                $('.activity_list'+bundle+' input').attr('readonly',true);
                $('.activity_list'+bundle+' input').attr('disabled',true);
            }else{
                sweetAlert("Oops...", "Activities are not saved,Please try again ", "error");
            }
        }
        
    });
}
function open_add(bundle){
    $('#wbbundle_id').val(bundle);
}
function add_activities()
{
	var activity = $('#activity').val();
    var bundle = $('#wbbundle_id').val();
	
	//var formData = {activity:activity};
	if(activity==""){
		alert('Please fill the activity');
        $('#activity').focus();
	}
	else {
	$.ajax({
        url: "activities.php",
        type: "POST",
        data: '&activities='+activity+'&bundle='+bundle+'&activity_add='+1+'&intervention_id='+<?php echo safe_b64decode($_REQUEST['intervention_id']); ?>,
        success: function(html) {
		 if(html==0){
             sweetAlert("Oops...", "Activity is not saved,Please try again ", "error");
         }
		else{
		    $('#collapse1'+bundle).addClass('in');
			$('.activity_list'+bundle).html(html);
            sweetAlert("Success...", "Activities are saved successfully", "success");
            $('#addpost_modal').modal('hide');
            $('#activity').val('');
            $('.activity_list'+bundle+' input').attr('disabled',true);
		} 
		
        }
    });
    }
}
</script>

