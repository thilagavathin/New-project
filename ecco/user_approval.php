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
     		<section >
	     		<div class="container">
				
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li class="active">Notifications </li>              
						</ol>
					</div>
				 </div>
				
				<?php
                $sql_users = "SELECT user_id,name,email,phone,region,AgencyName,administrator_notes,timestamp FROM login_users WHERE approved='NO'";
				$result_users = mysql_query($sql_users);
				$num_rows_users = mysql_num_rows($result_users);
                ?>
				<form id="frm" action="update_approve.php" method="post">
	     			<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">Notifications</h1>
                </div>
            </div>
            <div class="row grey_bg notify_actions">
              <div class="vcenter">
               <div class="col-md-1 col-sm-1 col-xs-12 pad0">
                  <label class="mar0 checkbox_normal" for="example-select-all">
                    <input type="checkbox" id="example-select-all" value="option1">
                    <span class="custom-icon checkbox-icon"></span>All
                  </label>
               </div>
               <span class="col-xs-5"><?php  if(isset($_GET['msg'])) { echo ($_GET['msg']=='success')? 'User has approved Successfully':''; } ?></span>
               
               <div class="col-md-11 col-sm-11 col-xs-12 pad0 text-right">
                  <div class=" mar0 notify_buttons">
                      <a href="assignments.php" class="button"><i class="fa fa-file"></i>Show Assignments</a>
                      <a href="users.php" class="button"><i class="fa fa-user"></i>Approved Users</a>
                      <button type="button" onclick="delete_user();" class="button"><i class="fa fa-trash"></i>Delete</button>
                      <button type="submit" class="button"><i class="fa fa-thumbs-o-up"></i>Approve</button>
                  </div>
               </div>
              </div>
            </div>
            <div class="site-table table-responsive"  style="overflow-x: visible;">
               <table id="notification_table" class="table notification_table">
                 <thead>
                   <tr>
                     <th>#</th>
                     <th>Full Name</th>
                     <th>Number</th>
                     <th>Email ID </th>
                     <th>Region</th>
                     <th>Selected Agency</th>
                     <th>Notes</th>
                     <th>Time</th>
                   </tr>
                 </thead>
                 <tbody>
				 <?php while($row = mysql_fetch_array($result_users)) {
                       $datetime=explode(' ',$row['timestamp']);
				 ?>
                    <tr>
                        <td><label class="mar0 checkbox_normal" for="<?php echo $row['user_id']; ?>">
                            <input type="checkbox" name="case[]" value="<?php echo $row['user_id']; ?>" id="<?php echo $row['user_id']; ?>">
                            <span class="custom-icon checkbox-icon"></span>
                          </label>
                        </td>
                        <td><span><?php echo $row['name']; ?></span></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['region']; ?></td>
                        <td><?php echo $row['AgencyName']; ?></td>
                        <td><?php echo $row['administrator_notes']; ?></td>
                        <td><?php echo $datetime[1]; ?> <?php echo $datetime[0]; ?></td>
                    </tr>
					<?php
					}
                    ?>
                 </tbody>
               </table> 
                </div>
				</form>
          </div>
     		</section>
<?php include_once('templates/footer.php'); ?>
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
      $(document).ready(function() {

          $('#notification_table').DataTable( {
              "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0 ] } ],
              "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
          });
      } );
    </script>
    <script>
    // Handle click on "Select all" control
    $('#example-select-all').on('click', function(){
        $('input[name="case[]"]').prop('checked', $(this).prop("checked"));   });
    function delete_user()
    {
        var splashArray = new Array();
        $('input[name="case[]"]:checked').each(function() {
            splashArray.push($(this).val());
        });
        if (splashArray.length === 0) {
            alert("Please select Users");
        }
        else{
            var answer = confirm('Are you sure you want to remove ?');
            if (answer) {
                $.ajax({
                    url: "remove_unapprove_user.php",
                    type: "POST",
                    data: {splashArray: splashArray},
                    success: function (data) {
                        if(data=='failure') alert('Due to internet problem not reachable database ,Try again');
                       else { alert("User Removed Successfully"); window.location = "user_approval.php"; }
                    }
                });
            }
        }
    }

</script>
    
  </body>
</html>
