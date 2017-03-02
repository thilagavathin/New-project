<?php include_once('header.php');
include_once('helpers.php');
date_default_timezone_set('America/New_York');
$today = date("l, F j, Y, g:i A");
$num_rec_per_page=10;
if (isset($_REQUEST["page"])) { $cPage  = $_REQUEST["page"]; } else { $cPage=1; }
$start_from = ($cPage-1) * $num_rec_per_page;

$sql_discussion=mysql_query("SELECT id,title,content,image,video,file,view_count,likes_count,userid,createduser,created_date,now() as cur FROM community_discussion ORDER BY created_date desc LIMIT $start_from, $num_rec_per_page");
$discussion_count=mysql_num_rows($sql_discussion);
$sql_discussion_total=mysql_query("SELECT id FROM community_discussion ORDER BY created_date desc");
$discussion_total=mysql_num_rows($sql_discussion_total);
$sql_users=mysql_query("SELECT count(user_id) FROM login_users");
$user_count=mysql_fetch_row($sql_users);
if($_SESSION['userrole']==3){
    $get_user_agen_q="SELECT agency_id FROM TTA_Forms WHERE assignedUser='".$_SESSION['adminlogin1']."' group by agency_id ";
    $get_user_agen = mysql_query($get_user_agen_q);
    $agency_join='';
    while($row=mysql_fetch_array($get_user_agen)) {
        $agency_join.=$row['agency_id'].',';
    }
    $agency_in=substr($agency_join, 0, -1);
    if($_SESSION['userrole']==3 ){
    $get_user_agency=mysql_query("SELECT A.id FROM agency A inner join login_users U on U.AgencyName=A.name WHERE U.user_id=".$_SESSION['adminlogin']);
    $user_agency_row=mysql_fetch_row($get_user_agency);

    if(isset($user_agency_row[0]))
    {
        if($agency_in=='') $agency_in=$user_agency_row[0];
        else $agency_in.=','.$user_agency_row[0];
    }
    $user_base_agency=" and agency_id in (".$agency_in.")";
    }
    else $user_base_agency=" and agency_id in (0)";
}
elseif($_SESSION['userrole']==2 ){ $user_base_agency=" and agency_id in (0)";}
else { $agency_in=''; $user_base_agency=''; }

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
$agency_comment= mysql_query("SELECT COUNT(comment) flag FROM TTA_Report_comment WHERE create_date>='".$report_start."' AND create_date <='".$report_end."' AND status='N' AND normal_status='N' ".$user_base_agency);
$comment=mysql_fetch_row($agency_comment);
$tta_comment=mysql_query("SELECT COUNT(id) FROM tta_regarding_notes where view_status='N'".$user_base_agency);
$comment_tta=mysql_fetch_row($tta_comment);
$community_comment=mysql_query("SELECT COUNT(id) FROM community_comments where view_status='N'");
$community_count=mysql_fetch_row($community_comment);
?>
<!-- START PAGE CONTENT WRAPPER -->
<div class="page-content-wrapper">
    <!-- START PAGE CONTENT -->
    <div class="content sm-gutter">
        <section class="bg-white">
        <!-- START CONTAINER FLUID -->
            <div class="container-fluid">
                <!-- START ROW -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="user-welcome">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="welcome-text">
                                        <h4>Welcome to ECCO!</h4>
                                        <h2 class="no-margin"><?php
                                            if(date('G')<=12) echo 'Good Morning';
                                            elseif(date('G')<=17) echo 'Good Afternoon';
                                            else echo 'Good Evening';
                                            echo ' '.ucfirst($user_name); ?></h2>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="pull-right">
                                        <ul class="list-inline">
                                            <li class="timezone-widget">
                                                <div class="pull-left icon"><i class="pg-clock"></i></div>
                                                <div class="pull-left">
                                                    <h3 class="no-margin"><?php echo date("g:i");?><span class="am-pm"> <?php echo date("A");?></span> <small class="fs-12">EST</small></h3>
                                                    <h4 class="no-margin"></h4>
                                                    <p class="small hint-text"><?php echo date("l, jS F Y");?></p>
                                                </div>
                                            </li>
                                            <li class="notification-info">
                                                <h5>You Have: </h5>
                                                <ul class="no-style">
                                                    <?php if($comment_tta<>0) { ?> <li><a href="dashboard.php"><i class="fa fa-bell"></i> TTA Updates</a></li> <?php } ?>
                                                    <?php if($comment[0]<>0) {?> <li><a href="reportdashboard.php" target="_self"><i class="pg-comment"></i> Report Comments </a></li><?php }  ?>
                                                    <?php if($community_count<>0) { ?>    <li><a href="discussions.php"><i class="pg-mail"></i> Community Messages</a></li> <?php } ?>
                                                    <?php if($comment[0]==0 && $comment_tta==0 && $community_count==0) { ?> <li class="no-info"><i class="fa fa-info-circle"></i> No events found</li> <?php } ?>
                                                </ul>
                                            </li>
                                        </ul>



                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END ROW -->
            </div>
        <!-- END CONTAINER FLUID -->
        </section>


        <section>
            <!-- START CONTAINER FLUID -->
            <div class="container-fluid">
            <!-- START ROW -->
            <div class="row m-t-40">
                <div class="col-md-2">
                    <!-- START WIDGET -->
                    <div class="panel quicknav">
                        <div class="panel-heading">
                            <div class="panel-title">
                                Quick Links
                            </div>
                        </div>
                        <div class="panel-body">
                            <ul class="no-style t-black">
                                <li><a href="http://ga-sps.org/resources" target="_blank">Resources</a></li>
                                <li><a href="http://ga-sps.org/training" target="_blank">Training Center</a></li>
                                <li><a href="http://ga-sps.org/calendar" target="_blank">Calendar</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- END WIDGET -->
                </div>

                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="discussion discussion-list">

                                <h1 class="title text-primary">Discussions</h1>

                                <!-- End Discussion Header -->
                                <div class="m-t-30">
                                    <div class="posts-container">
                                        <?php
                                        if($discussion_count>0) {
                                            while ($ls = mysql_fetch_array($sql_discussion)) {
                                                ?>
                                                <div class="panel post-row">
                                                    <div class="container-xs-height">
                                                        <div class="row-xs-height">
                                                            <div
                                                                class="social-user-profile col-xs-height text-center col-top">
                                                                <div class="thumbnail-wrapper d48 circular bg-success b-success">
                                                                <?php
                                                                $get_img=get_user_image($ls['userid']);
                                                                if($get_img=='')  $img_val ="assets/img/avatar-male.png";
                                                                else $img_val ="assets/profile/".$get_img;
                                                                ?>
                                                                    <img alt="Avatar" src="<?php echo $img_val; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-height p-l-40 p-r-20">
                                                                <a href="discussion_blog.php?id=<?php echo $ls['id'];?>"><h5 class="no-margin post-title"><?php echo $ls['title']; ?></h5></a>

                                                                <p class="posting-text ellipsis no-margin fs-14"><?php echo $ls['content']; ?></p>

                                                                <div class="post-footer hint-text small">
                                                                    <span class="user-name"><a href="#"><?php echo $ls['createduser']; ?></a></span>

                                                                    <div class="pull-right">
                                                                        <ul class="reactions">
                                                                            <li> <?php  echo dateDiff($ls['created_date'],$ls['cur']); ?> ago <i class="fa fa-clock-o"></i></li>


                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                        }
                                        else
                                        {
                                        ?>
                                        <div class="panel post-row">
                                            <div class="container-xs-height">
                                                <div class="row-xs-height">
                                                    <div class=" m-b-20 ">
                                                       <h5 class="no-margin post-title">No Record </h5>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                        <!-- / Post:1 -->

                                        <nav class="text-center m-b-20">
                                            <ul class="pagination">
                                                 <?php
                                                    $total_pages = ceil($discussion_total / $num_rec_per_page);
                                                    $previous = $cPage - 1;
                                                    $next = $cPage + 1;
                                                    ?>
                                                    <li><a href='reportdashboard.php?page=1'><<</a></li>
                                                    <?php
                                                    if($cPage != 1)
                                                        echo "<li><a href='discussions.php?page=".$previous."'>".'<'."</a></li> ";

                                                    for ($i=1; $i<=$total_pages; $i++) {
                                                        get_availPage($i,$cPage,$page_url);
                                                    };
                                                    if($cPage != $total_pages)
                                                        echo "<li><a href='discussions.php?page=".$next."'>".'>'."</a></li> ";

                                                    echo "<li><a href='discussions.php?page=$total_pages'>".'>>'."</a></li> ";

                                                    function get_availPage($i,$cPage){
                                                        if($i < ($cPage + 10) && $i >= $cPage){ ?>
                                                            <li><a href='discussions.php?page=<?php echo $i; ?>' <?php if($cPage == $i){ ?>class="active_Pagination"<?php } ?> ><?php echo $i; ?></a></li>
                                                        <?php }
                                                    } ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="join-group">
                                <div class="bg-dark-blue">
                                    <div class="p2">
                                        <h5 class="text-white no-margin">Join the commuinty </h5>
                                        <p class="text-white">To discussions  of general topics.</p>
                                        <a href="discussion_create.php" target="_self" class="btn btn-lg btn-yellow">Start New Topic</a>
                                    </div>
                                </div>

                            </div>
                            <div class="panel">
                                <div class="members members-count">
                                    <span class="pull-right"><span class="members-count"><?php echo $user_count[0]; ?></span></span>
                                    <h5 class="text-primary no-margin">Members</h5>
                                    <a href="users.php" target="_self" class="text-blue">View all members</a>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
            <!-- END ROW -->
            </div>
            <!-- END CONTAINER FLUID -->
        </section>



    </div>
    <!-- END PAGE CONTENT -->
    <!-- START COPYRIGHT -->
    <!-- START CONTAINER FLUID -->
    <div class="container-fluid container-fixed-lg footer">
        <div class="copyright sm-text-center">
                <p class="small no-margin pull-left sm-pull-reset col-sm-4">
                    <span class="hint-text">Copyright © <?php echo date('Y'); ?> </span>
                    <span class="font-montserrat">Prospectus Group, LLC.</span>
                    <span class="hint-text">All rights reserved. </span>
                </p>
                <p class="col-sm-4 text-center foot_logo">
                    <img src="assets/img/pgroup_full_new.png" width="250" alt="Powered by Progroup">
                </p>
                <p class="small no-margin pull-right sm-pull-reset col-sm-4">
                    <span class="sm-block"><a href="#" class="m-l-10 m-r-10">Terms of use</a> <span class="muted">&#8226;</span> <a href="#" class="m-l-10">Privacy Policy</a></span>
                </p>
                <div class="clearfix"></div>
            </div>
    </div>
    <!-- END COPYRIGHT -->
</div>
<!-- END PAGE CONTENT WRAPPER -->
</div>
<?php include_once('footer.php'); ?>