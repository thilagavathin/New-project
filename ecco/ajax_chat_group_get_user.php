<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}


$group_id = trim( $_POST["group_id"] );
$current_user = $_SESSION['adminlogin'];
$member_id = $html = "";

$user_admin = 0;
$q = mysql_query("SELECT user_level from login_users WHERE user_id=".$current_user) or die("Query Error");
if( mysql_num_rows($q) > 0){
    while($r = mysql_fetch_array($q)){
        $admin = unserialize($r["user_level"]);
        $user_admin =  $admin[0];
    }
}

$query = mysql_query("SELECT * FROM group_members  WHERE group_id=".$group_id) or die("Query Error");
$count = mysql_num_rows($query);
if( $user_admin != 1 && $count > 7){
    echo "You have no permission to add user to this group."; die;
}

if( mysql_num_rows($query) > 0 ) {
    while ($row = mysql_fetch_array($query)) {
        $admin_id = $row["created_user_id"];
        $member_id .= $row["member_user_id"];
        $member_id .= ",";
    }
}
$member_id = rtrim($member_id, ",");
$sql = mysql_query("SELECT * FROM login_users WHERE user_id NOT IN (".$member_id.") ORDER BY name ASC" ) or die("Query Error");

if( mysql_num_rows($sql) > 0 ){
    $html .= '
	<link href="assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen" />
	<script type="text/javascript" src="assets/plugins/bootstrap-select2/select2.min.js"></script>
        <form name="AddForm" method="post" action="#">
        <input type="hidden" name="add_group_id" id="add_group_id" value="'.$group_id.'">
        <div class="form-group">
		<select id="addmember" name="add_group_members[]" class="form-control member_select" data-init-plugin="select2" >
          <option value="">Please Select</option>
          ';
		while($r = mysql_fetch_array($sql)){
			$html .= '
				<option value="'.$r["user_id"].'">'.$r["name"].'</option>
			';
		}
		$html .= '
  	</select></div>
  
  <div class="form-group">
        <button id="AddUser" class="button" onclick="return addUserToG();">Add</button>
        <button class="button cancel_btn" onclick="slideup_userlist_close(); return false">Cancel</button>
        </div>
        </form>
        ';
}
echo $html; die;
