<?php include_once('header.php');

if(isset($_POST['discussion_id']))
{
  $discussion_id=$_POST['discussion_id'];
  $sql_discussion=mysql_query("SELECT id,title,content,image,video,file,view_count,likes_count,userid,createduser,created_date,now() as cur FROM community_discussion WHERE userid=".$_SESSION['adminlogin']." AND id=".$discussion_id);
  $discussion=mysql_fetch_assoc($sql_discussion);
    $discussion_id=isset($discussion['id'])? $discussion['id']:'';
}
else $discussion_id='';
?>
<link href="assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="assets/plugins/fileuploader/css/jquery.fileupload.css">
<link rel="stylesheet" href="assets/plugins/fileuploader/css/jquery.fileupload-ui.css">



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
                    <li><a href="#" class="active">New post</a>
                    </li>
                </ul>
                <!-- END BREADCRUMB -->


                <!-- START ROW -->
                <div class="row">

                    <div class="col-md-8 col-md-offset-2 col-sm-10">
                        <form id="fileupload" action="chk.php" method="POST" enctype="multipart/form-data">
                        <div class="new-post">
                            <h2 class="title text-primary">Start new discussion here</h2>
                            <div class="post-form">

                                <div class="form-group">
                                    <input name="discussion_title" id="discussion_title" type="text" placeholder="Type a short summary or title" value="" class="form-control input-lg">
                                </div>



                                <div class="m-t-40 m-b-20">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="fs-14 ">Upload files</p>
                                        </div>

                                        <div class="col-md-12">

                                            <div class="pull-left">


                                                <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                                                <div class="fileupload-buttonbar">
                                                    <!-- The fileinput-button span is used to style the file input field as button -->

                                                    <button type="button" class="btn btn-success fileinput-button">
                                                        <i class="fa fa-image"></i>
                                                        <span>Images</span>
                                                        <input type="file" name="files[]" multiple>
                                                    </button>

                                                    <button type="button" class="btn btn-warning fileinput-button" >
                                                        <i class="fa fa-paperclip"></i>
                                                        <span>Attachments</span>
                                                        <input type="file" name="files[]" multiple>
                                                    </button>


                                                    <!-- The global file processing state -->
                                                    <span class="fileupload-process"></span>
                                                    <!-- The global progress state -->
                                                    <div class="col-md-12 fileupload-progress fade">
                                                        <!-- The global progress bar -->
                                                        <div class="progress progress-bar progress-bar-primary active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                                        </div>
                                                        <!-- The extended global progress state -->
                                                        <div class="progress-extended">&nbsp;</div>
                                                    </div>
                                                </div>
                                                <!-- The table listing the files available for upload/download -->
                                                <table role="presentation" class="table table-striped no-margin "><tbody class="files"></tbody></table>

                                            </div>


                                        </div>

                                        <div class="col-md-12">
                                            <!-- Video External link -->
                                            <div class="m-b-20">

                                                <div class="form-group">
                                                    <label>Insert Video URL</label>
                                                    <input type="text" name="video_url" id="video_url" class="form-control " placeholder="Copy URL Here" value="">

                                                </div>

                                            </div>
                                            <!-- End: Video External link -->
                                        </div>

                                    </div>



                                </div>









                                <div class="form-group">
                                    <div class="wysiwyg5-wrapper b-a b-grey">
                                        <textarea id="wysiwyg5" name="discussion_content" class="wysiwyg demo-form-wysiwyg" placeholder="Enter text ..."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div class="m-t-40 text-right">
                                            <button type="button" class="btn btn-link">Cancel</button>
                                            <button type="button" onclick="discussions_post();" class="btn btn-primary">Post</button>
                                        </div>
                                    </div>
                                </div>



                            </div>
                        </div>
                        </form>
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
    function discussions_post()
    {
        var discussion_title=$('#discussion_title').val();
        var discussion_content=$('#wysiwyg5').val();
        var video_url=$('#video_url').val();
        var formData = {discussion_title:discussion_title,discussion_content:discussion_content,video_url:video_url};
        $.ajax({
            url : "insert_discussion.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                if(data=='success') window.location = "discussions.php";
                else if(data=='invalid')  alert('please check input contents ');
                else alert('Due to internet problem not reachable database ,Try again');
                return false;
            }
        });
    }
    function discussions_update()
    {
        var discussion_title=$('#discussion_title').val();
        var discussion_content=$('#wysiwyg5').val();
        var discussion_id=$('#editdiscussion_id').val();
        var formData = {discussion_title:discussion_title,discussion_content:discussion_content,discussion_id:discussion_id};
        $.ajax({
            url : "update_discussion.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                if(data=='success') window.location = "discussions.php?id="+discussion_id;
                else if(data=='invalid')  alert('please check input contents ');
                else alert('Due to internet problem not reachable database ,Try again');
            }
        });
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

<!-- BEGIN UPLOADER -->

<script src="assets/plugins/fileuploader/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="http://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="assets/plugins/fileuploader/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="assets/plugins/fileuploader/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="assets/plugins/fileuploader/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="assets/plugins/fileuploader/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="assets/plugins/fileuploader/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="assets/plugins/fileuploader/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="assets/plugins/fileuploader/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="assets/plugins/fileuploader/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="assets/plugins/fileuploader/main.js"></script>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade">
            <td>
                <span class="preview"></span>
            </td>
            <td>
                <p class="name">{%=file.name%}</p>
                <strong class="error text-danger"></strong>
            </td>
            <td>
                <p class="size">Processing...</p>
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
            </td>
            <td>
                {% if (!i && !o.options.autoUpload) { %}
                    <button class="btn btn-primary start" disabled>
                        <i class="glyphicon glyphicon-upload"></i>
                        <span>Start</span>
                    </button>
                {% } %}
                {% if (!i) { %}
                    <button class="btn btn-warning cancel">
                        <i class="glyphicon glyphicon-ban-circle"></i>
                        <span>Cancel</span>
                    </button>
                {% } %}
            </td>
        </tr>
    {% } %}
    </script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
            <td>
                <span class="preview">
                    {% if (file.thumbnailUrl) { %}
                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                    {% } %}
                </span>
            </td>
            <td>
                <p class="name">
                    {% if (file.url) { %}
                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                    {% } else { %}
                        <span>{%=file.name%}</span>
                    {% } %}
                </p>
                {% if (file.error) { %}
                    <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
            </td>
            <td>
                <span class="size">{%=o.formatFileSize(file.size)%}</span>
            </td>
            <td>
                {% if (file.deleteUrl) { %}
                    <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                        <i class="glyphicon glyphicon-trash"></i>
                        <span>Delete</span>
                    </button>

                {% } else { %}
                    <button class="btn btn-warning cancel">
                        <i class="glyphicon glyphicon-ban-circle"></i>
                        <span>Cancel</span>
                    </button>
                {% } %}
            </td>
        </tr>
    {% } %}
    </script>

<!-- END UPLOADER -->
