<?php include_once('header.php');
include_once('helpers.php');
$dis_id=$_GET["id"];
$sql_discussion=mysql_query("SELECT id,title,content,image,video,file,view_count,likes_count,userid,createduser,created_date,now() as cur FROM community_discussion WHERE id=".$dis_id);
$discussion=mysql_fetch_assoc($sql_discussion);
$sql_comments=mysql_query("SELECT discussion_id,id,username,userid,created_date,comments FROM community_comments WHERE discussion_id=".$dis_id);
$comments_count=mysql_num_rows($sql_comments);
?>
<link href="assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
    <!-- START PAGE CONTENT WRAPPER -->
    <div class="page-content-wrapper">
        <!-- START PAGE CONTENT -->
        <div class="content sm-gutter">
        <section>
        <!-- START CONTAINER FLUID -->
        <div class="container-fluid">

        <!-- START BREADCRUMB -->
        <ul class="breadcrumb">
            <li>
                <a href="systemdashboard.php">Dashboard</a>
            </li>
            <li>
                <a href="discussions.php">Discussions</a>
            </li>
            <li><a href="#" class="active">Discussion Details </a>
            </li>
        </ul>
        <!-- END BREADCRUMB -->


        <!-- START ROW -->
        <div class="row">

            <div class="col-md-12">

                <div class="single-post">
                    <div class="posts-container">
                        <div class="post-row">
                            <div class="container-xs-height">
                                <div class="row-xs-height">
                                    <div class="social-user-profile col-xs-height text-center col-top">
                                        <div class="thumbnail-wrapper d48 circular bg-success b-success">
                                            <img alt="Avatar" src="assets/img/avatar-male.png">
                                        </div>
                                    </div>
                                    <div class="col-xs-height p-l-40">
                                       <?php if($_SESSION['adminlogin']==$discussion['userid']) { ?> <?php } ?>
                                        <h3 class="no-margin post-title"><?php echo $discussion['title'];?></h3>
                                        <div class=" m-t-10 m-b-20">
                                            <ul class="list-inline">
                                                <li><span class="user-name"><a href="#" class="text-info"><?php echo $discussion['createduser'];?></a></span></li>

                                            </ul>
                                        </div>


                                        <div class="panel">

                                            <div class="post-full-content">
                                                <?php
                                                if(trim($discussion['video'])<>'') {?>
                                                <div class="media-container m-b-40 ">
                                                    <div class="embed-responsive embed-responsive-16by9 "> <iframe class="embed-responsive-item" src="<?php echo $discussion['video']; ?>" allowfullscreen=""></iframe> </div>
                                                </div>
                                                <?php } ?>
                                                <p> <?php echo $discussion['content']; ?> </p>
                                                <?php if(trim($discussion['file'])<>'' || trim($discussion['image'])<>'') {
                                                    $image_ar=unserialize($discussion['image']);
                                                    $file_ar=unserialize($discussion['file']);
                                                    ?>
                                                <div class="post-attachments">
                                                    <p class="fs-16 m-b-20 text-primary">Attachments</p>
                                                    <ul class="list-inline">
                                                        <?php
                                                        foreach($image_ar as $imgs)
                                                        {
                                                        ?>
                                                            <li>
                                                                <a class="" href="discussion/<?php echo $imgs;?>" target="_blank">
                                                            <span class="col-xs-height">
                                                            <span class="thumbnail-wrapper d32 circular bg-info text-white">
                                                               <i class="fa fa-file-text-o"></i>
                                                            </span>
                                                            </span>
                                                                    <p class="p-l-20 p-r-20  col-xs-height col-middle ">
                                                                        <span class="block"><span> <?php echo $imgs;?> </span></span>

                                                                    </p>
                                                                </a>
                                                            </li>
                                                        <?php
                                                        }
                                                        foreach($file_ar as $imgs)
                                                        {
                                                            ?>
                                                            <li>
                                                                <a class="" href="discussion/<?php echo $imgs;?>" target="_blank">
                                                            <span class="col-xs-height">
                                                            <span class="thumbnail-wrapper d32 circular bg-info text-white">
                                                               <i class="fa fa-file-text-o"></i>
                                                            </span>
                                                            </span>
                                                                    <p class="p-l-20 p-r-20  col-xs-height col-middle ">
                                                                        <span class="block"><span><?php echo $imgs;?></span></span>

                                                                    </p>
                                                                </a>
                                                            </li>
                                                            <?php
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php if($comments_count>0)
                                            {
                                            while($ls= mysql_fetch_array($sql_comments) ) {
                                                ?>
                                                <div class="panel post-row post-reply">
                                                    <div class="container-xs-height">
                                                        <div class="row-xs-height">
                                                            <div class="social-user-profile col-xs-height text-center col-top">
                                                                <div class="thumbnail-wrapper d48 circular bg-success b-success">
                                                                    <?php
                                                                    $get_img=get_user_image($ls['userid']);
                                                                    if($get_img=='')  $img_val ="assets/img/avatar-male.png";
                                                                    else $img_val ="assets/profile/".$get_img;
                                                                    ?>
                                                                    <img alt="Avatar" src="<?php echo $img_val; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-height p-l-30 p-r-10">
                                                                <div class="hint-text m-t-10 m-b-30">
                                                                    <span class="user-name"><a href="#" class="fs-16 text-primary"><?php echo $ls['username']; ?></a></span>
                                                                    <ul class="list-inline pull-right">
                                                                        <li></li>
                                                                        <li>
                                                                            <ul class="reactions no-padding">
                                                                                <li> <?php  echo dateDiff($ls['created_date'],$discussion['cur']); ?> ago <i
                                                                                        class="fa fa-clock-o"></i></li>

                                                                            </ul>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="reply-text">
                                                                    <p class="posting-text no-margin fs-16"> <?php echo $ls['comments']; ?> </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            }
                                            ?>
                                            <div class="panel-footer bg-white">
                                                <div class="padding-20">
                                                    <button class="btn btn-primary" data-toggle="collapse" data-target="#user-addnewcommentform" aria-expanded="false" aria-controls="collapseExample"><b>  Add reply</b></button>
                                                    <div class="collapse" id="user-addnewcommentform">
                                                        <form class="m-t-20" method="post" action="#">
                                                            <div class="wysiwyg5-wrapper b-a b-grey">
                                                                <textarea id="wysiwyg5" name="discussion_comments" class="wysiwyg demo-form-wysiwyg" placeholder="Enter text ..."></textarea>
                                                            </div>
                                                            <div class="m-t-10 text-right">
                                                                <input type="hidden" name="discussionid" value="<?php echo $dis_id; ?>">
                                                                <button type="button" onclick="comment_post();" class="btn btn-success">Submit</button></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- / Full post content -->
                                        <form id="editdiscussion" method="post" action="discussion_create.php">
                                            <input type="hidden" name="discussion_id" value="<?php echo $dis_id; ?>">
                                        </form>

                                    </div>
                                </div>
                            </div>



                        </div><!-- / Single Post -->

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
<!-- END PAGE CONTAINER -->
<script>
    function comment_post()
    {
        var discussion_comments=$('#wysiwyg5').val();
        var discussionid=$('input[name="discussionid"]').val();
        var formData = {discussion_comments:discussion_comments,discussionid:discussionid};
        $.ajax({
            url : "insert_comment.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                if(data=='success') window.location = "discussion_blog.php?id="+discussionid;
                else if(data=='invalid')  alert('please check input contents ');
                else alert('Due to internet problem not reachable database ,Try again');
            }
        });
    }
    function discussion_edit()
    {

        $("#editdiscussion").submit();
    }
</script>
<?php include_once('footer.php'); ?>
<script src="assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-autonumeric/autoNumeric.js"></script>
<script type="text/javascript" src="assets/plugins/dropzone/dropzone.min.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>
<script src="assets/plugins/boostrap-form-wizard/js/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="assets/plugins/summernote/js/summernote.min.js" type="text/javascript"></script>
<script src="assets/plugins/moment/moment.min.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>