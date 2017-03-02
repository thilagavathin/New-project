<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$strategy=$_POST['strategy'];
$node= $_POST['node'];
$agency= $_POST['agency'];
$user_id=$_SESSION['adminlogin'];
$regarding_sql=mysql_query("SELECT comments,created_date, user_id FROM wb_comments WHERE intervention_id='".$strategy."' AND node_id='".$node."' order by id DESC ");

function getUserImage($id){
    $query = mysql_query("SELECT user_id, user_level, username, name, user_image, region  FROM login_users WHERE approved='YES' AND user_id=".$id) or die("Query 4 error");
    $data = array();
    while($q_row = mysql_fetch_array($query)){
        if($q_row["user_image"] <> '') $user_image=@unserialize($q_row["user_image"]); else $user_image='';
        if($user_image=='')  $img ="assets/img/photo.jpg";
        else $img ="assets/profile/".$user_image[0];
        $data['user_image']= $img;
        $data['user_name'] = $q_row["username"];
    }
    return $data;
}
?>
							<div class="modal-footer no-border">		
                                <ul>
								<?php
$num_rows=0; $u_id =0;
$num_rows = mysql_num_rows($regarding_sql);
while($ls= mysql_fetch_array($regarding_sql) ) {

    $user_image = getUserImage($ls["user_id"]);
    $img_val = $user_image['user_image'];
    $user_name = $user_image['user_name'];

if(empty($img_val)) {
$img_val = "assets/img/photo.jpg";
}
    ?>
                                  <!-- left comment area -->
                                   <li class="row left-comment text-left">
                                      <div class="col-md-2 col-sm-2">
                                          <img src="<?php echo $img_val;?>" alt="profile icon" class="profile-image">
                                      </div>
                                      <div class="col-md-10 col-sm-10 pad_l0">
                                          <div class="comment-info">
                                             <p><span class="chat-username"><?php echo $user_name;?></span><small class="chat-datelocation"><i><?php echo date('d M Y h:i A',strtotime($ls['created_date'])); ?> NYC, New York</i></small></p>
                                             <span class="clearfix"></span>
                                             <p class="chat-content mar_tb10"><?php echo $ls['comments']; ?>
                                             </p>
                                          </div>
                                      </div>
                                   </li>
    <?php
}
date_default_timezone_set('America/New_York');
$current_date = date('Y-m-d H:i:s');
$sql_reg=mysql_query("SELECT id FROM wb_comments_status WHERE user_id='".$user_id."' AND intervention_id='".$strategy."' AND node_id='".$node."'");
$row_reg=mysql_fetch_row($sql_reg);

if($row_reg[0]) mysql_query("UPDATE wb_comments_status SET comment_count=".$num_rows." WHERE agency_id=".$agency." AND user_id='".$user_id."' AND intervention_id='".$strategy."' AND node_id='".$node."'");
else mysql_query("INSERT INTO wb_comments_status (agency_id,user_id,intervention_id,node_id,comment_count,updated_date) VALUES (".$agency.",".$user_id.",'".$strategy."','".$node."',".$num_rows.",'".$current_date."')");
?>
                                  </ul>
                                </div>







