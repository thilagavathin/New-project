<?php include 'templates/header.php'; 
session_start();
include_once('config.php');
$user_id=$_SESSION['adminlogin'];
$agency_list = mysql_query("select id,name from agency order by name");
$agency_where='';
$filter_region=isset($_POST['region'])? $_POST['region']:'';
$filter_agency=isset($_POST['agency'])? $_POST['agency']:'';
if($filter_region!=''){
   $agency_where.=" AND region='".$filter_region."' "; 
}
if($filter_agency!=''){
   $agency_where.=" AND agency.id='".$filter_agency."' "; 
}

// Pagination 
$per_page=9;
if (isset($_REQUEST["page"])) { $cPage  = $_REQUEST["page"]; } else { $cPage=1; }
$start_from = ($cPage-1) * $per_page;


if($_SESSION['userrole']<>1){
$agency_where.=" AND user_id=".$user_id;
$agency_name = "select agency.* from agency join agency_map on agency.id=agency_map.agency_id where agency.name!='' ".$agency_where;
}else{
$agency_name = "select * from agency where name!='' ".$agency_where;    
}
$agency_name_limit =$agency_name. " LIMIT $start_from, $per_page";
$agency_name1=mysql_query($agency_name_limit);




// Pagination 

$agency_name_pagination = mysql_query($agency_name) or die(mysql_error());

$pagination_counts = mysql_num_rows($agency_name_pagination);


?>
<link href="assets/css/components.css" rel="stylesheet" type="text/css">
  <section >
	<div class="container">
    
      <div class="row">
        <div class="col-md-12">
            <ol class="breadcrumb">
              <li><a href="systemdashboard.php">Dashboard</a></li>
              <li class="active">Implementation Planning Dashboard </li>              
            </ol>
        </div>
     </div>
     
 	  <div class="row">
        <div class="col-sm-12">
          <h1 class="page-title">Implementation Planning (IP) Dashboard <span class="info-badge text-lowercase" onclick="info_taggle()" data-toggle = "tooltip" data-placement = "right" title="Click to view Info">i</span></h1> 
          <div class="col-md-12 info_taggle" style="display: none;">
              <div class="custom-blockquote mar_b20">
                <p class="mar0">From the Implementation and Planning Dashboard, providers can build implementation strategy maps, and update progress on each strategy. Managers, evaluators and TTA team members can view and provide feedback on each report.</p>
              </div>
          </div>                 
          <p class="col-md-12">A place to keep project related documents such as TA plans,past SPF reports,community readiness Reports.</p>
        </div>
      </div>
      
             <!-- filter form -->
            <div class="row mar_t10 filter form">
            <form method="post">
              <div class="col-md-5 col-sm-4 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">By Region</label>
						<select id="region" name="region" onchange="region_name(this.value)" class="form-control cs-skin-slide m-r-10" >
							<option value=""> Select </option>
							<option <?php if($filter_region=='R-1') { echo 'selected'; }  ?> value="R-1">R-1</option>
							<option <?php if($filter_region=='R-2') { echo 'selected'; }  ?> value="R-2">R-2</option>
							<option <?php if($filter_region=='R-3') { echo 'selected'; }  ?> value="R-3">R-3</option>
							<option <?php if($filter_region=='R-4') { echo 'selected'; }  ?> value="R-4">R-4</option>
							<option <?php if($filter_region=='R-5') { echo 'selected'; }  ?> value="R-5">R-5</option>
							<option <?php if($filter_region=='R-6') { echo 'selected'; }  ?> value="R-6">R-6</option>
						</select>                  
					</div>
              </div>
              <div class="col-md-5 col-sm-4 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">By Agency</label>
						<select name="agency" id="agency" onchange="agency_name(this.value)" class="form-control">
							<option value="">Select an Agency</option>
							<?php
							while($row1=mysql_fetch_array($agency_list)) { ?>
								<option value="<?php echo $row1['id']; ?>" <?php if($row1['id']==$filter_agency) { echo 'selected'; }?>><?php echo $row1['name']; ?></option>
							<?php }   ?>
						</select>                  </div>
              </div>
              <div class="col-md-2 col-sm-4 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">&nbsp;</label>
                      <button class="wid100 mar_t0" type ="submit">Search</button>
                  </div>
              </div>
              </form>
            </div>
              <!-- search list items -->
              
              
              
              
              
              
              <div class="panel-group ip_dash" id="accordion1">
              <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title" id="load_graphs">
                      <div class="col-sm-12">
                          <i class="fa fa-angle-down down_arrow1" aria-hidden="true"></i><a data-toggle="collapse" data-parent="#accordion1" href="#collapse3" class="form-title">Implementation Data Dashboard</a>
                      </div>
                    </h4>
                  </div>
                  <div id="collapse3" class="panel-collapse collapse">
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
                               <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="chart-container border pad10">
                                      <div class="chart has-fixed-height" id="connect_column"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="chart-container border pad10">
                                      <div class="chart has-fixed-height" id="line_bar"></div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12 mar_t50">
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
                          <i class="fa fa-angle-down down_arrow1" aria-hidden="true"></i><a data-toggle="collapse" data-parent="#accordion1" href="#collapse2" class="form-title">View, Sort and Search Implementation Items</a>
                      </div>
                    </h4>
                  </div>
                  <div id="collapse2" class="panel-collapse collapse in">
                    <div class="panel-body pad_l0 pad_r0">
                    <div class="col-md-12 pad_l0 pad_r0">
                       <!-- search list items -->
                      <div class="row">  
        			  <?php 
                      if(mysql_num_rows($agency_name1)>0){
                      while($row = mysql_fetch_array($agency_name1)){ ?>	
        			  <div class="col-md-4 col-sm-6 col-xs-12">
        				 <div class="assign-box">
                              <div class="assign-title">
                                 <small class="pull-right"><i class="text_grey fb_300">Assigned to</i> <span class="text_black fb_500 " data-toggle = "tooltip" data-placement = "top" title="renee@yahoo.org"><?php echo $row['user_updated'];?></span></small>
                                 <span class="clearfix"></span>
                                 <h5 class="text_blue fb_500" data-toggle = "tooltip" data-placement = "top" title="<?php echo $row['name'];?>"><?php echo $row['name'];?></h5>
                              </div>
                             <div class="item_info">
                                <ul>
                                  <li class="update_icon"><span>Last Update</span><span><!--18 Aug 2016--></span></li>
                                  <li class="list_icon"><span>Report on Interventions</span></li>
                                  <li class="pad_l0"> 
                                  <?php
                                  $sql_inter="SELECT * FROM `interventions` WHERE `agency_id`=".$row['id']." ORDER BY id ASC";
                                  $inter_data=mysql_query($sql_inter);
                                  $inter_count=mysql_num_rows($inter_data);
                                  
                                  ?>
                                   <span class="item_strategieslist">
                                        <ul class="strategy-box-list">
                                            <?php while($inter_row=mysql_fetch_assoc($inter_data)){ 
                                              echo '<li data-toggle = "tooltip" data-placement = "top" title="'.$inter_row["intervention_name"].' - '.$inter_row["intervention_community_name"].'"><a href="worknode_dashboard.php?intervention_id='.safe_b64encode($inter_row['id']).'" >'.$inter_row["intervention_name"].' - '.$inter_row["intervention_community_name"].'</a><a href="mpdf/mpdf60/examples/intervention_download.php?intervention_id='.safe_b64encode($inter_row['id']).'" ><i class="fa fa-file-text text_black"></a></i></li>';  
                                              /* echo '<li data-toggle = "tooltip" data-placement = "top" title="'.$inter_row["intervention_name"].' - '.$inter_row["intervention_community_name"].'"><a href="worknode_dashboard.php?intervention_id='.safe_b64encode($inter_row['id']).'" >'.$inter_row["intervention_name"].' - '.$inter_row["intervention_community_name"].'</a><a href="strategy_download.php?intervention_id='.safe_b64encode($inter_row['id']).'" ><i class="fa fa-file-text text_black"></a></i></li>';  */ 
                                            } ?>
                                        </ul>
                                    </span>
                                    <?php  ?>
                                  </li>
                                </ul>
                              </div>
                              <div class="upload-item text-center mar_tb10">
                                 <a class="upload_button pad10" href="strategy_portfolio.php?agency=<?php echo safe_b64encode($row['id']); ?>">Start or Edit Interventions</a>
                              </div>
                            </div>
                         </div>
        				 <?php }}else{echo '<span style="color: red;"> No Results Found</span>';} ?>
                      </div>
                      <!-- pagination -->
                        <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <ul class="pagination">
							<?php
                            if($pagination_counts >9){
                                $total_pages = ceil($pagination_counts / $per_page);
                            
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
                             <li><a href="implementation_planning.php?page=<?php echo $previous; ?>">&#171;</a></li>
              
							 <?php for($i=1;$i<=$total_pages;$i++){
								if($i < ($cPage + 10) && $i >= $cPage){
								 ?>
								 <li><a href="implementation_planning.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
								 <?php
								} 
							 }?>
                              
                              <li><a href="dashboard.php?page=<?php echo $next; ?>">&#187;</a></li>
                              
							 <?php }?>
                            </ul>       
                        </div>
                    </div>
                        <!-- pagination ends -->
                    </div>
                    </div>
                  </div>
                </div>
              </div>
              
              
              
              
              
              
              
              
              
              
              
              
              
              
              
              
              
            </div>
     		</section>
<?php include 'templates/footer.php'; ?>
    <script type="text/javascript" src="assets/js/plugins/visualization/echarts/echarts.js"></script>

    <script type="text/javascript" src="assets/js/core/app.js"></script>
    <script type="text/javascript" src="assets/js/charts/echarts/pies_donuts.js"></script>
    <script type="text/javascript" src="assets/js/plugins/loaders/pace.min.js"></script>
    <script type="text/javascript" src="assets/js/plugins/loaders/blockui.min.js"></script>

   <script type="text/javascript">
     $("#load_graphs").click(function(){
        $.getScript("assets/js/core/app.js");
      }); 
   </script>    
    
    
    
    <script type="text/javascript">
    $(document).ready(function(){
        $('.count').each(function () {
            $(this).prop('Counter',0).animate({
                Counter: $(this).text()
            }, {
                duration: 4000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
    });
    </script>
    <script type="text/javascript">
    $(document).ready(function(){
           
        $('#accordion .panel-collapse').on('show.bs.collapse', function () {
            $(this).siblings('.panel-heading').addClass('active');
          });
        $('#accordion .panel-collapse').on('hide.bs.collapse', function () {
            $(this).siblings('.panel-heading').removeClass('active');
          });
    });
    </script>
    
    <script type="text/javascript">
    $(document).ready(function(){
      var maxHeight = 0;
        $(".item_info .strategy-box-list").each(function(){
           if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
        });
        $(".item_info .strategy-box-list").height(maxHeight);
      });
      
    </script>
  </body>
</html>
