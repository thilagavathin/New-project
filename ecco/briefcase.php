<?php include 'templates/header.php'; 
session_start();
include_once('config.php');
$user_id=$_SESSION['adminlogin'];
$agency_id = $select_region = $page_url = $url_region = $url_agency= $agency_in = "";
$num_rec_per_page=6;
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$start_from = ($page-1) * $num_rec_per_page;

$agency_list=mysql_query("SELECT id,name FROM agency order by name ");

$agency_map_query = "SELECT DISTINCT agency_id FROM agency_map WHERE user_id=".$_SESSION['adminlogin'];
$agency_map_query1 = mysql_query($agency_map_query);
if(mysql_num_rows($agency_map_query1) > 0){
    while($row = mysql_fetch_array($agency_map_query1)){
        $agency_in .= $row["agency_id"];
        $agency_in .= ",";
    }
}
if($_SESSION['userrole'] == 1){
    $agency_map_query = "SELECT id,name FROM agency order by name ";
    $agency_map_query1 = mysql_query($agency_map_query);
    while($row = mysql_fetch_array($agency_map_query1)){
    }
    $user_base_agency = "";
}
$agency_in = rtrim($agency_in, ",");
if($_SESSION['userrole']==3 ){
    if($agency_in=='')
        $user_base_agency=" and agency_id in (0)";
    else
        $user_base_agency=" and agency_id in (".$agency_in.")";
}
else if(($_SESSION['userrole']==2 || $_SESSION['userrole']==4) && $agency_in <>''){
    $user_base_agency=" and agency_id in (".$agency_in.")";
}


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


$agency_c_q=mysql_query("SELECT DISTINCT(agency_id) agency FROM TTA_Reports_imports WHERE created>='".$report_start."' and created <='".$report_end."' ".$user_base_agency."");


if(mysql_num_rows($agency_c_q) > 0){
    while($r = mysql_fetch_array($agency_c_q)){
        $agency_id .= $r["agency"];
        $agency_id .= ",";
    }
}
$agency_id = rtrim($agency_id, ",");
if($_SESSION['userrole']!= 1){
    $agency_id = $agency_in; //new change
}

$agency = "SELECT * FROM agency WHERE id IN (".$agency_id.") LIMIT $start_from, $num_rec_per_page";
$sql = "SELECT * FROM agency WHERE id IN (".$agency_id.") ";

if($_SESSION['userrole']== 1){
    $agency = "SELECT * FROM agency LIMIT $start_from, $num_rec_per_page";
    $sql = "SELECT * FROM agency";
}

if(isset($_REQUEST["select_region"])) {
    $select_region = trim($_REQUEST["select_region"]);
    $url_region='&select_region='.$_REQUEST['select_region'];
}
if(isset($_REQUEST["agency_id"]))  {
    $agency_id1 = $_REQUEST["agency_id"];
    $url_agency='&agency_id='.$_REQUEST['agency_id'];
}
if(isset($_REQUEST["agency_id"]))  {
    $agency_post = $_REQUEST["agency_id"];
}
$page_url=$url_region.$url_agency;

if(!empty($select_region) && empty($agency_id1)){
    $agency = "SELECT * FROM agency WHERE id IN (".$agency_id.") AND region='".$select_region."' LIMIT $start_from, $num_rec_per_page";
    $sql = "SELECT * FROM agency WHERE id IN (".$agency_id.") AND region='".$select_region."'";
    if($_SESSION['userrole']== 1){
        $agency = "SELECT * FROM agency WHERE region='".$select_region."' LIMIT $start_from, $num_rec_per_page";
        $sql = "SELECT * FROM agency WHERE region='".$select_region."'";
    }
}
if(!empty($agency_id1)){
    $agency = "SELECT * FROM agency WHERE id IN (".$agency_id.") AND id='".$agency_id1."'  LIMIT $start_from, $num_rec_per_page";
    $sql = "SELECT * FROM agency WHERE id IN (".$agency_id.") AND id='".$agency_id1."'";

    if($_SESSION['userrole']== 1){
        $agency = "SELECT * FROM agency WHERE id='".$agency_id1."'  LIMIT $start_from, $num_rec_per_page";
        $sql = "SELECT * FROM agency WHERE id='".$agency_id1."'";
    }
}

$agency_query = mysql_query($agency);

?>
	<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ol class="breadcrumb">
			  <li><a href="systemdashboard.php">Dashboard</a></li>
			  <li class="active">Briefcase Dashboard </li>              
			</ol>
		</div>
	 </div>
	     	<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">Briefcase Dashboard<span class="info-badge text-lowercase" onclick="info_taggle()" data-toggle = "tooltip" data-placement = "right" title="Click to view Info">i</span></h1>
                  <div class="col-md-12 info_taggle" style="display: none;">
                      <div class="custom-blockquote mar_b20">
                        <p class="mar0">Briefcase is a place where providers can upload and store Mission Critical Completed Reports and documents, s,uch as Needs Assessment Reports, Capacity Reports, Planning Documents, and others. Items in Briefcase can be accessed by those who have access to the agency's information.</p>
                      </div>
                  </div>
                  <p class="col-md-12">Store project related documents, Such as TA plans, past SPF reports, and community readiness reports.</p>
                </div>
            </div>
             <!-- filter form -->
            <div class="row mar_t10 filter form">
			<form name="frmsearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" >
              <div class="col-sm-12 mar_b10">
                <h1 class="page-title">Sort &amp; Search Items</h1>
              </div>
              <div class="col-md-5 col-sm-4 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">By Region</label>
						<select name="select_region" id="select_region"  class="form-control cs-skin-slide m-r-10" onchange="return selectRegion();">
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
              <div class="col-md-5 col-sm-4 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">By Agency</label>
                       <select name="agency_id" id="agency_id" class=" form-control ">
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
              <div class="col-md-2 col-sm-4 col-xs-12">
                  <div class="form-group">
                      <label class="ft_17">&nbsp;</label>
                      <button class="wid100 mar_t0"  type="submit" value="SEARCH" name="SEARCH" >Search</button>
                  </div>
              </div>
			  </form>
            </div>
              <!-- search list items -->
            
				<?php
				if(mysql_num_rows($agency_query) > 0){
					$count = 0; $jk=1;
					while($row1 = mysql_fetch_array($agency_query)){
						$uploads = mysql_query("SELECT * FROM briefcase_uploads WHERE agency_id=".$row1["id"]." AND status='Y' ORDER BY id DESC") or die("Query Error");
						$count = mysql_num_rows($uploads);

						$get_agency_query = "SELECT user_id FROM agency_map WHERE agency_id = '".$row1['id']."'";
						$get_agency = mysql_query($get_agency_query); $agency_user_id = "";
						if( mysql_num_rows($get_agency) > 0){
							while($row=mysql_fetch_array($get_agency)) {
								 $agency_user_id .=$row['user_id'];
								$agency_user_id .=",";
							}
						}
						$agency_user_id = rtrim($agency_user_id, ",");
						$user_level = array("4");
						$assigned_sql=mysql_query("SELECT name,email FROM login_users WHERE username <> 'admin' AND user_level <> '" . serialize($user_level) . "' AND  user_id IN (". $agency_user_id.")");
						$assigned_user=mysql_fetch_row($assigned_sql);
						if($assigned_user[0]==''){ $assigned_user[0]='admin'; $assigned_user[1]='admin@admin.com'; }
						if($jk==1){echo '<ul class="row"><li>';}
						?>
		<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="assign-box">
		  <div class="assign-title">
			 <small class="pull-right"><i class="text_grey fb_300">Assigned to</i> <span class="text_black fb_500 " data-toggle = "tooltip" data-placement = "top" title="renee@yahoo.org">L. Renee Jones</span></small>
			 <span class="clearfix"></span>
			 <h5 class="text_blue fb_500" data-toggle = "tooltip" data-placement = "top" title="<?php echo $row1['name']; ?>"><?php echo $row1['name']; ?></h5>
		  </div>
		  <div class="item_info">
			<ul>
			  <li class="update_icon"><span>Last Update</span><span>
			  <?php
					$i = 1; $files = $update_date = "";
					if($count > 0){
						while($r = mysql_fetch_array($uploads)){
							if($i==1){
								$update_date = date('d M Y',strtotime($r["created_at"]));
							}
							$file_path = "assets/briefcase/".$r["id"]."/".$r["file_name"];
							
							
							$files .=  '<li><a href="'.$file_path.'" title="'.$r["file_name"].'" data-original-title="'.$r["file_name"].'" 
							data-container="body" data-placement="top" data-toggle="tooltip" target="_blank" class="agency_'.$row1['id'].'">
							Item'.$i++.'</a><a id="agency_'.$row1['id'].'" class="agency_r_'.$row1['id'].' remove"  
							title="Remove" onclick="return removeFile('.$r["id"].');" data-container="body" data-placement="top" 
							data-toggle="tooltip" data-original-title="Remove"><i class="fa fa-trash text_black"></i></a></li> ';
						}
					}
					echo $update_date;
					?>
												</span></li>
			  <li class="list_icon"><span>Number of uploaded items</span><span class="text_light_red"><?php echo $count; ?></span></li>
			  <li class="item_icon"><span>Uploaded items</span>
				
				<span>
					<ul>
					<?php
				$files = rtrim($files, ", ");
				echo $files;
				?>
					</ul>
				</span>
			  </li>
			</ul>
		  </div>
		  <div class="upload-item text-center mar_tb10">
			 <button data-target="#uploadbriefcaseitem"  onclick="updateItem('<?php echo $row1['id']; ?>','<?php echo $count; ?>');" data-toggle="modal" class="upload_button"><i class="fa fa-upload"></i> <span class="pad_lr20">Upload Item</span></button>
		  </div>
		</div>
		</div>
			  <?php
                $total = mysql_num_rows($agency_query);
               if($total < 3){
                if($jk==$total){echo '</li></ul>';  } 
               }else{
            	if($jk==$total){echo '</li></ul>';  }else{
            	if(($jk % 3) ==0){echo '</li></ul><ul class="row"><li>';}   
            	} 
               }
               $jk++;
               }
				}
			  
				?>
                 
            
              <!-- pagination -->
              <div class="row">
                <div class="col-md-12 col-xs-12">
                    <ul class="pagination">
					<?php
                    $rs_result = mysql_query($sql); //run the query
                    $total_records = mysql_num_rows($rs_result);  //count number of records
                    $total_pages = ceil($total_records / $num_rec_per_page);
                    $current=$_GET["page"];

                    echo "<li><a href='briefcase.php?page=1".$page_url."'>".'&#171;'."</a></li> "; // Goto 1st page
					for ($i=1; $i<=$total_pages; $i++) {
                        echo "<li";
						if($current == $i){
                            echo ' class="active"';
                        }
						echo "><a";
                        
                        echo " href='briefcase.php?page=".$i.$page_url."'>".$i."</a></li> ";
                    };
					echo "<li><a href='briefcase.php?page=$total_pages".$page_url."'>".'&#187;'."</a></li> "; // Goto last page
					?>
                    </ul>       
                </div>
            </div>
            </div>
 
 </div>
 <div class="modal fade" id="uploadbriefcaseitem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content-wrapper">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                </button>
                <div class="container-xs-height full-height">
                    <div class="row-xs-height">
                        <div class="modal-body col-xs-height col-middle">
                            <h5 class="text-primary m-b-20">Upload Item</h5>
                            <div id="loadingimg" class="loader" style="display: none;"></div>
                            <form id="myform" class="form-horizontal ga-form" role="form" autocomplete="off" method="POST" action="briefcase_upload.php" enctype="multipart/form-data">
                                <div class="well">
                                    <div class="clearfix">
                                        <div class="pull-left"><input type="file" name="exampleInputFile" id="exampleInputFile"></div>
                                        <div class="pull-right"><input class="btn btn-lg btn-primary" type="submit" name="sub" value="UPLOAD" id="PbtnSubmit"></div>
                                    </div>

                                </div>
                                <div class="help-text m-t-5 m-b-20">
                                    <span class="error"></span>
                                    <b>Note:</b> <span class="m-t-20">Upload doc, docx, pdf, xls or xlsx file only.</span>
                                </div>
                                <input type="hidden" id="up_agency" name="up_agency" value="" >
                                <input type="hidden" id="file_count" name="file_count" value="" >
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
 <?php include 'templates/footer.php'; ?>
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
	function updateItem(id, count) {
        $("#up_agency").val(id);
        $("#file_count").val(count);
    }
	function selectRegion() {
        var select_region = $("#select_region").val();
        var form = new FormData();
        form.append('region', select_region);
        page = "ajax_briefcase_get_agency.php";
        $.ajax({
            url: page, // Url to which the request is send
            type: "POST",             // Type of request to be send, called as method
            data:form, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,        // To send DOMDocument or non processed data file it is set to false
            success: function(data)   // A function to be called if request succeeds
            {
                $('#agency_id').html('');
                $('#agency_id').html(data);
            }
        });
        return false;
    }
	
    function removeFile(id) {
        var r = confirm("Are you sure want to delete this file?");
        if (r == false){
            return false;
        }
        var form = new FormData();
        form.append('id', id);
        page="ajax_briefcase_file_delete.php";
        $.ajax({
            url: page, // Url to which the request is send
            type: "POST",             // Type of request to be send, called as method
            data:form, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,        // To send DOMDocument or non processed data file it is set to false
            success: function(data)   // A function to be called if request succeeds
            {
                $success = $.trim(data);
                if( $success == "success"){
                    alert("File Removed Successfully");
                    location.reload();
                }else{
                }
            }
        });
        return false;
    }

    </script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#PbtnSubmit').click(function(event) {
            var ext = $('#exampleInputFile').val().split('.').pop().toLowerCase();
            var file_count = $("#file_count").val();
            var agency = $("#up_agency").val();

            if(file_count == 6){
                var r = confirm("Maximum 6 no. of files allowed to upload. So, You want to remove files");
                if (r == true) {
                    $(".agency_r_"+agency).show();
                    $('#uploadbriefcaseitem').modal('hide');
                    return false;
                } else {
                    $(".error").html("You can not upload files");
                    return false;
                }
                return false;
            }
            if(ext == ""){
                $(".error").html("Please Upload File!");
                return false;
            }
            if($.inArray(ext, ['pdf','PDF','xls','xlsx', 'doc', 'docx']) == -1) {
                $(".error").html(ext + " is not allowed to upload!");
                return false;
            }
        });
        
    });
    $(document).ready(function(){
      var maxHeight = 0;
        $(".item_info ul li ul").each(function(){
           if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
        });
        $(".item_info ul li ul").height(maxHeight);
    });
</script>
  </body>
</html>
