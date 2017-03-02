<?php
ob_start(); session_start(); include_once('config.php');
ini_set('display_errors', 'On');
error_reporting( error_reporting() & ~E_NOTICE );
date_default_timezone_set('America/New_York');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$group_chat_ids=array();
$private_chat_ids=array();

$sender_id = $_SESSION['adminlogin'];
$o_group_id = $o_receiver_id = 0;
$group_details ='';
$recent_user_id = $recent_group_id  =""; $g_id = 0;

$get_group = mysql_query("SELECT DISTINCT group_id FROM group_members WHERE member_user_id='".$sender_id."'") or die("error");
if(mysql_num_rows($get_group) > 0){
    while($gr = mysql_fetch_array($get_group)){
        $g_id .= $gr["group_id"];
        $g_id .= ",";
    }
}
$g_id = rtrim($g_id, ",");
$online = mysql_query("SELECT * FROM chat_active WHERE receiver_id='".$sender_id."' AND status='Y'") or die("Query Online Error");
if(mysql_num_rows($online) > 0){
    while($online_row = mysql_fetch_array($online)){
            $o_group_id = $online_row["group_id"];
            $o_receiver_id = $online_row["sender_id"];
    }
}
if($o_group_id == 0){
    $on_group_id = 0;
    $online1 = mysql_query("SELECT * FROM chat_active WHERE group_id <> 0 AND status='Y'") or die("Query Online Error");
    if(mysql_num_rows($online1) > 0){
        while($online_row = mysql_fetch_array($online1)){
            $on_group_id = $online_row["group_id"];
            $g_created_time = $online_row["created_time"];
        }
    }
}
if($on_group_id != 0){
    $og_id = "a#g".$on_group_id; $g_created_time = "";
    $date = date("Y-m-d");
    $q = mysql_query("SELECT * FROM chats WHERE group_id<>0 AND created_at='".$date."' ORDER BY id DESC") or die("Query Error");
    $time = date("H:i:s");
    if(mysql_num_rows($q) > 0){
        while($blink_row = mysql_fetch_array($q)){
            $created_time = $blink_row["created_time"];
            $created_date = $blink_row["created_at"];
        }
    }
    $then = $created_date." ".$created_time;
    $datetime1 = new DateTime($then);
    $datetime2 = new DateTime();
    $interval = $datetime1->diff($datetime2);
    $sec =  $interval->format('%s');
    if($sec >= 1 && $sec <= 5 ){
        $g_blink = 1;
    }
}

if($o_receiver_id != 0){
    $or_id = "a#r".$o_receiver_id; $created_time = "";
    $date = date("Y-m-d");
    $q = mysql_query("SELECT * FROM chats WHERE sender_user_id='".$o_receiver_id."' AND receiver_user_id='".$sender_id."' AND view_status='Y' AND created_at='".$date."' ORDER BY id DESC") or die("Query Error");
    $time = date("H:i:s");
    if(mysql_num_rows($q) > 0){
        while($blink_row = mysql_fetch_array($q)){
            $created_time = $blink_row["created_time"];
            $created_date = $blink_row["created_at"];
        }
    }
    $then = $created_date." ".$created_time;
    $datetime1 = new DateTime($then);
    $datetime2 = new DateTime();
    $interval = $datetime1->diff($datetime2);
    $sec =  $interval->format('%s');
    if($sec >= 1 && $sec <= 5 ){
        $r_blink = 1;
    }
}
$sender_id = trim($_POST["sender_id"]);
$receiver_id = trim($_POST["receiver_id"]);
$group_id = trim($_POST["group_id"]);

$agency_in='';
$agency_list=mysql_query("SELECT id,name FROM agency order by name ");

$sender_id = $_SESSION['adminlogin'];
$receiver_id = 0;


$r_chat = $r_date = ""; $g_chat = $g_date = $recent_user_id2 = "";
$noti = $noti_sender_user_id = $noti_sender_user_id1="";
$not_query = "SELECT * FROM chats WHERE view_status = 'N' ORDER BY id DESC";
$not_query1 = mysql_query($not_query) or die("Query error");
$not_group_id = "";
if(mysql_num_rows($not_query1) > 0){
    while($not_row = mysql_fetch_array($not_query1)){
        $HiddenProducts = explode(',',$noti_sender_user_id);
        if($not_row["group_id"] == 0 && $not_row["receiver_user_id"] == $sender_id && (!in_array($not_row["sender_user_id"], $HiddenProducts)) ){
            $noti_sender_user_id .= $not_row["sender_user_id"];
            $noti_sender_user_id .= ",";
            $reg_noti = mysql_query('SELECT COUNT(*) FROM chats WHERE receiver_user_id="' . $_SESSION["adminlogin"] . '" AND view_status="N" AND sender_user_id="' . $not_row["sender_user_id"] . '"') or die("Query n Error");
            $chat_noti_count = 0;
            while ($reg_row_noti = mysql_fetch_array($reg_noti)) {
                $chat_noti_count = $reg_row_noti["0"];
            }

            $noti = getUserImage($not_row["sender_user_id"]);
            $date1 = date_create($not_row["created_at"]);
            $g_date1 =  date_format($date1, 'M d,Y');
            $private_chat_ids[]=$not_row["sender_user_id"];
			
			if($noti["user_name"]){$Name=$noti["user_name"];}else{$Name='--';}
			if($noti["position"]){$Position=$noti["position"];}else{$Position='--';}
			if($noti["agency"]){$Agencyname=$noti["agency"];}else{$Agencyname='--';}
			if($noti["region"]){$Region=$noti["region"];}else{$Region='--';}
			if($noti["email"]){$Email=$noti["email"];}else{$Email='--';}
			
            $group_details .= '<li class="1"><a href="?r='.$not_row["sender_user_id"].'" id="r'.$not_row["sender_user_id"].'">
            <span><img src="'.$noti['user_image'].'" alt="user image"></span>
             <span>'.$noti["user_name"].'
             <i class="fa fa-address-card hidden-xs">
                <div class="chatuser_sortinfo">
                  <div class="row mar0">
                    
                    <div class="col-md-12 col-sm-12 mar_t10">
                      <p><span>'.$Name.'</span></p>
                      <p><span>'.$Position.'</span></p>
                      <p><span>'.$Agencyname.'</span></p>
                      <p><span>'.$Region.'</span></p>
                      <p><span>'.$Email.'</span></p>
                    </div>';
					if($noti['user_logo']<>'') {
					$group_details .= '<div class="col-md-12 col-sm-12 text-center pad_tb10 mar_t10 grey_bg">
                       <img src="'.$noti['user_logo'].'" alt="">
                    </div>';
					}
                  $group_details .= '</div>
              </div>

              </i>
             
             </span>';
            if($r_blink != 0 && $not_row["sender_user_id"] == $o_receiver_id){
                $new_class = "blink_me hide";
                $chat_noti_count = "";
            }else{
                $new_class = "blink_me";
            }
            $group_details .= ' <span class="'.$new_class.'">'.$chat_noti_count.$r_blink.'</span>';
            $group_details .= '</p><p>'.$not_row["comments"].'</p>
                <p class="u-date">'.$g_date1.'</p>';
            $group_details .= '</a></li>';
        }
        $HiddenProducts1 = explode(',',$not_group_id);
        if($not_row["group_id"] != 0 && (!in_array($not_row["group_id"], $HiddenProducts1))){
            $not_group_id .=$not_row["group_id"];
            $not_group_id .= ",";
            $all_group = mysql_query("SELECT * FROM group_chats WHERE id=".$not_row['group_id']." AND id in (".$g_id.")") or die("Query 123 Error");
            if(mysql_num_rows($all_group) > 0){
                while($row = mysql_fetch_array($all_group)){
                    $current_group_id = $row["id"];
                    $group_chat_count = mysql_query("SELECT COUNT(*) FROM group_chat_views WHERE receiver_id=".$sender_id." AND group_id=".$current_group_id." AND view_status='N'") or die("Query 456 Error");
                    $group_chat_msg = mysql_query("SELECT * FROM chats WHERE receiver_user_id='0' AND group_id=".$current_group_id." AND view_status='N' ORDER BY id ASC") or die("Query 789 Error");
                    if(mysql_num_rows($group_chat_msg) > 0){
                        while($w = mysql_fetch_array($group_chat_msg)){
                            $g_chat = $w["comments"];
                            if( $w["upload_file"] != "" ){
                                $g_chat = "New File is Uploaded.".$w["upload_file"];
                            }
                            $date = date_create($w["created_at"]);
                            $g_date =  date_format($date, 'M d,Y');
                        }
                    }else{
                        $g_chat = $g_date = "";
                    }
                    if(mysql_num_rows($group_chat_count) > 0){
                        while($g_row = mysql_fetch_array($group_chat_count)){
                            $g_count = $g_row[0];
                        }
                    }else{
                        $g_count = 0;
                    }

                    if( !empty($row["profile_picture"]) ){
                        $img_url = 'assets/groups/'.$row["id"].'/'.$row["profile_picture"];
                    }else{
                        $img_url = "assets/img/group_icon.png";
                    }
                    $group_chat_ids[]=$row["id"];
                    $group_details .= '<li class="2"><a href="?g='.$row["id"].'" id="g'.$row["id"].'">
                    <span><img src="'.$img_url.'" alt="user image"></span>
                    <span>'.$row["name"].'</span>';
                    if($g_count != 0 || $g_blink != 0){
                        if($g_blink != 0){
                            $new_group_class = "blink_me hide";
                            $g_count = "";
                        }else{
                            $new_group_class = "blink_me";
                        }
                        $group_details .= ' <span class="'.$new_group_class.'">'.$g_count.$g_blink.'</span>';
                        $group_details .= '</p><p>'.$g_chat.'</p>
                <p class="u-date">'.$g_date.'</p>';
                    }

                    $group_details .= '</a></li>';
                }
            }
        }
    }
}
if(!empty($noti_sender_user_id)){
    $recent_user_id = $noti_sender_user_id.$recent_user_id;
}
$recent_query = mysql_query("SELECT DISTINCT group_id, receiver_user_id, sender_user_id FROM chats WHERE sender_user_id='".$sender_id."' OR receiver_user_id='".$sender_id."'") or die("Check your internet connection");
if(mysql_num_rows($recent_query) > 0){
    while($recent_row = mysql_fetch_array($recent_query)){
        if( $recent_row["receiver_user_id"] != 0 && $recent_row["receiver_user_id"] != $_SESSION["adminlogin"] && $recent_row["group_id"] == 0 && $recent_row["sender_user_id"] == $_SESSION["adminlogin"]) {

            $u_query = "SELECT user_id, user_level, username, name, user_image, user_logo, region  FROM login_users WHERE approved='YES' AND user_id = " . $recent_row['receiver_user_id'];
            if(!empty($recent_user_id)){
                $recent_user_id1 = rtrim($recent_user_id, ",");
                $u_query = "SELECT user_id, user_level, username, name, user_image, user_logo, region  FROM login_users WHERE approved='YES' AND user_id = " . $recent_row['receiver_user_id'] . " AND user_id NOT IN (".$recent_user_id1.")";
            }

            $users_query = mysql_query($u_query);
            if (mysql_num_rows($users_query) > 0) {
                while ($row = mysql_fetch_array($users_query)) {
                    if(!empty($recent_user_id)){
                        $comma = substr($recent_user_id, -1);
                        if($comma != ","){
                            $recent_user_id .= ",";
                        }
                    }
                    $recent_user_id .= $row["user_id"];
                    $recent_user_id .= ",";
                    $recent_user_id2 .= $row["user_id"];
                    $recent_user_id2 .= ",";

                }
            }
        }

        $recent_user_id = rtrim($recent_user_id, ",");
        $recent_users = explode(',',$recent_user_id);


        if( $recent_row["sender_user_id"] != 0 && $recent_row["sender_user_id"] != $_SESSION["adminlogin"] && (!in_array($recent_row["sender_user_id"], $recent_users))) {

            $u1_query = "SELECT user_id, user_level, username, name, user_image, user_logo, region  FROM login_users WHERE approved='YES' AND user_id = " . $recent_row['sender_user_id'];
            if(!empty($recent_user_id)){
                $u1_query = "SELECT user_id, user_level, username, name, user_image, user_logo, region  FROM login_users WHERE approved='YES' AND user_id = " . $recent_row['sender_user_id'] . " AND user_id NOT IN (".$recent_user_id.")";
            }

            $users_query1 = mysql_query($u1_query);
            if (mysql_num_rows($users_query1) > 0) {
                while ($row = mysql_fetch_array($users_query1)) {
                    if(!empty($recent_user_id)){
                        $comma = substr($recent_user_id, -1);
                        if($comma != ","){
                            $recent_user_id .= ",";
                        }
                    }
                    $recent_user_id .= $row["user_id"];
                    $recent_user_id .= ",";
                    $recent_user_id2 .= $row["user_id"];
                    $recent_user_id2 .= ",";
                }
            }
        }

        if( $recent_row["group_id"] != 0 ) {
            $recent_group_id .= $recent_row["group_id"];
            $recent_group_id .= ",";

            $all_group = mysql_query("SELECT * FROM group_chats WHERE id=".$recent_row['group_id']." and id in (".$g_id.")") or die("Query 123 Error");

            if(!empty($not_group_id)){
                $not_group_id = rtrim($not_group_id, ",");
                $all_group = mysql_query("SELECT * FROM group_chats WHERE id=".$recent_row['group_id']." AND id in (".$g_id.") and id NOT IN (".$not_group_id.")") or die("Query 123 Error");
            }

            if(mysql_num_rows($all_group) > 0){
                while($row = mysql_fetch_array($all_group)){
                    $current_group_id = $row["id"];
                    $group_chat_count = mysql_query("SELECT COUNT(*) FROM group_chat_views WHERE receiver_id=".$sender_id." AND group_id=".$current_group_id." AND view_status='N'") or die("Query 456 Error");
                    $group_chat_msg = mysql_query("SELECT * FROM chats WHERE receiver_user_id='0' AND group_id=".$current_group_id." AND view_status='N' ORDER BY id ASC") or die("Query 789 Error");
                    if(mysql_num_rows($group_chat_msg) > 0){
                        while($w = mysql_fetch_array($group_chat_msg)){
                            $g_chat = $w["comments"];
                            if( $w["upload_file"] != "" ){
                                $g_chat = "New File is Uploaded.".$w["upload_file"];
                            }
                            $date = date_create($w["created_at"]);
                            $g_date =  date_format($date, 'M d,Y');
                        }
                    }else{
                        $g_chat = $g_date = "";
                    }
                    if(mysql_num_rows($group_chat_count) > 0){
                        while($g_row = mysql_fetch_array($group_chat_count)){
                            $g_count = $g_row[0];
                        }
                    }else{
                        $g_count = 0;
                    }

                    if( !empty($row["profile_picture"]) ){
                        $img_url = 'assets/groups/'.$row["id"].'/'.$row["profile_picture"];
                    }else{
                        $img_url = "assets/img/group_icon.png";
                    }
                }
            }
        }
    }
}



if(!empty($noti_sender_user_id)){
}
$recent_user_id = rtrim($recent_user_id, ",");
$recent_user_id2 = rtrim($recent_user_id2, ",");
$recent_group_id = rtrim($recent_group_id, ",");


$result = implode(',', array_unique(explode(',', $recent_user_id2)));

if(!empty($result)){
    $u_query3 = "SELECT user_id, user_level, username, name, user_image, user_logo, region,email,position,AgencyName  FROM login_users WHERE approved='YES' AND  user_id IN (".$result.") AND user_id <> ".$sender_id." ORDER BY name ASC";
}else{
    $u_query3 = "SELECT user_id, user_level, username, name, user_image, user_logo, region,email,position,AgencyName  FROM login_users WHERE approved='YES' AND user_id <> ".$sender_id." ORDER BY name ASC";
}

$u_query3 = mysql_query($u_query3);

if(mysql_num_rows($u_query3) > 0){
    while ($row = mysql_fetch_array($u_query3)) {
        $current_user_userimage = $row["user_image"];
        if ($current_user_userimage <> '') $current_user_userimage = @unserialize($current_user_userimage); else $current_user_userimage = '';
        if ($current_user_userimage == '') $img_val = "assets/img/photo.jpg";
        else $img_val = "assets/profile/" . $current_user_userimage[0];
		
		$current_user_userlogo = $row["user_logo"];
        if ($current_user_userlogo <> '') $current_user_userlogo = @unserialize($current_user_userlogo); else $current_user_userlogo = '';
        if ($current_user_userlogo == '') $img_logo = '';
        else $img_logo = "assets/logo/" . $current_user_userlogo[0];
		
        $reg = mysql_query('SELECT COUNT(*) FROM chats WHERE receiver_user_id="' . $_SESSION["adminlogin"] . '" AND view_status="N" AND sender_user_id="' . $row["user_id"] . '"') or die("Query n Error");
        $chat_count = 0;
        while ($reg_row = mysql_fetch_array($reg)) {
            $chat_count = $reg_row["0"];
        }

        $chat_msg = mysql_query("SELECT * FROM chats WHERE receiver_user_id=" . $_SESSION["adminlogin"] . " AND group_id='0' AND view_status='N' AND sender_user_id=" . $row["user_id"] . " ORDER BY id ASC") or die("Query z Error");
        if (mysql_num_rows($chat_msg) > 0) {
            while ($w = mysql_fetch_array($chat_msg)) {
                $r_chat = $w["comments"];
                if ($w["upload_file"] != "") {
                    $r_chat = "New File is Uploaded." . $w["upload_file"];
                }
                $date = date_create($w["created_at"]);
                $r_date = date_format($date, 'M d,Y');
            }
        } else {
            $r_chat = $r_date = "";
        }
        if(!in_array($row["user_id"],$private_chat_ids)){
        $private_chat_ids[]=$row["user_id"]    ;
		if($row["name"]){$Name=$row["name"];}else{$Name='--';}
		if($row["position"]){$Position=$row["position"];}else{$Position='--';}
		if($row["AgencyName"]){$Agencyname=$row["AgencyName"];}else{$Agencyname='--';}
		if($row["region"]){$Region=$row["region"];}else{$Region='--';}
		if($row["email"]){$Email=$row["email"];}else{$Email='--';}
					
        $group_details .= '<li class="3"><a href="?r=' . $row["user_id"] . '" id="r' . $row["user_id"] . '">
                    <span><img src="'.$img_val.'" alt="user image"></span>
                    <span>'.$row["name"].'
                    <i class="fa fa-address-card hidden-xs">
                    <div class="chatuser_sortinfo">
                      <div class="row mar0">
                        
                        <div class="col-md-12 col-sm-12 mar_t10">
                          <p><span>'.$Name.'</span></p>
                          <p><span>'.$Position.'</span></p>
                          <p><span>'.$Agencyname.'</span></p>
                          <p><span>'.$Region.'</span></p>
                          <p><span>'.$Email.'</span></p>
                        </div>';
						if($row["user_logo"]<>'') { 
						$group_details .='<div class="col-md-12 col-sm-12 text-center pad_tb10 mar_t10 grey_bg">
                           <img src="'.$img_logo.'" alt="">
                        </div>';
						}
                      $group_details .='</div>
                  </div>
        
                  </i>
                    </span>';
        if ($chat_count != 0 || ( $r_blink != 0 && $row["user_id"] == $o_receiver_id)) {
            if($r_blink != 0 && $row["user_id"] == $o_receiver_id){
                $new_class = "blink_me hide";
                $chat_count = "";
            }else{
                $new_class = "blink_me";
            }
            $group_details .= ' <span class="'.$new_class.'">' . $chat_count .$r_blink. '</span>';
        }
        $group_details .= '</p><p>' . $r_chat . '</p>
                     <p class="u-date">' . $r_date . '</p>
                 </a></li>';} // Check in INArray
    }
}
/**
 * Search with Agency Name and Region
 */

if( isset($_POST["AgencySearch"]) ){
    $select_region = $_POST["select_region"];
    $agency_id = $_POST["agency_id"];

    if( empty($select_region) && empty($agency_id)){
        $users = mysql_query("SELECT user_id, user_level, username, name, user_image, user_logo, region  FROM login_users WHERE approved='YES' AND user_id <> ".$_SESSION['adminlogin']." ORDER BY name ASC");
    }else{
        $q = "SELECT id FROM agency WHERE region='".$select_region."'";
        $select_region_query = mysql_query($q) or die("Query a error");
        $agency_id_all = "";
        if( mysql_num_rows($select_region_query) > 0 ){
            while($sr_row = mysql_fetch_array($select_region_query)){
                $agency_id_all .= $sr_row["id"];
                $agency_id_all .= ",";
            }
        }
        if(!empty($agency_id)){
            $agency_id_all .= $agency_id;
        }
        $agency_id_all = rtrim($agency_id_all, ',');
        $all_user_id = mysql_query("SELECT DISTINCT user_id FROM agency_map WHERE agency_id IN (".$agency_id_all.")") or die("Query v Error");
        $all_id = "";
        if( mysql_num_rows($all_user_id) > 0){
            while($a_row = mysql_fetch_array($all_user_id)){
                $all_id .= $a_row["user_id"];
                $all_id .= ",";
            }
        }
        $all_id = rtrim($all_id, ',');
        $q = "SELECT * FROM login_users WHERE user_id IN (".$all_id.")";
        $users = mysql_query($q) or die("Query s Error");
        print_r($users); die;
    }

}else{
    /**
     * To get all users
     */
    if(!empty($recent_user_id)){
        $users = mysql_query("SELECT user_id, user_level, username, name, user_image, user_logo, region,email,position,AgencyName  FROM login_users WHERE approved='YES' AND user_id <> ".$_SESSION['adminlogin']." AND user_id NOT IN (".$recent_user_id.") ORDER BY name ASC");
    }else{
        $users = mysql_query("SELECT user_id, user_level, username, name, user_image, user_logo, region,email,position,AgencyName  FROM login_users WHERE approved='YES' AND user_id <> ".$_SESSION['adminlogin']." ORDER BY name ASC");
    }


    $all_groups = mysql_query("SELECT * FROM group_members WHERE member_user_id=".$_SESSION['adminlogin']." AND status='Y'" ) or die("Query 2 Error");
    $all_group_id = "";
    if( mysql_num_rows($all_groups) > 0){
        while($row = mysql_fetch_array($all_groups)){
            $all_group_id .= $row["group_id"];
            $all_group_id .= ",";
        }
        $all_group_id = rtrim($all_group_id, ",");
    }else{
        $all_group_id = 0;
    }
    if(!empty($recent_group_id)){
        $all_group = mysql_query("SELECT * FROM group_chats WHERE id IN (".$all_group_id.") AND id NOT IN (".$recent_group_id.")") or die("Query x Error");
    }else{
        $all_group = mysql_query("SELECT * FROM group_chats WHERE id IN (".$all_group_id.")") or die("Query x Error");
    }

    if( mysql_num_rows($all_group) > 0){
        while($row = mysql_fetch_array($all_group)){
            $current_group_id = $row["id"];
            $group_chat_count = mysql_query("SELECT COUNT(*) FROM group_chat_views WHERE receiver_id=".$sender_id." AND group_id=".$current_group_id." AND view_status='N'") or die("Query y Error");
            $group_chat_msg = mysql_query("SELECT * FROM chats WHERE receiver_user_id='0' AND group_id=".$current_group_id." AND view_status='N' ORDER BY id ASC") or die("Query w Error");
            if(mysql_num_rows($group_chat_msg) > 0){
                while($w = mysql_fetch_array($group_chat_msg)){
                    $g_chat = $w["comments"];
                    if( $w["upload_file"] != "" ){
                        $g_chat = "New File is Uploaded.".$w["upload_file"];
                    }
                    $date = date_create($w["created_at"]);
                    $g_date =  date_format($date, 'M d,Y');
                }
            }else{
                $g_chat = $g_date = "";
            }
            if(mysql_num_rows($group_chat_count) > 0){
                while($g_row = mysql_fetch_array($group_chat_count)){
                    $g_count = $g_row[0];
                }
            }else{
                $g_count = 0;
            }

            if( !empty($row["profile_picture"]) ){
                $img_url = 'assets/groups/'.$row["id"].'/'.$row["profile_picture"];
            }else{
                $img_url = "assets/img/group_icon.png";
            }
            if(!in_array($row["id"],$group_chat_ids)){
            $group_details .= '<li class="4"><a href="?g='.$row["id"].'" id="g'.$row["id"].'">
            <span><img src="'.$img_url.'" alt="user image"></span>
             <span>'.$row["name"].'</span>';
            if($g_count != 0 || $g_blink != 0){
                if($g_blink != 0){
                    $new_group_class = "blink_me hide";
                    $g_count = "";
                }else{
                    $new_group_class = "blink_me";
                }
                $group_details .= ' <span class="'.$new_group_class.'">'.$g_count.$g_blink.'</span>';
                $group_details .= '</p><p>'.$g_chat.'</p>
                <p class="u-date">'.$g_date.'</p>';
            }

            $group_details .= '</a></li>';
            }   //   Check In Array
        }
    }
}

if( mysql_num_rows($users) > 0){
    while($row=mysql_fetch_array($users)){
        $reg = mysql_query('SELECT COUNT(DISTINCT sender_user_id) FROM chats WHERE receiver_user_id="'.$_SESSION["adminlogin"].'" AND view_status="N" AND sender_user_id="'.$row["user_id"].'"') or die("Query 1 Error");
        $chat_count = 0;
        while( $reg_row = mysql_fetch_array($reg)  ){
            $chat_count = $reg_row["0"];
        }
        
        $current_user_userimage = $row["user_image"];
        if ($current_user_userimage <> '') $current_user_userimage = @unserialize($current_user_userimage); else $current_user_userimage = '';
        if ($current_user_userimage == '') $img_url = "assets/img/photo.jpg";
        else $img_url = "assets/profile/" . $current_user_userimage[0];
		
		$current_user_userlogo = $row["user_logo"];
        if ($current_user_userlogo <> '') $current_user_userlogo = @unserialize($current_user_userlogo); else $current_user_userlogo = '';
        if ($current_user_userlogo == '') $img_value = '';
        else $img_value = "assets/logo/" . $current_user_userlogo[0];

        $chat_msg = mysql_query("SELECT * FROM chats WHERE receiver_user_id=".$_SESSION["adminlogin"]." AND group_id='0' AND view_status='N' AND sender_user_id=".$row["user_id"]." ORDER BY id ASC") or die("Query z Error");
        if(mysql_num_rows($chat_msg) > 0){
            while($w = mysql_fetch_array($chat_msg)){
                $r_chat = $w["comments"];
                if( $w["upload_file"] != "" ){
                    $r_chat = "New File is Uploaded.".$w["upload_file"];
                }
                $date = date_create($w["created_at"]);
                $r_date =  date_format($date, 'M d,Y');
            }
        }else{
            $r_chat = $r_date = "";
        }
        $u_link = "?r".$row["user_id"];
        $link = "showHide('example');return false;";
        if(!in_array($row["user_id"],$private_chat_ids)){
        $private_chat_ids[]=$row["user_id"]    ;
		
		if($row["name"]){$Name=$row["name"];}else{$Name='--';}
		if($row["position"]){$Position=$row["position"];}else{$Position='--';}
		if($row["AgencyName"]){$Agencyname=$row["AgencyName"];}else{$Agencyname='--';}
		if($row["region"]){$Region=$row["region"];}else{$Region='--';}
		if($row["email"]){$Email=$row["email"];}else{$Email='--';}
		
        $group_details .= '<li class="5"><a href="?r='.$row["user_id"].'" id="r'.$row["user_id"].'" class="showLink" onclick="'.$link.'">
            <span><img src="'.$img_url.'" alt="user image"></span>
            <span>'.$row["name"].'
            <i class="fa fa-address-card hidden-xs">
                <div class="chatuser_sortinfo">
                  <div class="row mar0">
                    <div class="col-md-12 col-sm-12 mar_t10">
                      <p><span>'.$Name.'</span></p>
                      <p><span>'.$Position.'</span></p>
                      <p><span>'.$Agencyname.'</span></p>
                      <p><span>'.$Region.'</span></p>
                      <p><span>'.$Email.'</span></p>
                    </div>';
					if($row["user_logo"]<>'') { 
					$group_details .= '<div class="col-md-12 col-sm-12 text-center pad_tb10 mar_t10 grey_bg">
                       <img src="'.$img_value.'" alt="">
                    </div>';
					}
                  $group_details .= '</div>
              </div>

              </i>
            </span>';
        if($chat_count != 0 || ( $r_blink != 0 && $row["user_id"] == $o_receiver_id)){
            if($r_blink != 0 && $row["user_id"] == $o_receiver_id){
                $new_class = "blink_me hide";$chat_count = "";
            }else{
                $new_class = "blink_me";
            }
            $group_details .= ' <span class="'.$new_class.'">'.$chat_count.$r_blink.'</span>';
        }
        $group_details .= '</p>
                <p>'.$r_chat.'</p>
                <p class="u-date">'.$r_date.'</p></a></li>'; } // Check in INArray
    }
}


/**
 * Update online status
 */
$update_status = mysql_query("UPDATE login_users SET online='Y' WHERE user_id=".$_SESSION['adminlogin']." AND approved = 'YES'") or die("Query 2 Error");

/**
To get current user
 */
$current_users = mysql_query("SELECT user_id, user_level, username, name, user_image, user_logo, region FROM login_users WHERE user_id=".$_SESSION['adminlogin']." AND approved = 'YES'") or die("Query 3 Error");
while($row=mysql_fetch_array($current_users)) {
    $current_user_id =trim($row['user_id']);
    $current_user_level =trim($row['user_level']);
    $current_user_username =trim($row['username']);
    $current_user_name =trim($row['name']);
    $current_user_userimage = trim($row['user_image']);
	$current_user_userlogo = trim($row['user_logo']);
    $current_user_region = trim($row['region']);
}
if($current_user_userimage <> '') $current_user_userimage=@unserialize($current_user_userimage); else $current_user_userimage='';
if($current_user_userimage=='')  $img_val ="assets/img/photo.jpg";
else $img_val ="assets/profile/".$current_user_userimage[0];

if($current_user_userlogo <> '') $current_user_userlogo=@unserialize($current_user_userlogo); else $current_user_userlogo='';
if($current_user_userlogo=='')  $img_logo ='';
else $img_logo ="assets/logo/".$current_user_userlogo[0];

$current_user_levels = unserialize($current_user_level);

function getUserImage($id){
    $query = mysql_query("SELECT user_id, user_level, username, name, user_image, user_logo, region,email,position,AgencyName  FROM login_users WHERE approved='YES' AND user_id=".$id) or die("Query 4 error");
    $data = array();
    while($q_row = mysql_fetch_array($query)){
        if($q_row["user_image"] <> '') $user_image=@unserialize($q_row["user_image"]); else $user_image='';
        if($user_image=='')  $img ="assets/img/photo.jpg";
        else $img ="assets/profile/".$user_image[0];
		
		if($q_row["user_logo"] <> '') $user_logo=@unserialize($q_row["user_logo"]); else $user_logo='';
        if($user_logo=='')  $logo ='';
        else $logo ="assets/logo/".$user_logo[0];
        $data['user_image']= $img;
		$data['user_logo'] = $logo;
        $data['user_name'] = $q_row["name"];
        $data['region'] = $q_row["region"];
        $data['email'] = $q_row["email"];
        $data['agency'] = $q_row["AgencyName"];
        $data['position'] = $q_row["position"];
    }
    return $data;
}

if(isset($_GET["r"])){
    $sender_id = $current_user_id;
    $receiver_id = $_GET["r"];
    $select = mysql_query("SELECT * FROM chat_active WHERE sender_id=".$sender_id) or die("Query a Error");
    if( mysql_num_rows($select) > 0){
        mysql_query("DELETE FROM chat_active WHERE sender_id=".$sender_id);
    }

    $update_online_status = mysql_query("INSERT INTO chat_active (sender_id, receiver_id, group_id, active_at, active_time) VALUES ('".$sender_id."', '".$receiver_id."', '".$group_id."', now(),now())") or die("Status Query Error");

    $count_update = mysql_query('UPDATE chats SET view_status="Y" WHERE receiver_user_id="'.$_SESSION["adminlogin"].'" AND view_status="N" AND sender_user_id="'.$receiver_id.'"') or die("Query b Error");
    $r_user = mysql_query("SELECT * FROM login_users WHERE approved='YES' AND user_id=".$_GET["r"]) or die("Query c Error");
    while($row_user = mysql_fetch_array($r_user)){
        $receiver_id = $_GET["r"];
        $receiver_user_level =trim($row_user['user_level']);
        $receiver_user_username =trim($row_user['username']);
        $receiver_user_name =trim($row_user['name']);
        $receiver_user_userimage = trim($row_user['user_image']);
		$receiver_user_userlogo = trim($row_user['user_logo']);
        $receiver_user_region = trim($row_user['region']);

        if($receiver_user_userimage <> '') $receiver_user_userimage=@unserialize($receiver_user_userimage); else $receiver_user_userimage='';
        if($receiver_user_userimage=='')  $img_receiver ="assets/img/photo.jpg";
        else $img_receiver ="assets/profile/".$receiver_user_userimage[0];
		
		if($receiver_user_userlogo <> '') $receiver_user_userlogo=@unserialize($receiver_user_userlogo); else $receiver_user_userlogo='';
        if($receiver_user_userlogo=='')  $logo_receiver ='';
        else $logo_receiver ="assets/logo/".$receiver_user_userlogo[0];
    }
    $sql = mysql_query("SELECT * FROM chats WHERE status='Y' AND  sender_user_id=".$sender_id." AND receiver_user_id=".$receiver_id." OR sender_user_id=".$receiver_id." AND receiver_user_id=".$sender_id." ORDER BY created_at ASC") or die("Query d Error");
    $count = mysql_num_rows($sql);

    $update_online_status = mysql_query("UPDATE chats SET online='Y' WHERE status='Y' AND  sender_user_id=".$sender_id." AND receiver_user_id=".$receiver_id." OR sender_user_id=".$receiver_id." AND receiver_user_id=".$sender_id) or die("Query e Error");

}

if(isset($_GET["g"])){
    $select = mysql_query("SELECT * FROM chat_active WHERE sender_id=".$sender_id) or die("Query f Error");
    if( mysql_num_rows($select) > 0){
        mysql_query("DELETE FROM chat_active WHERE sender_id=".$sender_id);
    }

    $update_online_status = mysql_query("INSERT INTO chat_active (sender_id, receiver_id, group_id, active_at, active_time) VALUES ('".$sender_id."', '".$receiver_id."', '".$group_id."', now(),now())") or die("Status Query Error");

    $group_id = $_GET["g"];
    $all_groupss = mysql_query("SELECT * FROM group_chats WHERE id=".$group_id." AND id in (".$g_id.") ") or die("Query g Error");
    mysql_query("UPDATE group_chat_views SET view_status='Y' WHERE group_id='".$group_id."' AND group_id in (".$g_id.") AND receiver_id='".$sender_id."'") or die("Query h Error");
    if ( mysql_num_rows($all_groupss) > 0 ){
        while ($rowss = mysql_fetch_array($all_groupss)){
            if( empty($rowss["profile_picture"])){
                $img_receiver = "assets/img/group_icon.png";
            }else{
                $img_receiver = 'assets/groups/'.$rowss["id"].'/'.$rowss["profile_picture"];
            }
            $current_group_name = $rowss["name"];
        }

    }else{
        $img_receiver = "assets/img/group_icon.png";
        $current_group_name = "";
    }

}else{
    $group_id = 0;
}

function getGroupName($id){
    $g_group_id = $id;
    $g_name = "";
    $a = mysql_query("SELECT * FROM group_chats WHERE id=".$g_group_id) or die("Query k Error");
    if(mysql_num_rows($a) > 0){
        while($z = mysql_fetch_array($a)){
            $g_name = $z["name"];
        }
    }
    return $g_name;
}

echo $group_details;

?>
