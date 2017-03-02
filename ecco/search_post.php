<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
date_default_timezone_set('America/New_York');
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

$sender_id = $_POST["sender_id"];
$receiver_id = $_POST["receiver_id"];
$group_id = $_POST["group_id"];
$html = "<ul>";
$current_date = date('Y-m-d');
$current_time = date('H:i:s');



if($group_id != 0){
    $where='';
    if($_POST['search_topic']!=''){ $where.=" AND topic='".$_POST['search_topic']."'";}
    if($_POST['search_keyword']!=''){ $where.=" AND keywords LIKE '%\"".$_POST['search_keyword']."\"%'";}
    if($_POST['search_message']!=''){ $where.=" AND comments LIKE '%".$_POST['search_message']."%'";}
    $sql_query="SELECT * FROM chats WHERE status='Y' AND group_id=".$group_id." ".$where." ORDER BY created_at ASC";
    $sql = mysql_query($sql_query) or die("Query Error");
    $count = mysql_num_rows($sql);
    if($count > 0) {
        $d = $s = array();
        $currentDate = false;
        $i = 1;

        while ($row = mysql_fetch_array($sql)) {
            $file_path = "assets/chats/".$row["sender_user_id"]."/".$row["upload_file"];
            $d = getUserImage($row['sender_user_id']);
            $s = getUserImage($row['sender_user_id']);

            if ($row['created_at'] != $currentDate){
                $html .='<p class="date_divider"><span class="text_blue fb_500">'.convert_timezone_date($row['created_at']." ".$row['created_time'],'M d, Y').'</span></p>';
                $currentDate = $row['created_at'];
            }
            
            if( ($row['created_at'] != $row['updated_at']) || ($row['created_time'] != $row['updated_time']) ){
                $class = "edited";
            }else{
                $class = "";
            }
            if($row["deleted_at"] == "0000-00-00" ||  $row["deleted_at"] == NULL){
                $c_msg = $row["comments"];
                $r_class = "";
            }else{
                $c_msg = "Message Removed";
                $r_class = "removed";
            }
            if($row['sender_user_id'] == $sender_id){
            $html .=  '<li class="row left-comment text-left">';
                $e_msg = json_encode(array(
                    'message' => $row['comments'],
                    'id' => $row['id']
                ));
                $c = $i++;
				
                $html .= '<div class="col-md-1 col-sm-2 col-xs-2">
                                      <img src="'.$s["user_image"].'" alt="profile icon" class="profile-image">
                            </div>
                            <div class="col-md-10 col-sm-9 col-xs-9 pad_l20">
                                      <div class="comment-info">';
                                      $html .= "<p><span class='chat-username'>".$current_user_name."</span><small class='chat-datelocation'>";
                                      if($current_date == $row["created_at"]){
                                        $to_time = strtotime($current_time);
                                        $from_time = strtotime($row["created_time"]);
                                        $time =  round(abs($to_time - $from_time) / 60,2);
                                        if($time <= 10){
                                        if(empty(trim($row["upload_file"]))){
                                         $html .= "<i onclick='return editComment(".$e_msg.")' class='fa fa-pencil-square-o text_light_red'></i>";
                                         }
                                         $html .= "<i onclick='return deleteComment(".$row['id'].")' class='fa fa-trash text_light_red'></i>";
                                         
                                         }
                                         }
                                         $html .="<i>".convert_timezone_date($row['created_at']." ".$row['created_time'])."</i></small></p>";
                                         $html .= '<span class="clearfix"></span>
                                         <p class="chat-content mar_tb10">'.$c_msg.'
                                         
                                         <p class="lead emoji-picker-container"><p class="comments '.$class. " ".$r_class.' demo-item" id="item-'.$c.'"> </p>';
                            if(!empty(trim($row["upload_file"])) && $r_class != "removed"){
                 $html .= '<p class="pull-right file-attachment"><a href="'.$file_path.'" target="_blank" class="download"> <i class="fa fa-paperclip mar_r5"></i>'.$row["upload_file"].' </a> </p>
                                         <span class="clearfix"></span>';
                            }
                 $html .= '                    </div>
                                  </div> <div id="out-'.$c.'"></div>
                                  <p class="lead emoji-picker-container"><p class="comments"></p></p></p>';
                
              
                $html .= '</div>
                        </div> </li>';
            }
			else{
			
                $html .= '<li class="row right-comment text-left">
                <div class="col-md-10 col-md-offset-1 col-sm-offset-1 col-sm-9 col-xs-offset-1 col-xs-9 pad_l20"> 
                                      <div class="comment-info">
                                         <p><span class="chat-username">'.$d["user_name"].'</span><small class="chat-datelocation"><i>'.convert_timezone_date($row['created_at']." ".$row['created_time']).'</i></small></p>
                                         <span class="clearfix"></span>';
                $html .= '<p class="lead emoji-picker-container"><p class="comments '.$r_class.$class.'" >';
                $html .= '</p><p class="chat-content mar_tb10">'.$c_msg.'';
				
                 if(!empty(trim($row["upload_file"])) && $r_class != "removed"){
                 $html .= '<p class="pull-right file-attachment"><a href="'.$file_path.'" target="_blank" class="download"> <i class="fa fa-paperclip mar_r5"></i>'.$row["upload_file"].' </a> </p>
                                         <span class="clearfix"></span>';   
                 }
                 $html .= '</p> </p>
                                      </div>
                                  </div>
                                  <div class="col-md-1 col-sm-2 col-xs-2 pad_r10">
                                      <img src="'.$d["user_image"].'" alt="profile icon" class="profile-image pull-right">
                                  </div>';
                $html .= '</li>';
            }
            
        }
    }
    $html .= '</ul>';
    echo $html; die;

}else if($receiver_id != 0){
    $where='';
    if($_POST['search_topic']!=''){ $where.=" AND topic='".$_POST['search_topic']."'";}
    if($_POST['search_keyword']!=''){ $where.=" AND keywords LIKE '%\"".$_POST['search_keyword']."\"%'";}
    if($_POST['search_message']!=''){ $where.=" AND comments LIKE '%".$_POST['search_message']."%'";}
    $sql_query="SELECT * FROM chats WHERE status='Y' AND  (sender_user_id=".$sender_id." OR sender_user_id=".$receiver_id.") AND (receiver_user_id=".$receiver_id." OR receiver_user_id=".$sender_id.") AND group_id=".$group_id." ".$where."  ORDER BY created_at ASC";
    $sql = mysql_query($sql_query) or die("Query Error");
    $count = mysql_num_rows($sql);
    $html = "<ul>";

    if($count > 0) {
        $d = $s = array();
        $d = getUserImage($receiver_id);
        $s = getUserImage($sender_id);
        $currentDate = false;
        $i = $x = 1;
        while ($row = mysql_fetch_array($sql)) {
            $file_path = "assets/chats/".$row["sender_user_id"]."/".$row["upload_file"];
            if ($row['created_at'] != $currentDate){
                $html .='<p class="date_divider"><span class="text_blue fb_500">'.convert_timezone_date($row['created_at']." ".$row['created_time'],'M d, Y').'</span></p>';
                $currentDate = $row['created_at'];
            }
            
            if( ($row['created_at'] != $row['updated_at']) || ($row['created_time'] != $row['updated_time']) ){
                $class = "edited";
            }else{
                $class = "";
            }
            if($row["deleted_at"] == "0000-00-00" ||  $row["deleted_at"] == NULL ){
                $c_msg = $row["comments"];
                $r_class = "";
            }else{
                $c_msg = "Message Removed";
                $r_class = "removed";
            }
            if($row['sender_user_id'] == $sender_id){
                $html .=  '<li class="row left-comment text-left">';
                $e_msg = json_encode(array(
                    'message' => $row['comments'],
                    'id' => $row['id']
                ));
                $c = $i++;
				
                $html .= ' <div class="col-md-1 col-sm-2 col-xs-2">
                                      <img src="'.$s["user_image"].'" alt="profile icon" class="profile-image">
                            </div>
                            <div class="col-md-10 col-sm-9 col-xs-9 pad_l20">
                                      <div class="comment-info">';
                                      $html .= "<p><span class='chat-username'>".$current_user_name."</span><small class='chat-datelocation'>";
                                      if($current_date == $row["created_at"]){
                                        $to_time = strtotime($current_time);
                                        $from_time = strtotime($row["created_time"]);
                                        $time =  round(abs($to_time - $from_time) / 60,2);
                                        if($time <= 10){
                                        if(empty(trim($row["upload_file"]))){
                                         $html .= "<i onclick='return editComment(".$e_msg.")' class='fa fa-pencil-square-o text_light_red'></i>";
                                         }
                                         $html .= "<i onclick='return deleteComment(".$row['id'].")' class='fa fa-trash text_light_red'></i>";
                                         
                                         }
                                         }
                                         $html .="<i>".convert_timezone_date($row['created_at']." ".$row['created_time'])."</i></small></p>";
                                         $html .= '<span class="clearfix"></span>
                                         <p class="chat-content mar_tb10">'.$c_msg.'
                                         
                                         <p class="lead emoji-picker-container"><p class="comments '.$class. " ".$r_class.' demo-item" id="item-'.$c.'">';
                             if(!empty(trim($row["upload_file"])) && $r_class != "removed"){
                             $html .= '<p class="pull-right file-attachment"><a href="'.$file_path.'" target="_blank" class="download"> <i class="fa fa-paperclip mar_r5"></i>'.$row["upload_file"].'</a> </p>
                                                     <span class="clearfix"></span>';
                                        }
                             $html .= '                    </p> </div>
                                              </div> <div id="out-'.$c.'"></div>
                                              <p class="lead emoji-picker-container"><p class="comments"></p></p></p>';
                            
                          
                            $html .= '</div></div> </li>';
                            
            }else{
                $y = $x++;
				
                $html .= '<li class="row right-comment text-left">
                <div class="col-md-10 col-md-offset-1 col-sm-offset-1 col-sm-9 col-xs-offset-1 col-xs-9 pad_l20"> 
                                      <div class="comment-info">
                                         <p><span class="chat-username">'.$d["user_name"].'</span><small class="chat-datelocation"><i>'.convert_timezone_date($row['created_at']." ".$row['created_time']).'</i></small></p>
                                         <span class="clearfix"></span>';
                $html .= '<p class="lead emoji-picker-container"><p class="comments '.$r_class.$class.'" >';
                if($r_class != "removed"){
                    $html .= $row["comments"];
                }else if($r_class == "removed"){
                    $html .= "Message Removed";
                }
                
                 if(!empty(trim($row["upload_file"])) && $r_class != "removed"){
                 $html .= '<p class="pull-right file-attachment"><a href="'.$file_path.'" target="_blank" class="download"><i class="fa fa-paperclip mar_r5"></i>'.$row["upload_file"].'</a> </p>
                                         <span class="clearfix"></span>';   
                 }
                 $html .= '</p>
                                      </div>
                                  </div>
                                  <div class="col-md-1 col-sm-2 col-xs-2 pad_r10">
                                      <img src="'.$d["user_image"].'" alt="profile icon" class="profile-image pull-right">
                                  </div>';
                $html .= '</li>';
            }
            
        }
    }
    $html .= '</ul>';
    echo $html;

}

?>
