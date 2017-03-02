<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$user_id = $sender_id = $_SESSION['adminlogin'];
function getUserImage($id){
    $query = mysql_query("SELECT user_id, user_level, username, name, user_image, region  FROM login_users WHERE approved='YES' AND user_id=".$id) or die("Query error");
    $data = array();
    while($q_row = mysql_fetch_array($query)){
        if($q_row["user_image"] <> '') $user_image=@unserialize($q_row["user_image"]); else $user_image='';
        if($user_image=='')  $img ="assets/img/photo.jpg";
        else $img ="assets/profile/".$user_image[0];
        $data['user_image']= $img;
        $data['user_name'] = $q_row["name"];
    }
    return $data;
}

$name = trim( $_POST["name"] );
$query = "SELECT DISTINCT user_id, user_level, username, name, user_image, user_logo, region,email,position,AgencyName FROM login_users WHERE name LIKE '%".$name."%' AND user_id <> ".$user_id;
$sql = mysql_query($query) or die("Query Error");
$img_url = "";
$message = "";
$d = array();
if( mysql_num_rows($sql) > 0 ){
    while($row = mysql_fetch_array($sql)){
		$user_image = $row["user_image"];
		$user_logo = $row["user_logo"];
		if($user_image <> '') $user_image=@unserialize($user_image); else $user_image='';
        if($user_image=='')  $img_val ="assets/img/photo.jpg";
        else $img_val ="assets/profile/".$user_image[0];
		
		if($user_logo <> '') $user_logo=@unserialize($user_logo); else $user_logo='';
        if($user_logo=='')  $logo_val ='';
        else $logo_val ="assets/logo/".$user_logo[0];
		
        $reg = mysql_query('SELECT COUNT(DISTINCT sender_user_id) FROM chats WHERE receiver_user_id="' . $_SESSION["adminlogin"] . '" AND view_status="N" AND sender_user_id="' . $row["user_id"] . '"') or die("Query n Error");
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

        $d = getUserImage($row["user_id"]);
        if($row["name"]){$Name=$row["name"];}else{$Name='--';}
		if($row["position"]){$Position=$row["position"];}else{$Position='--';}
		if($row["AgencyName"]){$Agencyname=$row["AgencyName"];}else{$Agencyname='--';}
		if($row["region"]){$Region=$row["region"];}else{$Region='--';}
		if($row["email"]){$Email=$row["email"];}else{$Email='--';}
        $message .=  '
    <li class="5"><a href="?r='.$row["user_id"].'" id="'.$row["user_id"].'">
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
						$message .='<div class="col-md-12 col-sm-12 text-center pad_tb10 mar_t10 grey_bg">
                           <img src="'.$logo_val.'" alt="">
                        </div>';
						}
                      $message .='</div>
                  </div>
        
                  </i>
			 </span>';
        if ($chat_count != 0) {
            $message .= ' <span class="blink_me">' . $chat_count . '</span>';
        }
        $message .= '</p><p>' . $r_chat . '</p>
                     <p class="u-date">' . $r_date . '</p>';
        $message .= ' 
            </a></li>
        ';
    }
    $data = [
        'value' => $name,
        'message' => $message
    ];

}else{
  //
    $sql = mysql_query("SELECT * FROM group_chats WHERE name LIKE '%".$name."%'") or die("Query Error");
    if(mysql_num_rows($sql) > 0){
       while( $row = mysql_fetch_array($sql)){
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
           $message .=  '
    <li class="2"><a href="?g='.$row["id"].'" id="'.$row["id"].'">
            <span><img src="'.$img_url.'" alt="user image"></span>
             <span>'.$row["name"].'</span>';
            if($g_count != 0){
                $message .= ' <span class="blink_me">'.$g_count.'</span>';
                $message .= '</p><p>'.$g_chat.'</p>
                <p class="u-date">'.$g_date.'</p>';
            }
           $message .= '</a></li>';
        }
    }else{
        echo "No Users Matched With ".$name; die;
    }
}

echo $message;
die;
