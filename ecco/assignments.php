<?php include_once('templates/header.php');
if($_SESSION['userrole']==3 || $_SESSION['userrole']==2) {
    header('Location:dashboard.php'); die;
}
$today = date("l, F j, Y, g:i A");
$agency_in=''; $user_base_agency='';
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
$tta_comment=mysql_query("SELECT COUNT(id) FROM tta_regarding_notes where view_status='N'".$user_base_agency);
$comment_tta=mysql_fetch_row($tta_comment);
$community_comment=mysql_query("SELECT COUNT(id) FROM community_comments where view_status='N'");
$community_count=mysql_fetch_row($community_comment);

/*
 * Chat count
 */
$sender_id = $_SESSION['adminlogin'];
$group_chat_count = $chats_count = $chat_count1 = 0;
$group_chat_count = mysql_query("SELECT COUNT(*) FROM group_chat_views WHERE receiver_id=".$sender_id." AND group_id<>'0' AND view_status='N'") or die("Query Error");
if(mysql_num_rows($group_chat_count) > 0){
    while($g_row = mysql_fetch_array($group_chat_count)){
        $g_count = $g_row[0];
    }
}else{
    $g_count = 0;
}
$reg = mysql_query('SELECT COUNT(DISTINCT sender_user_id) FROM chats WHERE receiver_user_id="' . $_SESSION["adminlogin"] . '" AND view_status="N"') or die("Query Error");
$chat_count = 0;
if(mysql_num_rows($reg) > 0){
    while ($reg_row = mysql_fetch_array($reg)) {
        $chat_count = $reg_row["0"];
    }
}

$chats_count = $g_count + $chat_count;
?>
     		<section>
	     		<div class="container">
				
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li class="active">Assignments </li>              
						</ol>
					</div>
				 </div>
				
				<?php
				$sort_type=isset($_GET['sort'])? $_GET['sort']:'';
                $sql_agency = "SELECT name,id FROM agency";
				$result_agency = mysql_query($sql_agency);
				$num_rows_agency = mysql_num_rows($result_agency);
				?>
	     			<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">Assignments</h1>
                </div>
            </div>
            <div class="row grey_bg notify_actions">
              <div class="vcenter">
               <div class="col-md-4 col-sm-5 col-xs-12 pad0">
                    <label class="mar0 checkbox_normal" for="all">
                      <input class="chkNumber" type="checkbox" onclick="filterassign(this.value);" name="sort_a"  value="all" id="all" <?php if($_REQUEST['sort']!='assigned' && $_REQUEST['sort']!='notassigned' ){ echo 'checked';}  ?> >
                      <span class="custom-icon checkbox-icon"></span>All
                    </label>
                    <label class="mar_b0 mar_l10 checkbox_normal" for="Assigned">
                      <input class="chkNumber" type="checkbox" onclick="filterassign(this.value);" name="sort_a" value="assigned" id="Assigned" <?php if($_REQUEST['sort']=='assigned'){ echo 'checked';}  ?>>
                      <span class="custom-icon checkbox-icon"></span>Assigned
                    </label>
                    <label class="mar_b0 mar_l10 checkbox_normal" for="Not Assigned">
                      <input class="chkNumber" type="checkbox" onclick="filterassign(this.value);" name="sort_a" value="notassigned" id="Not Assigned" <?php if($_REQUEST['sort']=='notassigned'){ echo 'checked';}  ?>>
                      <span class="custom-icon checkbox-icon"></span>Not Assigned
                    </label>
               </div>
               <div class="col-md-8 col-sm-7 col-xs-12 pad0 notify_actions_buttons">
                  <div class=" mar0">
                      <label class="mar0"><span class="text_light_red"><?php echo $num_rows_agency; ?></span> Selected </label>
                      <button type="submit" class="button mar_l10" onclick="splassign();" data-toggle="modal" data-target="#createNewLevel" aria-expanded="true">Special User Assign</button>
                      <button type="submit" class="button mar_l10" onclick="splassign1();" data-toggle="modal" data-target="#createNewLevel1" aria-expanded="true">Middle Admin Assign</button>
                  </div>
               </div>
              </div>
            </div>
            <div class="assignment_list" id="agencies-assigned-list" aria-expanded="true">
            <ul>
				<li class="row">
			<?php
				while($row = mysql_fetch_array($result_agency)) {

                $sql_map=mysql_query("SELECT user_id FROM agency_map WHERE agency_id=".$row['id']);
                $row_map_count=mysql_num_rows($sql_map);
                $map_list='';
                if($row_map_count >0)
                {
                    while($ls= mysql_fetch_array($sql_map) ) { $map_list.=$ls['user_id'].','; }
                }
                    $map_list=rtrim($map_list,',');
                    $sql_tta=mysql_query("SELECT assignedUser FROM TTA_Forms WHERE agency_id=".$row['id']);
                    $row_tta_count=mysql_num_rows($sql_tta);
                    $u_list='';
                    if($row_tta_count >0)
                    {
                        while($ls= mysql_fetch_array($sql_tta) ) { $u_list.="'".$ls['assignedUser']."',"; }
                    }
                    $u_list=rtrim($u_list,',');
                    $u_list=($u_list=='')? 'null':$u_list;
                    $map_list=($map_list=='')? 0:$map_list;
				$sql_assign="SELECT user_id,user_level,user_image,name,username FROM login_users where username in (".$u_list.") OR user_id in (".$map_list.") OR user_level LIKE '%\"1\"%' ";
                $result_assign = mysql_query($sql_assign);
                $num_rows_assign = mysql_num_rows($result_assign);
               if($num_rows_assign>0 && $sort_type=='assigned' || $sort_type=='') {
                ?>
                
				<div class="col-md-4 col-sm-6 col-xs-12">
                <div class="assign-box">
                          <div class="assign-title">
                             <h5 class="text_blue fb_500" data-toggle = "tooltip" data-placement = "top"><?php echo $row['name'];?></h5>
                             <small class="text_light_red"><i>Assign to</i></small>
                          </div>
                          <div class="assign_user_list">
                            <ul>
                            <li >
   							<?php
							$user_list='';
							while ($ls = mysql_fetch_array($result_assign)) {
							$user_list .= $ls['user_id'] . ',';
							if (trim($ls['user_image']) <> '') {
							$user_img = @unserialize($ls['user_image']);
							$img_val = "assets/profile/" . $user_img[0];
							} else $img_val = "images/chat-noimage.png";
							?>
                              <figure data-userid="<?php echo $ls['user_id'] ?>" data-assignuser="<?php echo $ls['username'] ?>" data-agencyid="<?php echo $row['id'] ?>">
                                  <span><img src="<?php echo $img_val; ?>" alt="user profile icon" width="50"><i class="fa fa-check" aria-hidden="true"></i></span>
                                  <figcaption><a href="#"><?php echo $ls['name']; ?></a></figcaption>
                              </figure>
                              
							<?php } ?>
                            </li> 
                            </ul>
							</div>
								         
                          <div class="assign-buttons text-center">
						  <?php if($num_rows_assign<>0) { ?>
                             <button class="button green_bg" onclick="reassign('<?php echo $row['id']; ?>','<?php echo $row['name']; ?>','<?php echo $user_list; ?>');" data-target="#Reassign" data-toggle="modal">Assign Now</button>
							 <?php } else { ?>
							 <button class="button green_bg" onclick="reassign('<?php echo $row['id']; ?>','<?php echo $row['name']; ?>','<?php echo $user_list; ?>');" data-target="#Reassign" data-toggle="modal">Add</button>
							 <?php } ?>
                             <button class="button red_bg mar_l10" onclick="remove_record()" data-target="#remove" data-toggle="modal">Remove</button>
                          </div>
                          </div>
                          </div>
						  
						  <?php } 
						  
						  elseif($sort_type=='notassigned' && $num_rows_assign==0)
						  { ?>
                          <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="assign-box">
                                  <div class="assign-title">
                                     <h5 class="text_blue fb_500" data-toggle = "tooltip" data-placement = "top"><?php echo $row['name'];?></h5>
                                     <small class="text_light_red"><i>Assign to</i></small>
                                  </div>
                            </div>
                         </div>   
					  
                       <?php }
						} ?>
                        </li>
                        </ul>
					</div>
                      </div>
                 		</section>
			
<!-- Modal - Assign -->
<div class="modal comment-box right fade" id="createNewLevel" tabindex="-1" role="dialog" aria-hidden="true">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="pg-close"></i>
    </button>
    <div class="modal-dialog bg-white">
        <div class="modal-content">
            <div class="container-xs-height full-height">
                <div class="modal-header p-n">
                    <div class="text-center bg-primary modal-title">
                        Special User Assign Agency
                    </div>
                </div>
                <!-- START ROW -->
                <div id="htmlcomment_spl"> </div>
                <!-- END ROW -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal comment-box right fade" id="createNewLevel1" tabindex="-1" role="dialog" aria-hidden="true">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="pg-close"></i>
    </button>
    <div class="modal-dialog bg-white">
        <div class="modal-content">
            <div class="container-xs-height full-height">
                <div class="modal-header p-n">
                    <div class="text-center bg-primary modal-title">
                        Middle Admin Assign Agency
                    </div>
                </div>
                <!-- START ROW -->
                <div id="htmlcomment_spl1"> </div>
                <!-- END ROW -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<!-- Modal - Re Assign -->
<div class="modal comment-box right fade" id="Reassign" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;
        <i class="pg-close"></i>
    </button>
   <div class="modal-body no-shadow">
				<div class="row" id="comment-form">
					<div class="col-xs-12 form">
						<h3 class="modal-title text_blue">Assign Agency
                    </div>
                </div>
                <div id="htmlcomment"></div>
                <input type="hidden" id="com_agency" name="com_agency" value="" >
                <input type="hidden" id="com_agencyname" name="com_agencyname" value="" >
                <input type="hidden" id="com_users" name="com_users" value="" >
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
</div>


   

<?php include_once('templates/footer.php'); ?>

<script type="text/javascript">
    $(document).ready(function(){
      $(".assign_user_list ul li figure").click(function(){
        $(this).toggleClass("selected");
     });  
        
      var maxHeight = 0;
        $(".assign_user_list ul").each(function(){
           if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
        });
        $(".assign_user_list ul").height(maxHeight);
      });
    </script>			
<script>
    function reassign(agency,agencyname,users)
    {
        $('input[name="com_agency"]').val(agency);
        $('input[name="com_agencyname"]').val(agencyname);
        $('input[name="com_users"]').val(users);
        $("#htmlcomment").html('');
        var formData = {agency:agency,agencyname:agencyname,users:users};
        $.ajax({
            url : "assign_agency_form.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                $("#htmlcomment").html(data);
            }
        });
    }
    function reassign_admin(agency,agencyname,users)
    {
        $('input[name="com_agency"]').val(agency);
        $('input[name="com_agencyname"]').val(agencyname);
        $('input[name="com_users"]').val(users);
        $("#htmlcomment").html('');
        var formData = {agency:agency,agencyname:agencyname,users:users};
        $.ajax({
            url : "assign_special_admin_agency_form.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                $("#htmlcomment").html(data);
            }
        });
    }
	function remove_record(){
		var splashArray = new Array();
		var splashArray1 = new Array();
        var splashArray2= new Array();
		$('.selected').each(function(){
		   splashArray.push($(this).data('userid'));
		   splashArray1.push($(this).data('agencyid'));
           splashArray2.push($(this).data('assignuser'));
		});

		if (splashArray.length === 0) {
			alert("Please select any Users");
		}else{
			var answer = confirm('Are you sure you want to remove ?');
			if (answer) {
				$.ajax({
					url: "remove_agency_user.php",
					type: "POST",
					data: {splashArray: splashArray, splashArray1: splashArray1,splashArray2: splashArray2},
					success: function (data) {
						alert("User Removed Successfully");
						window.location = "assignments.php";
					}
				});
			}
		}
	}
    function addassign_normal()
    {
        var assign_user=$( "#assign_user" ).val();
        var agency= $('#com_agency').val();
        var agencyname=$('#com_agencyname').val();
        var users=$('#com_users').val();

        var formData = {agency:agency,assign_user:assign_user,agencyname:agencyname,users:users};
        $.ajax({
            url : "add_assign_normal.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                if(data=='success'){
                            alert('Agency has assigned successfully '); window.location = "assignments.php";
                } 
                else { 
                    alert('Due to internet problem not reachable database ,Try again');  
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    function splassign()
    {
        var formData = {};
        $.ajax({
            url : "splassign_agency_form.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                $("#htmlcomment_spl").html(data);
            }
        });
    }
    function splassign1()
    {
        var formData = {};
        $.ajax({
            url : "spladminassign_agency_form.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                $("#htmlcomment_spl1").html(data);
            }
        });
    }

    function check_user_role(name)
    {
        $.ajax({url: "spl_select_agency.php?id="+name, success: function(result){
            $("#demo").html(result);
        }});
    }
    function addassign_spl_admin() {
        var spl_user=$( "#spl_user" ).val();
        var agency_id= $('#agency_id').val();
        if(agency_id){
            var formData = {agency_id:agency_id,spl_user:spl_user};
            $.ajax({
                url : "add_assign_spl_admin.php",
                type: "POST",
                data : formData,
                success: function(data, textStatus, jqXHR)
                {
                    if(data=='success'){
                            alert('Agency has assigned successfully '); window.location = "assignments.php";
                    } 
                    else { 
                        alert('Due to internet problem not reachable database ,Try again');  
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
    
                }
            });
        }else{
            alert('Please choose agency!');
            $('#agency_id').focus();
            }
    }
    function addassign_spl()
    {
        var spl_user=$( "#spl_user" ).val();
        var agency_id= $('#agency_id').val();
        if(agency_id){
            var formData = {agency_id:agency_id,spl_user:spl_user};
            $.ajax({
                url : "add_assign_spl.php",
                type: "POST",
                data : formData,
                success: function(data, textStatus, jqXHR)
                {
                    if(data=='success'){
                        alert('Agency has assigned successfully '); window.location = "assignments.php";
                    } 
                    else { 
                        alert('Due to internet problem not reachable database ,Try again');  
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
    
                }
            });   
        }else{
            alert('Please choose agency!');
            $('#agency_id').focus();
            }
        
    }
    function filterassign(fav)
    {
        $.each($(".chkNumber:checked"), function(){
            if(fav=='all') window.location = "assignments.php";
            else if(fav=='assigned') window.location = "assignments.php?sort=assigned";
            else if(fav=='notassigned') window.location = "assignments.php?sort=notassigned";
        });
    }

</script>

  </body>
</html>
