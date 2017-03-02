<?php
$_SESSION['AttachmentUpload'] = array();
include_once('templates/header.php');

?>
<style>
.qq-upload-button {
  background: #284fa3 none repeat scroll 0 0 !important;
  border-radius: 5px !important;
  box-shadow: none !important;
  font-weight: bold;
  padding: 10px 0 !important;
}
a.button {
    cursor: pointer;
    padding: 7px 10px !important;
}
</style>
     		<section >
	     		<div class="container">
				
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li><a href="dashboard.php">Help Dashboard</a></li>              
						  <li class="active">Create Request </li>              
						</ol>
					</div>
				 </div>
				 
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
					<?php
                    #Enquiry Id generation
                    $contract_num = mt_rand( 10000000, 99999999);
                    $contract_num = "TTAREQ-".$contract_num
                    ?>
                      <h1 class="page-title">Create Request <span class="text_red pull-right fb_500 ft_20"><?php echo $contract_num; ?></span></h1>
                    </div>
                </div>
				<form class="ga-form" role="form" autocomplete="off" id="ttaform" method="post" enctype="multipart/form-data">
                
                <div class="row mar_t10">
                    <div class="col-md-12">
                      <div class="panel-body pad0">
                        <div class="panel-group" id="accordion1">
                        <div class="panel">
                          <div class="panel-heading" role="tab" id="headingOne<?php echo $acc_count1; ?>">
                            <h4 class="panel-title">
                              <div class="col-sm-12">
                                  <i class="fa fa-angle-down down_arrow1" aria-hidden="true"></i><a data-toggle="collapse" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne<?php echo $acc_count1; ?>" aria-controls="collapseOne" class="form-title">TA Logistics</a><small class="pull-right text_blue">[Don't forget to click save button below]</small>
                              </div>
                            </h4>
                          </div>
                          <div id="collapseOne<?php echo $acc_count1; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne<?php echo $acc_count1; ?>">
                            <div class="panel-body">
                              <div class="form">
                               <div class="col-xs-12 col-sm-6 col-md-6">
							   <input type="hidden" id="contract_num" name="contract_num" value="<?php echo $contract_num; ?>" />
							   <?php
                                    $ass = mysql_query("SELECT * FROM agency_map WHERE user_id=".$_SESSION['adminlogin']);
                                    $ass_agency_id = "";
                                    if(mysql_num_rows($ass) > 0){
                                        while ($ass_row = mysql_fetch_array($ass)){
                                            $ass_agency_id .= $ass_row["agency_id"];
                                            $ass_agency_id .= ",";
                                        }
                                    }
                                    $ass_agency_id = rtrim($ass_agency_id, ",");
                                    if($_SESSION['userrole']==1) $sql="SELECT distinct(name),id FROM agency order by name asc";
                                    else if($_SESSION['userrole']==3) {
                                        $sql = "SELECT name,agency.id FROM agency WHERE agency.id IN (" . $ass_agency_id . ") GROUP BY name order by name asc ";
                                    }
                                    else {
                                        $sql = "SELECT name,agency.id FROM agency WHERE agency.id IN (" . $ass_agency_id . ") GROUP BY name order by name asc ";
                                    }
                                    $result_mail = mysql_query($sql);
                                    $num_rows = mysql_num_rows($result_mail);
                                    if($num_rows==0)
                                    {
                                        $result_mail='';
                                        $result_mail=mysql_query("SELECT A.id,A.name FROM agency A inner join login_users U on U.AgencyName=A.name WHERE U.user_id=".$_SESSION['adminlogin']);

                                    }
                                    $select_agency_id=isset($_POST['agency_id'])? $_POST['agency_id']:'';
                                    ?>
                                    <div class="form-group">
                                      <label>Please Select Agency</label>
                                      <select class="form-control" data-init-plugin="cs-select" id="agency_id" name="agency_id">
									  <?php
                                            if ($_SESSION['userrole'] == 1 || $_SESSION['userrole'] == 2 || $_SESSION['userrole'] == 4 || $_SESSION['userrole'] == 3) {
                                                echo '<option value="">Select an Agency</option>';
                                            }
                                            if(mysql_num_rows($result_mail) > 0){
                                                while ($row = mysql_fetch_array($result_mail)) { ?>
                                                    <option
                                                        value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $select_agency_id) {
                                                        echo "selected";
                                                    } ?>><?php echo $row['name']; ?></option>
                                                <?php }
                                            }?>

									  </select>
                                    </div>
                                    <div class="form-group">
                                      <label>Requester's Name</label>
                                      <input type="text" class="form-control" placeholder="Referral Name" id="TTA_Referral" name="TTA_Referral" value="<?php echo isset($_POST['TTA_Referral'])? $_POST['TTA_Referral']:''; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <div class="form-group">
                                      <label>Contact Phone</label>
                                      <input type="text" class="form-control" placeholder="Contact Phone Number" id="TTA_Contact_Phone" name="TTA_Contact_Phone" value="<?php echo isset($_POST['TTA_Contact_Phone'])? $_POST['TTA_Contact_Phone']:''; ?>">
                                    </div>
                                    <div class="form-group">
                                      <label>Contact Email</label>
                                      <input type="text" class="form-control" placeholder="Email ID" id="TTA_Email" name="TTA_Email" value="<?php echo isset($_POST['TTA_Email'])? $_POST['TTA_Email']:''; ?>">
                                    </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="panel">
                          <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                              <div class="col-sm-12">
                                  <i class="fa fa-angle-down down_arrow1" role="button" aria-hidden="true"></i><a data-toggle="collapse" data-parent="#accordion1" href="#collapseTwo" aria-controls="collapseTwo" class="form-title">TA Request</a>
                              </div>
                            </h4>
                          </div>
                          <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="panel-body">
                               <div class="form">
							   <?php $TTA_inquiry_type=isset($_POST['TTA_inquiry_type'])? $_POST['TTA_inquiry_type']:''; ?>
                                   <div class="col-xs-12 col-sm-6 col-md-6">
                                      <div class="form-group">
                                        <label>Please Select Type Of Inquiry</label>
                                        <select class="form-control" data-init-plugin="cs-select" id="TTA_inquiry_type" name="TTA_inquiry_type">
                                          <option value="Training" <?php echo ($TTA_inquiry_type=='Training')? 'Selected':''; ?>>Training</option>
										  <option value="Technical Assistance" <?php echo ($TTA_inquiry_type=='Technical Assistance')? 'Selected':''; ?>>Technical Assistance </option>
                                        </select>
                                        <textarea class="form-control mar_t10" placeholder="Write something here" id="TTA_inquiry_notes" name="TTA_inquiry_notes"><?php if(isset($_POST['TTA_inquiry_notes'])) echo $_POST['TTA_inquiry_notes']; ?></textarea>
                                      </div>
									  <?php $regarding=isset($_POST['regarding'])? $_POST['regarding']:''; ?>
                                      <div class="form-group">
                                        <label>Regarding</label>
                                        <select class="form-control" data-init-plugin="cs-select" id="regarding" name="regarding">
                                            <option value="">-select-</option>
										    <option value="Implementation" <?php echo ($regarding=='Implementation')? 'Selected':''; ?>>Implementation</option>
                                            <option value="Capacity" <?php echo ($regarding=='Capacity')? 'Selected':''; ?> >Capacity</option>
                                            <option value="Evaluation" <?php echo ($regarding=='Evaluation')? 'Selected':''; ?> >Evaluation</option>
                                            <option value="Technology" <?php echo ($regarding=='Technology')? 'Selected':''; ?>>Technology</option>
                                            <option value="Other" <?php echo ($regarding=='Other')? 'Selected':''; ?> >Other</option>
                                        </select>
                                      </div>
                                      <div class="form-group">
                                        <label>Discovery Notes</label>
                                        <div>
                                          <a data-target="#usercomments-quickview" data-toggle="modal" onclick="regarding_comments('<?php echo $contract_num; ?>');" class="btn btn-default"><i class="fa fa-comments"></i> Chat</a>
                                        </div>
                                      </div>
									<?php $resources=isset($_POST['resources'])? $_POST['resources']:''; ?>
									<div class="form-group">
										<label>Resources (Select Resources Related To Your Request)</label>
											<select class="form-control resources_select" data-init-plugin="select2" multiple id="resources" name="resources[]">
                                            <?php
                                            $query_resources=mysql_query("SELECT document_name,id FROM documents ");

                                            while($row=mysql_fetch_array($query_resources)) {
                                                $document_link=$row['document_name'];
                                                $document_arr=explode('/',$document_link);
                                                $count_no=count($document_arr)-1;
                                                $document_det=explode('.',$document_arr[$count_no]);
                                                $return1=$document_det[0].' ('.$document_det[1].')';
                                                $return=str_replace('-',' ',$return1);

                                                ?>
                                                <option value="<?php echo $row['id'];?>" <?php if(is_array($resources)) {if(array_search($row['id'], $resources) !== false) {echo 'selected';} } ?> ><?php echo ucfirst($return);?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                  </div>
                                  <div class="col-xs-12 col-sm-6 col-md-6">
                                      <div class="form-group">
                                        <label>Upload Files</label>
                                        <div id="fine-uploader-manual-trigger"></div>
                                        <script type="text/template" id="qq-template-manual-trigger">
                                            <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
                                                <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                                                    <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
                                                </div>
                                                <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                                                    <span class="qq-upload-drop-area-text-selector"></span>
                                                </div>
                                                <div class="buttons">
                                                    <div class="qq-upload-button-selector qq-upload-button">
                                                        <div>Select files</div>
                                                    </div>
                                                </div>
													<span class="qq-drop-processing-selector qq-drop-processing">
														<span>Processing dropped files...</span>
														<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
													</span>
                                                <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                                                    <li>
                                                        <div class="qq-progress-bar-container-selector">
                                                            <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                                                        </div>
                                                        <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                                                        <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                                                        <span class="qq-upload-file-selector qq-upload-file"></span>
                                                        <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                                                        <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                                                        <span class="qq-upload-size-selector qq-upload-size"></span>
                                                        <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
                                                        <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
                                                        <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
                                                        <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                                                    </li>
                                                </ul>

                                                <dialog class="qq-alert-dialog-selector">
                                                    <div class="qq-dialog-message-selector"></div>
                                                    <div class="qq-dialog-buttons">
                                                        <button type="button" class="qq-cancel-button-selector">Close</button>
                                                    </div>
                                                </dialog>

                                                <dialog class="qq-confirm-dialog-selector">
                                                    <div class="qq-dialog-message-selector"></div>
                                                    <div class="qq-dialog-buttons">
                                                        <button type="button" class="qq-cancel-button-selector">No</button>
                                                        <button type="button" class="qq-ok-button-selector">Yes</button>
                                                    </div>
                                                </dialog>

                                                <dialog class="qq-prompt-dialog-selector">
                                                    <div class="qq-dialog-message-selector"></div>
                                                    <input type="text">
                                                    <div class="qq-dialog-buttons">
                                                        <button type="button" class="qq-cancel-button-selector">Cancel</button>
                                                        <button type="button" class="qq-ok-button-selector">Ok</button>
                                                    </div>
                                                </dialog>
                                            </div>
                                        </script>
                                      </div>
									  
                                      <div class="form-group">
                                        <label for="name">What Modality Will Be Used</label>
                                        <div class="modality_list">
                                          <ul>
                                            <li>
                                              <label class="mar0">
                                                <input type="checkbox" value="" name="mod_web" id="modality-web">
                                                <span class="custom-icon checkbox-icon"></span>Web/Screen Share
                                              </label>
                                            </li>
                                            <li>
                                              <label class="mar0">
                                                <input type="checkbox" value="" name="mod_correspondence" id="modality-correspondence">
                                                <span class="custom-icon checkbox-icon"></span>Phone/Correspondence
                                              </label>
                                            </li>
                                            <li>
                                              <label class="mar0">
                                                <input type="checkbox" value="" name="mod_faceface" id="modality-face2face">
                                                <span class="custom-icon checkbox-icon"></span>Face to Face
                                              </label>
                                            </li>
                                            <li>
                                              <label class="mar0">
                                                <input type="checkbox" value="" name="mod_other" id="modality-other">
                                                <span class="custom-icon checkbox-icon"></span>Other
                                              </label>
                                            </li>
                                            <li>
                                              <div class="form-group">
                                                 <input type="text" class="form-control" placeholder="Write something here" id="other_email" name="other_email">
                                              </div>
                                            </li>
                                            <li>
                                              <label class="mar0">
                                                <input type="checkbox" value="" name="mod_combination" id="modality-combination">
                                                <span class="custom-icon checkbox-icon"></span>Combination
                                              </label>
                                            </li>
                                          </ul>
                                        </div>
                                      </div>
                                  </div>
                                </div> 
                            </div>
                          </div>
                        </div>
                        
                        <div class="row mar_tb40">
                          <div class="col-md-offset-3 col-md-6 col-sm-12 col-xs-12">
                              <div class="form-group">
                                  <label>Description Of Service Requested</label>
                                  <textarea class="form-control" id="TTA_desc" placeholder="Describe Requested Service Here" name="TTA_desc"><?php echo isset($_POST['TTA_desc'])? $_POST['TTA_desc']:''; ?></textarea>
                              </div>
                                  
                              <div class="col-xs-12 col-sm-12 text-center form">
                                <a class="button" data-toggle="modal" data-target="" onclick="ajax_insert_ttaenq();">Save</a>
                                <a class="mar_l10 cancel_btn button">Clear</a>
                              </div>     
                          </div>
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
 <!--START QUICKVIEW -->
<div id="quickview" class="quickview-wrapper" data-pages="quickview">

    <ul class="no-style m-t-40 padding-30">
        <li><a href="#"><i class="sl-settings"></i> Settings</a>
        </li>
        </li>
        <li><a href="#"><i class="sl-question"></i> Help</a>
        </li>
        <li class="m-t-20">
            <a href="#" class="clearfix">
                <span class="pull-left">Logout</span>
                <span class="pull-right"><i class="sl-logout"></i></span>
            </a>
        </li>
    </ul>
    <a class="btn btn-default quickview-toggle" data-toggle-element="#quickview" data-toggle="quickview"><i class="pg-close"></i></a>

</div>
<!-- END QUICKVIEW-->
<div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-success">Successfully Completed</h4>
            </div>
            <div class="modal-body">
                <div class="text-center m-t-30">
                    <p>Thank you</p>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- MODAL COMMENTS -->
<div class="modal fade modal-bottom-full slide-right" id="usercomments-quickview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content-wrapper">
            <div class="modal-content ">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                </button>
                <div class="container-xs-height ">
                    <div class="row-xs-height">
                        <div class="modal-body col-middle">
                            <h5 class="text-primary m-b-20">Comments and Question</h5>
                            <div id="loading_comments" class="loading" style="display: none;"></div>
                            <hr>
                            <div class="m-b-20">
                                <button class="btn btn-primary" data-toggle="collapse" data-target="#user-addnewcommentform" aria-expanded="false" aria-controls="collapseExample"><b><i class="fs-14 pg-plus"></i> Write your comment here</b></button>
                                <div class="collapse" id="user-addnewcommentform">
                                    <form class="m-t-20">
                                        <textarea type="text" id="regarding_notes" name="regarding_notes" rows="2" placeholder="write your comment here" class="form-control"></textarea>
                                        <div class="m-t-10 text-right"><button type="button" onclick="add_comments();" class="btn btn-success">Submit</button></div>
                                        <input type="hidden" id="com_agency" name="com_agency" value="" >
                                        <input type="hidden" id="com_contract" name="com_contract" value="" >

                                    </form>
                                </div>
                            </div>
                            <div id="comment_status"></div>
                            <div id="htmlcomment"> </div>




                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- END MODAL -->

<!-- Progress MODAL COMMENTS -->
<div class="modal fade modal-bottom-full slide-right" id="progresscomments-quickview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content-wrapper">
            <div class="modal-content ">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                </button>
                <div class="container-xs-height ">
                    <div class="row-xs-height">
                        <div class="modal-body col-middle">
                            <h5 class="text-primary m-b-20">Comments and Question</h5>
                            <div id="loading_progress" class="loading" style="display: none;"></div>
                            <hr>
                            <div class="m-b-20">
                                <button class="btn btn-primary" data-toggle="collapse" data-target="#progress-addnewcommentform" aria-expanded="false" aria-controls="collapseExample"><b><i class="fs-14 pg-plus"></i> Write your comment here</b></button>
                                <div class="collapse" id="progress-addnewcommentform">
                                    <form class="m-t-20">
                                        <textarea type="text" id="progress_notes" name="progress_notes" rows="2" placeholder="write your comment here" class="form-control"></textarea>
                                        <div class="m-t-10 text-right"><button type="button" onclick="add_progresscomments();" class="btn btn-success">Submit</button></div>
                                        <input type="hidden" id="progress_agency" name="progress_agency" value="" >
                                        <input type="hidden" id="progress_contract" name="progress_contract" value="" >

                                    </form>
                                </div>
                            </div>
                            <div id="progresscomment_status"></div>
                            <div id="progresshtmlcomment"> </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- Progress END MODAL -->           
 <?php include_once('templates/footer.php'); ?>   

<!-- BEGIN PAGE LEVEL JS -->
<script src="assets/js/form_wizard.js" type="text/javascript"></script>
<script src="assets/js/form_elements.js" type="text/javascript"></script>
<script src="assets/js/scripts.js" type="text/javascript"></script>
<script src="assets/plugins/upload/all.fine-uploader.min.js"></script>
<script src="assets/plugins/upload/upload-gallery.js"></script>

<link rel="stylesheet" href="pages/css/fine-uploader-new.min.css" type="text/css" />

<!-- END PAGE LEVEL JS -->
 
<script type="text/javascript">   

    $(document).ready(function(){ 
    $('.resources_select').select2();
    });
    
    $("button[name='next']").click(function() { if(document.getElementById('agency_id').value=='') { alert("Please Choose Agency"); return false; } });

    var manualUploader = new qq.FineUploader({
        element: document.getElementById('fine-uploader-manual-trigger'),
        template: 'qq-template-manual-trigger',
        request: {
            endpoint: "/assets/uploader/php-traditional-server/endpoint.php"
        },
        deleteFile: {
            enabled: true,
            endpoint: "/assets/uploader/php-traditional-server/endpoint.php"
        },
        chunking: {
            enabled: true,
            concurrent: {
                enabled: true
            },
            success: {
                endpoint: "/assets/uploader/php-traditional-server/endpoint.php?done"
            }
        },
        resume: {
            enabled: true
        },
        retry: {
            enableAuto: true,
            showButton: true
        },
        autoUpload: true,
        debug: true
    });

    qq(document.getElementById("trigger-upload")).attach("click", function() {
        manualUploader.uploadStoredFiles();
    });
    function ajax_insert_ttaenq()
    {
        if(document.getElementById('agency_id').value=='') {
            alert("Please Choose Agency");
            return false;
        }
        else{
        var contract_num= $('input[name="contract_num"]').val();
        var agency_id= $('select[name="agency_id"]').val();
        var TTA_inquiry_type= $('select[name="TTA_inquiry_type"]').val();
        var TTA_inquiry_notes=$('#TTA_inquiry_notes').val();
        var regarding= $('select[name="regarding"]').val();
        var TTA_desc= $('#TTA_desc').val();
        var TTA_Referral= $('input[name="TTA_Referral"]').val();
        var TTA_Contact_Phone= $('input[name="TTA_Contact_Phone"]').val();
        var TTA_Email= $('input[name="TTA_Email"]').val();
        var assigned_staff= $('input[name="assigned_staff"]').val();
        var supporting_docs= $('input[name="supporting_docs"]').val();

        var resources= $('#resources').val();
        var uploadedimage= $('input[name="uploadedimage"]').val();

        var training_date= "";

        var est_tot= 0;
        var est_q4=  0;
        var est_q3=  0;
        var est_q2=  0;
        var est_q1=  0;
        var frame_end= "";
        var frame_start= "";

        var mod_combination='';
        var other_email= $('input[name="other_email"]').val();
        var mod_other= '';
        var mod_faceface= '';
        var mod_correspondence= '';
        var mod_web= '';

        
        var ismod_combination = $("input[name=mod_combination]").is(":checked");
        if(ismod_combination) mod_combination='combination'; else mod_combination='';
        var ismod_other = $("input[name=mod_other]").is(":checked");
        if(ismod_other) mod_other='other'; else mod_other='';
        var ismod_faceface = $("input[name=mod_faceface]").is(":checked");
        if(ismod_faceface) mod_faceface='faceface'; else mod_faceface='';
        var ismod_correspondence = $("input[name=mod_correspondence]").is(":checked");
        if(ismod_correspondence) mod_correspondence='correspondence'; else mod_correspondence='';
        var ismod_web = $("input[name=mod_web]").is(":checked");
        if(ismod_web) mod_web='web'; else mod_web='';
        
        var formData = {contract_num:contract_num,agency_id:agency_id,TTA_inquiry_type:TTA_inquiry_type,TTA_inquiry_notes:TTA_inquiry_notes,regarding:regarding,TTA_desc:TTA_desc,TTA_Referral:TTA_Referral,TTA_Contact_Phone:TTA_Contact_Phone,TTA_Email:TTA_Email,assigned_staff:assigned_staff,supporting_docs:supporting_docs,resources:resources,uploadedimage:uploadedimage,training_date:training_date,est_tot:est_tot,est_q4:est_q4,est_q3:est_q3,est_q2:est_q2,est_q1:est_q1,frame_end:frame_end,frame_start:frame_start,mod_combination:mod_combination,mod_other:mod_other,mod_faceface:mod_faceface,mod_correspondence:mod_correspondence,mod_web:mod_web,other_email:other_email};
        $.ajax({
            url : "insert_ttaenq.php",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
				alert(data);
                var result=myTrim(data);
                if(result=='success') window.location = "dashboard.php";
                else
                    alert('TTA not saved,Please try again');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
					alert(errorThrown); return false;
            }
        });
      }  
    }
    function loading_comments()
    {
        document.getElementById("loading_comments").style.display = 'block';

    }
    function regarding_comments(contract)
    {
        var agency= $('#agency_id').val();
        $('input[name="com_agency"]').val(agency);
        $('input[name="com_contract"]').val(contract);
        $('#comment_status').html('');$('#regarding_notes').val('');
        if(agency=='')
        {
            alert("Please Choose Agency"); 
            window.location='tta_enquiry.php'; 
            return false;
        }
        var formData = {contract:contract,agency:agency};
        $.ajax({
            url : "get_regarding_notes.php",
            type: "POST",
            data : formData,
            beforeSend: function() {
                loading_comments();
            },
            success: function(data, textStatus, jqXHR)
            {
                document.getElementById("loading_comments").style.display = 'none';
                $("#htmlcomment").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    function add_comments()
    {
        var agency= $('#com_agency').val();
        var contract=$('#com_contract').val();

        var regarding_notes=$('#regarding_notes').val();
        $('#htmlcomment').html('');$('#comment_status').html('');
        var formData = {contract:contract,agency:agency,regarding_notes:regarding_notes};
        $.ajax({
            url : "insert_regarding_notes.php",
            type: "POST",
            data : formData,
            beforeSend: function() {
                loading_comments();
            },
            success: function(data, textStatus, jqXHR)
            {
                document.getElementById("loading_comments").style.display = 'none';
                regarding_comments(contract);
                if(data=='failure') alert('Due to internet problem not reachable database ,Try again');
                else $('#comment_status').html('Comments updated successfully!!!');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    function loadingprogress_comments()
    {
        document.getElementById("loading_progress").style.display = 'block';

    }
    function progress_comments(contract)
    {
        $('#progresshtmlcomment').html('');$('#progresscomment_status').html('');
        var agency= $('#agency_id').val();
        $('input[name="progress_agency"]').val(agency);
        $('input[name="progress_contract"]').val(contract);
        $('#progress_notes').val('');
        if(agency=='')
        {
            alert("Please Choose Agency"); window.location='tta_enquiry.php'; return false;
        }
        var formData = {contract:contract,agency:agency};
        $.ajax({
            url : "get_progress_notes.php",
            type: "POST",
            data : formData,
            beforeSend: function() {
                loadingprogress_comments();
            },
            success: function(data, textStatus, jqXHR)
            {
                document.getElementById("loading_progress").style.display = 'none';
                $("#progresshtmlcomment").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    function add_progresscomments()
    {
        var agency= $('#progress_agency').val();
        var contract=$('#progress_contract').val();

        var progress_notes=$('#progress_notes').val();
        $('#progresshtmlcomment').html('');$('#progresscomment_status').html('');
        var formData = {contract:contract,agency:agency,progress_notes:progress_notes};
        $.ajax({
            url : "insert_progress_notes.php",
            type: "POST",
            data : formData,
            beforeSend: function() {
                loading_comments();
            },
            success: function(data, textStatus, jqXHR)
            {
                document.getElementById("loading_progress").style.display = 'none';
                progress_comments(contract);
                if(data=='failure') alert('Due to internet problem not reachable database ,Try again');
                else $('#progresscomment_status').html('Comments updated successfully!!!');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
    $(".allow_number_only").keydown(function (event) {


        if (event.shiftKey == true) {
            event.preventDefault();
        }

        if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

        } else {
            event.preventDefault();
        }

        if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
            event.preventDefault();

    });
    function calc_estimate_time()
    {
        var sum = 0;
        $('.esttime').each(function(){
            if(this.value != "")
            {
                sum += parseFloat(this.value);
            }
            else
            {
                $(this).val("0");
            }
        });
        $("#est_tot").val(sum);
    }
    function myTrim(x) {
        return x.replace(/^\s+|\s+$/gm,'');
    }
</script>
 
    <script type="text/javascript">
    $(document).ready(function(){
        $('#accordion .panel-collapse').on('show.bs.collapse', function () {
            $(this).siblings('.panel-heading').addClass('active');
            
          });
        $('#accordion .panel-collapse').on('hide.bs.collapse', function () {
            $(this).siblings('.panel-heading').removeClass('active');
          });
    });
    </script>
   
  </body>
</html>
