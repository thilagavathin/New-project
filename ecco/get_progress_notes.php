<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$contract= $_POST['contract'];
$regarding_sql=mysql_query("SELECT C.username,user_image,comments,created_date FROM tta_progress_notes C inner join login_users U on U.user_id=C.user_id WHERE contract_num='".$contract."' order by C.id DESC ");

while($ls= mysql_fetch_array($regarding_sql) ) {
    if (trim($ls['user_image']) <> '') {
        $user_img = @unserialize($ls['user_image']);
        $img_val = "assets/profile/" . $user_img[0];
    } else $img_val = "assets/img/photo.jpg";

    ?>

    <div class="quickview-comments comments-wrapper">
        <div class="card share comment">
            <div class="circle" data-toggle="tooltip" title="" data-container="body" data-original-title="Label">
            </div>
            <div class="card-header clearfix">
                <div class="user-pic">
                    <img alt="Profile Image" width="33" height="33" src="<?php echo $img_val;?>">
                </div>
                <h5><?php echo $ls['username'];?></h5>
                <h6><?php echo date('d M Y h:i A',strtotime($ls['created_date'])); ?>
                    <span class="location semi-bold"><i class="fa fa-map-marker"></i> NYC, New York</span>
                </h6>
            </div>
            <div class="card-description">
                <p><?php echo $ls['comments']; ?></p>
            </div>
        </div>
    </div>
    <?php
}
?>