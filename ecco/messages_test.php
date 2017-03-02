<?php
include_once('templates/header.php'); 
date_default_timezone_set('America/New_York');
$today = date("l, F j, Y, g:i A");
$agency_in='';
$agency_list=mysql_query("SELECT id,name FROM agency order by name ");

$sender_id = $_SESSION['adminlogin'];
$receiver_id = 0;

/**
 * Search with Agency Name and Region
 */
if( isset($_POST["AgencySearch"]) ){
    $select_region = $_POST["select_region"];
    $agency_id = $_POST["agency_id"];

    if( empty($select_region) && empty($agency_id)){
        $users = mysql_query("SELECT user_id, user_level, username, name, user_image, region  FROM login_users WHERE approved='YES' AND user_id <> ".$_SESSION['adminlogin']." ORDER BY name ASC");
    }else{
        $q = "SELECT id FROM agency WHERE region='".$select_region."'";
        $select_region_query = mysql_query($q) or die("Query error");
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
        $all_user_id = mysql_query("SELECT DISTINCT user_id FROM agency_map WHERE agency_id IN (".$agency_id_all.")") or die("Query Error");
        $all_id = "";
        if( mysql_num_rows($all_user_id) > 0){
            while($a_row = mysql_fetch_array($all_user_id)){
                $all_id .= $a_row["user_id"];
                $all_id .= ",";
            }
        }
        $all_id = rtrim($all_id, ',');
        $q = "SELECT * FROM login_users WHERE user_id IN (".$all_id.")";
        $users = mysql_query($q) or die("Query Error");
    }

}else{
    /**
     * To get all users
     */
    $users = mysql_query("SELECT user_id, user_level, username, name, user_image, region  FROM login_users WHERE approved='YES' AND user_id <> ".$_SESSION['adminlogin']." ORDER BY name ASC");

    $all_groups = mysql_query("SELECT * FROM group_members WHERE member_user_id=".$_SESSION['adminlogin']." AND status='Y'" ) or die("Query Error");
    $all_group_id = "";
    $group_details = "<ul>";
    if( mysql_num_rows($all_groups) > 0){
        while($row = mysql_fetch_array($all_groups)){
            $all_group_id .= $row["group_id"];
            $all_group_id .= ",";
        }
        $all_group_id = rtrim($all_group_id, ",");
    }else{
        $all_group_id = 0;
    }

    $all_group = mysql_query("SELECT * FROM group_chats WHERE id IN (".$all_group_id.")") or die("Query Error");
    if( mysql_num_rows($all_group) > 0){
        while($row = mysql_fetch_array($all_group)){
            $current_group_id = $row["id"];
           $group_chat_count = mysql_query("SELECT COUNT(*) FROM group_chat_views WHERE receiver_id=".$sender_id." AND group_id=".$current_group_id." AND view_status='N'") or die("Query Error");
            $group_chat_msg = mysql_query("SELECT * FROM chats WHERE receiver_user_id='0' AND group_id=".$current_group_id." AND view_status='N' ORDER BY id ASC") or die("Query Error");
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
            $group_details .= '<li> 
            <span><img src="'.$img_url.'" alt="user image"></span>
             <span><a href="?g='.$row["id"].'" id="'.$row["id"].'">'.$row["name"].'</a> ('.$g_count.')</span>
                
                <p>'.$g_chat.'</p>
                <p class="u-date">'.$g_date.'</p>
                
            </li>';
        }
    }
}

if( mysql_num_rows($users) > 0){
    while($row=mysql_fetch_array($users)){
        $reg = mysql_query('SELECT COUNT(DISTINCT sender_user_id) FROM chats WHERE receiver_user_id="'.$_SESSION["adminlogin"].'" AND view_status="N" AND sender_user_id="'.$row["user_id"].'"') or die("Query Error");
        $chat_count = 0;
        while( $reg_row = mysql_fetch_array($reg)  ){
            $chat_count = $reg_row["0"];
        }
        if($user_userimage <> '') $user_userimage=@unserialize($row["user_image"]); else $user_userimage='';
        if($user_userimage=='')  $img_url ="assets/img/photo.jpg";
        else $img_url ="assets/profile/".$row["user_image"][0];

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

       $group_details .= '<li> 
            <span><img src="'.$img_url.'" alt="user image"></span>
             <span><a href="?g='.$row["id"].'" id="'.$row["id"].'">'.$row["name"].'</a> ('.$g_count.')</span>
                <p>'.$r_chat.'</p>
                <p class="u-date">'.$r_date.'</p>
            </li>';
    }
}


/**
 * Update online status
 */
$update_status = mysql_query("UPDATE login_users SET online='Y' WHERE user_id=".$_SESSION['adminlogin']." AND approved = 'YES'") or die("Query Error");

/**
To get current user
 */
$current_users = mysql_query("SELECT user_id, user_level, username, name, user_image, region FROM login_users WHERE user_id=".$_SESSION['adminlogin']." AND approved = 'YES'") or die("Query Error");
while($row=mysql_fetch_array($current_users)) {
    $current_user_id =trim($row['user_id']);
    $current_user_level =trim($row['user_level']);
    $current_user_username =trim($row['username']);
    $current_user_name =trim($row['name']);
    $current_user_userimage = trim($row['user_image']);
    $current_user_region = trim($row['region']);
}
if($current_user_userimage <> '') $current_user_userimage=@unserialize($current_user_userimage); else $current_user_userimage='';
if($current_user_userimage=='')  $img_val ="assets/img/photo.jpg";
else $img_val ="assets/profile/".$current_user_userimage[0];

$current_user_levels = unserialize($current_user_level);

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

if(isset($_GET["r"])){
    $sender_id = $current_user_id;
    $receiver_id = $_GET["r"];
    $select = mysql_query("SELECT * FROM chat_active WHERE sender_id=".$sender_id) or die("Query Error");
    if( mysql_num_rows($select) > 0){
        mysql_query("DELETE FROM chat_active WHERE sender_id=".$sender_id);
    }

    $update_online_status = mysql_query("INSERT INTO chat_active (sender_id, receiver_id, group_id, active_at, active_time) VALUES ('".$sender_id."', '".$receiver_id."', '0', now(),now())") or die("Status Query Error");

    $count_update = mysql_query('UPDATE chats SET view_status="Y" WHERE receiver_user_id="'.$_SESSION["adminlogin"].'" AND view_status="N" AND sender_user_id="'.$receiver_id.'"') or die("Query Error");
        $r_user = mysql_query("SELECT * FROM login_users WHERE approved='YES' AND user_id=".$_GET["r"]) or die("Query Error");
        while($row_user = mysql_fetch_array($r_user)){
            $receiver_id = $_GET["r"];
            $receiver_user_level =trim($row_user['user_level']);
            $receiver_user_username =trim($row_user['username']);
            $receiver_user_name =trim($row_user['name']);
            $receiver_user_userimage = trim($row_user['user_image']);
            $receiver_user_region = trim($row_user['region']);

            if($receiver_user_userimage <> '') $receiver_user_userimage=@unserialize($receiver_user_userimage); else $receiver_user_userimage='';
            if($receiver_user_userimage=='')  $img_receiver ="assets/img/photo.jpg";
            else $img_receiver ="assets/profile/".$receiver_user_userimage[0];
        }
    $sql = mysql_query("SELECT * FROM chats WHERE status='Y' AND  sender_user_id=".$sender_id." AND receiver_user_id=".$receiver_id." OR sender_user_id=".$receiver_id." AND receiver_user_id=".$sender_id." ORDER BY created_at ASC") or die("Query Error");
    $count = mysql_num_rows($sql);

    $update_online_status = mysql_query("UPDATE chats SET online='Y' WHERE status='Y' AND  sender_user_id=".$sender_id." AND receiver_user_id=".$receiver_id." OR sender_user_id=".$receiver_id." AND receiver_user_id=".$sender_id) or die("Query Error");

}

if(isset($_GET["g"])){
    $group_id = $_GET["g"];
    $select = mysql_query("SELECT * FROM chat_active WHERE sender_id=".$sender_id) or die("Query Error");
    if( mysql_num_rows($select) > 0){
        mysql_query("DELETE FROM chat_active WHERE sender_id=".$sender_id);
    }

    $update_online_status = mysql_query("INSERT INTO chat_active (sender_id, receiver_id, group_id, active_at, active_time) VALUES ('".$sender_id."', '".$receiver_id."', '".$group_id."', now(),now())") or die("Status Query Error");


    $all_groupss = mysql_query("SELECT * FROM group_chats WHERE id=".$group_id) or die("Query Error");
    mysql_query("UPDATE group_chat_views SET view_status='Y' WHERE group_id='".$group_id."' AND receiver_id='".$sender_id."'") or die("Query Error");
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
    $a = mysql_query("SELECT * FROM group_chats WHERE id=".$g_group_id) or die("Query Error");
    if(mysql_num_rows($a) > 0){
        while($z = mysql_fetch_array($a)){
            $g_name = $z["name"];
        }
    }
    return $g_name;
	
}
function call_array_merge($old_keyword,$new_keyword){
   return array_merge($old_keyword,$new_keyword);
}
$new_key = array();
$real_keywords=mysql_query("SELECT keywords from chats");
while($keywords=mysql_fetch_array($real_keywords)){
$rest_key = $keywords['keywords'];
if($rest_key!=''){
   $new_key[]=unserialize($rest_key); 
   
}
 
}
foreach($new_key as $key_words){
    if(is_array($key_words)){
        foreach($key_words as $key_word){
            $post_keys[]=$key_word;
        }
    }else{
        $post_keys[]=$key_words;
    }
}
$post_keys=array_unique($post_keys);
?>
<style>
/*.modal-backdrop{ z-index:0;}*/
</style>
<section >
	<div class="container">
	
	<div class="row">
		<div class="col-md-12">
			<ol class="breadcrumb">
			  <li><a href="systemdashboard.php">Dashboard</a></li>
			  <li class="active">Community </li>              
			</ol>
		</div>
	 </div>
	
		<div class="row">
    <div class="col-md-3 col-sm-4 col-xs-12 pad_r0">
      <div class="chat_userlist">
          <h2 class="text_blue mar0 pad0 fb_300"><i class="fa fa-users mar_r15"></i>Community<span class="info-badge text-lowercase" onclick="info_taggle()" data-toggle = "tooltip" data-placement = "right" title="Click to view Info">i</span></h2>					 
		  <div class="user_status"><button class="mar_t20" id="window_width_button" type="button" onclick="window_width()" style="display: none;">Search Chat</button></div>
          <div class="search_chatlistbox d-message white_bg_msg">
            <div class = "input-group user_searchbox">
                <input type ="text" class ="form-control pa-5" id="namesearch" name="namesearch" placeholder="Search">
                <span class = "input-group-btn">
                    <button type = "button"><i class="fa fa-search"></i></button>
                </span>
            </div>
            <div class="search_chatlistbox1">
            <div id="user-parent1">
            
            </div>

            <div id="user-parent">
                            <ul></ul>
                    </div>
            </div>
          </div>
      </div>
    </div>
	<div class="col-md-9 col-sm-8 col-xs-12 info_taggle" style="display: none;">
		<div class="custom-blockquote mar_b20">
			<p class="mar0">Community page is a place were users can have conversations, post information and tools for others to view. Please note that this page is not moderated and that all posts and communication between users is uniquely identified to the those users. Additional any communication or posts can be requested by the sponsoring client at anytime. Features on this page are not confedential nor privet, please use accordingly.</p>
		  </div>
	</div>
    <div class="col-md-9 col-sm-8 col-xs-12 pad_l0 u-scroll" id="node">
    
    <form name="chatForm" id="chatForm" method="post" enctype="multipart/form-data"   <?php if($group_id == 0 && $receiver_id ==0){ echo "style='display:none'"; } ?>>
       <div class="chat_window">
          <div class="user_status">
             <button type="button" data-target="#groupform" data-toggle="modal" onclick="return false;"><i class="fa fa-plus-circle"></i>Create Group</button>
			 <button type="button" data-toggle="modal" data-target="#create_post" style="background-color: #628ee5;"><i class="fa fa-plus-circle"></i>Create Post</button>
			 <button type="button" data-toggle="modal" data-target="#search_post" style="background-color: #89b0f9;"><i class="fa fa-search"></i>Search Post</button>
             <div class="pull-right text-right"><span class="text_blue fb_500 ft_17"><?php echo $current_user_name; ?></span><small class="text_green"><i><i class="fa fa-comment mar_r5"></i>Online</i></small></div>
          </div>
          <div class="chat_box">
          <div class="group_chat">
              <div class="group_details d-message-r white_bg_msg">
              <?php if($group_id != 0 || $receiver_id != 0){?>
                <div class="row vcenter">
                    <div class="col-md-7 col-sm-6 col-xs-12"> 
                     <?php if($group_id != 0 || $receiver_id != 0){ 
                        $admin_reg='';
                        if($group_id != 0){
                        $admin_reg =  'Admin : '.$current_group_name;
                        }else{ $admin_reg= $receiver_user_region;}?>
                        
                        <img src="<?php echo $img_receiver; ?>" alt="Group icon" class="group-icon pull-left"> <p class="group_name mar0"><?php echo $receiver_user_name;?></p><small class="text_black ft_12"><?php echo $admin_reg; ?></small> 
                        <?php } ?>
                    </div>    
                    <div class="col-md-5 col-sm-6 col-xs-12 text-right">
                      <!-- participant list -->  
                        <div class="group_participant">
                        <?php
                        if( $group_id != 0 || $receiver_id != 0){ ?>
                                                          
                        <?php if( $group_id != 0) { ?>
                        <div class="group_participant_list">
                         <span>
                        <span class="participant_title">Participants <i class="fa fa-caret-down" onclick='return getGroupDetails("<?php echo $group_id; ?>")'></i></span>
                              <ul class="dropdown-menu2">
                              </ul>
                              <span class="clearfix"></span>
                           </span> </div>
                           
                        <!-- add participant icon-->
                        <div class="add_participant_icon">
                        <?php
                        if($receiver_id != 0){
                        ?>
                        <a href="javascrip:void(0);" data-target="#groupform" data-toggle="modal"><img src="new/images/add_user.png" class="select_user"></a>
                        <?php }if($group_id != 0){ ?>
                        <img src="new/images/add_user.png" class="select_user" onclick='return addPersonToGroup("<?php echo $group_id; ?>");' />
                        <?php } ?>
                        </div>
                           <?php }} ?>
                      
                      </div>
                    </div>
                </div>
                <?php } ?>
              </div>
              <!-- add participants list -->
              <?php if($group_id != 0){ ?>
             <div class="chatuser_list dropdown-menu1">
                
            </div>
            <?php } ?>
            </div>
            <div class="chat_area">
            <input type="hidden" name="receiver_id" id="receiver_id" value="<?php echo $receiver_id; ?>">
            <input type="hidden" name="sender_id" id="sender_id" value="<?php echo $sender_id; ?>">
            <input type="hidden" name="group_id" id="group_id" value="<?php echo $group_id; ?>">
            <div id="user-scroll">
            <div class="refresh"></div>
            <div class="refresh1"></div>
            </div>
            </div>
              <div class="comment-box">
                  <div class="row">
                      <div class="col-md-9 col-sm-9 col-xs-9 pad_r0">
                          <div class="form-group mar0">
                              <textarea class="form-control msg-cmd-box" required="required" name="message" id="message" data-emojiable="true"></textarea>
                          </div>
                      </div>
                      <div class="col-md-3 col-sm-3 col-xs-3 form pad_l0">
                        <button type="submit" value="SEND" class="msg-cmd-btn button mar0" id="insertmsg" >SEND</button>
                        <button type="submit" value="UPDATE" class="msg-cmd-btn button mar0" id="updatemsg" style="display: none;">UPDATE</button>
                      </div>
                  </div>
              </div>
          </div>
       </div>
       </form>
    </div>
    </div>
	</div>
</section>
    
<!-- START MODAL -->

  <div id="groupform" class="modal  right fade"  role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h3 class="modal-title text_blue">Create Group</h3>
        </div>
        <div id="loadingimg" class="loader" style="display: none;"></div>
        <div class="modal-body no-shadow">
          <div class="row" id="comment-form">
             <div class="col-xs-12 form">
                <form id="myform" class=""  autocomplete="off" method="POST" action="ajax_chat_group_create.php" enctype="multipart/form-data">
                  <div class="form-group">
                      <label>Name</label>
                      <input type="text" class="form-control" name="group_name" id="group_name">
                      <input type="hidden" name="group_admin" id="group_admin" value="<?php echo $_SESSION['adminlogin']; ?>">
                   </div>
                   <div class="form-group">
                      <label>Members</label>
                      <?php
                        $users = mysql_query("SELECT user_id, user_level, username, name, user_image, region  FROM login_users WHERE approved='YES' AND user_id <> ".$_SESSION['adminlogin']." ORDER BY name ASC");
                        ?>
                        <select name="group_members[]" id="group_members" class="form-control member_select" data-init-plugin="select2" multiple>
                            <option value="">Please Select</option>
                            <?php
                            while($row = mysql_fetch_array($users)){
                                echo '<option value="'.$row["user_id"].'">'.$row["name"].'</option>';
                            }
                            ?>
                        </select>
                   </div>
                   <div class="form-group">
                      <label>Profile Picture</label>
                      <div class="custom-fileupload">
                        <label class="text_white">
                          <i class="fa fa-plus"></i> Add files
                          <input type="file" class="form-control hidden" name="group_profile" id="group_profile" >
                        </label>
                      </div>
                   </div>
                   <div class="col-xs-12 col-sm-12 text-center form">
                      <button class="" type="submit" id="group_create" name="group_create" value="CREATE">Create</button>
                      <button class="mar_l10 cancel_btn" data-dismiss="modal">Cancel</button>
                   </div>
                    <input type="hidden" name="up_agency" value="" >
                    <input type="hidden" name="up_createdate" value="" >
                </form>
             </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="create_post" class="modal  right fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h3 class="modal-title text_blue">Add Post</h3>
        </div>
        <div class="modal-body no-shadow">
          <div class="row" id="comment-form">
             <form>
               <div class="col-md-6 col-sm-12 col-xl-12">
                   <div class="form-group">
                      <label>Topic</label>
                      <select class="form-control" name="post_topic" id="post_topic">
                        <option>Topic 1</option>
                        <option>Topic 2</option>
                        <option>Topic 3</option>
                        <option>Topic 4</option>
                      </select>
                   </div>
               </div>
               <div class="col-md-6 col-sm-12 col-xl-12">
                   <div class="form-group">
                      <label>Keywords<small>(3Max)</small></label><span class="button add_keyword_btn" onclick="show_more_keys()">Add More</span>
                      <select class="form-control member_select" name="post_keywords[]" id="post_keywords" multiple="multiple" >
					  <?php foreach($post_keys as $key) { ?>
                      <option value="<?php echo $key; ?>"><?php echo $key; ?></option> <?php } ?>
                      </select>
                      
                      <input style="display: none;" type="text" class="form-control mar_t10" id="more_keywords" name="more_keywords" placeholder="Seperate with comma(,)" />
                   </div>
               </div>
               <div class="col-md-12 col-sm-12 col-xl-12">
                    <div class="form-group">
                      <label>Addition</label>
                      <textarea name="post_message" id="post_message" class="form-control"></textarea>
                   </div>
               </div>
               <div class="col-md-12 col-sm-12 col-xl-12">
                   <div class="form-group">
                      <div class="custom-fileupload">
                        <label class="text_white">
                          <i class="fa fa-plus"></i> Add files
                          <input type="file" class="form-control hidden" name="post_file" >
                        </label>
                      </div>
                   </div>
               </div>
               <div class="col-xs-12 col-sm-12 text-center form">
                  <button type="button" onclick="add_post()">Create</button>
                  <button type="button" class="mar_l10 cancel_btn" data-dismiss="modal">Cancel</button>
               </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<div id="search_post" class="modal  right fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h3 class="modal-title text_blue">Search Post</h3>
        </div>
        <div class="modal-body no-shadow">
          <div class="row" id="comment-form">
             <form>
               <div class="col-md-6 col-sm-12 col-xl-12">
                   <div class="form-group">
                      <label>Topic</label>
                      <select class="form-control" id="search_topic" name="search_topic">
                        <option>Topic 1</option>
                        <option>Topic 2</option>
                        <option>Topic 3</option>
                        <option>Topic 4</option>
                      </select>
                   </div>
               </div>
               <div class="col-md-6 col-sm-12 col-xl-12">
                   <div class="form-group">
                      <label>Current Keywords</label>
                      <select class="form-control member_select" id="search_keyword" name="search_keyword">
                      <option value="">Select</option>
                      <?php foreach($post_keys as $key) { ?>
                      <option value="<?php echo $key; ?>"><?php echo $key; ?></option> <?php } ?>
                      </select>
                   </div>
               </div>
               <div class="col-md-12 col-sm-12 col-xl-12">
                    <div class="form-group">
                      <label>Search Keywords</label>
                      <input type="text" class="form-control" name="search_post_keyword" id="search_post_keyword">
                   </div>
               </div>
               <div class="col-xs-12 col-sm-12 text-center form">
                  <button type="button" onclick="search_post()">Search</button>
                  <button type="button" class="mar_l10 cancel_btn" onclick="clear_post()">Clear</button>
               </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- END MODAL -->
<?php include 'templates/footer.php'; ?>
<!-- END QUICKVIEW-->
<!-- BEGIN VENDOR JS -->

<!-- success message -->
<script>
    $( document ).ready(function() {
        $("#infor").slideUp( 3000 ).delay( 4000 ).fadeOut( 400 );
        $("#namesearch").blur();
    });
</script>
<!-- sucess message -->

<script type="text/javascript">
    

    $(document).everyTime(3000, function(i) {
        var sender_id;
        sender_id ="<?php echo $current_user_id; ?>";
        var receiver_id=<?php
            if(!empty($receiver_id)){
                echo $receiver_id;
            }else{
                echo "0";
            } ?>;
        var group_id = <?php echo $group_id; ?>;
        var formData = {receiver_id:receiver_id, sender_id:sender_id, group_id:group_id};
        $.ajax({
            url: "ajax_chat_user_list_refresh.php",
            type: "POST",
            data : formData,
            cache: false,
            success: function(html){
               $("#user-parent ul").html(html);
               $('.search_chatlistbox1 ul').slimScroll({
                                    position: 'right',
                                    height:'420px',
                                    railVisible: true,
                                    alwaysVisible: true
                                });
            }
        });
    }, 0);

    $( document ).ready(function() {
        var scr = 10000000000000000;
        $('#user-scroll').scrollTop('10000000000000000');

        $("textarea#message").val($.trim($("textarea#message").val()));
        var sender_id;
        sender_id ="<?php echo $current_user_id; ?>";

        $(".refresh").everyTime(3000,function(i){
            scr = scr + 100;
            $('#user-scroll').scrollTop(scr);
            var receiver_id=<?php
                if(!empty($receiver_id)){
                    echo $receiver_id;
                }else{
                    echo "0";
                } ?>;
            var group_id = <?php echo $group_id; ?>;
            var formData = {receiver_id:receiver_id, sender_id:sender_id, group_id:group_id};
            $.ajax({
                url: "ajax_chat_refresh.php",
                type: "POST",
                data : formData,
                cache: false,
                success: function(html){
                    $(".refresh").append(html);
                    var itemContainer1 = $('.chat_area');
                    var scrollTo_int1 = itemContainer1.prop('scrollHeight') + 'px'; 
                    itemContainer1.slimScroll({
                       scrollTo : scrollTo_int1,
						height:'317px',
						start:'bottom', 
						position: 'right',    
						alwaysVisible: true
                                           
                    });
                }
            });
        });
		 
		
        var sender_id;
        sender_id ="<?php echo $current_user_id; ?>";
        var receiver_id=<?php
            if(!empty($receiver_id)){
                echo $receiver_id;
            }else{
                echo "0";
            } ?>;
        var group_id = <?php echo $group_id; ?>;
		var see = 's';
            var formData = {receiver_id:receiver_id, sender_id:sender_id, group_id:group_id, see:see};
            $.ajax({
                url: "ajax_chat_refresh.php",
                type: "POST",
                data : formData,
                cache: false,
                success: function(html){
                    $(".refresh").html(html);
                    var itemContainer1 = $('.chat_area');
                  var scrollTo_int1 = itemContainer1.prop('scrollHeight') + 'px'; 
                  itemContainer1.slimScroll({
                        scrollTo : scrollTo_int1,
                        position: 'right',
                        height:'317px',
                        start:'bottom',
                        alwaysVisible: true
                    });  
                }
            });
        
        $('.showuser').click(function () {
            var receiver_id;
            receiver_id = this.id;
            $("#receiver_id").val(receiver_id);
            var page;
            page="get_user_chats_ajax.php";
            var formData = {receiver_id:receiver_id, sender_id:sender_id};
            $.ajax({
                url : page,
                type: "POST",
                data : formData,
                success: function(data)
                {
                    $(".ToUser").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
            return false;
        });

        var searchRequest = null;
            $("#namesearch").keyup(function () {
                var that = this,
                    value = $(this).val();
                    if (searchRequest != null)
                        searchRequest.abort();
                         searchRequest = $.ajax({
                        type: "POST",
                        url: "search_user_chat.php",
                        data: {
                            'name' : value
                        },
                        dataType: "text",
                        success: function(msg){
                            //we need to check if the value is the same
                            if (value==$(that).val()) {
                                //Receiving the result of search here
                                $("#user-parent1").html('<ul>'+msg+'</ul>');
                                $("#user-parent").hide();
                                
                            }
                        }
                    });
                 });

        $('#AgencySearch').click(function (){
            var select_region = $.trim($("#select_region").val());
            var agency_id = $.trim($("#agency_id").val());
            var form = {select_region:select_region, agency_id:agency_id};
            var page = "agency_ajax_user_search.php";
            $.ajax({
                url : page,
                type: "POST",
                data : form,
                success: function(data)
                {
                    $success = $.trim(data);
                        $('.user-partview1').html(data);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
            return false;
        });

        $('#insertmsg').click(function () {  

            var receiver_id, message, page, group_id;
            receiver_id = $.trim($("#receiver_id").val());
            message = $.trim($("#message").val());

            group_id = $.trim($("#group_id").val());

            if(message!=''){
            var formData = {receiver_id:receiver_id, sender_id:sender_id, message:message, group_id:group_id};
            page="inser_user_chat.php";
            $.ajax({
                url : page,
                type: "POST",
                data : formData,
                success: function(data)
                {
                   $success = $.trim(data);
                    if( $success == "success"){
                        $('#message').val('');
                    // Message list get                 
                    var sender_id;
                    sender_id ="<?php echo $current_user_id; ?>";
                    var receiver_id=<?php
                        if(!empty($receiver_id)){
                            echo $receiver_id;
                        }else{
                            echo "0";
                        } ?>;
                    var group_id = <?php echo $group_id; ?>;
                        var formData = {receiver_id:receiver_id, sender_id:sender_id, group_id:group_id};
                        $.ajax({
                            url: "ajax_chat_refresh.php",
                            type: "POST",
                            data : formData,
                            cache: false,
                            success: function(html){
                                $(".refresh").append(html);
                                var itemContainer1 = $('.chat_area');
                                var scrollTo_int1 = itemContainer1.prop('scrollHeight') + 'px'; 
								itemContainer1.slimScroll({
                                    scrollTo : scrollTo_int1,
                                    position: 'right',
                                    height:'317px',
                                    start:'bottom',
                                    alwaysVisible: true
                                });  
                            }
                        });
                    }else{
                        alert(data);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
            }else
            {
              alert('Please say somthing in the box!');
              $('#message').focus();
            }
            return false;
        });

    });
    
    function add_post(){

            var receiver_id, message, page, group_id,sender_id;
            sender_id ="<?php echo $current_user_id; ?>";
            receiver_id = $.trim($("#receiver_id").val());
            message = $.trim($("#post_message").val());
            post_topic = $.trim($("#post_topic").val());
            post_keywords = $.trim($("#post_keywords").val());
            more_keywords = $.trim($("#more_keywords").val());

            group_id = $.trim($("#group_id").val());
            post_message=1;

            if(message!=''){
                $('#create_post').modal('hide');

            var formData = {receiver_id:receiver_id, sender_id:sender_id, message:message, group_id:group_id, post_topic:post_topic, post_keywords:post_keywords, more_keywords:more_keywords, post_message:post_message};
            page="inser_user_chat.php";
            $.ajax({
                url : page,
                type: "POST",
                data : formData,
                success: function(data)
                {
                   $success = $.trim(data);
                    if( $success == "success"){
                        $('#post_message').val('');
                        $('#post_keywords').val('');
                        $('#more_keywords').val('');
                    // Message list get                 
                    var sender_id;
                    sender_id ="<?php echo $current_user_id; ?>";
                    var receiver_id=<?php
                        if(!empty($receiver_id)){
                            echo $receiver_id;
                        }else{
                            echo "0";
                        } ?>;
                    var group_id = <?php echo $group_id; ?>;
                        var formData = {receiver_id:receiver_id, sender_id:sender_id, group_id:group_id};
                        $.ajax({
                            url: "ajax_chat_refresh.php",
                            type: "POST",
                            data : formData,
                            cache: false,
                            success: function(html){
                                $(".refresh").append(html);
                                var itemContainer1 = $('.chat_area');
                                var scrollTo_int1 = itemContainer1.prop('scrollHeight') + 'px'; 
								itemContainer1.slimScroll({
                                    scrollTo : scrollTo_int1,
                                    position: 'right',
                                    height:'317px',
                                    start:'bottom',
                                    alwaysVisible: true
                                });  
                            }
                        });
                        
                        $.ajax({
                            url: "get_chat_keys.php",
                            type: "POST",
                            cache: false,
                            success: function(html){
                                $("#post_keywords").html(html); 
                            }
                        });
                    }else{
                        alert(data);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
            }else
            {
              alert('Please say somthing in the box!');
              $('#post_message').focus();
            }
            
            
            return false;
        }

function search_post(){
		var receiver_id, page, group_id,sender_id;
		sender_id ="<?php echo $current_user_id; ?>";
		receiver_id = $.trim($("#receiver_id").val());
		search_message = $.trim($("#search_post_keyword").val());
		search_topic = $.trim($("#search_topic").val());
		search_keyword = $.trim($("#search_keyword").val());

		group_id = $.trim($("#group_id").val());
		post_message=1;

		
			$('#search_post').modal('hide')
		var formData = {receiver_id:receiver_id, sender_id:sender_id, group_id:group_id, search_topic:search_topic, search_keyword:search_keyword, search_message:search_message};
		page ="search_post.php";
		$.ajax({
                url : page,
                type: "POST",
                data : formData,
                success: function(html)
                {
					$(".refresh").append(html);
					//$(".refresh1").html(html);
                    //$(".refresh").hide();
                    //$(".refresh1").show();
				},
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
		
		
		
		return false;
	}
	function clear_post(){
	    $(".refresh").show();
        $(".refresh1").hide();
        $('#search_post').modal('hide');
	}	
    function editComment(data) {
        var obj = JSON.stringify(data);
        var json = JSON.parse(obj);
        var comment_id = json.id;
        var comment_message = json.message;
        document.getElementById("message").value = comment_message;
        document.getElementById("updatemsg").style.display = "block";
        document.getElementById("insertmsg").style.display = "none";
        document.getElementById("updatemsg").setAttribute("onclick", "return updateComment("+comment_id+");");
        return false;
    }

    function updateComment(id) {
        comment_message = document.getElementById("message").value;
        var form = new FormData();
        form.append('id', id);
        form.append('message', comment_message);
        page="ajax_chat_update.php";
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
                    $("textarea#message").val('');
                    
                    document.getElementById("updatemsg").style.display = "none";
                    document.getElementById("insertmsg").style.display = "block";
                    
                    var itemContainer1 = $('.chat_area');
                    var scrollTo_int1 = itemContainer1.prop('scrollHeight') + 'px'; 
					itemContainer1.slimScroll({
                        scrollTo : scrollTo_int1,
                        position: 'right',
                        height:'317px',
                        start:'bottom',
                        alwaysVisible: true
                    }); 

                }else{
                    alert(data);
                }
            }
        });
        return false;
    }

    function getGroupDetails(id){
        var form = new FormData();
        form.append('id', id);
        page="ajax_chat_group_details.php";
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
				var succs = $success.split('***');
				$(".dropdown-menu2").html(succs[0]);
            }
        });
        return false;
    }

    function removeUserFromGroup(id, group_id) {
        var form = new FormData();
        form.append('id', id);
        form.append('group_id', group_id);
        page="ajax_chat_group_remove_user.php";
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
                alert($success);
                getGroupDetails(group_id);
            }
        });
        return false;
    }

    function addPersonToGroup(group_id){
        var form = new FormData();
        form.append('group_id', group_id);
        page="ajax_chat_group_get_user.php";
        $.ajax({
            url: page, // Url to which the request is send
            type: "POST",             // Type of request to be send, called as method
            data:form, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,        // To send DOMDocument or non processed data file it is set to false
            success: function(data)   // A function to be called if request succeeds
            {
               $(".dropdown-menu1").html(data);
            }
        });
        return false;
    }

    function addUserToG() {
        var group_id = $("#add_group_id").val();
        var group_members = $("#addmember").val();
        var form = new FormData();
        form.append('group_id', group_id);
        form.append('group_members', group_members);
        page="ajax_chat_group_add_user.php";
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
                alert($success);
                getGroupDetails(group_id);
            }
        });
        return false;
    }

    function deleteComment(id) {

        var form = new FormData();
        form.append('id', id);
        page="ajax_chat_delete.php";
        $.ajax({
            url: page, // Url to which the request is send
            type: "POST",             // Type of request to be send, called as method
            data:form, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,        // To send DOMDocument or non processed data file it is set to false
            success: function(data)   // A function to be called if request succeeds
            {
                if( data == "success"){
                    $("#message").val();
                }else{
                    alert(data);
                }
            }
        });
        return false;
    }
</script>

<style type="text/css">
    label.myLabel input[type="file"] {
        position: fixed;
        top: -1000px;
    }

    /***** Example custom styling *****/
    .myLabel {
        display: inline-block;
		padding:20px 0; 
    }
    #uploadFile{
        margin: 5px;
        display: none;
        padding-left: 5px;
    }

   
</style>
<link rel="stylesheet" type="text/css" href="assets/emoji/css/jquery.emojipicker.css">
<script type="text/javascript" src="assets/emoji/js/jquery.emojipicker.js"></script>


<link rel="stylesheet" type="text/css" href="assets/emoji/css/jquery.emojipicker.tw.css">
<script type="text/javascript" src="assets/emoji/js/jquery.emojis.js"></script>
<script type="text/javascript">
    $(document).ready(function(e) {
        $('textarea#message').emojiPicker({
            height: '300px',
            width: '450px'
			
        });
    });
</script>
<style>
	.emojiPicker { top: 230px !important;}
	.shortcode{display:none;}
</style>



<script src="assets/js/jquery.slimscroll.js"></script>
<script type="text/javascript">
    $( document ).ready(function() {
        $('.search_chatlistbox1 ul').slimScroll({
        position: 'right',
        height:'420px',
        railVisible: true,
        alwaysVisible: true 
        });
    });
    $( document ).ready(function() {
    });
</script>

<script>
    	$(document).ready(function() {
			$(".showLink").click(function() {
            	$("#user-parent").hide();
            return false;   
        	});
			
        });
    </script>
    <style>
		/*.d-message .slimScrollDiv{ height:auto !important;}*/
		#node .slimScrollDiv{ height:auto !important;}
	</style>
<script type="text/javascript">
    $(document).everyTime(3000, function(i) {
        var formData ="";
        $.ajax({
            url: "get_message_chat_count.php",
            type: "POST",
            data : formData,
            cache: false,
            success: function(html){
                var $success = $.trim(html);
                if($success != "0"){
                    $("#msg_chat_count").html($success);
                }
            }
        });
    }, 0);
</script>
<style>
    .blink_me.hide {
        -webkit-animation: cssAnimation 0s ease-in 5s forwards;
        -moz-animation: cssAnimation 0s ease-in 5s forwards;
        -o-animation: cssAnimation 0s ease-in 5s forwards;
        animation: cssAnimation 0s ease-in 5s forwards;
        -webkit-animation-fill-mode: forwards;
        animation-fill-mode: forwards;
    }
    @keyframes cssAnimation {
        from {
            visibility:hidden;
        }
        to {
            width:0;
            height:0;
            visibility:hidden;
        }
    }
    @-webkit-keyframes cssAnimation {
        from {
            visibility:hidden;
        }
        to {
            width:0;
            height:0;
            visibility:hidden;
        }
    }
</style>
<script src="assets/js/drawer.js" type="text/javascript"></script>
    <script type="text/javascript">
    $("[data-tooltip = 'tooltip']").tooltip();
       $('#drawerExample').drawer({ toggle: false });
       $('#other-toggle').click(function() {
       $('#drawerExample').drawer('toggle');
         return false;
       });
    </script>
    <script type="text/javascript">
function show_section(){
		$( "li" ).find( "ul.sum1" ).css( "display", "block" );
		$( "li" ).find( "ul.sum2" ).css( "display", "none" );
		$(".arr1").attr('onclick','hide_section()');
		$(".arr2").attr('onclick','show_section1()');
  }
  function hide_section(){
		$( "li" ).find( "ul.sum1" ).css( "display", "none" );
		$(".arr1").attr('onclick','show_section()');
  }
  function show_section1(){
		$( "li" ).find( "ul.sum2" ).css( "display", "block" );
		$( "li" ).find( "ul.sum1" ).css( "display", "none" );
		$(".arr1").attr('onclick','show_section()');
		$(".arr2").attr('onclick','hide_section1()');
  }
  function hide_section1(){
		$( "li" ).find( "ul.sum2" ).css( "display", "none" );
		$(".arr2").attr('onclick','show_section1()');
  }
  $(document).ready(function(){
  
        $(".chatuser_list").hide();
        $(".select_user").on('click',function(){
          $(".chatuser_list").slideToggle();
          $(".group_participant_list ul").slideUp();
        });
        
        $(".group_participant_list ul").hide();
        $(".participant_title").on('click',function(){
          $(".group_participant_list ul").slideToggle();
          $(".chatuser_list").slideUp();
        });
                
         $('.member_select').select2();
      });
      
      function slideup_userlist_close(){
            $(".chatuser_list").slideUp(); 
            return false;
      }
      $(window).load(function(){
        
        $("#file").change( function(e) {
            var sender_id;
            sender_id ="<?php echo $current_user_id; ?>";
            
            var file = this.files[0];
            var receiver_id = $("#receiver_id").val();
            var group_id = $("#group_id").val();
            var form = new FormData();
            form.append('file', file);
            form.append('sender_id', sender_id);
            form.append('receiver_id', receiver_id);
            form.append('group_id', group_id);
            $.ajax({
                url: "ajax_chat_upload.php", // Url to which the request is send
                type: "POST",             // Type of request to be send, called as method
                data:form, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData:false,        // To send DOMDocument or non processed data file it is set to false
                success: function(data)   // A function to be called if request succeeds
                {
                    $success = $.trim(data);
                    alert($success);
                    if( $success == "success"){
                        $('#file').val('');
                        // Message list get                 
                        var sender_id;
                        sender_id ="<?php echo $current_user_id; ?>";
                        var receiver_id=<?php
                            if(!empty($receiver_id)){
                                echo $receiver_id;
                            }else{
                                echo "0";
                            } ?>;
                        var group_id = <?php echo $group_id; ?>;
                            var formData = {receiver_id:receiver_id, sender_id:sender_id, group_id:group_id};
                            $.ajax({
                                url: "ajax_chat_refresh.php",
                                type: "POST",
                                data : formData,
                                cache: false,
                                success: function(html){
                                    $(".refresh").html(html);
                                    var itemContainer1 = $('.chat_area');
                                    var scrollTo_int1 = itemContainer1.prop('scrollHeight') + 'px'; 
									itemContainer1.slimScroll({
                                        scrollTo : scrollTo_int1,
                                        position: 'right',
                                        height:'317px',
                                        start:'bottom',
                                        alwaysVisible: true
                                    });  
                                }
                            });
                         
                    }else{
                        alert(data);
                    }
                }
            });
        });
      })
		
     $(document).ready(function() {
			$win_width=$(window).width();
			if($win_width < 768){
				$(".search_chatlistbox").hide();
                $("#window_width_button").show();
                $("#node").removeClass('pad_l0');
			}
			
	});
	function window_width(){
			$(".search_chatlistbox").slideToggle();
		
	}
    function show_more_keys(){
        $("#more_keywords").toggle();
    }
  </script>
  
  <style>
    .emojiPickerIcon { width:30px !important; height:30px !important; background-color:#fff !important; top:1px !important; right:1px !important;}
    .msg_upload_icon{ position:absolute; right:0; bottom:0;} .msg_upload_icon label i{ font-size:21px !important;}
  </style>

</body>
</html>